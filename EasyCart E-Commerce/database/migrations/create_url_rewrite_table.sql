-- Create URL Rewrite table for flexible, SEO-friendly routing
CREATE TABLE IF NOT EXISTS url_rewrite (
    url_rewrite_id SERIAL PRIMARY KEY,
    request_path VARCHAR(255) NOT NULL UNIQUE,     -- The visible URL fragment (e.g., 'p/nexusgear-item')
    target_path VARCHAR(255) NOT NULL,      -- The internal path (e.g., 'product/view/123')
    entity_id BIGINT,                           -- Optional reference to the content ID
    entity_type VARCHAR(50),                    -- Optional reference to content type ('product', 'category')
    redirect_type SMALLINT DEFAULT 0,           -- 0 = Rewrite, 301 = Permanent Redirect, 302 = Temporary
    metadata JSONB,                             -- Store flexible metadata
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index for fast lookups
CREATE INDEX IF NOT EXISTS idx_url_rewrite_request_path ON url_rewrite(request_path);
CREATE INDEX IF NOT EXISTS idx_url_rewrite_entity ON url_rewrite(entity_type, entity_id);

-- Add comments for clarity
COMMENT ON COLUMN url_rewrite.request_path IS 'The SEO-friendly URL relative to root';
COMMENT ON COLUMN url_rewrite.target_path IS 'Internal system path or canonical slug';
COMMENT ON COLUMN url_rewrite.redirect_type IS 'HTTP status code for redirects, 0 if internal rewrite';
