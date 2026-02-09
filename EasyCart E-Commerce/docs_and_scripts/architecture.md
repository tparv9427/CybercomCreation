# EasyCart Architecture & Call Hierarchy

This diagram visualizes the flow of data and control for the **Cart** and **Checkout** features, utilizing the **ADR/Layered MVC** pattern.

```mermaid
graph TD
    classDef client fill:#e1f5fe,stroke:#01579b,stroke-width:2px;
    classDef router fill:#fff3e0,stroke:#e65100,stroke-width:2px;
    classDef controller fill:#e8f5e9,stroke:#1b5e20,stroke-width:2px;
    classDef service fill:#f3e5f5,stroke:#4a148c,stroke-width:2px;
    classDef repo fill:#fff9c4,stroke:#fbc02d,stroke-width:2px;
    classDef db fill:#eceff1,stroke:#37474f,stroke-width:1px;

    subgraph Client_Layer [Client Layer (Browser)]
        direction TB
        MainJS[main.js]:::client
        CheckoutJS[checkout scripts]:::client
    end

    subgraph Routing_Layer [Routing Layer]
        AjaxRouter[public/ajax_cart.php]:::router
    end

    subgraph Controller_Layer [Controller Layer]
        CartCtrl[CartController.php]:::controller
    end

    subgraph Service_Layer [Service Layer]
        CartSvc[CartService.php]:::service
        PricingSvc[PricingService.php]:::service
    end

    subgraph Repository_Layer [Repository Layer]
        CartRepo[CartRepository.php]:::repo
        ProdRepo[ProductRepository.php]:::repo
        SaveRepo[SaveForLaterRepository.php]:::repo
    end

    subgraph Data_Layer [Data Storage]
        Session[(Session $_SESSION)]:::db
        Database[(Database / Array)]:::db
    end

    %% Client Interactions
    MainJS -- "fetch('ajax_cart.php')" --> AjaxRouter
    CheckoutJS -- "fetch('ajax_cart.php')" --> AjaxRouter

    %% Routing
    AjaxRouter -- "Action: add/update/remove" --> CartCtrl

    %% Controller Logic
    CartCtrl -- "CartService->add()" --> CartSvc
    CartCtrl -- "CartService->update()" --> CartSvc
    CartCtrl -- "CartService->remove()" --> CartSvc
    CartCtrl -- "CartService->saveForLater()" --> CartSvc
    CartCtrl -- "PricingService->calculateAll()" --> PricingSvc

    %% Service Logic
    CartSvc -- "ProductRepo->find()" --> ProdRepo
    CartSvc -- "CartRepo->get/save()" --> CartRepo
    CartSvc -- "SaveRepo->save()" --> SaveRepo

    PricingSvc -- "ProductRepo->find()" --> ProdRepo

    %% Repository Logic
    CartRepo -- "Read/Write" --> Session
    ProdRepo -- "Read" --> Database
    SaveRepo -- "Read/Write" --> Session

    %% Specific Call Flows (Examples)
    note_add[Button Click: Add to Cart] -.-> MainJS
    note_save[Button Click: Save for Later] -.-> MainJS
```

## Hierarchy Breakdown

### 1. Client Layer (Javascript)

- **File:** `public/assets/js/main.js`
- **Role:** Handles user events (clicks), updates the UI visually, and sends asynchronous requests to the server.
- **Key Functions:**
  - `addToCart(productId, quantity)`
  - `removeFromCart(productId)`
  - `saveForLater(productId)`
  - `updateQuantity(productId, quantity)`

### 2. Routing Layer (Entry Point)

- **File:** `public/ajax_cart.php`
- **Role:** Acts as a traffic cop. It receives the pure AJAX request, sets the internal route, and forwards it to the main application bootstrapper.

### 3. Controller Layer (The Coordinator)

- **File:** `app/Controllers/CartController.php`
- **Role:** Interprets the request. It doesn't know _how_ to calculate taxes or where data is stored; it just knows _who_ to ask.
- **Key Methods:**
  - `add()`: Receives POST data, calls Service, returns JSON.
  - `saveForLater()`: Coordinates moving data from Cart to Saved list.

### 4. Service Layer (The Brain)

- **File:** `app/Services/CartService.php`
- **Role:** Contains the business logic. It ensures rules are followed (e.g., "Quantity cannot be negative", "Moving to Saved removes from Cart").
- **Key Methods:**
  - `add($id, $qty)`
  - `moveToCartFromSaved($id)`

### 5. Repository Layer ( The Librarian)

- **Files:** `CartRepository.php`, `ProductRepository.php`
- **Role:** The only layer that touches the raw data (Database or Session). If we switch from Sessions to MySQL later, we **only** change this layer.
