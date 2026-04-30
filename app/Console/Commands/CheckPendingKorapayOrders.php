<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Seller;
use App\Models\Notification;
use App\Models\FlashSale;
use App\Services\KorapayService;
use App\Services\WalletService;
use App\Services\ShipbubbleService;
use App\Services\BrevoMailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckPendingKorapayOrders extends Command
{
    protected $signature   = 'korapay:check-pending-orders';
    protected $description = 'Check pending Korapay orders and update payment status';

    public function __construct(
        protected WalletService     $walletService,
        protected ShipbubbleService $shipbubble,
        protected BrevoMailService  $brevo,
    ) {
        parent::__construct();
    }

    public function handle(KorapayService $korapay): int
    {
        $pendingOrders = Order::where('payment_method', 'korapay')
            ->where('payment_status', 'pending')
            ->whereNotNull('payment_reference')
            ->where('created_at', '>=', now()->subHours(24))
            ->with(['items.product', 'items.seller', 'user'])
            ->get();

        if ($pendingOrders->isEmpty()) {
            $this->info('No pending Korapay orders found.');
            return self::SUCCESS;
        }

        $this->info("Found {$pendingOrders->count()} pending order(s). Checking...");

        $paid    = 0;
        $failed  = 0;
        $skipped = 0;
        $errors  = 0;

        $bar = $this->output->createProgressBar($pendingOrders->count());
        $bar->start();

        foreach ($pendingOrders as $order) {
            try {
                $data          = $korapay->verifyTransaction($order->payment_reference);
                $korapayStatus = $data['status'] ?? null;

                if ($korapayStatus === 'success') {
                    DB::transaction(function () use ($order) {
                        $order->update(['payment_status' => 'paid']);

                        // Flash sale quantity — same as callback()
                        foreach ($order->items as $orderItem) {
                            $product = $orderItem->orderable;
                            if (!$product) continue;

                            $flashSale = FlashSale::where('product_id', $product->id)
                                ->where('is_active', true)
                                ->where('starts_at', '<=', now())
                                ->where('ends_at', '>=', now())
                                ->first();

                            if ($flashSale) {
                                $flashSale->increment('quantity_sold', $orderItem->quantity);
                            }
                        }

                        // Hold escrow
                        $this->walletService->holdEscrow($order);
                    });

                    // Book shipment — outside transaction, same as callback()
                    $this->bookShipmentForOrder($order);

                    // Send emails + seller notifications
                    $this->sendOrderEmails($order);

                    $paid++;

                } elseif (in_array($korapayStatus, ['failed', 'expired', 'reversed'])) {
                    DB::transaction(function () use ($order) {
                        // 1. Revert inventory changes
                        foreach ($order->items as $orderItem) {
                            $product = $orderItem->orderable;
                            if (!$product) continue;
                            
                            // Revert stock (opposite of what happened when order was created)
                            $product->increment('stock', $orderItem->quantity);
                            
                            // Revert total_sold (opposite of what happened when order was created)
                            $product->decrement('total_sold', $orderItem->quantity);

                        }
                        
                        // 2. Mark order as failed
                        $order->update([
                            'payment_status' => 'failed',
                            'status'         => 'cancelled',
                        ]);
                    });
                    $failed++;
                } else {
                    $skipped++;
                }

            } catch (\Exception $e) {
                $errors++;
                \Log::error("korapay:check-pending-orders — order #{$order->order_number}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Result', 'Count'],
            [
                ['Paid',                   $paid],
                ['Failed',                 $failed],
                ['Skipped (still pending)', $skipped],
                ['Errors',                 $errors],
            ]
        );

        return self::SUCCESS;
    }

    // ── Mirrors CheckoutController::bookShipmentForOrder() ────────────────
    protected function bookShipmentForOrder(Order $order): void
    {
        $order->load('items.product');

        $sellerGroups = $order->items->groupBy('seller_id');
        $rateDataMap  = $order->shipping_rate_data ?? [];

        foreach ($sellerGroups as $sellerId => $sellerItems) {
            $rateData = $order->is_multi_seller
                ? ($rateDataMap[(string) $sellerId] ?? [])
                : (isset($rateDataMap[(string) $sellerId])
                    ? $rateDataMap[(string) $sellerId]
                    : (array_values($rateDataMap)[0] ?? $rateDataMap));

            $courierId   = $rateData['courier_id'] ?? $rateData['id'] ?? null;
            $serviceCode = $rateData['service_code'] ?? $order->shipping_service_code;
            $token       = $rateData['request_token'] ?? null;

            if (!$courierId) {
                \Log::warning("checkPendingKorapay — skipping seller {$sellerId}: missing courier_id");
                continue;
            }

            $sellerWeight = $sellerItems->sum(fn($i) => ($i->product->weight_kg ?? 0.5) * $i->quantity);
            $sellerValue  = $sellerItems->sum('total_price');
            $seller       = Seller::find($sellerId);

            try {
                $shipment = $this->shipbubble->createShipment(
                    $serviceCode,
                    (string) $courierId,
                    $this->buildSenderPayload($seller),
                    $this->buildRecipientPayload($order),
                    $this->buildParcelPayload($sellerItems, max($sellerWeight, 0.5), $sellerValue, $order->order_number),
                    $token
                );

                $trackingNumber = $shipment['courier']['tracking_code'] ?? null;
                $trackingUrl    = $shipment['tracking_url'] ?? null;
                $shipmentId     = $shipment['order_id'] ?? null;
                $estDelivery    = $shipment['estimated_delivery_date'] ?? null;
                $shipStatus     = $shipment['status'] ?? 'pending';

                foreach ($sellerItems as $orderItem) {
                    $orderItem->update([
                        'shipbubble_shipment_id'  => $shipmentId,
                        'courier_id'              => $courierId,
                        'tracking_number'         => $trackingNumber,
                        'tracking_url'            => $trackingUrl,
                        'shipping_status'         => $shipStatus,
                        'estimated_delivery_date' => $estDelivery,
                    ]);
                }

            } catch (\Exception $e) {
                \Log::error("checkPendingKorapay — shipment failed for seller {$sellerId} order #{$order->order_number}: " . $e->getMessage());
            }
        }
    }

    // ──  CheckoutController::sendOrderEmails() ─────────────────────
    protected function sendOrderEmails(Order $order): void
    {
        try {
            $user = $order->user;
            $this->brevo->sendOrderPlacedBuyer($user, $order);

            $order->load('items.seller');
            $sellerIds = $order->items->pluck('seller_id')->unique();

            foreach ($sellerIds as $sellerId) {
                $seller = Seller::find($sellerId);
                if ($seller) {
                    $this->brevo->sendOrderNotifySeller($seller, $order);
                    Notification::create([
                        'notifiable_type' => 'App\Models\Seller',
                        'notifiable_id'   => $sellerId,
                        'type'            => 'new_order',
                        'title'           => 'New Order Received',
                        'body'            => "New order #{$order->order_number} is waiting for you.",
                        'action_url'      => route('seller.orders.show', $order->id),
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::error("checkPendingKorapay — sendOrderEmails failed for order #{$order->order_number}: " . $e->getMessage());
        }
    }

    // ── Same helpers as CheckoutController ───────────────────────────────
    protected function buildSenderPayload(?Seller $seller): array
    {
        return [
            'name'    => $seller->business_name ?? config('app.name'),
            'email'   => $seller->email         ?? config('mail.from.address'),
            'phone'   => $seller->phone         ?? '08000000000',
            'address' => $seller->address       ?? 'Orderer Fulfillment Center, Lagos',
            'city'    => $seller->city          ?? 'Lagos',
            'state'   => $seller->state         ?? 'Lagos',
            'country' => 'NG',
        ];
    }

    protected function buildRecipientPayload(Order $order): array
    {
        return [
            'name'    => $order->shipping_name,
            'email'   => $order->user->email ?? '',
            'phone'   => $order->shipping_phone,
            'address' => $order->shipping_address,
            'city'    => $order->shipping_city,
            'state'   => $order->shipping_state,
            'country' => $order->shipping_country ?? 'NG',
        ];
    }

    protected function buildParcelPayload($items, float $weight, float $value, string $orderNumber): array
    {
        return [
            'weight' => $weight,
            'length' => 20,
            'width'  => 20,
            'height' => 20,
            'items'  => $items->map(fn($i) => [
                'name'     => $i->item_name,
                'quantity' => $i->quantity,
                'value'    => max((float) $i->unit_price, 10),
            ])->toArray(),
        ];
    }
}