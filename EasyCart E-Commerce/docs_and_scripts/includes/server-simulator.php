<?php
/**
 * EasyCart Server Simulator v4.0-fake-server
 * Mimics real server delays for testing purposes
 * 
 * Toggle delays on/off and adjust profiles for different scenarios
 */

// ==================== CONFIGURATION ====================

// Enable/Disable Server Delays
define('SERVER_DELAYS_ENABLED', true); // Set to false to disable all delays

// Delay Profile: 'FAST', 'NORMAL', 'SLOW', 'UNRELIABLE'
define('SERVER_PROFILE', 'NORMAL');

// ==================== DELAY SETTINGS (in milliseconds) ====================

$delay_profiles = [
    'FAST' => [
        'AJAX_DELAY' => [50, 150],          // Cart, wishlist operations
        'PAGE_LOAD_DELAY' => [100, 300],    // Initial page loads
        'SEARCH_DELAY' => [80, 200],        // Search queries
        'AUTH_DELAY' => [150, 400],         // Login, signup
        'CHECKOUT_DELAY' => [300, 600],     // Order processing
        'IMAGE_DELAY' => [30, 100],         // Image loading
    ],
    'NORMAL' => [
        'AJAX_DELAY' => [300, 800],
        'PAGE_LOAD_DELAY' => [500, 1200],
        'SEARCH_DELAY' => [400, 1000],
        'AUTH_DELAY' => [600, 1500],
        'CHECKOUT_DELAY' => [1000, 2000],
        'IMAGE_DELAY' => [100, 300],
    ],
    'SLOW' => [
        'AJAX_DELAY' => [800, 2000],
        'PAGE_LOAD_DELAY' => [1500, 3000],
        'SEARCH_DELAY' => [1000, 2500],
        'AUTH_DELAY' => [1500, 3500],
        'CHECKOUT_DELAY' => [2500, 5000],
        'IMAGE_DELAY' => [300, 800],
    ],
    'UNRELIABLE' => [
        'AJAX_DELAY' => [200, 3000],        // Random spikes
        'PAGE_LOAD_DELAY' => [300, 4000],
        'SEARCH_DELAY' => [200, 3500],
        'AUTH_DELAY' => [400, 5000],
        'CHECKOUT_DELAY' => [1000, 8000],
        'IMAGE_DELAY' => [50, 1500],
    ]
];

// ==================== FUNCTIONS ====================

/**
 * Simulate server delay
 * @param string $type - Type of delay (AJAX_DELAY, PAGE_LOAD_DELAY, etc.)
 */
function simulateDelay($type = 'AJAX_DELAY') {
    global $delay_profiles;
    
    if (!SERVER_DELAYS_ENABLED) {
        return; // Skip if delays are disabled
    }
    
    $profile = $delay_profiles[SERVER_PROFILE] ?? $delay_profiles['NORMAL'];
    
    if (!isset($profile[$type])) {
        $type = 'AJAX_DELAY'; // Fallback
    }
    
    $delay_range = $profile[$type];
    $delay_ms = rand($delay_range[0], $delay_range[1]);
    
    // Convert milliseconds to microseconds for usleep
    usleep($delay_ms * 1000);
    
    // Log delay (optional - for debugging)
    if (defined('DEBUG_DELAYS') && DEBUG_DELAYS) {
        error_log("Server Delay: {$type} = {$delay_ms}ms");
    }
}

/**
 * Simulate random server error (for UNRELIABLE profile)
 * @param float $error_rate - Probability of error (0.0 to 1.0)
 * @return bool - True if error should occur
 */
function simulateRandomError($error_rate = 0.05) {
    if (!SERVER_DELAYS_ENABLED || SERVER_PROFILE !== 'UNRELIABLE') {
        return false;
    }
    
    return (rand(1, 100) / 100) <= $error_rate;
}

/**
 * Simulate database query delay
 */
function simulateDBQuery() {
    simulateDelay('PAGE_LOAD_DELAY');
}

/**
 * Simulate file operation delay
 */
function simulateFileOperation() {
    simulateDelay('PAGE_LOAD_DELAY');
}

/**
 * Get current delay profile info
 * @return array
 */
function getDelayProfile() {
    global $delay_profiles;
    return [
        'enabled' => SERVER_DELAYS_ENABLED,
        'profile' => SERVER_PROFILE,
        'delays' => $delay_profiles[SERVER_PROFILE] ?? []
    ];
}

/**
 * Simulate network packet loss (for UNRELIABLE profile)
 */
function simulatePacketLoss() {
    if (SERVER_PROFILE === 'UNRELIABLE' && rand(1, 20) === 1) {
        // 5% chance of significant delay
        usleep(rand(2000, 5000) * 1000);
    }
}

// ==================== HELPER MESSAGES ====================

/**
 * Get delay status message (for debugging)
 */
function getDelayStatus() {
    if (!SERVER_DELAYS_ENABLED) {
        return "Server delays: DISABLED";
    }
    
    $profile_descriptions = [
        'FAST' => 'Optimized server (fast response)',
        'NORMAL' => 'Standard server (realistic delays)',
        'SLOW' => 'Overloaded server (slow response)',
        'UNRELIABLE' => 'Unstable connection (random delays)'
    ];
    
    $desc = $profile_descriptions[SERVER_PROFILE] ?? 'Unknown';
    return "Server delays: ENABLED | Profile: " . SERVER_PROFILE . " ({$desc})";
}

// ==================== AUTO-INCLUDE ====================

// Display status in HTML comments (only in debug mode)
if (defined('DEBUG_MODE') && DEBUG_MODE) {
    $status = getDelayStatus();
    echo "<!-- {$status} -->\n";
}

?>
