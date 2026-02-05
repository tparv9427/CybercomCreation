-- Restructured & Optimized EasyCart Schema Migration (REFINED)
-- Author: Senior Database Engineer
-- Purpose: Restructure DB for optimization, normalization, and scalability.

BEGIN;

-- 1. CLEANUP & SCHEMA SETUP
CREATE SCHEMA IF NOT EXISTS optimized;
SET search_path TO optimized;

-- 2. CREATE OPTIMIZED TABLES

-- Users Table
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Brands Table
CREATE TABLE brands (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL,
    slug VARCHAR(255) UNIQUE,
    is_active BOOLEAN DEFAULT TRUE
);

-- Categories Table
CREATE TABLE categories (
    id SERIAL PRIMARY KEY,
    parent_id INTEGER REFERENCES categories(id) ON DELETE SET NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    position INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products Table (Flattened common attributes)
CREATE TABLE products (
    id SERIAL PRIMARY KEY,
    category_id INTEGER REFERENCES categories(id) ON DELETE SET NULL,
    brand_id INTEGER REFERENCES brands(id) ON DELETE SET NULL,
    sku VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    price NUMERIC(12, 2) NOT NULL DEFAULT 0.00,
    original_price NUMERIC(12, 2),
    stock INTEGER NOT NULL DEFAULT 0,
    description TEXT,
    rating NUMERIC(3, 2) DEFAULT 0.00,
    reviews_count INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,
    is_new BOOLEAN DEFAULT FALSE,
    metadata JSONB, -- For future flexibility
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Product Images
CREATE TABLE product_images (
    id SERIAL PRIMARY KEY,
    product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    image_path VARCHAR(255) NOT NULL,
    position INTEGER DEFAULT 0,
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Carts
CREATE TABLE carts (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    session_id VARCHAR(255),
    status VARCHAR(20) DEFAULT 'active' CHECK (status IN ('active', 'converted', 'abandoned')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cart Items
CREATE TABLE cart_items (
    id SERIAL PRIMARY KEY,
    cart_id INTEGER NOT NULL REFERENCES carts(id) ON DELETE CASCADE,
    product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    quantity INTEGER NOT NULL DEFAULT 1 CHECK (quantity > 0),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(cart_id, product_id)
);

-- Orders
CREATE TABLE orders (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    status VARCHAR(50) NOT NULL,
    subtotal NUMERIC(12, 2) NOT NULL,
    tax NUMERIC(12, 2) DEFAULT 0.00,
    shipping_cost NUMERIC(12, 2) DEFAULT 0.00,
    discount NUMERIC(12, 2) DEFAULT 0.00,
    total NUMERIC(12, 2) NOT NULL,
    shipping_method VARCHAR(100),
    payment_method VARCHAR(100),
    is_archived BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Order Items
CREATE TABLE order_items (
    id SERIAL PRIMARY KEY,
    order_id INTEGER NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    product_id INTEGER REFERENCES products(id) ON DELETE SET NULL,
    product_name VARCHAR(255) NOT NULL,
    product_sku VARCHAR(100) NOT NULL,
    price NUMERIC(12, 2) NOT NULL,
    quantity INTEGER NOT NULL CHECK (quantity > 0),
    total_price NUMERIC(12, 2) NOT NULL
);

-- Wishlists
CREATE TABLE wishlists (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(user_id, product_id)
);

-- Product Reviews
CREATE TABLE reviews (
    id SERIAL PRIMARY KEY,
    product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    rating INTEGER NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    is_approved BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. DATA MIGRATION FROM PUBLIC SCHEMA

-- Users
INSERT INTO users (id, name, email, password, phone, created_at, updated_at)
SELECT id, name, email, password, phone, created_at, updated_at FROM public.users;
SELECT setval('users_id_seq', (SELECT MAX(id) FROM users));

-- Brands
INSERT INTO brands (id, name, slug)
SELECT id, name, LOWER(REPLACE(name, ' ', '-')) FROM public.brands;
SELECT setval('brands_id_seq', (SELECT MAX(id) FROM brands));

-- Categories
INSERT INTO categories (id, parent_id, name, slug, position, is_active, created_at, updated_at)
SELECT entity_id, parent_id, name, slug, position, is_active, created_at, updated_at FROM public.catalog_category_entity;
SELECT setval('categories_id_seq', (SELECT MAX(id) FROM categories));

-- Products
INSERT INTO products (
    id, category_id, brand_id, sku, name, slug, price, original_price, 
    stock, description, is_active, is_featured, is_new, rating, reviews_count, created_at, updated_at
)
SELECT 
    p.entity_id, 
    (SELECT category_entity_id FROM public.catalog_category_product WHERE product_entity_id = p.entity_id LIMIT 1),
    b.id,
    p.sku, p.name, LOWER(REPLACE(p.name, ' ', '-')) || '-' || p.entity_id, 
    p.price, p.original_price, p.stock, p.description, 
    p.is_active, p.is_featured, p.is_new, p.rating, p.reviews_count, p.created_at, p.updated_at
FROM public.catalog_product_entity p
LEFT JOIN public.catalog_product_attribute pa ON p.entity_id = pa.product_entity_id AND pa.attribute_code = 'brand'
LEFT JOIN public.brands b ON pa.attribute_value = b.name;
SELECT setval('products_id_seq', (SELECT MAX(id) FROM products));

-- Product Images (REFIXED: image_position instead of position)
INSERT INTO product_images (id, product_id, image_path, position, is_primary, created_at)
SELECT image_id, product_entity_id, image_path, image_position, is_primary, created_at FROM public.catalog_product_image;
SELECT setval('product_images_id_seq', (SELECT MAX(id) FROM product_images));

-- Carts
INSERT INTO carts (id, user_id, session_id, status, created_at, updated_at)
SELECT cart_id, user_id, session_id, 
    CASE WHEN is_active THEN 'active' ELSE 'converted' END, 
    created_at, updated_at FROM public.sales_cart;
SELECT setval('carts_id_seq', (SELECT MAX(id) FROM carts));

-- Cart Items
INSERT INTO cart_items (id, cart_id, product_id, quantity, created_at, updated_at)
SELECT item_id, cart_id, product_entity_id, quantity, created_at, updated_at FROM public.sales_cart_product;
SELECT setval('cart_items_id_seq', (SELECT MAX(id) FROM cart_items));

-- Orders (REFIXED: shipping_cost instead of 0.00)
INSERT INTO orders (
    id, user_id, order_number, status, subtotal, tax, shipping_cost, discount, total, 
    shipping_method, payment_method, is_archived, created_at, updated_at
)
SELECT 
    o.order_id, o.user_id, o.order_number, o.status, o.subtotal, o.tax, o.shipping_cost, o.discount, o.total,
    op.shipping_method, op.payment_method, o.is_archived, o.created_at, o.updated_at
FROM public.sales_order o
LEFT JOIN public.sales_order_payment op ON o.order_id = op.order_id;
SELECT setval('orders_id_seq', (SELECT MAX(id) FROM orders));

-- Order Items
INSERT INTO order_items (id, order_id, product_id, product_name, product_sku, price, quantity, total_price)
SELECT item_id, order_id, product_entity_id, product_name, product_sku, product_price, quantity, row_total FROM public.sales_order_product;
SELECT setval('order_items_id_seq', (SELECT MAX(id) FROM order_items));

-- Wishlists
INSERT INTO wishlists (id, user_id, product_id, created_at)
SELECT wishlist_id, user_id, product_entity_id, created_at FROM public.catalog_wishlist;
SELECT setval('wishlists_id_seq', (SELECT MAX(id) FROM wishlists));

-- Reviews
INSERT INTO reviews (id, product_id, user_id, rating, comment, is_approved, created_at)
SELECT review_id, product_entity_id, user_id, rating, comment, is_approved, created_at FROM public.product_reviews;
SELECT setval('reviews_id_seq', (SELECT MAX(id) FROM reviews));

COMMIT;
