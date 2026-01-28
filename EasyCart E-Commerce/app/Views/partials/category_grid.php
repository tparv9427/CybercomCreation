<?php
/**
 * Reusable Category Grid Component
 * 
 * This partial renders a grid of category cards with links.
 * 
 * Required Variables:
 * @var array $categories - Array of category data, each with keys: id, name
 * @var string $title - Section title (optional, default: "Shop by Category")
 * @var string $subtitle - Section subtitle (optional, default: "Find what you're looking for")
 */

// Set defaults if not provided
$title = $title ?? 'Shop by Category';
$subtitle = $subtitle ?? "Find what you're looking for";
?>

<!-- Categories Section -->
<div class="container">
    <div class="section-header">
        <h2 class="section-title"><?php echo htmlspecialchars($title); ?></h2>
        <p class="section-subtitle"><?php echo htmlspecialchars($subtitle); ?></p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
        <?php foreach ($categories as $category): ?>
            <a href="products.php?category=<?php echo $category['id']; ?>" style="text-decoration: none;">
                <div style="background: var(--card-bg); padding: 3rem 2rem; border-radius: 20px; text-align: center; transition: all 0.3s; box-shadow: var(--shadow); cursor: pointer;">
                    <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.8rem; color: var(--primary); margin-bottom: 0.5rem;">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </h3>
                    <p style="color: var(--secondary);">Explore →</p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>
