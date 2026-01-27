# public/ Directory

## 1. Directory Overview

**Purpose**: Web Root - publicly accessible files served by the web server.

**Why it exists**: Security - only files in this directory are web-accessible. Application code in `app/` is protected.

**Responsibility**: Entry point (`index.php`) and static assets (CSS, JS, images).

---

## 2. Files Breakdown

### index.php
- **Purpose**: Front controller - single entry point for all requests
- **Migrated from**: All page files (index.php, products.php, etc.)
- **Responsibilities**:
  1. Load Composer autoloader
  2. Initialize session
  3. Route requests to controllers
  4. Handle errors
- **Used by**: Web server (all requests route here)
- **Dependencies**: `vendor/autoload.php`, `config/`, `routes/web.php`
- **Side effects**: Executes controllers, outputs HTML/JSON

### .htaccess
- **Purpose**: Apache URL rewriting configuration
- **Logic**: Rewrite all requests to `index.php`
- **Used by**: Apache web server
- **Dependencies**: mod_rewrite enabled

### assets/
- **Purpose**: Static files (CSS, JS, images)
- **Subdirectories**: `css/`, `js/`, `images/`
- **Used by**: All views
- **Dependencies**: None

---

## 3. Functional Responsibilities

### index.php Flow:
```
1. Load autoloader
2. Load config
3. Initialize session
4. Parse route from URL
5. Instantiate controller
6. Call controller method
7. Output response
```

---

## 4. Dependency & Impact Analysis

### Dependencies:
- **Depends on**: `app/` (all application code)
- **Depends on**: `config/`
- **Depends on**: `routes/`
- **Used by**: Web server

### Impact of changes:

#### If index.php changes:
- ⚠️ **AFFECTS**: All requests
- ⚠️ **TEST**: Thoroughly before deploying

#### If .htaccess changes:
- ⚠️ **AFFECTS**: URL routing
- ⚠️ **TEST**: All URLs

---

## 5. Modification Guidelines

### Safe changes:
- ✅ Adding static assets
- ✅ Adding new routes

### Changes requiring caution:
- ⚠️ Changing index.php routing logic
- ⚠️ Changing .htaccess rules

### Common mistakes to avoid:
- ❌ Don't put application code here (use `app/`)
- ❌ Don't put config here (use `config/`)
- ❌ Don't hardcode routes (use `routes/web.php`)

---

## 7. Ownership & Boundaries

### What MUST be here:
- ✅ index.php (entry point)
- ✅ Static assets (CSS, JS, images)
- ✅ .htaccess (web server config)

### What MUST NOT be here:
- ❌ Application code (use `app/`)
- ❌ Configuration (use `config/`)
- ❌ Data files (use `data/`)

---

## Quick Reference

**Web server config**: Point document root to `public/`  
**Add asset**: Place in `public/assets/`  
**Rule of thumb**: If web server serves it, it belongs here.
