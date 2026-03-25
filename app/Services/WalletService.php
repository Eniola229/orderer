<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\EscrowHold;
use App\Models\Order;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WalletService
{
    /**
     * Get or create a wallet for any model (User or Seller)
     */
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
                'currency'       => 'USD',
            ]
        );
    }

    /**
     * Credit a wallet
     */
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
            $wallet = $this->getOrCreate($owner);

            $balanceBefore = $wallet->balance;
            $wallet->increment('balance', $amount);
            $wallet->refresh();

            // Also sync the denormalized balance on the model
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
     * Debit a wallet
     */
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
     * Hold money in escrow when order is placed
     */
    public function holdEscrow(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $seller = $item->seller;
                $category = \App\Models\Category::find(
                    \App\Models\Product::find($item->orderable_id)?->category_id
                );

                $commissionRate   = $category?->commission_rate ?? 10.00;
                $commissionAmount = round($item->total_price * ($commissionRate / 100), 2);
                $sellerAmount     = $item->total_price - $commissionAmount;

                EscrowHold::create([
                    'order_id'           => $order->id,
                    'seller_id'          => $seller->id,
                    'buyer_id'           => $order->user_id,
                    'amount'             => $item->total_price,
                    'commission_amount'  => $commissionAmount,
                    'seller_amount'      => $sellerAmount,
                    'status'             => 'held',
                    'release_at'         => now()->addDays(7),
                ]);

                // Update order item with commission
                $item->update([
                    'commission_rate'   => $commissionRate,
                    'commission_amount' => $commissionAmount,
                    'seller_earnings'   => $sellerAmount,
                ]);
            }
        });
    }

    /**
     * Release escrow to seller wallet when order is delivered
     */
    public function releaseEscrow(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $escrows = EscrowHold::where('order_id', $order->id)
                ->where('status', 'held')
                ->get();

            foreach ($escrows as $escrow) {
                $seller = $escrow->seller;

                // Credit seller
                $this->credit(
                    $seller,
                    $escrow->seller_amount,
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
     * Refund escrow to buyer if order is cancelled
     */
    public function refundEscrow(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $escrows = EscrowHold::where('order_id', $order->id)
                ->where('status', 'held')
                ->get();

            foreach ($escrows as $escrow) {
                $buyer = $escrow->buyer;

                $this->credit(
                    $buyer,
                    $escrow->amount,
                    'escrow_refund',
                    "Refund for cancelled order #{$order->order_number}",
                    'order',
                    $order->id
                );

                $escrow->update(['status' => 'refunded']);
            }
        });
    }

    /**
     * Debit seller ads balance
     */
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

    /**
     * Top up ads balance
     */
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