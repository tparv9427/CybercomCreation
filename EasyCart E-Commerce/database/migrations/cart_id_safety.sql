-- ============================================================================
-- Migration: Cart ID Safety — Deleted cart_ids must never be reused
-- Purpose: Prevent cart_id reuse after deletion using a tracking table + trigger
-- Run: psql -d easycart -f cart_id_safety.sql
-- ============================================================================

-- 1. Track deleted cart IDs to prevent reuse
CREATE TABLE IF NOT EXISTS sales_cart_deleted (
    cart_id INT PRIMARY KEY,
    deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

COMMENT ON TABLE sales_cart_deleted IS 'Tracks deleted cart IDs to prevent reuse. Business rule: once a cart is deleted, its ID must never be assigned again.';

-- 2. Before DELETE on sales_cart — record the ID
CREATE OR REPLACE FUNCTION track_deleted_cart_id()
RETURNS TRIGGER AS $$
BEGIN
    INSERT INTO sales_cart_deleted (cart_id)
    VALUES (OLD.cart_id)
    ON CONFLICT (cart_id) DO NOTHING;
    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_track_deleted_cart ON sales_cart;
CREATE TRIGGER trg_track_deleted_cart
    BEFORE DELETE ON sales_cart
    FOR EACH ROW
    EXECUTE FUNCTION track_deleted_cart_id();

-- 3. Before INSERT on sales_cart — block reuse of deleted IDs
CREATE OR REPLACE FUNCTION prevent_cart_id_reuse()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.cart_id IS NOT NULL AND EXISTS (
        SELECT 1 FROM sales_cart_deleted WHERE cart_id = NEW.cart_id
    ) THEN
        RAISE EXCEPTION 'Cart ID % has been deleted and cannot be reused', NEW.cart_id;
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_prevent_cart_reuse ON sales_cart;
CREATE TRIGGER trg_prevent_cart_reuse
    BEFORE INSERT ON sales_cart
    FOR EACH ROW
    EXECUTE FUNCTION prevent_cart_id_reuse();
