<?php

namespace EasyCart\Services;

use EasyCart\Repositories\OrderRepository;

class DashboardService
{
    private $orderRepo;

    public function __construct()
    {
        $this->orderRepo = new OrderRepository();
    }

    /**
     * Get consolidated dashboard data
     */
    public function getDashboardData($userId)
    {
        return [
            'stats' => $this->orderRepo->getDashboardStats($userId),
            'chart' => $this->getChartTimeline($userId)
        ];
    }

    /**
     * Generate a complete 30-day timeline for the chart
     */
    public function getChartTimeline($userId)
    {
        $dbData = $this->orderRepo->getChartData($userId);

        // Map DB results by date for fast lookup
        $revenueMap = [];
        $countMap = [];

        foreach ($dbData as $row) {
            $dateStr = date('Y-m-d', strtotime($row['order_date']));
            $revenueMap[$dateStr] = (float) ($row['daily_total'] ?? 0);
            $countMap[$dateStr] = (int) ($row['order_count'] ?? 1); // Fallback to 1 if not in query
        }

        $labels = [];
        $revenue = [];
        $orders = [];

        // Generate the last 30 days
        for ($i = 29; $i >= 0; $i--) {
            $timestamp = strtotime("-$i days");
            $dateKey = date('Y-m-d', $timestamp);

            $labels[] = date('M d', $timestamp);
            $revenue[] = $revenueMap[$dateKey] ?? 0;
            $orders[] = $countMap[$dateKey] ?? 0;
        }

        return [
            'labels' => $labels,
            'revenue' => $revenue,
            'orders' => $orders
        ];
    }
}
