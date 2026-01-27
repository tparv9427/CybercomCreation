# app/Repositories/ Directory

## 1. Directory Overview

**Purpose**: Data Access Layer - handles all data persistence operations (reading/writing JSON files, future: database queries).

**Why it exists**: To isolate data access logic from business logic, making it easy to switch data sources (JSON → Database) without changing other code.

**Responsibility**: CRUD operations (Create, Read, Update, Delete) for all entities.

**Single Responsibility**: Data persistence only - no business logic, no HTTP handling.

---

## 2. Files Breakdown

### ProductRepository.php
- **Migrated from**: `includes/config.php` (lines 158-201)
- **Purpose**: Manage product data access
- **Methods**: `find($id)`, `getAll()`, `getFeatured()`, `getNew()`, `findByCategory()`, `findByBrand()`, `filterByPrice()`, `filterByRating()`
- **Used by**: `ProductController`, `HomeController`, `CartService`
- **Dependencies**: Reads `data/products.json`
- **Side effects**: File I/O (reads JSON file)

### UserRepository.php
- **Migrated from**: `includes/config.php` (lines 65-152)
- **Purpose**: Manage user data access
- **Methods**: `find($id)`, `getAll()`, `create($email, $password, $name)`, `findByEmail($email)`, `save($users)`
- **Used by**: `AuthService`, `AuthController`
- **Dependencies**: Reads/writes `data/users.json`
- **Side effects**: File I/O (reads/writes JSON)

### CartRepository.php
- **Migrated from**: `includes/session-manager.php` (lines 18-51)
- **Purpose**: Manage cart data persistence
- **Methods**: `get()`, `save($cartData)`, `saveToDisk($userId, $cartData)`, `loadFromDisk($userId)`
- **Used by**: `CartService`, `CartController`
- **Dependencies**: Session, `data/user_carts.json`
- **Side effects**: Session modification, file I/O

### WishlistRepository.php
- **Migrated from**: `includes/session-manager.php` (lines 56-96)
- **Purpose**: Manage wishlist data persistence
- **Methods**: `get()`, `save($wishlistData)`, `saveToDisk($userId, $wishlistData)`, `loadFromDisk($userId)`
- **Used by**: `WishlistService`, `WishlistController`
- **Dependencies**: Session, `data/user_wishlists.json`
- **Side effects**: Session modification, file I/O

### CategoryRepository.php
- **Migrated from**: `includes/config.php` (lines 27-33, 163-166)
- **Purpose**: Manage category data access
- **Methods**: `find($id)`, `getAll()`
- **Used by**: `ProductController`, `HomeController`
- **Dependencies**: `config/constants.php`
- **Side effects**: None (reads from in-memory array)

### BrandRepository.php
- **Migrated from**: `includes/config.php` (lines 36-49, 168-171)
- **Purpose**: Manage brand data access
- **Methods**: `find($id)`, `getAll()`
- **Used by**: `ProductController`
- **Dependencies**: `config/constants.php`
- **Side effects**: None (reads from in-memory array)

### OrderRepository.php
- **Purpose**: Manage order data persistence (future implementation)
- **Methods**: `create($orderData)`, `find($id)`, `findByUser($userId)`
- **Used by**: `CheckoutController`, `OrderController`
- **Dependencies**: `data/orders.json` (to be created)
- **Side effects**: File I/O

---

## 3. Functional Responsibilities

### Common pattern in all Repositories:

#### Constructor
- **Logic**: Load data from source (JSON file, session, config)
- **Side effects**: File I/O, session access

#### find($id)
- **Purpose**: Get single entity by ID
- **Logic**: Search array for matching ID
- **Returns**: Entity array or null
- **Side effects**: None (read-only)

#### getAll()
- **Purpose**: Get all entities
- **Logic**: Return entire dataset
- **Returns**: Array of entities
- **Side effects**: None (read-only)

#### save($data)
- **Purpose**: Persist data
- **Logic**: Write to JSON file or session
- **Side effects**: File I/O or session modification

---

## 4. Dependency & Impact Analysis

### Dependencies:
- **Depends on**: `data/` directory (JSON files)
- **Depends on**: PHP session (for Cart/Wishlist)
- **Depends on**: `config/constants.php` (for Category/Brand)
- **Used by**: Services, Controllers

