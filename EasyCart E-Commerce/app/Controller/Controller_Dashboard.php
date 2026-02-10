<?php

namespace EasyCart\Controller;

use EasyCart\Services\AuthService;
use EasyCart\Services\DashboardService;
use EasyCart\Collection\Collection_Category;
use EasyCart\View\View_Dashboard;

/**
 * Controller_Dashboard — User Dashboard
 * 
 * No SQL, no HTML.
 */
class Controller_Dashboard extends Controller_Abstract
{
    private $dashboardService;
    private $categoryCollection;

    public function __construct()
    {
        $this->dashboardService = new DashboardService();
        $this->categoryCollection = new Collection_Category();
    }

    public function index(): void
    {
        if (!AuthService::check()) {
            $this->redirect('/login');
        }

        $userId = $_SESSION['user_id'];
        $data = $this->dashboardService->getDashboardData($userId);
        $categories = $this->categoryCollection->getAll();

        $contentView = new View_Dashboard([
            'stats' => $data['stats'],
            'categories' => $categories,
        ]);

        $this->renderWithLayout($contentView, [
            'page_title' => 'User Dashboard',
            'categories' => $categories,
        ]);
    }

    public function chartData(): void
    {
        if (!AuthService::check()) {
            $this->jsonResponse(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $chartData = $this->dashboardService->getChartTimeline($userId);
        $this->jsonResponse(array_merge(['success' => true], $chartData));
    }
}
