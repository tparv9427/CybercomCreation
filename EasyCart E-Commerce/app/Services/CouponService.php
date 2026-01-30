<?php

namespace EasyCart\Services;

class CouponService
{
    private $couponsFile;

    public function __construct()
    {
        $this->couponsFile = __DIR__ . '/../../data/coupons.json';
    }

    /**
     * Validate a coupon code and return the discount percentage/details.
     * 
     * @param string $code
     * @return array|false Returns ['percent' => int] if valid, false otherwise.
     */
    public function validateCoupon($code)
    {
        if (!file_exists($this->couponsFile)) {
            return false;
        }

        $json = file_get_contents($this->couponsFile);
        $coupons = json_decode($json, true);

        if (!$coupons) {
            return false;
        }

        // Case-insensitive check
        $code = strtoupper(trim($code));

        if (array_key_exists($code, $coupons)) {
            return [
                'code' => $code,
                'percent' => $coupons[$code]
            ];
        }

        return false;
    }
}
