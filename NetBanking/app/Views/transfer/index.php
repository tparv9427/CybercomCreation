<div class="glass-card" style="padding: 0; display: flex; overflow: hidden; height: 85vh;">
    <!-- Reuse Sidebar (Ideally this is a partial) -->
    <aside
        style="width: 80px; border-right: 1px solid rgba(255, 255, 255, 0.3); display: flex; flex-direction: column; align-items: center; padding-top: 30px; background: rgba(255,255,255,0.1);">
        <a href="/" class="glass-btn"
            style="width: 45px; height: 45px; padding: 0; display: flex; align-items: center; justify-content: center; margin-bottom: 30px; border-radius: 50%; color: #333; text-decoration: none;">
            <i class="fas fa-university fa-lg"></i>
        </a>

        <div style="display: flex; flex-direction: column; gap: 20px;">
            <a href="/" class="glass-btn"
                style="width: 40px; height: 40px; padding:0; display:flex; justify-content:center; align-items:center; border-radius:12px; opacity: 0.7; color: #333;">
                <i class="fas fa-home"></i>
            </a>
            <a href="#" class="glass-btn"
                style="width: 40px; height: 40px; padding:0; display:flex; justify-content:center; align-items:center; border-radius:12px; opacity: 0.7; color: #333;">
                <i class="far fa-credit-card"></i>
            </a>
            <a href="/transfer" class="glass-btn"
                style="width: 40px; height: 40px; padding:0; display:flex; justify-content:center; align-items:center; border-radius:12px; background: rgba(255,255,255,0.6); color: #333;">
                <i class="fas fa-exchange-alt"></i>
            </a>
        </div>

        <div style="margin-top: auto; margin-bottom: 30px;">
            <a href="/logout" class="glass-btn"
                style="width: 40px; height: 40px; padding:0; display:flex; justify-content:center; align-items:center; border-radius:12px; opacity: 0.7; color: #e74c3c;">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </aside>

    <main style="flex: 1; padding: 40px; overflow-y: auto;">
        <h2 style="color: #fff; margin-bottom: 30px;">Transfer Money</h2>

        <?php if (isset($error)): ?>
            <div
                style="background: rgba(231, 76, 60, 0.4); border: 1px solid #e74c3c; color: #fff; padding: 10px; border-radius: 8px; margin-bottom: 20px;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <div class="glass-card" style="padding: 30px; max-width: 600px;">
            <form action="/transfer/process" method="POST">
                <div style="margin-bottom: 20px;">
                    <label style="display: block; color: rgba(255,255,255,0.9); margin-bottom: 10px;">Recipient
                        Name</label>
                    <input type="text" name="recipient_name" class="glass-input" placeholder="e.g. John Doe" required>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; color: rgba(255,255,255,0.9); margin-bottom: 10px;">Account
                        Number</label>
                    <input type="text" name="account_number" class="glass-input" placeholder="e.g. 1234567890" required>
                </div>

                <div style="margin-bottom: 30px;">
                    <label style="display: block; color: rgba(255,255,255,0.9); margin-bottom: 10px;">Amount ($)</label>
                    <input type="number" step="0.01" name="amount" class="glass-input" placeholder="0.00" required>
                </div>

                <button type="submit" class="glass-btn"
                    style="width: 100%; padding: 15px; font-size: 1.1rem; background: #fff; color: #333;">
                    <i class="fas fa-paper-plane"></i> Send Money
                </button>
            </form>
        </div>
    </main>
</div>