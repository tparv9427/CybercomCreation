EasyCart E-Commerce
├── app/                      # Core application logic (PSR-4 autoloaded)
│   ├── Controllers/          # HTTP request handlers + view orchestration
│   │   ├── AuthController.php
│   │   │   └── AuthController
│   │   │       ├── showLogin()
│   │   │       ├── login()
│   │   │       ├── showSignup()
│   │   │       ├── signup()
│   │   │       ├── logout()
│   │   │       └── renderAuthView($mode, $error, $success)
│   │   │
│   │   ├── CartController.php
│   │   │   └── CartController
│   │   │       ├── index()                        # show cart page
│   │   │       ├── add()                          # AJAX
│   │   │       ├── update()                       # AJAX
│   │   │       ├── remove()                       # AJAX
│   │   │       ├── saveForLater()                 # AJAX
│   │   │       ├── moveToCart()                   # AJAX
│   │   │       ├── generateSavedItemHtml($item)
│   │   │       └── generateCartItemHtml($item)
│   │   │
│   │   ├── CheckoutController.php
│   │   │   └── CheckoutController
│   │   │       ├── index()                        # checkout page
│   │   │       ├── pricing()                      # AJAX – calculate totals
│   │   │       └── process()                      # finalize order
│   │   │
│   │   ├── HomeController.php
│   │   │   └── HomeController
│   │   │       └── index()                        # homepage + featured products
│   │   │
│   │   ├── OrderController.php
│   │   │   └── OrderController
│   │   │       ├── index()                        # order history
│   │   │       └── success()                      # thank you / confirmation
│   │   │
│   │   ├── ProductController.php
│   │   │   └── ProductController
│   │   │       ├── index()                        # listing + filters
│   │   │       ├── show($id)                      # single product page
│   │   │       ├── search()                       # search results
│   │   │       └── brand($id)                     # products by brand
│   │   │
│   │   └── WishlistController.php
│   │       └── WishlistController
│   │           ├── index()                        # wishlist page
│   │           ├── toggle()                       # AJAX add/remove
│   │           └── moveToCart()                   # AJAX
│   │
│   ├── Services/             # Business logic layer
│   │   ├── AuthService.php
│   │   │   └── AuthService
│   │   │       ├── check()
│   │   │       ├── login($email, $password)
│   │   │       ├── register($email, $password, $name)
│   │   │       ├── logout()
│   │   │       └── getCurrentUser()
│   │   │
│   │   ├── CartService.php
│   │   │   └── CartService
│   │   │       ├── add($productId, $quantity)
│   │   │       ├── update($productId, $quantity)
│   │   │       ├── remove($productId)
│   │   │       ├── empty()
│   │   │       ├── getCount()
│   │   │       ├── getTotal()
│   │   │       ├── has($productId)
│   │   │       ├── get()
│   │   │       ├── saveForLater($productId)
│   │   │       ├── moveToCartFromSaved($productId)
│   │   │       └── getSavedItems()
│   │   │
│   │   ├── PricingService.php
│   │   │   └── PricingService
│   │   │       ├── calculateSubtotal($cart)
│   │   │       ├── calculateShipping($subtotal, $method)
│   │   │       ├── calculateTax($subtotal, $shipping)
│   │   │       ├── calculatePaymentFee($method)
│   │   │       ├── calculateTotal($subtotal, $shipping, ...)
│   │   │       └── calculateAll($cart, ...)
│   │   │
│   │   ├── SessionService.php
│   │   │   └── SessionService
│   │   │       ├── init()
│   │   │       └── mergeGuestData($userId)
│   │   │
│   │   └── WishlistService.php
│   │       └── WishlistService
│   │           ├── toggle($productId)
│   │           ├── add($productId)
│   │           ├── remove($productId)
│   │           ├── has($productId)
│   │           ├── getCount()
│   │           └── get()
│   │
│   └── Repositories/         # Data access (JSON files / session)
│       ├── CartRepository.php
│       │   └── CartRepository
│       │       ├── get()
│       │       ├── save($cartData)
│       │       ├── saveToDisk($userId, $cartData)
│       │       └── loadFromDisk($userId)
│       │
│       ├── ProductRepository.php
│       │   └── ProductRepository
│       │       ├── loadProducts()
│       │       ├── dynamicDiscounts()
│       │       ├── getAll()
│       │       ├── find($id)
│       │       ├── getFeatured($limit)
│       │       ├── getNew($limit)
│       │       ├── findByCategory($categoryId)
│       │       ├── findByBrand($brandId)
│       │       ├── filterByPrice($products, $priceRange)
│       │       ├── filterByRating($products, $rating)
│       │       ├── getSimilarByBrand(...)
│       │       ├── getSimilarByCategory(...)
│       │       └── getFromOtherCategories(...)
│       │
│       ├── SaveForLaterRepository.php
│       │   └── SaveForLaterRepository
│       │       ├── get()
│       │       ├── save($data)
│       │       ├── saveToDisk($userId, $data)
│       │       └── loadFromDisk($userId)
│       │
│       ├── UserRepository.php
│       │   └── UserRepository
│       │       ├── getAll()
│       │       ├── find($userId)
│       │       ├── findByEmail($email)
│       │       ├── save($users)
│       │       ├── create($email, $password, $name)
│       │       └── getNextId($users)
│       │
│       └── WishlistRepository.php
│           └── WishlistRepository
│               ├── get()
│               ├── save($wishlistData)
│               ├── saveToDisk($userId, $wishlistData)
│               └── loadFromDisk($userId)
│
├── public/                   # Web server document root
│   └── assets/
│       └── js/
│           └── main.js
│               ├── performSearch()
│               ├── toggleView(view)
│               ├── goToSlide(index)
│               ├── nextSlide()
│               ├── updateProductCount()
│               ├── validateEmail(email)
│               ├── validatePhone(phone)
│               ├── validateZip(zip)
│               ├── showFieldError(input, message)
│               ├── clearFieldError(input)
│               ├── addToCart(productId, quantity)
│               ├── updateQuantity(productId, quantity, isInput)
│               ├── removeFromCart(productId)
│               ├── saveForLater(productId)
│               ├── moveToCartFromSaved(productId)
│               ├── updateCartCount(count)
│               ├── updateCartSummary(data)
│               ├── updateWishlistCount(count)
│               ├── updateWishlistBtn(productId, inWishlist)
│               ├── toggleWishlist(productId, btnElement)
│               └── showNotification(message, type)
└── ...                       # (other folders: views, config, etc.)