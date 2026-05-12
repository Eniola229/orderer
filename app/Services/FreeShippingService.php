<?php
// app/Services/FreeShippingService.php

namespace App\Services;

use App\Models\FreeShippingRule;
use App\Models\User;

class FreeShippingService
{
    /**
     * Find the best applicable free-shipping rule for this user & cart.
     * Returns the rule (or null) and the discount amount to apply.
     *
     * @param  User   $user
     * @param  float  $shippingFee       — the courier's quoted fee
     * @param  float  $orderSubtotal     — cart subtotal before shipping
     * @param  array  $cartProductIds    — product IDs in the cart
     * @param  array  $cartSellerIds     — seller IDs in the cart
     * @return array{rule: FreeShippingRule|null, discount: float}
     */
    public function resolve(
        User $user,
        float $shippingFee,
        float $orderSubtotal,
        array $cartProductIds,
        array $cartSellerIds
    ): array {
        $rules = FreeShippingRule::active()
            ->orderByDesc('created_at')
            ->get();

        foreach ($rules as $rule) {
            // Check minimum order amount
            if ($rule->minimum_order_amount && $orderSubtotal < $rule->minimum_order_amount) {
                continue;
            }

            // Check user eligibility
            if (!$rule->qualifiesForUser($user)) {
                continue;
            }

            // Check product/seller scope
            if (!$rule->qualifiesForCart($cartProductIds, $cartSellerIds)) {
                continue;
            }

            // Calculate discount (capped if needed)
            $discount = $rule->max_discount_amount
                ? min($shippingFee, $rule->max_discount_amount)
                : $shippingFee;

            return ['rule' => $rule, 'discount' => round($discount, 2)];
        }

        return ['rule' => null, 'discount' => 0.0];
    }
}