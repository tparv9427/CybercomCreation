# app/Helpers/ Directory

## 1. Directory Overview

**Purpose**: Utility Functions - provide reusable helper methods for formatting, validation, and UI rendering.

**Why it exists**: To avoid code duplication and provide common utilities across the application.

**Responsibility**: Pure utility functions with no side effects (where possible).

**Single Responsibility**: Utilities only - no business logic, no data access.

---

## 2. Files Breakdown

### FormatHelper.php
- **Migrated from**: `includes/config.php` (formatPrice)
- **Methods**: `price($price)`, `date($date, $format)`, `truncate($text, $length, $suffix)`
- **Used by**: All views, controllers
- **Dependencies**: `config/app.php` (CURRENCY constant)
- **Side effects**: None (pure functions)

### UIHelper.php
- **Extracted from**: Inline logic in `index.php`, `products.php`, `product.php`
- **Methods**: `renderStarRating($rating)`, `renderProductBadge($product)`, `renderBreadcrumb($items)`
- **Used by**: All views displaying products
- **Dependencies**: None
- **Side effects**: Echoes HTML

### ValidationHelper.php
- **Purpose**: Input validation utilities
- **Methods**: `validateEmail($email)`, `validatePassword($password)`, `validatePhone($phone)`, `sanitize($input)`
- **Used by**: Services, Controllers
- **Dependencies**: None
- **Side effects**: None (pure functions)

---

## 3. Functional Responsibilities

### FormatHelper

#### price($price)
- **Logic**: Format number with currency symbol
- **Returns**: string (e.g., "$99.99")
- **Side effects**: None

### UIHelper

#### renderStarRating($rating)
- **Logic**: Generate star HTML based on rating (0-5)
- **Returns**: void (echoes HTML)
- **Side effects**: Echoes HTML

---

## 4. Dependency & Impact Analysis

### Dependencies:
- **Depends on**: `config/app.php` (constants)
- **Used by**: All layers (Controllers, Views, Services)

### Impact of changes:

#### If helper signature changes:
- ❌ **BREAKS**: All code calling this helper

---

## 5. Modification Guidelines

### Safe changes:
- ✅ Adding new helpers
- ✅ Optimizing internal logic

### Changes requiring caution:
- ⚠️ Changing signatures
- ⚠️ Changing return types

### Common mistakes to avoid:
- ❌ Don't add business logic
- ❌ Don't access data
- ❌ Don't maintain state

---

## 7. Ownership & Boundaries

### What MUST be here:
- ✅ Formatting functions
- ✅ Validation functions
- ✅ UI rendering utilities

### What MUST NOT be here:
- ❌ Business logic
- ❌ Data access
- ❌ HTTP handling

---

## Quick Reference

**Add helper**: Create static method → Use anywhere  
**Rule of thumb**: If it's a pure utility, it belongs here.
