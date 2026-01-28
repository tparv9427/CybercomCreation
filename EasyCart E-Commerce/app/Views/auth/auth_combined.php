<?php
// Variables passed from AuthController:
// $page_title, $error (if any), $success (if any), $mode ('login' or 'signup')
?>
    <link rel="stylesheet" href="assets/css/auth-combined.css">

    <div class="auth-wrapper">
        <div class="container-auth <?php echo $mode === 'signup' ? 'right-panel-active' : ''; ?>" id="container">
            
            <!-- Sign Up Form (Left side in logic, visually right when active) -->
            <div class="form-container sign-up-container">
                <form action="signup.php" method="POST">
                    <h1>Create Account</h1>
                    
                    <div class="social-container">
                        <a href="#" class="social"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social"><i class="fab fa-google-plus-g"></i></a>
                        <a href="#" class="social"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                    <span>or use your email for registration</span>
                    
                    <?php if (!empty($error) && $mode === 'signup'): ?>
                        <div class="msg error"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <input type="text" name="name" placeholder="Name" required />
                    <input type="email" name="email" placeholder="Email" required />
                    <input type="password" name="password" placeholder="Password" required />
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required />
                    
                    <button type="submit">Sign Up</button>
                    <button type="button" class="mobile-toggle" id="mobile-signIn">Already have an account? Sign In</button>
                </form>
            </div>

            <!-- Sign In Form (Right side in logic, visually left when active) -->
            <div class="form-container sign-in-container">
                <form action="login.php" method="POST">
                    <h1>Sign in</h1>
                    
                    <div class="social-container">
                        <a href="#" class="social"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social"><i class="fab fa-google-plus-g"></i></a>
                        <a href="#" class="social"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                    <span>or use your account</span>

                    <?php if (!empty($error) && $mode === 'login'): ?>
                        <div class="msg error"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if (!empty($success)): ?>
                        <div class="msg success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <input type="email" name="email" placeholder="Email" required />
                    <input type="password" name="password" placeholder="Password" required />
                    <a href="#">Forgot your password?</a>
                    <button type="submit">Sign In</button>
                    <button type="button" class="mobile-toggle" id="mobile-signUp">New here? Sign Up</button>
                </form>
            </div>

            <!-- Overlay Container (The moving part) -->
            <div class="overlay-container">
                <div class="overlay">
                    <!-- Left Overlay (Visible when Sign In form is shown) -->
                    <div class="overlay-panel overlay-left">
                        <h1>Welcome Back!</h1>
                        <p>To keep connected with us please login with your personal info</p>
                        <button class="ghost" id="signIn">Sign In</button>
                    </div>

                    <!-- Right Overlay (Visible when Sign Up form is shown) -->
                    <div class="overlay-panel overlay-right">
                        <h1>Hello, Friend!</h1>
                        <p>Enter your personal details and start journey with us</p>
                        <button class="ghost" id="signUp">Sign Up</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/auth.js"></script>
