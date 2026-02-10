-- Rename user_id to customer_id if it exists
DO $$ 
BEGIN 
  IF EXISTS(SELECT * FROM information_schema.columns WHERE table_name='sales_cart' AND column_name='user_id') THEN
    ALTER TABLE sales_cart RENAME COLUMN user_id TO customer_id;
  END IF;
END $$;

-- Add items_count if not exists
DO $$ 
BEGIN 
  IF NOT EXISTS(SELECT * FROM information_schema.columns WHERE table_name='sales_cart' AND column_name='items_count') THEN
    ALTER TABLE sales_cart ADD COLUMN items_count INTEGER DEFAULT 0;
  END IF;
END $$;

-- Add grand_total if not exists
DO $$ 
BEGIN 
  IF NOT EXISTS(SELECT * FROM information_schema.columns WHERE table_name='sales_cart' AND column_name='grand_total') THEN
    ALTER TABLE sales_cart ADD COLUMN grand_total DECIMAL(12,4) DEFAULT 0.0000;
  END IF;
END $$;
