-- EasyCart E-Commerce - New Database Schema
-- Naming Convention: lowercase, underscores only, NO numbers
-- Numbers must be spelled out (e.g., line_one, line_two)

-- ============================================================================
-- CATALOG TABLES (Product & Category Management with EAV Pattern)
-- ============================================================================

-- Core product information (Entity)
CREATE TABLE IF NOT EXISTS catalog_product_entity (
    entity_id SERIAL PRIMARY KEY,
    sku VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    original_price DECIMAL(10, 2),
    stock INT DEFAULT 0,
    description TEXT,
    is_featured BOOLEAN DEFAULT FALSE,
    is_new BOOLEAN DEFAULT FALSE,
    rating DECIMAL(3, 2) DEFAULT 0,
    reviews_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_product_sku ON catalog_product_entity(sku);
CREATE INDEX IF NOT EXISTS idx_product_featured ON catalog_product_entity(is_featured);
CREATE INDEX IF NOT EXISTS idx_product_new ON catalog_product_entity(is_new);
CREATE INDEX IF NOT EXISTS idx_product_price ON catalog_product_entity(price);

-- Product attributes (color, size, brand, material, etc.)
CREATE TABLE IF NOT EXISTS catalog_product_attribute (
    attribute_id SERIAL PRIMARY KEY,
    product_entity_id INT NOT NULL REFERENCES catalog_product_entity(entity_id) ON DELETE CASCADE,
    attribute_code VARCHAR(100) NOT NULL,
    attribute_value TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_product_attr_entity ON catalog_product_attribute(product_entity_id);
CREATE INDEX IF NOT EXISTS idx_product_attr_code ON catalog_product_attribute(attribute_code);
CREATE INDEX IF NOT EXISTS idx_product_attr_value ON catalog_product_attribute(attribute_value);

-- Product images (avoiding comma-separated values)
CREATE TABLE IF NOT EXISTS catalog_product_image (
    image_id SERIAL PRIMARY KEY,
    product_entity_id INT NOT NULL REFERENCES catalog_product_entity(entity_id) ON DELETE CASCADE,
    image_path VARCHAR(255) NOT NULL,
    image_position INT DEFAULT 0,
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_product_image_entity ON catalog_product_image(product_entity_id);
CREATE INDEX IF NOT EXISTS idx_product_image_primary ON catalog_product_image(is_primary);

-- Core category information
CREATE TABLE IF NOT EXISTS catalog_category_entity (
    entity_id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    parent_id INT REFERENCES catalog_category_entity(entity_id) ON DELETE SET NULL,
    position INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_category_slug ON catalog_category_entity(slug);
CREATE INDEX IF NOT EXISTS idx_category_parent ON catalog_category_entity(parent_id);
CREATE INDEX IF NOT EXISTS idx_category_active ON catalog_category_entity(is_active);

-- Category attributes
CREATE TABLE IF NOT EXISTS catalog_category_attribute (
    attribute_id SERIAL PRIMARY KEY,
    category_entity_id INT NOT NULL REFERENCES catalog_category_entity(entity_id) ON DELETE CASCADE,
    attribute_code VARCHAR(100) NOT NULL,
    attribute_value TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_category_attr_entity ON catalog_category_attribute(category_entity_id);
CREATE INDEX IF NOT EXISTS idx_category_attr_code ON catalog_category_attribute(attribute_code);

-- Many-to-many: Categories and Products
CREATE TABLE IF NOT EXISTS catalog_category_product (
    relation_id SERIAL PRIMARY KEY,
    category_entity_id INT NOT NULL REFERENCES catalog_category_entity(entity_id) ON DELETE CASCADE,
    product_entity_id INT NOT NULL REFERENCES catalog_product_entity(entity_id) ON DELETE CASCADE,
    position INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(category_entity_id, product_entity_id)
);

CREATE INDEX IF NOT EXISTS idx_cat_prod_category ON catalog_category_product(category_entity_id);
CREATE INDEX IF NOT EXISTS idx_cat_prod_product ON catalog_category_product(product_entity_id);

-- ============================================================================
-- CART TABLES (Shopping Cart Management)
-- ============================================================================

-- Cart header (stores cart_id in session)
CREATE TABLE IF NOT EXISTS sales_cart (
    cart_id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id) ON DELETE CASCADE,
    session_id VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_cart_user ON sales_cart(user_id);
CREATE INDEX IF NOT EXISTS idx_cart_session ON sales_cart(session_id);
CREATE INDEX IF NOT EXISTS idx_cart_active ON sales_cart(is_active);

-- Cart line items
CREATE TABLE IF NOT EXISTS sales_cart_product (
    item_id SERIAL PRIMARY KEY,
    cart_id INT NOT NULL REFERENCES sales_cart(cart_id) ON DELETE CASCADE,
    product_entity_id INT NOT NULL REFERENCES catalog_product_entity(entity_id) ON DELETE CASCADE,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(cart_id, product_entity_id)
);

CREATE INDEX IF NOT EXISTS idx_cart_product_cart ON sales_cart_product(cart_id);
CREATE INDEX IF NOT EXISTS idx_cart_product_entity ON sales_cart_product(product_entity_id);

-- Cart addresses (shipping and billing)
CREATE TABLE IF NOT EXISTS sales_cart_address (
    address_id SERIAL PRIMARY KEY,
    cart_id INT NOT NULL REFERENCES sales_cart(cart_id) ON DELETE CASCADE,
    address_type VARCHAR(20) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address_line_one VARCHAR(255) NOT NULL,
    address_line_two VARCHAR(255),
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    country VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_cart_address_cart ON sales_cart_address(cart_id);
CREATE INDEX IF NOT EXISTS idx_cart_address_type ON sales_cart_address(address_type);

-- Cart payment and shipping method
CREATE TABLE IF NOT EXISTS sales_cart_payment (
    payment_id SERIAL PRIMARY KEY,
    cart_id INT NOT NULL UNIQUE REFERENCES sales_cart(cart_id) ON DELETE CASCADE,
    shipping_method VARCHAR(50),
    payment_method VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_cart_payment_cart ON sales_cart_payment(cart_id);

-- ============================================================================
-- ORDER TABLES (Order Management - Mirror Cart Structure)
-- ============================================================================

-- Order header
CREATE TABLE IF NOT EXISTS sales_order (
    order_id SERIAL PRIMARY KEY,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    user_id INT REFERENCES users(id) ON DELETE SET NULL,
    original_cart_id INT REFERENCES sales_cart(cart_id),
    subtotal DECIMAL(10, 2) NOT NULL,
    shipping_cost DECIMAL(10, 2) DEFAULT 0,
    tax DECIMAL(10, 2) DEFAULT 0,
    discount DECIMAL(10, 2) DEFAULT 0,
    total DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'processing',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_order_number ON sales_order(order_number);
CREATE INDEX IF NOT EXISTS idx_order_user ON sales_order(user_id);
CREATE INDEX IF NOT EXISTS idx_order_status ON sales_order(status);
CREATE INDEX IF NOT EXISTS idx_order_created ON sales_order(created_at);

-- Order line items (snapshot of products at purchase time)
CREATE TABLE IF NOT EXISTS sales_order_product (
    item_id SERIAL PRIMARY KEY,
    order_id INT NOT NULL REFERENCES sales_order(order_id) ON DELETE CASCADE,
    product_entity_id INT REFERENCES catalog_product_entity(entity_id) ON DELETE SET NULL,
    product_name VARCHAR(255) NOT NULL,
    product_sku VARCHAR(100) NOT NULL,
    product_price DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL,
    row_total DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_order_product_order ON sales_order_product(order_id);
CREATE INDEX IF NOT EXISTS idx_order_product_entity ON sales_order_product(product_entity_id);

-- Order addresses (snapshot)
CREATE TABLE IF NOT EXISTS sales_order_address (
    address_id SERIAL PRIMARY KEY,
    order_id INT NOT NULL REFERENCES sales_order(order_id) ON DELETE CASCADE,
    address_type VARCHAR(20) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address_line_one VARCHAR(255) NOT NULL,
    address_line_two VARCHAR(255),
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    country VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_order_address_order ON sales_order_address(order_id);
CREATE INDEX IF NOT EXISTS idx_order_address_type ON sales_order_address(address_type);

-- Order payment and shipping method (snapshot)
CREATE TABLE IF NOT EXISTS sales_order_payment (
    payment_id SERIAL PRIMARY KEY,
    order_id INT NOT NULL UNIQUE REFERENCES sales_order(order_id) ON DELETE CASCADE,
    shipping_method VARCHAR(50) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_order_payment_order ON sales_order_payment(order_id);

-- ============================================================================
-- COMMENTS
-- ============================================================================

COMMENT ON TABLE catalog_product_entity IS 'Core product information (EAV entity)';
COMMENT ON TABLE catalog_product_attribute IS 'Flexible product attributes (color, size, brand, etc.)';
COMMENT ON TABLE catalog_product_image IS 'Product images - avoids comma-separated values';
COMMENT ON TABLE catalog_category_entity IS 'Core category information';
COMMENT ON TABLE catalog_category_attribute IS 'Flexible category attributes';
COMMENT ON TABLE catalog_category_product IS 'Many-to-many relationship between categories and products';

COMMENT ON TABLE sales_cart IS 'Shopping cart header - cart_id stored in session';
COMMENT ON TABLE sales_cart_product IS 'Cart line items';
COMMENT ON TABLE sales_cart_address IS 'Cart shipping and billing addresses';
COMMENT ON TABLE sales_cart_payment IS 'Cart payment and shipping method selection';

COMMENT ON TABLE sales_order IS 'Order header - created from inactive cart';
COMMENT ON TABLE sales_order_product IS 'Order line items with product snapshots';
COMMENT ON TABLE sales_order_address IS 'Order addresses (snapshot from cart)';
COMMENT ON TABLE sales_order_payment IS 'Order payment info (snapshot from cart)';

COMMENT ON COLUMN sales_cart.is_active IS 'Set to FALSE when order is placed - cart is not deleted';
COMMENT ON COLUMN sales_order.original_cart_id IS 'Reference to the cart that created this order';
