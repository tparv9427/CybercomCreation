<?php
/** @var array $stats */
$totalOrders = $stats['total_orders'] ?? 0;
$totalSpent = $stats['total_spent'] ?? 0;
?>
<link rel="stylesheet" href="/assets/css/dashboard.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="breadcrumb">
    <a href="/">Home</a> / Dashboard
</div>

<div class="container dashboard-container">
    <div class="section-header">
        <h2 class="section-title">Account Dashboard</h2>
        <p class="section-subtitle">Overview of your activity and spending</p>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📦</div>
            <div class="stat-content">
                <span class="stat-label">Total Orders</span>
                <h3 class="stat-value">
                    <?php echo $totalOrders; ?>
                </h3>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-content">
                <span class="stat-label">Total Spent</span>
                <h3 class="stat-value">
                    <?php echo \EasyCart\Helpers\FormatHelper::price($totalSpent); ?>
                </h3>
            </div>
        </div>

        <div class="stat-card profile-card">
            <div class="stat-icon">👤</div>
            <div class="stat-content">
                <span class="stat-label">Welcome back,</span>
                <h3 class="stat-value">
                    <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
                </h3>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="chart-section">
        <div class="chart-header">
            <h3>Spending Overview</h3>
            <p>Your orders over the last 30 days</p>
        </div>
        <div class="chart-container">
            <canvas id="orderChart"></canvas>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <a href="/orders" class="action-card">
            <span>View Recent Orders</span>
            <span class="arrow">→</span>
        </a>
        <a href="/wishlist" class="action-card">
            <span>Manage Wishlist</span>
            <span class="arrow">→</span>
        </a>
        <a href="/products" class="action-card">
            <span>Continue Shopping</span>
            <span class="arrow">→</span>
        </a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        fetch('/api/dashboard/chart')
            .then(response => response.json())
            .then(data => {
                if (!data.success) return;

                const ctx = document.getElementById('orderChart').getContext('2d');

                // Create Gradient
                const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, 'rgba(37, 99, 235, 0.4)');
                gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Daily Spending',
                            data: data.revenue,
                            borderColor: '#2563eb',
                            borderWidth: 3,
                            backgroundColor: gradient,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#2563eb',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#1f2937',
                                padding: 12,
                                titleFont: { size: 14 },
                                bodyFont: { size: 14 },
                                callbacks: {
                                    label: function (context) {
                                        return ' Spent: <?php echo CURRENCY; ?>' + context.parsed.y.toLocaleString();
                                    }
                                }
                            }
                        },
                        animation: {
                            duration: 2000,
                            easing: 'easeOutQuart'
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(0,0,0,0.05)' },
                                ticks: {
                                    callback: value => '<?php echo CURRENCY; ?>' + value.toLocaleString()
                                }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            });
    });
</script>