<?php
/**
 * Reusable Banner Carousel Component
 * 
 * This partial renders a banner carousel with multiple slides.
 * 
 * Required Variables:
 * @var array $banners - Array of banner data, each with keys: title, subtitle, button_text, button_link
 * 
 * Example:
 * $banners = [
 *     ['title' => 'Banner Title', 'subtitle' => 'Description', 'button_text' => 'Click Me', 'button_link' => 'page.php'],
 *     ...
 * ];
 */
?>
<!-- Banner Carousel -->
<div class="banner-carousel">
    <?php foreach ($banners as $banner): ?>
        <div class="banner-slide">
            <div class="banner-content">
                <h1><?php echo htmlspecialchars($banner['title']); ?></h1>
                <p><?php echo htmlspecialchars($banner['subtitle']); ?></p>
                <a href="<?php echo htmlspecialchars($banner['button_link']); ?>" class="btn">
                    <?php echo htmlspecialchars($banner['button_text']); ?>
                </a>
            </div>
        </div>
    <?php endforeach; ?>
</div>
