# routes/ Directory

## 1. Directory Overview

**Purpose**: Route Definitions - map URLs to controllers.

**Why it exists**: Centralize routing configuration.

**Responsibility**: Define all application routes.

---

## 2. Files Breakdown

### web.php
- **Purpose**: Define all web routes
- **Format**: Array mapping routes to controllers
- **Used by**: `public/index.php`
- **Dependencies**: None

---

## 3. Route Format

```php
$routes = [
    'GET /' => 'HomeController@index',
    'GET /products' => 'ProductController@index',
    'GET /product' => 'ProductController@show',
    'POST /cart/add' => 'CartController@add',
    // ...
];
```

---

## 4. Modification Guidelines

### Safe changes:
- ✅ Adding new routes

### Changes requiring caution:
- ⚠️ Changing existing routes (breaks bookmarks/links)
- ⚠️ Removing routes (breaks functionality)

---

## Quick Reference

**Add route**: Add to web.php → Create controller method  
**Rule of thumb**: If it's a URL, it belongs here.
