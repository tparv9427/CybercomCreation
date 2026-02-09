-- Migration: Add url_key column for product URL slugs
-- Purpose: Store SEO-friendly URL slugs for products (e.g., "wireless-headphones" instead of ID)

-- 1. Add url_key column if it doesn't exist
DO $$ 
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'catalog_product_entity' AND column_name = 'url_key'
    ) THEN
        ALTER TABLE catalog_product_entity ADD COLUMN url_key VARCHAR(255);
    END IF;
END $$;

-- 2. Create function to generate URL slug from product name
-- Keeps title case, replaces spaces with hyphens (e.g., "Ultra Tablet Series" -> "Ultra-Tablet-Series")
CREATE OR REPLACE FUNCTION generate_url_key(product_name TEXT)
RETURNS TEXT AS $$
DECLARE
    slug TEXT;
BEGIN
    -- Replace spaces with hyphens (keep original case)
    slug := REPLACE(product_name, ' ', '-');
    -- Replace special characters (except hyphens) with hyphens
    slug := REGEXP_REPLACE(slug, '[^a-zA-Z0-9-]+', '-', 'g');
    -- Remove leading/trailing hyphens
    slug := TRIM(BOTH '-' FROM slug);
    -- Remove consecutive hyphens
    slug := REGEXP_REPLACE(slug, '-+', '-', 'g');
    RETURN slug;
END;
$$ LANGUAGE plpgsql;

-- 3. Populate url_key for existing products that don't have one
UPDATE catalog_product_entity 
SET url_key = generate_url_key(name)
WHERE url_key IS NULL OR url_key = '';

-- 4. Handle duplicate slugs by appending entity_id
WITH duplicates AS (
    SELECT entity_id, url_key, 
           ROW_NUMBER() OVER (PARTITION BY url_key ORDER BY entity_id) as rn
    FROM catalog_product_entity
)
UPDATE catalog_product_entity p
SET url_key = p.url_key || '-' || p.entity_id
FROM duplicates d
WHERE p.entity_id = d.entity_id AND d.rn > 1;

-- 5. Add unique constraint on url_key
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint WHERE conname = 'catalog_product_entity_url_key_unique'
    ) THEN
        ALTER TABLE catalog_product_entity ADD CONSTRAINT catalog_product_entity_url_key_unique UNIQUE (url_key);
    END IF;
END $$;

-- 6. Create index for fast lookups
CREATE INDEX IF NOT EXISTS idx_catalog_product_entity_url_key ON catalog_product_entity(url_key);

-- 7. Create trigger to auto-generate url_key on insert/update
CREATE OR REPLACE FUNCTION auto_generate_url_key()
RETURNS TRIGGER AS $$
DECLARE
    base_slug TEXT;
    final_slug TEXT;
    counter INTEGER := 0;
BEGIN
    -- Only generate if url_key is null or empty
    IF NEW.url_key IS NULL OR NEW.url_key = '' THEN
        base_slug := generate_url_key(NEW.name);
        final_slug := base_slug;
        
        -- Handle duplicates by appending counter
        WHILE EXISTS (SELECT 1 FROM catalog_product_entity WHERE url_key = final_slug AND entity_id != COALESCE(NEW.entity_id, 0)) LOOP
            counter := counter + 1;
            final_slug := base_slug || '-' || counter;
        END LOOP;
        
        NEW.url_key := final_slug;
    END IF;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS auto_url_key_trigger ON catalog_product_entity;
CREATE TRIGGER auto_url_key_trigger
    BEFORE INSERT OR UPDATE ON catalog_product_entity
    FOR EACH ROW
    EXECUTE FUNCTION auto_generate_url_key();

-- 8. Add comment
COMMENT ON COLUMN catalog_product_entity.url_key IS 'SEO-friendly URL slug generated from product name';
