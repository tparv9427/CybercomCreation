# 🛒 EasyCart E-Commerce: Full Project Report

## 1. What it is made for
**EasyCart** is a modern, high-performance e-commerce platform built using a custom **PSR-4 compliant MVC (Model-View-Controller)** architecture in PHP. The project was specifically designed to transition from a procedural codebase to a clean, maintainable, and scalable professional structure. It aims to provide a seamless shopping experience for users while offering developers a robust framework for building e-commerce features like product catalogs, persistent carts, and multi-step checkout processes.

## 2. Key Features
- **Clean MVC Architecture**: Strict separation of concerns between logic (Controllers/Services), data (Models/Repositories), and presentation (Views).
- **Advanced Product Catalog**: Supports dynamic filtering, category/brand-based browsing, relative search, and featured product showcases.
- **Persistent Cart & Wishlist**: Data is synchronized between transient session state and persistent storage (JSON/Database), ensuring users don't lose items across sessions.
- **Robust Authentication**: Fully implemented Login, Signup, and Secure Logout with password hashing and session management.
- **Complex Checkout Engine**: A dedicated Pricing Service handles subtotal, shipping, taxes, and payment fees dynamically based on user selections.
- **Modern User Interface**: Responsive design with interactive components powered by a unified JavaScript handling system (`main.js`).
- **Data Persistence**: Uses a flexible Repository pattern that currently interfaces with JSON files but is architected for easy migration to a relational database (SQL).

## 3. Project Structure
```text
EasyCart E-Commerce/
├── app/                        # Application Core (Source Code)
│   ├── Controllers/            # Request Handlers & View Orchestration
│   ├── Core/                   # Framework Core logic (Routing, Session, etc.)
│   ├── Repositories/           # Data Access Layer (JSON/DB interactions)
│   ├── Services/               # Business Logic Layer (Calculations, Logic)
│   ├── Models/                 # Data Entity definitions
│   ├── Helpers/                # Utility functions (Formatting, Validation)
│   └── Views/                  # UI Templates (HTML/PHP)
├── config/                     # Configuration Files
├── public/                     # Web Root (Publicly accessible files)
│   ├── assets/                 # CSS, JS, Images
│   └── index.php               # Front Controller (Entry Point)
├── data/                       # Persistent JSON Storage
├── routes/                     # URL Routing Definitions
├── scripts/                    # Automation & Utility Scripts
└── tests/                      # Testing Suite
```

## 4. Detailed File Analysis

### Core Framework (`app/Core/`)
- **`Router.php`**: The heart of the application's URL handling. It parses incoming requests and dispatches them to the correct controller method based on defined routes.
- **`View.php`**: A rendering engine that manages how HTML templates are loaded. It ensures data passed from controllers is safely extracted and available to the view files.
- **`Session.php`**: Manages user session state across the application. It provides a clean interface for storing and retrieving user data securely during their visit.
- **`Middleware.php`**: Logic that runs before or after a request, used for authentication checks and security filters. It ensures only authorized users can access protected pages.
- **`CSRF.php`**: Implements Cross-Site Request Forgery protection. It generates and validates security tokens to prevent unauthorized form submissions from external sites.

### Controllers (`app/Controllers/`)
- **`AuthController.php`**: Orchestrates the login and registration process. It communicates with the AuthService to verify credentials and manages the redirection of users.
- **`CartController.php`**: Handles all user interactions with the shopping cart. This includes adding items, updating quantities, and removing products via AJAX or standard requests.
- **`ProductController.php`**: Manages the display of the product catalog. It handles product listings, individual detail pages, search results, and brand-specific filtering.
- **`CheckoutController.php`**: Guides the user through the final purchase steps. It calculates totals, processes shipping info, and finalizes the order creation.
- **`OrderController.php`**: Provides users with their purchase history and order details. It also handles the "Success" page shown after a completed transaction.

