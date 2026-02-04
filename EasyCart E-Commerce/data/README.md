# data/ Directory

## 1. Directory Overview

**Purpose**: Data Storage - JSON files storing application data.

**Why it exists**: Persist data without a database.

**Responsibility**: Store products, users, carts, wishlists, orders.

---

## 2. Files Breakdown

### products.json
- **Purpose**: Product catalog
- **Used by**: `ProductRepository`
- **Format**: Array of product objects
- **Writable**: No (read-only)

### users.json
- **Purpose**: User accounts
- **Used by**: `UserRepository`
- **Format**: Array of user objects
- **Writable**: Yes (new registrations)

### user_carts.json
- **Purpose**: Persistent user carts
- **Used by**: `CartRepository`
- **Format**: Object mapping user_id to cart data
- **Writable**: Yes

### user_wishlists.json
- **Purpose**: Persistent user wishlists
- **Used by**: `WishlistRepository`
- **Format**: Object mapping user_id to wishlist data
- **Writable**: Yes

---

## 3. Functional Responsibilities

### Data persistence:
- All Repositories read/write to these files
- JSON format for easy editing
- Future: Migrate to database

---

## 4. Modification Guidelines

### Safe changes:
- ✅ Adding new data files
- ✅ Adding new fields (with defaults)

### Changes requiring caution:
- ⚠️ Changing JSON structure (requires migration)
- ⚠️ Deleting files (breaks application)

### Common mistakes to avoid:
- ❌ Don't edit manually (use application)
- ❌ Don't commit user data to git

---

## Quick Reference

**Add data**: Use Repository methods  
**Backup**: Copy files before major changes  
**Rule of thumb**: If it's data, it belongs here.
