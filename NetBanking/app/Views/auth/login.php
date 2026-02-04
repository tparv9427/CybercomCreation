<div class="flex-center" style="height: 80vh;">
    <div class="glass-card" style="padding: 40px; width: 100%; max-width: 400px; text-align: center;">
        <div style="margin-bottom: 30px; font-size: 2rem; color: #fff;">
            <i class="fas fa-university"></i> NetBank
        </div>

        <h2 style="color: #fff; margin-bottom: 30px;">Welcome Back</h2>

        <?php if (isset($error)): ?>
            <div
                style="background: rgba(231, 76, 60, 0.4); border: 1px solid #e74c3c; color: #fff; padding: 10px; border-radius: 8px; margin-bottom: 20px;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="/authenticate" method="POST">
            <input type="text" name="username" class="glass-input" placeholder="Username" required>
            <input type="password" name="password" class="glass-input" placeholder="Password" required>

            <button type="submit" class="glass-btn"
                style="width: 100%; padding: 12px; margin-top: 10px; font-size: 1rem;">Login</button>
        </form>

        <p style="margin-top: 20px; color: rgba(255,255,255,0.7);">
            Don't have an account? <a href="#" style="color: #fff;">Sign up</a>
        </p>
        <p style="font-size: 0.8rem; color: rgba(255,255,255,0.5);">
            (Try: alex / password123)
        </p>
    </div>
</div>