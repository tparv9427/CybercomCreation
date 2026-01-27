# app/Controllers/ Directory

## 1. Directory Overview

**Purpose**: HTTP Request Handlers - handle incoming requests, coordinate services/repositories, return responses.

**Why it exists**: To separate HTTP concerns from business logic.

**Responsibility**: Handle HTTP requests/responses, coordinate services, pass data to views.

**Single Responsibility**: HTTP handling only - no business logic, no data access.

---

## 2. Files Breakdown

### HomeController.php
- **Migrated from**: `index.php`
- **Methods**: `index()`
- **Routes**: `GET /`
- **Used by**: `public/index.php` (router)
- **Dependencies**: `ProductRepository`, `CategoryRepository`
- **Renders**: `app/Views/home/index.php`

### ProductController.php
- **Migrated from**: `products.php`, `product.php`, `search.php`, `brand.php`
- **Methods**: `index()`, `show($id)`, `search()`, `brand($id)`
- **Routes**: `GET /products`, `GET /product`, `GET /search`, `GET /brand`
- **Dependencies**: `ProductRepository`, `CategoryRepository`, `BrandRepository`
- **Renders**: `app/Views/products/index.php`, `app/Views/products/detail.php`

### CartController.php
- **Migrated from**: `cart.php`, `ajax_cart.php`
- **Methods**: `index()`, `add()`, `update()`, `remove()`
- **Routes**: `GET /cart`, `POST /cart/add`, `POST /cart/update`, `POST /cart/remove`
- **Dependencies**: `CartService`, `ProductRepository`
- **Renders**: `app/Views/cart/index.php` or JSON response

### CheckoutController.php
- **Migrated from**: `checkout.php`
- **Methods**: `index()`, `process()`
- **Routes**: `GET /checkout`, `POST /checkout`
- **Dependencies**: `CartService`, `AuthService`, `PricingService`
- **Renders**: `app/Views/checkout/index.php`

### AuthController.php
- **Migrated from**: `login.php`, `signup.php`, `logout.php`
- **Methods**: `showLogin()`, `login()`, `showSignup()`, `signup()`, `logout()`
- **Routes**: `GET /login`, `POST /login`, `GET /signup`, `POST /signup`, `GET /logout`
- **Dependencies**: `AuthService`
- **Renders**: `app/Views/auth/login.php`, `app/Views/auth/signup.php`

### WishlistController.php
- **Migrated from**: `wishlist.php`, `ajax_wishlist.php`
- **Methods**: `index()`, `toggle()`
- **Routes**: `GET /wishlist`, `POST /wishlist/toggle`
- **Dependencies**: `WishlistService`, `ProductRepository`
- **Renders**: `app/Views/wishlist/index.php` or JSON response

### OrderController.php
- **Migrated from**: `orders.php`, `order-success.php`
- **Methods**: `index()`, `success()`
- **Routes**: `GET /orders`, `GET /order-success`
- **Dependencies**: `OrderRepository`, `AuthService`
- **Renders**: `app/Views/orders/index.php`, `app/Views/orders/success.php`

---

## 3. Functional Responsibilities

### Common pattern in all Controllers:

#### Constructor
- **Logic**: Instantiate required repositories/services
- **Side effects**: None

#### Action methods (index, show, etc.)
- **Logic**: 
  1. Get request parameters
  2. Call services/repositories
  3. Prepare data for view
  4. Include view template
- **Side effects**: HTTP response (HTML or JSON)

---

## 4. Dependency & Impact Analysis

### Dependencies:
- **Depends on**: Services, Repositories
- **Used by**: `public/index.php` (router)

### Impact of changes:

#### If route changes:
- ⚠️ Update `routes/web.php`
- ⚠️ Update all links in views

#### If method signature changes:
- ❌ **BREAKS**: Router calls

---

## 5. Modification Guidelines

### Safe changes:
- ✅ Adding new action methods
- ✅ Changing view logic

### Changes requiring caution:
- ⚠️ Changing method signatures
- ⚠️ Changing routes

### Common mistakes to avoid:
- ❌ Don't add business logic (use Services)
- ❌ Don't access data directly (use Repositories)
- ❌ Don't echo HTML (use Views)

---

## 7. Ownership & Boundaries

### What MUST be here:
- ✅ HTTP request handling
- ✅ Response generation
- ✅ Service coordination

### What MUST NOT be here:
- ❌ Business logic (use Services)
- ❌ Data access (use Repositories)
- ❌ HTML (use Views)

---

## Quick Reference

**Add new page**: Create Controller → Add method → Add route → Create view  
**Rule of thumb**: If it handles HTTP, it belongs here.