### Impact of changes:

#### If data source changes (JSON → Database):
- ✅ **ONLY** Repositories need updating
- ✅ Services, Controllers, Views remain unchanged
- ✅ This is the benefit of Repository pattern

#### If method signature changes:
- ❌ **BREAKS**: All Services calling this method
- ❌ **BREAKS**: All Controllers calling this method

#### If JSON structure changes:
- ⚠️ **REQUIRES**: Data migration script
- ⚠️ **REQUIRES**: Update Repository mapping logic

---

## 5. Modification Guidelines

### Safe changes:
- ✅ Adding new methods (doesn't break existing code)
- ✅ Optimizing internal queries
- ✅ Adding caching
- ✅ Changing data source (JSON → DB) without changing public API

### Changes requiring caution:
- ⚠️ Changing method signatures (breaks all callers)
- ⚠️ Changing return types (breaks type hints)
- ⚠️ Removing methods (breaks all callers)
- ⚠️ Changing JSON structure (requires data migration)

### Files to modify together:
When changing a Repository:
1. Update corresponding Service (if business logic affected)
2. Update corresponding Controller (if API changed)
3. Update data files (if structure changed)
4. Update tests

### Common mistakes to avoid:
- ❌ Don't add business logic (use Services)
- ❌ Don't handle HTTP requests (use Controllers)
- ❌ Don't render HTML (use Views)
- ❌ Don't validate data (use Services)
- ❌ Don't expose internal data structure

---

## 6. Usage Notes

### Execution flow:
```
Controller → Repository → Data Source (JSON/Session)
                ↓
            Model instance
                ↓
            Service (business logic)
                ↓
            Controller → View
```

### Example usage:
```php
// In Controller
$productRepo = new ProductRepository();
$product = $productRepo->find($id);

// In Service
$cartRepo = new CartRepository();
$cart = $cartRepo->get();
$cart[$productId] = $quantity;
$cartRepo->save($cart);
```

### Environment assumptions:
- `data/` directory exists and is writable
- Session is initialized
- JSON files are valid JSON format

### Configuration relied upon:
- `config/constants.php` for categories/brands
- Session configuration

---

## 7. Ownership & Boundaries

### What MUST be implemented here:
- ✅ All data access operations
- ✅ File I/O
- ✅ Database queries (future)
- ✅ Session access for cart/wishlist
- ✅ Data mapping (array → Model)

### What MUST NOT be implemented here:
- ❌ Business logic (use Services)
- ❌ Validation (use Services)
- ❌ HTTP handling (use Controllers)
- ❌ HTML rendering (use Views)
- ❌ Calculations (use Services)

### Boundary rules:
- Repositories are the **ONLY** layer that accesses data sources
- Repositories should return **raw data** or **Model instances**
- Repositories should be **stateless** (no instance variables except data source)

---

## 8. Design Patterns

### Repository Pattern
Provides abstraction over data access:
```php
interface ProductRepositoryInterface {
    public function find($id);
    public function getAll();
}

// Can swap implementations:
class JsonProductRepository implements ProductRepositoryInterface { }
class DatabaseProductRepository implements ProductRepositoryInterface { }
```

---

## 9. Testing

### Unit testing Repositories:
```php
// Test find()
$repo = new ProductRepository();
$product = $repo->find(1);
assert($product !== null);
assert($product['id'] === 1);

// Test save()
$cartRepo = new CartRepository();
$cartRepo->save([1 => 2, 3 => 1]);
$cart = $cartRepo->get();
assert($cart[1] === 2);
```

---

## 10. Migration from Legacy

### Before:
```php
// Global functions in config.php
function getProduct($id) {
    global $products;
    return $products[$id] ?? null;
}
```

### After:
```php
// Repository class
class ProductRepository {
    public function find($id) {
        return $this->products[$id] ?? null;
    }
}
```

### Benefits:
- ✅ Testable in isolation
- ✅ Easy to swap data sources
- ✅ No global state
- ✅ Type-safe

---

## Quick Reference

**Add new Repository**: Create class → Implement CRUD methods → Inject into Service  
**Change data source**: Update Repository internals → Keep public API same  
**Add new method**: Add to Repository → Use in Service  
**Fix data bug**: Update Repository → Test → Deploy

**Rule of thumb**: If it touches data storage, it belongs here.
