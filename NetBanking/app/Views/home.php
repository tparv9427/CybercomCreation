<div class="glass-card" style="padding: 0; display: flex; overflow: hidden; height: 85vh;">
    <!-- Sidebar -->
    <aside
        style="width: 80px; border-right: 1px solid rgba(255, 255, 255, 0.3); display: flex; flex-direction: column; align-items: center; padding-top: 30px; background: rgba(255,255,255,0.1);">
        <div class="glass-btn"
            style="width: 45px; height: 45px; padding: 0; display: flex; align-items: center; justify-content: center; margin-bottom: 30px; border-radius: 50%;">
            <i class="fas fa-university fa-lg"></i>
        </div>

        <div style="display: flex; flex-direction: column; gap: 20px;">
            <div class="glass-btn"
                style="width: 40px; height: 40px; padding:0; display:flex; justify-content:center; align-items:center; border-radius:12px; background: rgba(255,255,255,0.6);">
                <i class="fas fa-home"></i>
            </div>
            <div class="glass-btn"
                style="width: 40px; height: 40px; padding:0; display:flex; justify-content:center; align-items:center; border-radius:12px; opacity: 0.7;">
                <i class="far fa-credit-card"></i>
            </div>
            <div class="glass-btn"
                style="width: 40px; height: 40px; padding:0; display:flex; justify-content:center; align-items:center; border-radius:12px; opacity: 0.7;">
                <i class="fas fa-exchange-alt"></i>
            </div>
        </div>

        <div style="margin-top: auto; margin-bottom: 30px;">
            <div class="glass-btn"
                style="width: 40px; height: 40px; padding:0; display:flex; justify-content:center; align-items:center; border-radius:12px; opacity: 0.7; color: #e74c3c;">
                <i class="fas fa-sign-out-alt"></i>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main style="flex: 1; padding: 40px; overflow-y: auto;">
        <header style="margin-bottom: 40px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="margin: 0; font-weight: 600; color: #fff;">Hello,
                    <?= $user ?>!
                </h2>
                <p style="margin: 5px 0 0; color: rgba(255,255,255,0.8);">Welcome back to your wallet.</p>
            </div>
            <div>
                <!-- User Profile or Date -->
                <span class="glass-btn">
                    <?= date('D, d M Y') ?>
                </span>
            </div>
        </header>

        <!-- Cards Row -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">

            <!-- Balance Card -->
            <div class="glass-card"
                style="padding: 25px; background: linear-gradient(135deg, rgba(255,255,255,0.4) 0%, rgba(255,255,255,0.1) 100%); border: 1px solid rgba(255,255,255,0.6);">
                <div
                    style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px;">
                    <i class="fab fa-cc-visa" style="font-size: 2.5rem; color: #fff;"></i>
                    <span style="font-family: monospace; color: #fff; font-size: 1.2rem;">.... 4215</span>
                </div>
                <div>
                    <span
                        style="display: block; font-size: 0.9rem; color: rgba(255,255,255,0.8); margin-bottom: 5px;">Total
                        Balance</span>
                    <span style="font-size: 2rem; font-weight: 700; color: #fff;">$ 24,562.00</span>
                </div>
            </div>

            <!-- Quick Action / Transfer -->
            <div style="flex: 1;">
                <h3 style="margin-top: 0; color: #fff; margin-bottom: 20px;">Quick Transfer</h3>

                <div class="glass-card"
                    style="padding: 15px; display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                    <div
                        style="width: 45px; height: 45px; border-radius: 50%; background: #eee; display: flex; justify-content: center; align-items: center; font-weight: bold; color: #555;">
                        JD</div>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: #333;">John Doe</div>
                        <div style="font-size: 0.8rem; color: #666;">Ending in 8842</div>
                    </div>
                    <button class="glass-btn" style="padding: 8px 15px; font-size: 0.9rem;">Send</button>
                </div>

                <div class="glass-card" style="padding: 15px; display: flex; align-items: center; gap: 15px;">
                    <div
                        style="width: 45px; height: 45px; border-radius: 50%; background: #eee; display: flex; justify-content: center; align-items: center; font-weight: bold; color: #555;">
                        AS</div>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: #333;">Anna Smith</div>
                        <div style="font-size: 0.8rem; color: #666;">Ending in 1234</div>
                    </div>
                    <button class="glass-btn" style="padding: 8px 15px; font-size: 0.9rem;">Send</button>
                </div>
            </div>
        </div>

        <!-- Transactions Section -->
        <h3 style="color: #fff; margin-top: 40px; margin-bottom: 20px;">Recent Transactions</h3>
        <div class="glass-card" style="padding: 0;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.3); text-align: left;">
                        <th style="padding: 15px 25px; color: #555;">Description</th>
                        <th style="padding: 15px 25px; color: #555;">Category</th>
                        <th style="padding: 15px 25px; color: #555;">Date</th>
                        <th style="padding: 15px 25px; color: #555; text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                        <td style="padding: 15px 25px; font-weight: 600;">Amazon Prime</td>
                        <td style="padding: 15px 25px; color: #666;">Subscription</td>
                        <td style="padding: 15px 25px; color: #666;">Oct 24, 2025</td>
                        <td style="padding: 15px 25px; font-weight: 600; text-align: right;">- $14.99</td>
                    </tr>
                    <tr>
                        <td style="padding: 15px 25px; font-weight: 600;">Salary Credit</td>
                        <td style="padding: 15px 25px; color: #666;">Income</td>
                        <td style="padding: 15px 25px; color: #666;">Oct 22, 2025</td>
                        <td style="padding: 15px 25px; font-weight: 600; text-align: right; color: #2ecc71;">+ $3,500.00
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </main>
</div>