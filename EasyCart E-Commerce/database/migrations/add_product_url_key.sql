-- ============================================================================
-- Migration: Add url_key column to catalog_product_entity
-- Purpose: Enable slug-based product URLs (e.g. /product/iPhone-16-Pro-Max)
-- Run: psql -d easycart -f add_product_url_key.sql
-- ============================================================================

-- 1. Add the url_key column (nullable initially for existing rows)
ALTER TABLE catalog_product_entity
ADD COLUMN IF NOT EXISTS url_key VARCHAR(255);

-- 2. Populate url_key for all existing products from their name
-- Converts: "iPhone 16 Pro Max" → "iPhone-16-Pro-Max"
UPDATE catalog_product_entity
SET url_key = REPLACE(
    REGEXP_REPLACE(
        REGEXP_REPLACE(name, '[^a-zA-Z0-9 -]+', '', 'g'),
        '\s+', '-', 'g'
    ),
    '--', '-'
)
WHERE url_key IS NULL;

-- 3. Handle duplicates by appending entity_id
WITH duplicates AS (
    SELECT entity_id, url_key,
           ROW_NUMBER() OVER (PARTITION BY url_key ORDER BY entity_id) as rn
    FROM catalog_product_entity
)
UPDATE catalog_product_entity p
SET url_key = p.url_key || '-' || p.entity_id
FROM duplicates d
WHERE p.entity_id = d.entity_id AND d.rn > 1;

-- 4. Add unique index for fast lookup + enforce uniqueness
CREATE UNIQUE INDEX IF NOT EXISTS idx_product_url_key
ON catalog_product_entity(url_key);

-- 5. Make url_key NOT NULL after population
ALTER TABLE catalog_product_entity
ALTER COLUMN url_key SET NOT NULL;

-- 6. Add a trigger to auto-generate url_key on INSERT if not provided
CREATE OR REPLACE FUNCTION generate_product_url_key()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.url_key IS NULL OR NEW.url_key = '' THEN
        NEW.url_key := REPLACE(
            REGEXP_REPLACE(
                REGEXP_REPLACE(NEW.name, '[^a-zA-Z0-9 -]+', '', 'g'),
                '\s+', '-', 'g'
            ),
            '--', '-'
        );
        -- Ensure uniqueness by appending entity_id if conflict
        IF EXISTS (SELECT 1 FROM catalog_product_entity WHERE url_key = NEW.url_key AND entity_id != COALESCE(NEW.entity_id, 0)) THEN
            NEW.url_key := NEW.url_key || '-' || NEW.entity_id;
        END IF;
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_product_url_key ON catalog_product_entity;
CREATE TRIGGER trg_product_url_key
    BEFORE INSERT ON catalog_product_entity
    FOR EACH ROW
    EXECUTE FUNCTION generate_product_url_key();
