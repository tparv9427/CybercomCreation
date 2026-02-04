<?php

namespace EasyCart\Controllers;

use EasyCart\Services\AuthService;
use EasyCart\Services\DashboardService;
use EasyCart\Repositories\CategoryRepository;

class DashboardController
{
    private $dashboardService;
    private $categoryRepo;

    public function __construct()
    {
        $this->dashboardService = new DashboardService();
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
        $data = $this->dashboardService->getDashboardData($userId);

        $stats = $data['stats'];
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
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $chartData = $this->dashboardService->getChartTimeline($userId);

        echo json_encode(array_merge(['success' => true], $chartData));
    }
}
