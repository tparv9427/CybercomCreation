# app/Services/ Directory

## 1. Directory Overview

**Purpose**: Business Logic Layer - implements complex business rules, workflows, and calculations.

**Why it exists**: To separate business logic from data access (Repositories) and HTTP handling (Controllers).

**Responsibility**: All business rules, validations, calculations, and workflows.

**Single Responsibility**: Business logic only - no data access, no HTTP handling, no HTML rendering.

---

## 2. Files Breakdown

### SessionService.php
- **Migrated from**: `includes/session-manager.php` (lines 7-11, 124-153)
- **Methods**: `init()`, `mergeGuestData($userId)`
- **Used by**: `public/index.php`, `AuthService`
- **Dependencies**: PHP session, `CartRepository`, `WishlistRepository`
- **Side effects**: Session initialization, data merging

### AuthService.php
- **Migrated from**: `login.php`, `signup.php`, `logout.php`, `config.php` (isLoggedIn)
- **Methods**: `check()`, `login($email, $password)`, `register($email, $password, $name)`, `logout()`, `getCurrentUser()`
- **Used by**: `AuthController`, all protected pages
- **Dependencies**: `UserRepository`, `SessionService`
- **Side effects**: Session modification, user creation

### CartService.php
- **Migrated from**: `ajax_cart.php`, `config.php` (cart functions)
- **Methods**: `add($productId, $quantity)`, `update($productId, $quantity)`, `remove($productId)`, `getCount()`, `getTotal()`, `has($productId)`
- **Used by**: `CartController`, `CheckoutController`
- **Dependencies**: `CartRepository`, `ProductRepository`, `PricingService`
- **Side effects**: Cart modification

### WishlistService.php
- **Migrated from**: `ajax_wishlist.php`, `config.php` (wishlist functions)
- **Methods**: `toggle($productId)`, `add($productId)`, `remove($productId)`, `has($productId)`, `getCount()`
- **Used by**: `WishlistController`
- **Dependencies**: `WishlistRepository`
- **Side effects**: Wishlist modification

### PricingService.php
- **Extracted from**: `checkout.php`, `ajax_cart.php`
- **Methods**: `calculateSubtotal($cart)`, `calculateShipping($totalItems)`, `calculateTax($subtotal)`, `calculateTotal($subtotal, $shipping, $tax)`
- **Used by**: `CartService`, `CheckoutController`
- **Dependencies**: `ProductRepository`
- **Side effects**: None (pure calculations)

---

## 3. Functional Responsibilities

### SessionService

#### init()
- **Logic**: Start PHP session if not already started
- **Side effects**: Session initialization
- **When called**: Every request (in `public/index.php`)

#### mergeGuestData($userId)
- **Logic**: Merge guest cart/wishlist into user account on login
- **Side effects**: Session modification, file I/O
- **When called**: After successful login

### AuthService

#### check()
- **Logic**: Check if user is logged in
- **Returns**: boolean
- **Side effects**: None

#### login($email, $password)
- **Logic**: Verify credentials, set session, merge guest data
- **Returns**: User array or false
- **Side effects**: Session modification, data merging

#### register($email, $password, $name)
- **Logic**: Validate input, create user, auto-login
- **Returns**: User ID or false
- **Side effects**: User creation, session modification

### CartService

#### add($productId, $quantity)
- **Logic**: Validate product exists, add to cart, save
- **Returns**: Success boolean
- **Side effects**: Cart modification

#### getTotal()
- **Logic**: Calculate cart total using PricingService
- **Returns**: float
- **Side effects**: None

### PricingService

#### calculateSubtotal($cart)
- **Logic**: Sum (price × quantity) for all items
- **Returns**: float
- **Side effects**: None (pure function)

#### calculateShipping($totalItems)
- **Logic**: $10 per item
- **Returns**: float
- **Side effects**: None

#### calculateTax($subtotal)
- **Logic**: 8% of subtotal
- **Returns**: float
- **Side effects**: None

---

## 4. Dependency & Impact Analysis

### Dependencies:
- **Depends on**: Repositories (for data access)
- **Depends on**: Models (for type hints)
- **Used by**: Controllers

### Impact of changes:

#### If business rule changes:
- ✅ **ONLY** Services need updating
- ✅ Controllers, Repositories unchanged

#### If method signature changes:
- ❌ **BREAKS**: All Controllers calling this method

---

## 5. Modification Guidelines

### Safe changes:
- ✅ Changing business logic internals
- ✅ Adding new methods
- ✅ Optimizing calculations

### Changes requiring caution:
- ⚠️ Changing method signatures
- ⚠️ Changing return types
- ⚠️ Removing methods

### Common mistakes to avoid:
- ❌ Don't access data directly (use Repositories)
- ❌ Don't handle HTTP (use Controllers)
- ❌ Don't render HTML (use Views)

---

## 7. Ownership & Boundaries

### What MUST be here:
- ✅ Business rules
- ✅ Validations
- ✅ Calculations
- ✅ Workflows

### What MUST NOT be here:
- ❌ Data access (use Repositories)
- ❌ HTTP handling (use Controllers)
- ❌ HTML (use Views)

---

## Quick Reference

**Add business logic**: Create/update Service → Call from Controller  
**Change rule**: Update Service method → Test  
**Rule of thumb**: If it's business logic, it belongs here.
