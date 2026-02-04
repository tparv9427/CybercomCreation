<?php

namespace EasyCart\Controllers;

use EasyCart\Services\AuthService;
use EasyCart\Repositories\OrderRepository;
use EasyCart\Repositories\CategoryRepository;
use EasyCart\Core\View;

class DashboardController
{
    private $orderRepo;
    private $categoryRepo;

    public function __construct()
    {
        $this->orderRepo = new OrderRepository();
        $this->categoryRepo = new CategoryRepository();
    }

    /**
     * Display user dashboard
     */
    public function index()
    {
        if (!AuthService::check()) {
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $stats = $this->orderRepo->getDashboardStats($userId);

        $categories = $this->categoryRepo->getAll();
        $page_title = 'User Dashboard';

        include __DIR__ . '/../Views/layouts/header.php';
        include __DIR__ . '/../Views/dashboard/index.php';
        include __DIR__ . '/../Views/layouts/footer.php';
    }

    /**
     * API endpoint for chart data
     */
    public function chartData()
    {
        header('Content-Type: application/json');

        if (!AuthService::check()) {
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $data = $this->orderRepo->getChartData($userId);

        echo json_encode([
            'success' => true,
            'labels' => array_column($data, 'order_date'),
            'values' => array_map(function ($v) {
                return (float) $v; }, array_column($data, 'daily_total'))
        ]);
    }
}
