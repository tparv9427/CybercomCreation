# Legacy Files Archive

## 📦 Archive Created: January 27, 2026

This directory contains the original procedural PHP files that have been migrated to the new PSR-4 MVC architecture.

---

## 📁 Directory Structure

```
legacy/
├── pages/          (15 files) - Old page files
└── includes/       (4 files)  - Old include files
```

---

## 📄 Archived Files

### Pages (15 files)
All these files have been migrated to Controllers + Views:

1. **index.php** → `HomeController` + `app/Views/home/index.php`
2. **products.php** → `ProductController::index()` + `app/Views/products/index.php`
3. **product.php** → `ProductController::show()` + `app/Views/products/detail.php`
4. **cart.php** → `CartController::index()` + `app/Views/cart/index.php`
5. **checkout.php** → `CheckoutController` + `app/Views/checkout/index.php`
6. **login.php** → `AuthController::showLogin()` + `app/Views/auth/login.php`
7. **signup.php** → `AuthController::showSignup()` + `app/Views/auth/signup.php`
8. **logout.php** → `AuthController::logout()`
9. **wishlist.php** → `WishlistController` + `app/Views/wishlist/index.php`
10. **orders.php** → `OrderController` + `app/Views/orders/index.php`
11. **order-success.php** → `OrderController::success()` + `app/Views/orders/success.php`
12. **ajax_cart.php** → `CartController` AJAX methods
13. **ajax_wishlist.php** → `WishlistController::toggle()`
14. **search.php** → `ProductController::search()`
15. **brand.php** → `ProductController::brand()`

### Includes (4 files)
All these files have been migrated to Config, Services, Repositories, and Helpers:

1. **config.php** → Migrated to:
   - `config/app.php` (constants)
   - `config/constants.php` (categories, brands)
   - `app/Repositories/ProductRepository.php` (product functions)
   - `app/Repositories/UserRepository.php` (user functions)
   - `app/Repositories/CategoryRepository.php` (category functions)
   - `app/Repositories/BrandRepository.php` (brand functions)
   - `app/Helpers/FormatHelper.php` (formatPrice function)

2. **session-manager.php** → Migrated to:
   - `app/Services/SessionService.php` (session management)
   - `app/Repositories/CartRepository.php` (cart persistence)
   - `app/Repositories/WishlistRepository.php` (wishlist persistence)

3. **header.php** → Copied to `app/Views/layouts/header.php`
4. **footer.php** → Copied to `app/Views/layouts/footer.php`

---

## ⚠️ Important Notes

### Do NOT Use These Files
These files are kept for **reference only**. The application now uses the new PSR-4 MVC structure.

### Accessing Old Code
If you need to reference the old implementation:
1. Open files in `legacy/pages/` or `legacy/includes/`
2. Compare with new implementation in `app/`
3. Do NOT modify these files

### Restoration
If you need to restore the old structure:
1. Copy files from `legacy/` back to root
2. Remove `app/`, `public/`, `config/` directories
3. Restore `includes/` directory

**Note**: This is NOT recommended. The new structure is superior in every way.

---

## 🗑️ Safe to Delete?

**After thorough testing**, if everything works perfectly, you can delete this `legacy/` directory.

**Recommendation**: Keep it for at least 1-2 weeks to ensure everything works correctly.

---

## 📊 Migration Summary

| Category | Old Files | New Location | Status |
|----------|-----------|--------------|--------|
| Page Files | 15 | Controllers + Views | ✅ Migrated |
| Config | 1 | config/ + Repositories + Helpers | ✅ Migrated |
| Session Manager | 1 | Services + Repositories | ✅ Migrated |
| Layouts | 2 | app/Views/layouts/ | ✅ Copied |
| **Total** | **19** | **Multiple locations** | ✅ **Complete** |

---

## ✅ Verification

To verify the migration was successful:
1. Test all pages at `http://localhost:8000/`
2. Check that all functionality works
3. Verify no errors in server logs
4. Test cart, wishlist, login, checkout

If everything works, the migration is successful! 🎉

---

**Archive Date**: January 27, 2026  
**Archived By**: PSR-4 MVC Refactoring  
**Purpose**: Reference and safety backup
