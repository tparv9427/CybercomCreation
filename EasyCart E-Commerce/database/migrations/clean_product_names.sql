-- ============================================================================
-- Migration: Remove numeric IDs from product names and store in separate column
-- Purpose: Data cleanup and normalization
-- ============================================================================

-- 1. Add the product_index column
ALTER TABLE catalog_product_entity 
ADD COLUMN IF NOT EXISTS product_index INT;

-- 2. Extract numeric suffix from name into product_index 
-- and clean up the name column
UPDATE catalog_product_entity 
SET 
    product_index = (substring(name from '([0-9]+)$'))::INT,
    name = trim(regexp_replace(name, '\s+[0-9]+$', ''))
WHERE name ~ '\s+[0-9]+$';

-- 3. (Skipped) Update url_key to reflect the cleaner name
-- We skipping this for now because items with the same name would collide
-- and violate the UNIQUE INDEX idx_product_url_key.
-- If needed, we can handle it later with a dedicated duplicate resolution logic.

-- 4. Create an index on product_index for faster lookups
CREATE INDEX IF NOT EXISTS idx_product_index ON catalog_product_entity(product_index);
