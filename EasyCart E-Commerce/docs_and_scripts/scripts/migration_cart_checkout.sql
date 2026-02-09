-- ============================================================================
-- EasyCart Cart & Checkout System Migration
-- Migration: Add production-grade cart state columns and order snapshot fields
-- Date: 2026-02-06
-- ============================================================================

-- ============================================================================
-- PHASE 2.1: UPDATE sales_cart TABLE
-- Add state management columns for checkout locking, soft delete, and temp carts
-- ============================================================================

-- Add is_temp column: TRUE when user_id IS NULL (guest cart)
ALTER TABLE sales_cart ADD COLUMN IF NOT EXISTS is_temp BOOLEAN DEFAULT FALSE;

-- Add is_checkout column: TRUE when checkout is in progress
ALTER TABLE sales_cart ADD COLUMN IF NOT EXISTS is_checkout BOOLEAN DEFAULT FALSE;

-- Add archived column: TRUE when cart has been converted to order
ALTER TABLE sales_cart ADD COLUMN IF NOT EXISTS archived BOOLEAN DEFAULT FALSE;

-- Add deleted_at column: Soft delete timestamp (NULL = not deleted)
ALTER TABLE sales_cart ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL;

-- Add checkout_locked_at column: When checkout lock began (for 10-min timeout)
ALTER TABLE sales_cart ADD COLUMN IF NOT EXISTS checkout_locked_at TIMESTAMP NULL;

-- ============================================================================
-- PHASE 2.2: CREATE INDEXES FOR NEW COLUMNS
-- Optimize queries for cart state filtering
-- ============================================================================

CREATE INDEX IF NOT EXISTS idx_cart_temp ON sales_cart(is_temp);
CREATE INDEX IF NOT EXISTS idx_cart_checkout ON sales_cart(is_checkout);
CREATE INDEX IF NOT EXISTS idx_cart_archived ON sales_cart(archived);
CREATE INDEX IF NOT EXISTS idx_cart_deleted ON sales_cart(deleted_at);
CREATE INDEX IF NOT EXISTS idx_cart_checkout_locked ON sales_cart(checkout_locked_at);

-- Composite index for common query pattern: active carts for user
CREATE INDEX IF NOT EXISTS idx_cart_user_active ON sales_cart(user_id, is_active, deleted_at);

-- ============================================================================
-- PHASE 2.3: UPDATE sales_order_product TABLE
-- Add tax and discount columns for checkout-time snapshots
-- ============================================================================

-- Add tax column: Tax amount calculated at checkout
ALTER TABLE sales_order_product ADD COLUMN IF NOT EXISTS tax DECIMAL(10, 2) DEFAULT 0;

-- Add discount column: Discount amount calculated at checkout
ALTER TABLE sales_order_product ADD COLUMN IF NOT EXISTS discount DECIMAL(10, 2) DEFAULT 0;

-- ============================================================================
-- PHASE 2.4: DATA MIGRATION
-- Set is_temp flag based on existing data
-- ============================================================================

-- Set is_temp = TRUE for all guest carts (where user_id IS NULL)
UPDATE sales_cart 
SET is_temp = TRUE 
WHERE user_id IS NULL AND is_temp = FALSE;

-- Set is_temp = FALSE for all logged-in user carts
UPDATE sales_cart 
SET is_temp = FALSE 
WHERE user_id IS NOT NULL AND is_temp = TRUE;

-- ============================================================================
-- PHASE 2.5: ENFORCE SINGLE CART PER USER
-- Merge duplicate carts and soft-delete extras
-- ============================================================================

-- First, identify users with multiple active carts
-- This query is for verification only (run SELECT first to see affected users)
-- SELECT user_id, COUNT(*) as cart_count 
-- FROM sales_cart 
-- WHERE user_id IS NOT NULL 
--   AND is_active = TRUE 
--   AND deleted_at IS NULL 
-- GROUP BY user_id 
-- HAVING COUNT(*) > 1;

