<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\EscrowHold;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function getOrCreate($owner): Wallet
    {
        return Wallet::firstOrCreate(
            [
                'walletable_type' => get_class($owner),
                'walletable_id'   => $owner->id,
            ],
            [
                'balance'        => 0.00,
                'escrow_balance' => 0.00,
                'ads_balance'    => 0.00,
                'currency'       => 'NGN',
            ]
        );
    }

    public function credit(
        $owner,
        float $amount,
        string $type,
        string $description,
        string $relatedType = null,
        string $relatedId   = null
    ): WalletTransaction {
        return DB::transaction(function () use (
            $owner, $amount, $type, $description, $relatedType, $relatedId
        ) {
            $wallet        = $this->getOrCreate($owner);
            $balanceBefore = $wallet->balance;
            $wallet->increment('balance', $amount);
            $wallet->refresh();
            $owner->update(['wallet_balance' => $wallet->balance]);

            return WalletTransaction::create([
                'wallet_id'      => $wallet->id,
                'type'           => $type,
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $wallet->balance,
                'description'    => $description,
                'related_type'   => $relatedType,
                'related_id'     => $relatedId,
                'status'         => 'completed',
            ]);
        });
    }

    public function debit(
        $owner,
        float $amount,
        string $type,
        string $description,
        string $relatedType = null,
        string $relatedId   = null
    ): WalletTransaction {
        return DB::transaction(function () use (
            $owner, $amount, $type, $description, $relatedType, $relatedId
        ) {
            $wallet = $this->getOrCreate($owner);

            if ($wallet->balance < $amount) {
                throw new \Exception('Insufficient wallet balance.');
            }

            $balanceBefore = $wallet->balance;
            $wallet->decrement('balance', $amount);
            $wallet->refresh();
            $owner->update(['wallet_balance' => $wallet->balance]);

            return WalletTransaction::create([
                'wallet_id'      => $wallet->id,
                'type'           => $type,
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $wallet->balance,
                'description'    => $description,
                'related_type'   => $relatedType,
                'related_id'     => $relatedId,
                'status'         => 'completed',
            ]);
        });
    }

    /**
     * Create ONE escrow row per order item.
     * Simple, clean — no merging, no incrementing.
     */
    public function holdEscrow(Order $order): void
    {
        // Hard guard — never run twice on the same order
        $alreadyExists = EscrowHold::where('order_id', $order->id)->exists();
        if ($alreadyExists) {
            \Log::warning("holdEscrow — escrow already exists for order #{$order->order_number}, skipping.");
            return;
        }

        $order->loadMissing('items');

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                if ($item->status === 'cancelled') continue;

                $commissionRate   = (float) ($item->commission_rate ?? 10.00);
                $commissionAmount = round((float) $item->total_price * ($commissionRate / 100), 2);
                $sellerAmount     = (float) $item->total_price - $commissionAmount;

                EscrowHold::create([
                    'order_id'          => $order->id,
                    'order_item_id'     => $item->id,        // ← one row per item
                    'seller_id'         => $item->seller_id,
                    'buyer_id'          => $order->user_id,
                    'amount'            => $item->total_price,
                    'commission_amount' => $commissionAmount,
                    'seller_amount'     => $sellerAmount,
                    'status'            => 'held',
                    'release_at'        => now()->addDays(7),
                ]);

                // Keep item commission fields in sync
                $item->update([
                    'commission_rate'   => $commissionRate,
                    'commission_amount' => $commissionAmount,
                    'seller_earnings'   => $sellerAmount,
                ]);

                \Log::info("holdEscrow — created escrow for item '{$item->item_name}' seller #{$item->seller_id}, amount ₦{$item->total_price}");
            }
        });
    }

    /**
     * Release escrow for a single delivered item → credit seller.
     * If all items delivered → mark order completed.
     */
    public function releaseEscrowForItem(OrderItem $item): void
    {
        DB::transaction(function () use ($item) {
            $order  = $item->order;
            $seller = $item->seller;

            // Find this item's specific escrow row
            $escrow = EscrowHold::where('order_item_id', $item->id)
                ->where('status', 'held')
                ->first();

            if (!$escrow) {
                \Log::warning("releaseEscrowForItem — no held escrow for item #{$item->id} '{$item->item_name}'");
                return;
            }

            // Credit seller with this item's earnings
            $this->credit(
                $seller,
                (float) $escrow->seller_amount,
                'escrow_release',
                "Payment released for item '{$item->item_name}' in order #{$order->order_number}",
                'order',
                $order->id
            );

            $escrow->update([
                'status'      => 'released',
                'released_at' => now(),
            ]);

            $item->update([
                'status'       => 'completed',
                'delivered_at' => now(),
            ]);

            \Log::info("releaseEscrowForItem — ₦{$escrow->seller_amount} released to seller #{$seller->id} for item '{$item->item_name}'");

            // Check if ALL items are now delivered/completed
            $order->load('items');
            if ($order->allItemsDelivered()) {
                $order->update([
                    'status'       => 'completed',
                    'completed_at' => now(),
                ]);

                OrderStatusLog::create([
                    'order_id'        => $order->id,
                    'from_status'     => 'delivered',
                    'to_status'       => 'completed',
                    'changed_by_type' => 'system',
                    'changed_by_id'   => null,
                    'note'            => 'All items delivered. Order auto-completed.',
                ]);
            }
        });
    }

    /**
     * Force-release ALL held escrows to sellers (admin force-complete).
     */
    public function releaseEscrow(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $escrows = EscrowHold::where('order_id', $order->id)
                ->where('status', 'held')
                ->with('seller')
                ->get();

            foreach ($escrows as $escrow) {
                $seller = $escrow->seller;

                $this->credit(
                    $seller,
                    (float) $escrow->seller_amount,
                    'escrow_release',
                    "Payment released for order #{$order->order_number}",
                    'order',
                    $order->id
                );

                $escrow->update([
                    'status'      => 'released',
                    'released_at' => now(),
                ]);
            }

            $order->update([
                'status'       => 'completed',
                'completed_at' => now(),
            ]);
        });
    }

    /**
     * Refund a single order item's escrow to the buyer.
     * Looks up escrow by order_item_id — guaranteed unique, no ambiguity.
     */
    public function cancelItemEscrow(OrderItem $item): void
    {
        DB::transaction(function () use ($item) {
            $order = $item->order;
            $buyer = $order->user;

            if (!$buyer) {
                throw new \Exception("Buyer not found for order #{$order->order_number}");
            }

            // Guard — item already done
            if (in_array($item->status, ['cancelled', 'delivered', 'completed'])) {
                throw new \Exception("Item #{$item->id} already {$item->status}, cannot cancel.");
            }

            // Find THIS item's specific escrow row — no ambiguity
            $escrow = \App\Models\EscrowHold::where('order_item_id', $item->id)
                ->where('status', 'held')
                ->first();

            if (!$escrow) {
                throw new \Exception("No held escrow found for item '{$item->item_name}'. Cannot refund.");
            }
            
            $refundAmount = (float) $escrow->amount;

            \Log::info("cancelItemEscrow — refunding ₦{$refundAmount} to buyer #{$buyer->id} for item '{$item->item_name}' on order #{$order->order_number}");

            $this->credit(
                $buyer,
                $refundAmount,
                'escrow_refund',
                "Refund for cancelled item '{$item->item_name}' in order #{$order->order_number}",
                'order_item',
                $item->id
            );

            $escrow->update([
                'status'      => 'refunded',
                'released_at' => now(),
            ]);

            $item->update([
                'status'       => 'cancelled',
                'delivered_at' => null,
            ]);

            // Check if entire order is now done
            $order->load('items');
            $allDone      = $order->items->every(fn($i) => in_array($i->status, ['cancelled', 'delivered', 'completed']));
            $allDelivered = $order->items->every(fn($i) => in_array($i->status, ['delivered', 'completed']));

            if ($allDelivered) {
                $order->update(['status' => 'completed', 'completed_at' => now()]);
            } elseif ($allDone) {
                $order->update(['status' => 'cancelled']);
            }
        });
    }
    /**
     * Full order refund fallback — refunds buyer order->total (subtotal + shipping).
     * Used only when forceRefund needs a single-shot full refund.
     */
    public function refundEscrow(Order $order): void
    {
        DB::transaction(function () use ($order) {
            if (in_array($order->status, ['cancelled', 'refunded'])) {
                \Log::warning("refundEscrow — order #{$order->order_number} already {$order->status}, skipping.");
                return;
            }

            $heldEscrows = EscrowHold::where('order_id', $order->id)
                ->where('status', 'held')
                ->get();

            if ($heldEscrows->isEmpty()) {
                \Log::warning("refundEscrow — no held escrows for order #{$order->order_number}");
                return;
            }

            $buyer = $order->user;
            if (!$buyer) {
                \Log::error("refundEscrow — buyer not found for order #{$order->order_number}");
                return;
            }

            $refundAmount = (float) $order->total;

            $this->credit(
                $buyer,
                $refundAmount,
                'escrow_refund',
                "Refund for cancelled order #{$order->order_number} (subtotal ₦{$order->subtotal} + shipping ₦{$order->shipping_fee})",
                'order',
                $order->id
            );

            foreach ($heldEscrows as $escrow) {
                $escrow->update(['status' => 'refunded', 'released_at' => now()]);
            }
        });
    }

    public function debitAdsBalance(Seller $seller, float $amount, string $adId): void
    {
        DB::transaction(function () use ($seller, $amount, $adId) {
            $wallet = $this->getOrCreate($seller);

            if ($wallet->ads_balance < $amount) {
                throw new \Exception('Insufficient ads balance.');
            }

            $wallet->decrement('ads_balance', $amount);
            $seller->decrement('ads_balance', $amount);

            WalletTransaction::create([
                'wallet_id'      => $wallet->id,
                'type'           => 'ads_debit',
                'amount'         => $amount,
                'balance_before' => $wallet->ads_balance + $amount,
                'balance_after'  => $wallet->ads_balance,
                'description'    => 'Ads spend deduction',
                'related_type'   => 'ad',
                'related_id'     => $adId,
                'status'         => 'completed',
            ]);
        });
    }

    public function topupAdsBalance(Seller $seller, float $amount): void
    {
        DB::transaction(function () use ($seller, $amount) {
            $wallet = $this->getOrCreate($seller);
            $wallet->increment('ads_balance', $amount);
            $seller->increment('ads_balance', $amount);

            WalletTransaction::create([
                'wallet_id'      => $wallet->id,
                'type'           => 'credit',
                'amount'         => $amount,
                'balance_before' => $wallet->ads_balance - $amount,
                'balance_after'  => $wallet->ads_balance,
                'description'    => 'Ads balance top-up',
                'status'         => 'completed',
            ]);
        });
    }
}