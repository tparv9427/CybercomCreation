# config/ Directory

## 1. Directory Overview

**Purpose**: Configuration Files - application settings and constants.

**Why it exists**: Centralize configuration, separate from code.

**Responsibility**: Define constants, settings, and global data.

---

## 2. Files Breakdown

### app.php
- **Migrated from**: `includes/config.php` (lines 8-13)
- **Purpose**: Application constants
- **Contents**: `SITE_NAME`, `CURRENCY`, `EASYCART_VERSION`, `DEBUG`
- **Used by**: All application code
- **Dependencies**: None

### constants.php
- **Migrated from**: `includes/config.php` (lines 27-49)
- **Purpose**: Global data arrays
- **Contents**: `$categories`, `$brands`
- **Used by**: `CategoryRepository`, `BrandRepository`
- **Dependencies**: None

---

## 3. Functional Responsibilities

### app.php
```php
define('SITE_NAME', 'EasyCart');
define('CURRENCY', '$');
define('EASYCART_VERSION', '4.0-psr4-mvc');
define('DEBUG', false);
```

### constants.php
```php
$categories = [
    1 => ['id' => 1, 'name' => 'Electronics', 'slug' => 'electronics'],
    // ...
];

$brands = [
    1 => ['id' => 1, 'name' => 'TechPro'],
    // ...
];
```

---

## 4. Dependency & Impact Analysis

### Dependencies:
- **Depends on**: Nothing
- **Used by**: All application code

### Impact of changes:

#### If constant changes:
- ⚠️ **AFFECTS**: All code using this constant
- ⚠️ **TEST**: Thoroughly

---

## 5. Modification Guidelines

### Safe changes:
- ✅ Adding new constants
- ✅ Changing constant values (with testing)

### Changes requiring caution:
- ⚠️ Removing constants (breaks code using them)
- ⚠️ Renaming constants (breaks all references)

### Common mistakes to avoid:
- ❌ Don't put business logic here
- ❌ Don't put functions here (use Helpers)

---

## 7. Ownership & Boundaries

### What MUST be here:
- ✅ Application constants
- ✅ Global configuration
- ✅ Environment settings

### What MUST NOT be here:
- ❌ Business logic
- ❌ Functions
- ❌ Classes

---

## Quick Reference

**Add config**: Add to app.php or constants.php → Use in code  
**Rule of thumb**: If it's configuration, it belongs here.
