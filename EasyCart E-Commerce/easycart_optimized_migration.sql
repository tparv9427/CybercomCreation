
BEGIN;

CREATE SCHEMA IF NOT EXISTS optimized;
SET search_path TO optimized;

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
  brand_id INTEGER,
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  FOREIGN KEY (brand_id) REFERENCES brands(id)
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
  position INTEGER DEFAULT 0,
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
  cart_id BIGINT,
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

CREATE INDEX idx_product_sku ON catalog_product_entity(sku);
CREATE INDEX idx_product_price ON catalog_product_entity(price);
CREATE INDEX idx_category_product_cat ON catalog_category_product(category_entity_id);
CREATE INDEX idx_cart_user ON sales_cart(user_id);
CREATE INDEX idx_order_user ON sales_order(user_id);
CREATE INDEX idx_review_product ON product_reviews(product_entity_id);

INSERT INTO users SELECT * FROM public.users;
INSERT INTO brands SELECT * FROM public.brands;
INSERT INTO catalog_category_entity SELECT * FROM public.catalog_category_entity;
INSERT INTO catalog_category_attribute SELECT * FROM public.catalog_category_attribute;

INSERT INTO catalog_product_entity
SELECT entity_id, sku, name, price, stock, NULL, is_active, created_at, updated_at
FROM public.catalog_product_entity;

INSERT INTO catalog_product_attribute SELECT * FROM public.catalog_product_attribute;
INSERT INTO catalog_product_image SELECT * FROM public.catalog_product_image;

INSERT INTO catalog_category_product
SELECT category_entity_id, product_entity_id, position, created_at
FROM public.catalog_category_product;

INSERT INTO sales_cart
SELECT cart_id, user_id, session_id,
CASE WHEN is_active THEN 'active' ELSE 'converted' END,
created_at, updated_at
FROM public.sales_cart;

INSERT INTO sales_cart_product
SELECT item_id, cart_id, product_entity_id, quantity, NULL, created_at
FROM public.sales_cart_product;

INSERT INTO sales_order
SELECT order_id, order_number, user_id, original_cart_id,
subtotal, tax, discount, total, status, created_at
FROM public.sales_order;

INSERT INTO sales_order_product SELECT * FROM public.sales_order_product;
INSERT INTO catalog_wishlist SELECT * FROM public.catalog_wishlist;
INSERT INTO product_reviews SELECT * FROM public.product_reviews;

COMMIT;
