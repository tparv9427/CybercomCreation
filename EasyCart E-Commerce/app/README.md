# app/ Directory

## 1. Directory Overview

**Purpose**: This is the core application source code directory containing all PSR-4 compliant PHP classes.

**Why it exists**: To organize all business logic, data access, and presentation code following MVC architecture and PSR-4 autoloading standards.

**Responsibility**: Houses all application code separated by architectural layers (Models, Views, Controllers, Repositories, Services, Helpers).

**Namespace**: `EasyCart\`

---

## 2. Subdirectories Breakdown

| Directory | Purpose | Responsibility |
|-----------|---------|----------------|
| **Controllers/** | HTTP request handlers | Handle incoming requests, coordinate services/repositories, return responses |
| **Models/** | Data structures | Define data entities and their properties |
| **Repositories/** | Data access layer | Handle all data persistence (JSON files, future: database) |
| **Services/** | Business logic | Implement complex business rules and workflows |
| **Helpers/** | Utility functions | Provide reusable utility methods (formatting, validation, etc.) |
| **Views/** | HTML templates | Render HTML output for pages |

---

## 3. Functional Responsibilities

### What belongs in app/:
- ✅ All PHP classes following PSR-4
- ✅ Business logic
- ✅ Data access code
- ✅ View templates
- ✅ Utility helpers

### What does NOT belong here:
- ❌ Configuration files (use `config/`)
- ❌ Public assets (CSS, JS, images - use `public/assets/`)
- ❌ Data files (use `data/`)
- ❌ Routes (use `routes/`)
- ❌ Entry points (use `public/`)

---

## 4. Dependency & Impact Analysis

### Dependencies:
- **Depends on**: `config/` for configuration constants
- **Depends on**: `data/` for JSON data files
- **Used by**: `public/index.php` (front controller)
- **Used by**: All page controllers

### Impact of changes:
- **Renaming a class**: Update all imports across the project
- **Changing namespace**: Update `composer.json` PSR-4 mapping
- **Moving files**: Update autoloader and all references
- **Deleting a class**: Check all dependencies first (search for `use EasyCart\...`)

---

## 5. Modification Guidelines

### Safe changes:
- ✅ Adding new classes (doesn't break existing code)
- ✅ Adding new methods to existing classes (if backward compatible)
- ✅ Refactoring internal logic without changing public APIs

### Changes requiring caution:
- ⚠️ Changing method signatures (breaks all callers)
- ⚠️ Renaming public methods (breaks all callers)
- ⚠️ Changing class names (breaks all imports)
- ⚠️ Moving classes to different namespaces

### Files to modify together:
- When changing a Repository, update corresponding Service
- When changing a Model, update corresponding Repository
- When changing a Controller, update corresponding View

### Common mistakes to avoid:
- ❌ Don't use global variables in classes
- ❌ Don't mix business logic in Controllers (use Services)
- ❌ Don't put HTML in Controllers (use Views)
- ❌ Don't access data directly in Controllers (use Repositories)
- ❌ Don't create circular dependencies

---

## 6. Usage Notes

### Execution flow:
1. Request hits `public/index.php`
2. Autoloader loads classes from `app/`
3. Router determines which Controller to call
4. Controller uses Services/Repositories
5. Controller passes data to View
6. View renders HTML

### Environment assumptions:
- PHP >= 7.4
- PSR-4 autoloading enabled
- Session started before any class instantiation

### Configuration relied upon:
- `config/app.php` - Application constants
- `config/constants.php` - Global data arrays

---

## 7. Ownership & Boundaries

### What MUST be implemented here:
- All application business logic
- All data access code
- All view rendering
- All request handling

### What MUST NOT be implemented here:
- Web server configuration
- Database schema (future)
- Third-party libraries
- Static assets

### Boundary rules:
- **Controllers**: Only handle HTTP, delegate to Services
- **Services**: Only business logic, no HTTP concerns
- **Repositories**: Only data access, no business logic
- **Models**: Only data structures, no behavior
- **Helpers**: Only pure functions, no state
- **Views**: Only presentation, no logic

---

## 8. PSR-4 Autoloading

### Namespace mapping:
```
EasyCart\Controllers\HomeController
→ app/Controllers/HomeController.php

EasyCart\Models\Product
→ app/Models/Product.php

EasyCart\Repositories\ProductRepository
→ app/Repositories/ProductRepository.php
```

### Usage in code:
```php
use EasyCart\Controllers\HomeController;
use EasyCart\Models\Product;
use EasyCart\Repositories\ProductRepository;

$controller = new HomeController();
$product = new Product();
$repo = new ProductRepository();
```

---

## 9. Testing

### Unit testing:
Each class should be testable in isolation:
```php
// tests/Unit/Repositories/ProductRepositoryTest.php
use EasyCart\Repositories\ProductRepository;

$repo = new ProductRepository();
$product = $repo->find(1);
```

### Integration testing:
Test how classes work together:
```php
// tests/Integration/Controllers/HomeControllerTest.php
use EasyCart\Controllers\HomeController;

$controller = new HomeController();
ob_start();
$controller->index();
$output = ob_get_clean();
```

---

## 10. Migration from Legacy Code

### Original structure:
```
includes/config.php → Functions scattered
index.php → Mixed logic and HTML
products.php → Mixed logic and HTML
```

### New structure:
```
app/Repositories/ProductRepository.php → Data access
app/Services/ProductService.php → Business logic
app/Controllers/ProductController.php → Request handling
app/Views/products/index.php → HTML only
```

### Migration status:
- ✅ Directory structure created
- ⏳ Classes being implemented
- ⏳ Views being migrated
- ⏳ Testing in progress

---

## Quick Reference

**Add new feature**: Create Model → Repository → Service → Controller → View  
**Fix bug**: Identify layer → Update class → Test  
**Refactor**: Keep public APIs stable → Update internal logic  
**Delete code**: Check dependencies first → Remove → Test  

For specific guidance, see README.md in each subdirectory.
