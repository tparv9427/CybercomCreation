-- Reverted Complex Schema Migration
-- Purpose: Restore the complex naming convention (catalog_..., sales_...) and optimize.

BEGIN;

-- 1. SETUP SCHEMA
CREATE SCHEMA IF NOT EXISTS optimized;
SET search_path TO optimized;

-- 2. CORE ENTITIES (Normalized/EAV Pattern as requested)

CREATE TABLE users (
  id BIGINT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(20),
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

CREATE TABLE brands (
  id INTEGER PRIMARY KEY,
  name VARCHAR(255) UNIQUE NOT NULL
);

CREATE TABLE catalog_category_entity (
  entity_id BIGINT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  slug VARCHAR(255) UNIQUE NOT NULL,
  parent_id BIGINT,
  position INTEGER DEFAULT 0,
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  FOREIGN KEY (parent_id) REFERENCES catalog_category_entity(entity_id)
);

CREATE TABLE catalog_category_attribute (
  attribute_id BIGINT PRIMARY KEY,
  category_entity_id BIGINT NOT NULL,
  attribute_code VARCHAR(100) NOT NULL,
  attribute_value TEXT NOT NULL,
  created_at TIMESTAMP,
  UNIQUE(category_entity_id, attribute_code),
  FOREIGN KEY (category_entity_id) REFERENCES catalog_category_entity(entity_id)
);

CREATE TABLE catalog_product_entity (
  entity_id BIGINT PRIMARY KEY,
  sku VARCHAR(100) UNIQUE NOT NULL,
  name VARCHAR(255) NOT NULL,
  price NUMERIC(10,2) NOT NULL,
  stock INTEGER DEFAULT 0,
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

CREATE TABLE catalog_product_attribute (
  attribute_id BIGINT PRIMARY KEY,
  product_entity_id BIGINT NOT NULL,
  attribute_code VARCHAR(100) NOT NULL,
  attribute_value TEXT NOT NULL,
  created_at TIMESTAMP,
  UNIQUE(product_entity_id, attribute_code),
  FOREIGN KEY (product_entity_id) REFERENCES catalog_product_entity(entity_id)
);

CREATE TABLE catalog_product_image (
  image_id BIGINT PRIMARY KEY,
  product_entity_id BIGINT NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  image_position INTEGER DEFAULT 0,
  is_primary BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP,
  FOREIGN KEY (product_entity_id) REFERENCES catalog_product_entity(entity_id)
);

CREATE TABLE catalog_category_product (
  category_entity_id BIGINT NOT NULL,
  product_entity_id BIGINT NOT NULL,
  position INTEGER DEFAULT 0,
  created_at TIMESTAMP,
  PRIMARY KEY (category_entity_id, product_entity_id),
  FOREIGN KEY (category_entity_id) REFERENCES catalog_category_entity(entity_id),
  FOREIGN KEY (product_entity_id) REFERENCES catalog_product_entity(entity_id)
);

-- SALES TABLES (Keeping the verbose labels as requested)

CREATE TABLE sales_cart (
  cart_id BIGINT PRIMARY KEY,
  user_id BIGINT,
  session_id VARCHAR(255),
  status VARCHAR(20) DEFAULT 'active',
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE sales_cart_product (
  item_id BIGINT PRIMARY KEY,
  cart_id BIGINT NOT NULL,
  product_entity_id BIGINT NOT NULL,
  quantity INTEGER NOT NULL,
  price_snapshot NUMERIC(10,2),
  created_at TIMESTAMP,
  FOREIGN KEY (cart_id) REFERENCES sales_cart(cart_id),
  FOREIGN KEY (product_entity_id) REFERENCES catalog_product_entity(entity_id)
);

CREATE TABLE sales_order (
  order_id BIGINT PRIMARY KEY,
  order_number VARCHAR(50) UNIQUE NOT NULL,
  user_id BIGINT,
  original_cart_id BIGINT,
  subtotal NUMERIC(10,2),
  tax NUMERIC(10,2),
  discount NUMERIC(10,2),
  total NUMERIC(10,2),
  status VARCHAR(50),
  created_at TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE sales_order_product (
  item_id BIGINT PRIMARY KEY,
  order_id BIGINT NOT NULL,
  product_entity_id BIGINT,
  product_name VARCHAR(255),
  product_sku VARCHAR(100),
  product_price NUMERIC(10,2),
  quantity INTEGER,
  row_total NUMERIC(10,2),
  FOREIGN KEY (order_id) REFERENCES sales_order(order_id)
);

-- Additional verbose tables requested by user
CREATE TABLE sales_order_payment (
    payment_id BIGINT PRIMARY KEY,
    order_id BIGINT NOT NULL UNIQUE,
    shipping_method VARCHAR(50),
    payment_method VARCHAR(50),
    created_at TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES sales_order(order_id)
);

CREATE TABLE sales_order_address (
    address_id BIGINT PRIMARY KEY,
    order_id BIGINT NOT NULL,
    address_type VARCHAR(20),
    first_name VARCHAR(255),
    last_name VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(255),
    address_line_one TEXT,
    city VARCHAR(255),
    state VARCHAR(255),
    postal_code VARCHAR(255),
    country VARCHAR(255),
    created_at TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES sales_order(order_id)
);

-- OTHER TABLES
CREATE TABLE catalog_wishlist (
  wishlist_id BIGINT PRIMARY KEY,
  user_id BIGINT NOT NULL,
  product_entity_id BIGINT NOT NULL,
  created_at TIMESTAMP,
  UNIQUE(user_id, product_entity_id),
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (product_entity_id) REFERENCES catalog_product_entity(entity_id)
);

CREATE TABLE product_reviews (
  review_id INTEGER PRIMARY KEY,
  product_entity_id BIGINT NOT NULL,
  user_id BIGINT NOT NULL,
  rating INTEGER CHECK (rating BETWEEN 1 AND 5),
  comment TEXT,
  is_approved BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP,
  FOREIGN KEY (product_entity_id) REFERENCES catalog_product_entity(entity_id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 3. MIGRATION FROM PUBLIC (Fixed Column Selection)

INSERT INTO users (id, name, email, password, phone, created_at, updated_at)
SELECT id, name, email, password, phone, created_at, updated_at FROM public.users;

INSERT INTO brands (id, name)
SELECT id, name FROM public.brands;

INSERT INTO catalog_category_entity (entity_id, name, slug, parent_id, position, is_active, created_at, updated_at)
SELECT entity_id, name, slug, parent_id, position, is_active, created_at, updated_at FROM public.catalog_category_entity;

INSERT INTO catalog_category_attribute (attribute_id, category_entity_id, attribute_code, attribute_value, created_at)
SELECT attribute_id, category_entity_id, attribute_code, attribute_value, created_at FROM public.catalog_category_attribute;

INSERT INTO catalog_product_entity (entity_id, sku, name, price, stock, is_active, created_at, updated_at)
SELECT entity_id, sku, name, price, stock, is_active, created_at, updated_at FROM public.catalog_product_entity;

INSERT INTO catalog_product_attribute (attribute_id, product_entity_id, attribute_code, attribute_value, created_at)
SELECT attribute_id, product_entity_id, attribute_code, attribute_value, created_at FROM public.catalog_product_attribute;

INSERT INTO catalog_product_image (image_id, product_entity_id, image_path, image_position, is_primary, created_at)
SELECT image_id, product_entity_id, image_path, image_position, is_primary, created_at FROM public.catalog_product_image;

INSERT INTO catalog_category_product (category_entity_id, product_entity_id, position, created_at)
SELECT category_entity_id, product_entity_id, position, created_at FROM public.catalog_category_product;

INSERT INTO sales_cart (cart_id, user_id, session_id, status, created_at, updated_at)
SELECT cart_id, user_id, session_id, (CASE WHEN is_active THEN 'active' ELSE 'converted' END), created_at, updated_at FROM public.sales_cart;

INSERT INTO sales_cart_product (item_id, cart_id, product_entity_id, quantity, created_at)
SELECT item_id, cart_id, product_entity_id, quantity, created_at FROM public.sales_cart_product;

INSERT INTO sales_order (order_id, order_number, user_id, original_cart_id, subtotal, tax, discount, total, status, created_at)
SELECT order_id, order_number, user_id, original_cart_id, subtotal, tax, discount, total, status, created_at FROM public.sales_order;

INSERT INTO sales_order_product (item_id, order_id, product_entity_id, product_name, product_sku, product_price, quantity, row_total)
SELECT item_id, order_id, product_entity_id, product_name, product_sku, product_price, quantity, row_total FROM public.sales_order_product;

INSERT INTO sales_order_payment (payment_id, order_id, shipping_method, payment_method, created_at)
SELECT payment_id, order_id, shipping_method, payment_method, created_at FROM public.sales_order_payment;

INSERT INTO sales_order_address (address_id, order_id, address_type, first_name, last_name, email, phone, address_line_one, city, state, postal_code, country, created_at)
SELECT address_id, order_id, address_type, first_name, last_name, email, phone, address_line_one, city, state, postal_code, country, created_at FROM public.sales_order_address;

INSERT INTO catalog_wishlist (wishlist_id, user_id, product_entity_id, created_at)
SELECT wishlist_id, user_id, product_entity_id, created_at FROM public.catalog_wishlist;

INSERT INTO product_reviews (review_id, product_entity_id, user_id, rating, comment, is_approved, created_at)
SELECT review_id, product_entity_id, user_id, rating, comment, is_approved, created_at FROM public.product_reviews;

COMMIT;
