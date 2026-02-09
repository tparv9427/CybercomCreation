# 🗺️ EasyCart E-Commerce: Full Project Flowchart

This document provides a comprehensive visual representation of the user journey, data states, and application transitions within the EasyCart system.

## 1. High-Level System Architecture

```mermaid
graph TD
    User([User Website Visit]) --> Entry{Entry Point}
    
    subgraph "Public Area"
        Entry --> Home[Homepage /]
        Entry --> ProductList[Product Listing /products]
        Entry --> Search[Search Results /search]
        ProductList --> ProductDetail[Product Detail /product/id]
        Search --> ProductDetail
    end

    subgraph "User Authentication"
        Entry --> Login[Login Page /login]
        Entry --> Signup[Signup Page /signup]
        Login --> |Success| AuthState[Authenticated State]
        Signup --> |Success| AuthState
    end

    subgraph "Cart & Wishlist (Shared)"
        ProductDetail --> |AJAX| AddCart[Add to Cart]
        ProductDetail --> |AJAX| AddWish[Toggle Wishlist]
        AddCart --> CartView[Cart Page /cart]
        AddWish --> WishView[Wishlist Page /wishlist]
        WishView --> |Move| CartView
    end

    subgraph "Protected Area (Auth Required)"
        CartView --> |Proceed| Checkout[Checkout /checkout]
        AuthState --> Dashboard[Dashboard /dashboard]
        AuthState --> Orders[Order History /orders]
        Checkout --> Pay[Payment Selection]
        Pay --> OrderSuccess[Success /order/success]
        OrderSuccess --> Orders
    end

    Dashboard -.-> Chart[Dashboard Charts]
    Orders --> OrderDetail[Order Details /order/id]
    OrderDetail --> Invoice[Generate Invoice]
```

---

## 2. Detailed Interaction & State Logic

### A. Authentication & Session Flow
This flow describes how user data (especially the Cart) transitions between a Guest state and an Authenticated state.

```mermaid
flowchart LR
    Start((Start)) --> IsAuth{User Logged In?}
    
    IsAuth -- No --> GuestSession[Guest Session Data]
    GuestSession --> Browse[Browse & Add to Cart]
    Browse --> GuestCart[(Guest Cart JSON)]
    
    GuestCart --> TriggerLogin[Login Required for Checkout]
    TriggerLogin --> InputCreds[Enter Credentials]
    
    InputCreds --> Verify{Valid?}
    Verify -- No --> Error[Show Error]
    Verify -- Yes --> PostLogin[Login Successful]
    
    PostLogin --> Merge[Merge Guest Cart with User Cart]
    Merge --> UserSession[Authenticated Session Data]
    IsAuth -- Yes --> UserSession
    
    UserSession --> Access[Access Dashboard/Orders/Checkout]
```

### B. Add to Cart & Management Flow (AJAX)
Every item interaction happens asynchronously without page reloads for a premium UX.

```mermaid
flowchart TD
    PDP[Product Detail Page] --> ClickAdd[Click 'Add to Cart']
    ClickAdd --> AJAX_Add[POST /cart/add]
    
    AJAX_Add --> Service[CartService::add]
    Service --> Repos[CartRepository::save]
    Repos --> Disk[(user_carts.json)]
    
    Disk --> Response{JSON Success?}
    Response --> UpdateUI[Update Header Cart Count]
    UpdateUI --> Toast[Show Success Notification]
    
    subgraph "Cart Page Operations"
        Cart[View /cart] --> Qty[Update Quantity]
        Cart --> Remove[Remove Item]
        Cart --> SFL[Save for Later]
        Qty --> AJAX_Update[AJAX Re-calculation]
        SFL --> SavedArea[Moved to Saved items Section]
    end
```

### C. Checkout & Order Processing
The most complex logic path involving pricing, validation, and finalization.

```mermaid
sequenceDiagram
    participant U as User
    participant C as CheckoutController
    participant P as PricingService
    participant O as OrderRepository
    participant D as Data Storage

    U->>C: GET /checkout
    C->>P: calculateSubtotal()
    P->>U: Display Pricing Summary
    
    U->>C: Select Shipping/Payment
    U->>C: POST /checkout/pricing (AJAX)
    C->>P: calculateAll(Tax + Shipping + Fees)
    P->>U: Updated Totals
    
    U->>C: POST /checkout (Submit Order)
    C->>C: Validate CSRF & Session
    C->>O: createOrder(CartData + Profile)
    O->>D: Save to user_orders.json
    D-->>C: Order ID Generated
    C->>C: emptyCart()
    C->>U: Redirect /order/success
```

### D. User Dashboard & Order Management
The personal space for users to manage their history.

```mermaid
graph TD
    DB[Dashboard] --> Stats[Total Spent / Orders Count]
    DB --> Chart[Monthly Spending Chart]
    Chart --> API[GET /api/dashboard/chart]
    
    OH[Order History] --> List[Display All Past Orders]
    List --> ClickRow[Click View Details]
    ClickRow --> Detail[Order Detail Page]
    
    Detail --> Inv[Print Modern Invoice]
    Detail --> Archive[Archive/Delete Order]
    
    subgraph "Admin Utilities"
        Adm[Import/Export] --> CSV[Upload Product CSV]
        CSV --> Parser[generate_mixed_csv.php]
        Parser --> DB_Seed[Populate Catalog]
    end
```

---

## 3. Data Flow Overview (Technical)

| Trigger | Component | Logic Handler | Storage Target |
| :--- | :--- | :--- | :--- |
| **Search** | `ProductController` | `ProductRepository::search()` | `products.json` |
| **Login** | `AuthController` | `AuthService::login()` | `users.json` |
| **Add Product** | `CartController` | `CartService::add()` | `user_carts.json` |
| **Pay/Finalize** | `CheckoutController` | `PricingService` | `user_orders.json` |
| **Dashboard** | `DashboardController` | `FormatHelper` | Aggregated JSON Data |
