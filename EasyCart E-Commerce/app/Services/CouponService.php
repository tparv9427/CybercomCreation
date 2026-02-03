<?php

namespace EasyCart\Services;

class CouponService
{
    // DB-driven, no file needed

    /**
     * Validate a coupon code and return the discount percentage/details.
     * 
     * @param string $code
     * @return array|false Returns ['percent' => int] if valid, false otherwise.
     */
    public function validateCoupon($code)
    {
        $pdo = \EasyCart\Core\Database::getInstance()->getConnection();

        $code = trim($code);
        $stmt = $pdo->prepare("SELECT discount_percent FROM coupons WHERE code = :code");
        $stmt->execute([':code' => $code]);
        $row = $stmt->fetch();

        if ($row) {
            return [
                'code' => $code,
                'percent' => $row['discount_percent']
            ];
        }

        return false;
    }

    /**
     * Clear applied coupon if user navigates away from checkout
     * 
     * @param string $currentRoute
     * @return void
     */
    public function clearIfNavigatedAway($currentRoute)
    {
        // Allowed routes that preserve the coupon
        // 'checkout' is the main one. 
        // 'checkout-pricing' is an AJAX route for pricing updates, so we must allow it.
        // 'ajax/cart' etc are different routes.

        $allowed = ['checkout', 'checkout-pricing'];

        if (!in_array($currentRoute, $allowed) && isset($_SESSION['applied_coupon'])) {
            unset($_SESSION['applied_coupon']);
        }
    }
}
