<?php
require_once 'includes/config.php';
$page_title = 'Sign Up';
$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'All fields are required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        $user_id = addUser($email, $password, $name);
        if ($user_id) {
            $success = 'Account created successfully! You can now login.';
            // Auto-login the user
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            header('Location: index.php');
            exit;
        } else {
            $error = 'Email already exists. Please use a different email.';
        }
    }
}
include 'includes/header.php';
?>
<link rel="stylesheet" href="assets/css/auth.css">
<div class="auth-container">
    <div class="auth-card">
        <h2>Create Account</h2>
        <p class="auth-subtitle">Join us today</p>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?> <a href="login.php">Login now</a></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" required placeholder="John Doe">
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="your@email.com">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Create a password">
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required placeholder="Confirm your password">
            </div>
            <button type="submit" class="btn btn-primary">Create Account</button>
        </form>
        <p class="auth-link">Already have an account? <a href="login.php">Login</a></p>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
