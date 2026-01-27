# app/Models/ Directory

## 1. Directory Overview

**Purpose**: Contains data structure classes (Models) that represent business entities in the application.

**Why it exists**: To define clear data structures with properties and basic methods, separating data representation from business logic.

**Responsibility**: Define the shape and structure of data entities (Product, User, Category, Brand, Cart, Order, Wishlist).

**Single Responsibility**: Data structure definition only - no business logic, no data access.

---

## 2. Files Breakdown

### Product.php
- **Purpose**: Represents a product entity
- **Why created**: To encapsulate product data (id, name, price, category, brand, rating, stock, etc.)
- **Used by**: `ProductRepository`, `ProductController`, `CartService`
- **Dependencies**: None (standalone data structure)
- **Properties**: `id`, `name`, `slug`, `description`, `price`, `original_price`, `category_id`, `brand_id`, `rating`, `stock`, `featured`, `new`, `icon`, `features`, `specifications`

### User.php
- **Purpose**: Represents a user/customer entity
- **Why created**: To encapsulate user data (id, email, password, name, created_at)
- **Used by**: `UserRepository`, `AuthService`, `AuthController`
- **Dependencies**: None
- **Properties**: `id`, `email`, `password`, `name`, `created_at`
- **Methods**: `verifyPassword($password)` - Check password hash

### Category.php
- **Purpose**: Represents a product category
- **Why created**: To encapsulate category data (id, name, slug)
- **Used by**: `CategoryRepository`, `ProductController`
- **Dependencies**: None
- **Properties**: `id`, `name`, `slug`

### Brand.php
- **Purpose**: Represents a product brand
- **Why created**: To encapsulate brand data (id, name)
- **Used by**: `BrandRepository`, `ProductController`
- **Dependencies**: None
- **Properties**: `id`, `name`

### Cart.php
- **Purpose**: Represents a shopping cart
- **Why created**: To encapsulate cart structure (user_id, items, totals)
- **Used by**: `CartRepository`, `CartService`, `CartController`
- **Dependencies**: None
- **Properties**: `user_id`, `items` (array of product_id => quantity), `subtotal`, `shipping`, `tax`, `total`

### Order.php
- **Purpose**: Represents a customer order
- **Why created**: To encapsulate order data (id, user_id, items, shipping_info, payment_info, status)
- **Used by**: `OrderRepository`, `CheckoutController`
- **Dependencies**: None
- **Properties**: `id`, `user_id`, `items`, `shipping_address`, `payment_method`, `status`, `created_at`

### Wishlist.php
- **Purpose**: Represents a user's wishlist
- **Why created**: To encapsulate wishlist structure (user_id, product_ids)
- **Used by**: `WishlistRepository`, `WishlistService`
- **Dependencies**: None
- **Properties**: `user_id`, `product_ids` (array)

---

## 3. Functional Responsibilities

### Common methods in all Models:

#### `toArray()`
- **Purpose**: Convert model to associative array
- **Logic**: Return all properties as array
- **Side effects**: None (pure function)
- **Usage**: When saving to JSON or passing to views

#### `fromArray($data)`
- **Purpose**: Create model instance from array
- **Logic**: Map array keys to object properties
- **Side effects**: None (pure function)
- **Usage**: When loading from JSON or database

### Model-specific methods:

#### User::verifyPassword($password)
- **Purpose**: Verify password against stored hash
- **Logic**: Use `password_verify()` PHP function
- **Side effects**: None
- **Returns**: boolean

---

## 4. Dependency & Impact Analysis

### Dependencies:
- **Depends on**: Nothing (Models are standalone)
- **Used by**: 
  - All Repositories (for data mapping)
  - All Services (for type hinting)
  - All Controllers (for passing data to views)

### Impact of changes:

#### If a Model property is renamed:
- ❌ **BREAKS**: All Repositories that access this property
- ❌ **BREAKS**: All Services that use this property
- ❌ **BREAKS**: All Views that display this property
- ❌ **BREAKS**: JSON data structure (requires data migration)

#### If a Model method signature changes:
- ❌ **BREAKS**: All code calling this method
- ⚠️ **CAUTION**: May break serialization/deserialization

