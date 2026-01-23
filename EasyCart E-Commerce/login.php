<?php
require_once 'includes/config.php';
$page_title = 'Login';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $users = loadUsers();
    $found = false;
    
    foreach ($users as $user) {
        if ($user['email'] === $email && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];

            // Merge guest data with user data
            mergeGuestToUser($user['id']);

            $found = true;
            
            // Redirect to checkout if that's where they came from
            if (isset($_SESSION['checkout_redirect'])) {
                unset($_SESSION['checkout_redirect']);
                header('Location: checkout.php');
            } else {
                header('Location: index.php');
            }
            exit;
        }
    }
    
    if (!$found) {
        $error = 'Invalid email or password';
    }
}
include 'includes/header.php';
?>
<link rel="stylesheet" href="assets/css/auth.css">
<div class="auth-container">
    <div class="auth-card">
        <h2>Welcome Back</h2>
        <p class="auth-subtitle">Login to your account</p>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="your@email.com">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Enter your password">
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <p class="auth-link">Don't have an account? <a href="signup.php">Sign Up</a></p>
        <div class="demo-credentials">
            <strong>Demo Account:</strong><br>
            Email: demo@easycart.com<br>
            Password: demo123
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
