# EasyCart E-Commerce Website

A modern, feature-rich e-commerce platform built with PHP, following Design Option 3 (Premium Soft).

## 🎉 What's New

- **500 Products**: 100 products in each category (Electronics, Fashion, Home & Living, Sports, Books)
- **5 User Accounts**: Pre-configured with different users
- **Working Signup**: New users are saved to `data/users.json`
- **Persistent Storage**: User data persists across sessions

## Features Implemented

### Phase 1 & 2 - Complete ✅
- **Dynamic Product System**: 500 products loaded from PHP data
- **Category Filtering**: Browse products by category with working filters
- **Price Range Filters**: Filter by price (Under $50, $50-$100, etc.)
- **Rating Filters**: Filter by product ratings
- **Grid/Row View Toggle**: Switch between grid and list view
- **Dark Mode**: Full dark theme support with localStorage persistence

### Shopping Features
- **Add to Cart**: AJAX-powered cart system
- **Wishlist**: Save favorite products with heart animation
- **Session Management**: Temporary cart before login, persistent after
- **Product Details**: Dynamic product pages with 3 rows of recommendations:
  1. Same category, different brands
  2. Same category products
  3. Other category suggestions

### User Authentication
- **Login/Signup**: User authentication system with file-based storage
- **5 Pre-configured Users**: Ready to test
- **New User Registration**: Signup actually saves new users
- **Session Persistence**: Cart and wishlist saved per user
- **Guest Shopping**: Browse and add to cart before login
- **Protected Checkout**: Must login to complete purchase

### Checkout System
- **Shipping Options**: Standard and Express shipping
- **Payment Methods**:
  - Credit/Debit Card
  - UPI
  - Net Banking
  - Wallet
  - Cash on Delivery
- **Order Processing**: Complete checkout flow
- **Order Success Page**: Confirmation with order ID

### Additional Pages
- Shopping Cart with quantity controls
- Wishlist page
- My Orders page
- Order success confirmation

## 📊 Product Statistics

- **Total Products**: 500
- **Electronics**: 100 products
- **Fashion**: 100 products  
- **Home & Living**: 100 products
- **Sports**: 100 products
- **Books**: 100 products

Each product has:
- Unique name and description
- Pricing with discounts (0-40% off)
- Ratings (3.0 - 5.0 stars)
- Review counts
- Stock levels
- Multiple variants (colors, sizes)
- Brand associations

## 👥 User Accounts

All passwords are: **password123**

1. **demo@easycart.com** - Demo User
2. **john.doe@example.com** - John Doe
3. **jane.smith@example.com** - Jane Smith
4. **mike.wilson@example.com** - Mike Wilson
5. **sarah.jones@example.com** - Sarah Jones

**New users** can sign up and will be automatically saved to `data/users.json`!

## Key Features

### Design (Option 3 - Premium Soft)
- Cormorant Garamond + DM Sans fonts
- Soft color palette with warm accents
- Smooth gradients and elegant animations
- Fully responsive design
- Dark mode toggle

### Technical Highlights
- **No Database Required**: Uses PHP arrays (easily migrable to database)
- **Session-based Cart**: Works without login, persists after login
- **AJAX Operations**: Smooth cart and wishlist updates
- **Filter System**: Multiple filters work together
- **Scalable Structure**: Ready for database integration

### Product Recommendations
Each product page shows 3 different recommendation rows:
1. **Alternative Brands**: Same type of product from different brands
2. **Category Products**: More from the same category
3. **Cross-Category**: Products from other categories

### Shopping Flow
1. Browse products (guest or logged in)
2. Add to cart/wishlist
3. View cart and adjust quantities
4. Proceed to checkout (login required)
5. Enter shipping details
6. Select payment method
7. Place order
8. View order confirmation

## Browser Support
- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers

## Future Database Migration
The code is structured to easily migrate to MySQL/PostgreSQL:
- All data in `includes/config.php`
- Functions ready to be replaced with DB queries
- Session management already in place

## Notes
- All product data is in `includes/config.php`
- Styles use CSS variables for easy theming
- Dark mode preference saved in localStorage
- View preference (grid/row) saved in localStorage
- Guest cart/wishlist transfers to user account on login

---
Built with ❤️ following modern PHP best practices