-- Merge duplicate carts into the most recently updated one
-- Step 1: For each user with multiple carts, merge cart products into latest cart
WITH latest_carts AS (
    SELECT DISTINCT ON (user_id) cart_id, user_id
    FROM sales_cart
    WHERE user_id IS NOT NULL 
      AND is_active = TRUE 
      AND deleted_at IS NULL
      AND archived = FALSE
    ORDER BY user_id, updated_at DESC
),
duplicate_carts AS (
    SELECT c.cart_id, c.user_id, lc.cart_id as target_cart_id
    FROM sales_cart c
    JOIN latest_carts lc ON c.user_id = lc.user_id
    WHERE c.cart_id != lc.cart_id
      AND c.is_active = TRUE
      AND c.deleted_at IS NULL
      AND c.archived = FALSE
)
-- Merge products from duplicate carts into target cart
INSERT INTO sales_cart_product (cart_id, product_entity_id, quantity)
SELECT 
    dc.target_cart_id,
    cp.product_entity_id,
    cp.quantity
FROM duplicate_carts dc
JOIN sales_cart_product cp ON dc.cart_id = cp.cart_id
ON CONFLICT (cart_id, product_entity_id) 
DO UPDATE SET 
    quantity = sales_cart_product.quantity + EXCLUDED.quantity,  -- Limit enforced in PHP layer
    updated_at = CURRENT_TIMESTAMP;

-- Step 2: Capping now handled in PHP layer
-- UPDATE sales_cart_product
-- SET quantity = 7, updated_at = CURRENT_TIMESTAMP
-- WHERE quantity > 7;

-- Step 3: Soft-delete duplicate carts (keep only latest per user)
WITH latest_carts AS (
    SELECT DISTINCT ON (user_id) cart_id, user_id
    FROM sales_cart
    WHERE user_id IS NOT NULL 
      AND is_active = TRUE 
      AND deleted_at IS NULL
      AND archived = FALSE
    ORDER BY user_id, updated_at DESC
)
UPDATE sales_cart 
SET 
    deleted_at = CURRENT_TIMESTAMP,
    is_active = FALSE,
    updated_at = CURRENT_TIMESTAMP
WHERE user_id IS NOT NULL
  AND deleted_at IS NULL
  AND archived = FALSE
  AND cart_id NOT IN (SELECT cart_id FROM latest_carts);

-- ============================================================================
-- PHASE 2.6: ADD CONSTRAINTS
-- Enforce business rules at database level
-- ============================================================================

-- Add check constraint for max quantity per product
-- REMOVED: Managed in PHP layer (MAX_QUANTITY_PER_ITEM)
/*
DO $$ 
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint WHERE conname = 'chk_cart_product_quantity_max'
    ) THEN
        ALTER TABLE sales_cart_product 
        ADD CONSTRAINT chk_cart_product_quantity_max 
        CHECK (quantity >= 1 AND quantity <= 7);
    END IF;
END $$;
*/

-- ============================================================================
-- PHASE 2.7: ADD COMMENTS
-- Document new columns
-- ============================================================================

COMMENT ON COLUMN sales_cart.is_temp IS 'TRUE for guest carts (user_id IS NULL)';
COMMENT ON COLUMN sales_cart.is_checkout IS 'TRUE when checkout is in progress - cart is locked';
COMMENT ON COLUMN sales_cart.archived IS 'TRUE when cart has been converted to an order';
COMMENT ON COLUMN sales_cart.deleted_at IS 'Soft delete timestamp - NULL means not deleted';
COMMENT ON COLUMN sales_cart.checkout_locked_at IS 'When checkout lock began - auto-unlock after 10 minutes';

COMMENT ON COLUMN sales_order_product.tax IS 'Tax amount calculated at checkout time';
COMMENT ON COLUMN sales_order_product.discount IS 'Discount amount calculated at checkout time';

-- ============================================================================
-- VERIFICATION QUERIES (Run after migration to verify success)
-- ============================================================================

-- Check new columns exist on sales_cart
-- SELECT column_name, data_type, column_default 
-- FROM information_schema.columns 
-- WHERE table_name = 'sales_cart' 
-- ORDER BY ordinal_position;

-- Check new columns exist on sales_order_product
-- SELECT column_name, data_type, column_default 
-- FROM information_schema.columns 
-- WHERE table_name = 'sales_order_product' 
-- ORDER BY ordinal_position;

-- Verify no users have multiple active carts
-- SELECT user_id, COUNT(*) 
-- FROM sales_cart 
-- WHERE user_id IS NOT NULL 
--   AND is_active = TRUE 
--   AND deleted_at IS NULL 
-- GROUP BY user_id 
-- HAVING COUNT(*) > 1;

-- ============================================================================
-- MIGRATION COMPLETE
-- ============================================================================