### Business Logic & Data (`app/Services/` & `app/Repositories/`)
- **`CartService.php`**: Contains the core business logic for cart operations. It calculates quantities, handles "Save for Later" logic, and ensures cart data is valid.
- **`PricingService.php`**: A specialized service focused on monetary calculations. it calculates taxes, shipping fees, and discounts to provide a final order total.
- **`ProductRepository.php`**: The unified interface for retrieving product data. It encapsulates the retrieval logic from JSON files, providing clean methods for the rest of the app.
- **`UserRepository.php`**: Manages user data persistence. It handles finding users by email/ID and saving new registration records to the storage layer.
- **`CartRepository.php`**: Responsible for the low-level storage of cart data. It syncs the user's active cart between the session and the persistent storage disk.

### Supporting Files
- **`public/index.php`**: The application entry point. It initializes the autoloader, starts the session, and triggers the router to process the current request.
- **`public/assets/js/main.js`**: The primary JavaScript file. It handles all client-side interactivity, including AJAX cart updates, form validations, and UI notifications.
- **`config/app.php`**: Stores global application settings. This includes the base URL, application name, and environment-specific configurations.
- **`routes/web.php`**: The central navigation map. It defines every URL available in the application and maps them to their respective controller actions.
- **`app/Helpers/FormatHelper.php`**: Provides utility methods for data display. It handles currency formatting, date manipulation, and string cleaning for the UI.

### Scripts & Data Migrations (`/`, `scripts/`, `data/`)
- **`easycart_optimized_migration.sql`**: A comprehensive SQL schema and data migration file. It contains the structure needed to move from JSON storage to a high-performance relational database.
- **`generate_mixed_csv.php`**: A utility script used for bulk data generation. it creates realistic product datasets in CSV format for testing and catalog expansion.
- **`add_extra_products.php`**: A maintenance script that allows for the programmatic addition of products to the system. It is used to seed the database or JSON files with new inventory.
- **`data/products.json`**: The primary data source for the application's catalog. It stores all product attributes, prices, and relationships in a structured JSON format.
- **`scripts/manage-server.cmd`**: A convenient developer utility script. It provides a menu-driven interface to start, list, and manage local PHP development servers for the project.

## 5. Data Flow & User Experience

### Data Flow (The Request Lifecycle)
1. **Request**: The user clicks a link or submits a form (e.g., `/product?id=123`).
2. **Entry**: The request hits `public/index.php`, which boots the system.
3. **Routing**: `Router.php` identifies the path and calls the relevant `Controller` (e.g., `ProductController@show`).
4. **Service Call**: The `Controller` asks a `Service` to fetch data (e.g., `ProductService` getting details).
5. **Persistence**: The `Service` uses a `Repository` to read from `data/products.json`.
6. **Processing**: Logic is applied (e.g., formatting price, checking stock).
7. **Response**: The `Controller` passes data to the `View`, which generates the HTML.
8. **Interactivity**: `main.js` monitors the page for user actions (like "Add to Cart"), triggering AJAX loops back to step 1 without refreshing the page.

### User Experience (UX) Journey
- **Discovery**: Users arrive at the Homepage, seeing featured products and banners. They can search or browse by category smoothly.
- **Interactions**: On product pages, users can zoom into images and toggle items in their Wishlist. Notifications appear instantly for every action.
- **Refinement**: The Shopping Cart allows for quick quantity adjustments and "Save for Later" functionality to keep the cart organized.
- **Conversion**: The Checkout process is streamlined into predictable steps, showing a transparent price breakdown before the final order placement.
- **Retention**: Post-purchase, users can view their "My Orders" page to track history, creating a professional and trustworthy e-commerce environment.

---

## 🔗 Related Documentation
- [Full Project Flowchart](./PROJECT_FLOW_CHART.md) - Visual logic diagrams for all system stages.
- [Project Structure](./PROJECT_STRUCTURE.md) - Detailed directory and method outline.
