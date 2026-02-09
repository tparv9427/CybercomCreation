-- Migration: Cart ID Deletion Safety
-- Purpose: Ensure deleted cart_ids are never reused

-- 1. Create a table to track deleted cart IDs
CREATE TABLE IF NOT EXISTS sales_cart_deleted (
    cart_id BIGINT PRIMARY KEY,
    deleted_at TIMESTAMP DEFAULT NOW(),
    deleted_by_user_id INTEGER REFERENCES users(id)
);

-- 2. Create a trigger function to prevent cart_id reuse
CREATE OR REPLACE FUNCTION prevent_cart_id_reuse()
RETURNS TRIGGER AS $$
BEGIN
    IF EXISTS (SELECT 1 FROM sales_cart_deleted WHERE cart_id = NEW.cart_id) THEN
        RAISE EXCEPTION 'Cart ID % has been deleted and cannot be reused', NEW.cart_id;
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- 3. Create trigger on sales_cart table
DROP TRIGGER IF EXISTS check_cart_id_reuse ON sales_cart;
CREATE TRIGGER check_cart_id_reuse
    BEFORE INSERT ON sales_cart
    FOR EACH ROW
    EXECUTE FUNCTION prevent_cart_id_reuse();

-- 4. Create a trigger to log deleted cart IDs
CREATE OR REPLACE FUNCTION log_deleted_cart()
RETURNS TRIGGER AS $$
BEGIN
    INSERT INTO sales_cart_deleted (cart_id, deleted_by_user_id)
    VALUES (OLD.cart_id, OLD.user_id);
    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

-- 5. Create trigger to capture deletions
DROP TRIGGER IF EXISTS log_cart_deletion ON sales_cart;
CREATE TRIGGER log_cart_deletion
    BEFORE DELETE ON sales_cart
    FOR EACH ROW
    EXECUTE FUNCTION log_deleted_cart();

-- 6. Add comment to document the safety mechanism
COMMENT ON TABLE sales_cart_deleted IS 'Tracks deleted cart IDs to prevent reuse - MVC compliance';