#### If a Model is deleted:
- ❌ **BREAKS**: Corresponding Repository
- ❌ **BREAKS**: Corresponding Service
- ❌ **BREAKS**: All Controllers using this Model

---

## 5. Modification Guidelines

### Safe changes:
- ✅ Adding new properties (with default values)
- ✅ Adding new methods (without changing existing ones)
- ✅ Adding documentation/comments
- ✅ Adding type hints
- ✅ Adding validation in setters

### Changes requiring caution:
- ⚠️ Renaming properties (update all usages + JSON data)
- ⚠️ Changing property types (may break serialization)
- ⚠️ Removing properties (breaks backward compatibility)
- ⚠️ Changing method signatures (breaks all callers)

### Files to modify together:
When changing a Model, also update:
1. Corresponding Repository (data mapping)
2. Corresponding Service (business logic)
3. Views that display this data
4. JSON data files (if structure changed)

### Common mistakes to avoid:
- ❌ Don't add business logic to Models (use Services)
- ❌ Don't add data access code to Models (use Repositories)
- ❌ Don't add validation logic (use Services or Validators)
- ❌ Don't make Models depend on other classes
- ❌ Don't use static properties (breaks testability)

---

## 6. Usage Notes

### Execution flow:
1. Repository loads data from JSON
2. Repository creates Model instance via `fromArray()`
3. Service receives Model, performs business logic
4. Controller receives Model, passes to View
5. View accesses Model properties for display

### Example usage:

```php
// In Repository
$data = json_decode($json, true);
$product = Product::fromArray($data);

// In Service
function calculateDiscount(Product $product) {
    return $product->original_price - $product->price;
}

// In Controller
$product = $this->productRepo->find($id);
include 'views/product.php'; // View accesses $product

// In View
echo $product->name;
echo $product->price;
```

### Environment assumptions:
- PHP >= 7.4 (for typed properties)
- No database (using JSON files)

### Configuration relied upon:
- None (Models are configuration-independent)

---

## 7. Ownership & Boundaries

### What MUST be implemented here:
- ✅ Data structure definitions (properties)
- ✅ Simple getter/setter methods
- ✅ Data transformation methods (`toArray`, `fromArray`)
- ✅ Type declarations

### What MUST NOT be implemented here:
- ❌ Business logic (use Services)
- ❌ Data persistence (use Repositories)
- ❌ Validation rules (use Services or Validators)
- ❌ Calculations (use Services)
- ❌ External API calls
- ❌ Database queries

### Boundary rules:
- Models are **dumb data containers**
- Models should be **serializable** (to/from arrays)
- Models should have **no dependencies** on other classes
- Models should be **immutable** where possible

---

## 8. Design Patterns

### Data Transfer Object (DTO)
Models act as DTOs to transfer data between layers:
```
Repository → Model → Service → Model → Controller → Model → View
```

### Value Object (for simple entities)
```php
class Category {
    public int $id;
    public string $name;
    public string $slug;
}
```

---

## 9. Testing

### Unit testing Models:
```php
// Test toArray()
$product = new Product();
$product->id = 1;
$product->name = 'Test Product';
$array = $product->toArray();
assert($array['id'] === 1);

// Test fromArray()
$data = ['id' => 1, 'name' => 'Test'];
$product = Product::fromArray($data);
assert($product->id === 1);
```

---

## 10. Migration from Legacy

### Before (procedural):
```php
// Data as associative arrays
$product = [
    'id' => 1,
    'name' => 'Product',
    'price' => 99.99
];
```

### After (OOP):
```php
// Data as objects
$product = new Product();
$product->id = 1;
$product->name = 'Product';
$product->price = 99.99;
```

### Benefits:
- ✅ Type safety
- ✅ IDE autocomplete
- ✅ Clear structure
- ✅ Easier refactoring

---

## Quick Reference

**Add new Model**: Create class → Define properties → Add `toArray()` and `fromArray()`  
**Add property**: Add to class → Update `toArray()`/`fromArray()` → Update JSON data  
**Change property**: Update all Repositories, Services, Views, and JSON data  
**Delete Model**: Remove all usages first (Repository, Service, Controller, View)

**Rule of thumb**: If it's data structure, it belongs here. If it's logic, it belongs in Services.
