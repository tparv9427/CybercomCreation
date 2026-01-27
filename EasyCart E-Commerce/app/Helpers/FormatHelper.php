<?php

namespace EasyCart\Helpers;

/**
 * FormatHelper
 * 
 * Migrated from: includes/config.php (formatPrice function)
 */
class FormatHelper
{
    /**
     * Format price with currency symbol
     * 
     * @param float $price
     * @return string
     */
    public static function price($price)
    {
        $currency = defined('CURRENCY') ? CURRENCY : '$';
        return $currency . number_format($price, 2);
    }

    /**
     * Format date
     * 
     * @param string $date
     * @param string $format
     * @return string
     */
    public static function date($date, $format = 'F j, Y')
    {
        return date($format, strtotime($date));
    }

    /**
     * Truncate text
     * 
     * @param string $text
     * @param int $length
     * @param string $suffix
     * @return string
     */
    public static function truncate($text, $length = 100, $suffix = '...')
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length) . $suffix;
    }
}
