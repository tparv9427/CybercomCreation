<div class="container" style="padding: 4rem 2rem; min-height: 60vh;">
    <div class="section-header">
        <h2 class="section-title">Product Import / Export</h2>
        <p class="section-subtitle">Manage your product catalog in bulk</p>
    </div>

    <!-- Feedback Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"
            style="padding: 1rem; background: #dcfce7; color: #166534; border-left: 4px solid #166534; margin-bottom: 2rem;">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"
            style="padding: 1rem; background: #fee2e2; color: #991b1b; border-left: 4px solid #991b1b; margin-bottom: 2rem;">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['import_errors'])): ?>
        <div class="alert alert-warning"
            style="padding: 1rem; background: #fef9c3; color: #854d0e; border-left: 4px solid #854d0e; margin-bottom: 2rem;">
            <strong>Wait, some rows had issues:</strong>
            <ul style="margin-top: 0.5rem; padding-left: 1.5rem;">
                <?php foreach ($_SESSION['import_errors'] as $error): ?>
                    <li>
                        <?= htmlspecialchars($error) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php unset($_SESSION['import_errors']); ?>
        </div>
    <?php endif; ?>

    <div class="admin-grid"
        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">

        <!-- Import Section -->
        <div class="card"
            style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                📥 Import Products
            </h3>
            <p style="color: var(--secondary); margin-bottom: 1.5rem;">
                Upload a CSV file to bulk add or update products.
                Existing SKUs will be skipped.
            </p>

            <form action="/admin/import" method="POST" enctype="multipart/form-data">
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="csv_file" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Select CSV
                        File</label>
                    <input type="file" name="csv_file" id="csv_file" accept=".csv" required
                        style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 8px;">
                    <small style="display: block; margin-top: 0.5rem; color: var(--secondary);">
                        Required columns: <code>sku, name, price</code>
                    </small>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Upload & Import</button>
            </form>

            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px dashed var(--border);">
                <h4>CSV Format Guide</h4>
                <ul
                    style="font-size: 0.9rem; color: var(--secondary); margin-top: 0.5rem; padding-left: 1.5rem; line-height: 1.6;">
                    <li>Headers must be lowercase (e.g., <code>sku</code>, <code>name</code>).</li>
                    <li><strong>Required:</strong> sku, name, price</li>
                    <li><strong>Optional:</strong> stock, brand, color, image_url, description</li>
                </ul>
            </div>
        </div>

        <!-- Export Section -->
        <div class="card"
            style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                📤 Export Products
            </h3>
            <p style="color: var(--secondary); margin-bottom: 1.5rem;">
                Download your entire product catalog as a CSV file for offline editing or backup.
            </p>

            <div style="background: #f8fafc; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>Total Products:</span>
                    <strong>
                        <?= count((new \EasyCart\Repositories\ProductRepository())->getAll()) ?>
                    </strong>
                </div>
                <!-- Add more stats here if needed -->
            </div>

            <a href="/admin/export" class="btn btn-outline"
                style="display: block; text-align: center; width: 100%; border: 2px solid var(--primary); color: var(--primary); background: transparent;">
                Download CSV Export
            </a>
        </div>

    </div>
</div>