<?php

namespace EasyCart\Services;

class OrderStatusService
{
    /**
     * Get the display status based on the database status and order timestamps.
     * This simulates a logistics progression.
     * 
     * @param string $dbStatus The raw status from the database.
     * @param string $createdAt The creation timestamp of the order.
     * @return string The resolved status for display.
     */
    public static function getStatus($dbStatus, $createdAt)
    {
        // If order is specifically cancelled, respect that.
        if ($dbStatus === 'cancelled') {
            return 'cancelled';
        }

        // Logic based on Order age (in seconds)
        // This makes the system feel "alive" even without a logistics API.
        $age = time() - strtotime($createdAt);

        $minute = 60;
        $hour = 3600;
        $day = 86400;

        // progression: Processing -> Shipped -> Out for Delivery -> Delivered
        if ($age < 5 * $minute) {
            return 'processing';
        } elseif ($age < 1 * $hour) {
            return 'shipped';
        } elseif ($age < 1 * $day) {
            return 'out_for_delivery';
        } else {
            return 'delivered';
        }
    }

    /**
     * Translates a internal status slug to a human-readable label.
     * 
     * @param string $status
     * @return string
     */
    public static function getLabel($status)
    {
        $labels = [
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'out_for_delivery' => 'Out for Delivery',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled'
        ];

        return $labels[$status] ?? ucfirst($status);
    }
}
