# app/Views/ Directory

## 1. Directory Overview

**Purpose**: HTML Templates - render HTML output for pages.

**Why it exists**: To separate presentation from logic.

**Responsibility**: HTML rendering only.

**Single Responsibility**: Display data - no business logic, no data access.

---

## 2. Subdirectories Breakdown

| Directory | Purpose | Migrated From |
|-----------|---------|---------------|
| **layouts/** | Header, footer, common layouts | `includes/header.php`, `includes/footer.php` |
| **home/** | Homepage views | `index.php` (HTML portion) |
| **products/** | Product listing and detail views | `products.php`, `product.php` |
| **cart/** | Shopping cart views | `cart.php` |
| **checkout/** | Checkout views | `checkout.php` |
| **auth/** | Login, signup views | `login.php`, `signup.php` |
| **orders/** | Order history and success views | `orders.php`, `order-success.php` |
| **wishlist/** | Wishlist views | `wishlist.php` |

---

## 3. Functional Responsibilities

### Views receive data from Controllers:
```php
// In Controller
$products = $this->productRepo->getAll();
include __DIR__ . '/../Views/products/index.php';

// In View
foreach ($products as $product) {
    echo $product['name'];
}
```

### Views can use Helpers:
```php
use EasyCart\Helpers\FormatHelper;
use EasyCart\Helpers\UIHelper;

echo FormatHelper::price($product['price']);
UIHelper::renderStarRating($product['rating']);
```

---

## 4. Dependency & Impact Analysis

### Dependencies:
- **Depends on**: Data passed from Controllers
- **Depends on**: Helpers (for formatting/rendering)
- **Used by**: Controllers

### Impact of changes:

#### If view structure changes:
- ✅ **SAFE**: Only affects display
- ⚠️ **CSS**: May need CSS updates

---

## 5. Modification Guidelines

### Safe changes:
- ✅ Changing HTML structure
- ✅ Adding CSS classes
- ✅ Changing display logic

### Changes requiring caution:
- ⚠️ Changing variable names (must match Controller)
- ⚠️ Removing elements (may break JavaScript)

### Common mistakes to avoid:
- ❌ Don't add business logic
- ❌ Don't access Repositories directly
- ❌ Don't modify session/data

---

## 7. Ownership & Boundaries

### What MUST be here:
- ✅ HTML markup
- ✅ Display logic (loops, conditionals)
- ✅ Helper calls

### What MUST NOT be here:
- ❌ Business logic
- ❌ Data access
- ❌ Complex calculations

---

## Quick Reference

**Edit view**: Update HTML → Test display  
**Rule of thumb**: If it's HTML, it belongs here.
