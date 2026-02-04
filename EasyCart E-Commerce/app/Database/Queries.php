<?php

namespace EasyCart\Database;

/**
 * Centralized SQL Queries for New Schema
 * 
 * Only includes queries that are actually being used by repositories
 */
class Queries
{
    // ============================================================================
    // PRODUCT QUERIES
    // ============================================================================

    const PRODUCT_BASE_SELECT = "
        SELECT 
            p.*,
            (SELECT attribute_value FROM catalog_product_attribute 
             WHERE product_entity_id = p.entity_id AND attribute_code = 'brand'
             LIMIT 1) as brand_name
        FROM catalog_product_entity p
    ";

    const PRODUCT_GET_ALL = self::PRODUCT_BASE_SELECT . " ORDER BY p.entity_id";
    const PRODUCT_GET_ALL_PAGINATED = self::PRODUCT_BASE_SELECT . " ORDER BY p.entity_id LIMIT :limit OFFSET :offset";
    const PRODUCT_COUNT_ALL = "SELECT COUNT(*) FROM catalog_product_entity";

    const PRODUCT_FIND_BY_ID = self::PRODUCT_BASE_SELECT . " WHERE p.entity_id = :id";

    const PRODUCT_GET_FEATURED = self::PRODUCT_BASE_SELECT . " WHERE p.is_featured = true LIMIT :limit";

    const PRODUCT_GET_NEW = self::PRODUCT_BASE_SELECT . " WHERE p.is_new = true LIMIT :limit";

    const PRODUCT_FIND_BY_CATEGORY = "
        SELECT DISTINCT
            p.*,
            cp.position as category_position,
            (SELECT attribute_value FROM catalog_product_attribute 
             WHERE product_entity_id = p.entity_id AND attribute_code = 'brand') as brand_name
        FROM catalog_product_entity p
        JOIN catalog_category_product cp ON p.entity_id = cp.product_entity_id
        WHERE cp.category_entity_id = :id
        ORDER BY cp.position ASC, p.entity_id ASC
    ";

    const PRODUCT_FIND_BY_CATEGORY_PAGINATED = "
        SELECT DISTINCT
            p.*,
            cp.position as category_position,
            (SELECT attribute_value FROM catalog_product_attribute 
             WHERE product_entity_id = p.entity_id AND attribute_code = 'brand') as brand_name
        FROM catalog_product_entity p
        JOIN catalog_category_product cp ON p.entity_id = cp.product_entity_id
        WHERE cp.category_entity_id = :id
        ORDER BY cp.position ASC, p.entity_id ASC
        LIMIT :limit OFFSET :offset
    ";

    const PRODUCT_COUNT_BY_CATEGORY = "
        SELECT COUNT(DISTINCT p.entity_id)
        FROM catalog_product_entity p
        JOIN catalog_category_product cp ON p.entity_id = cp.product_entity_id
        WHERE cp.category_entity_id = :id
    ";

    const PRODUCT_FIND_BY_BRAND = "
        SELECT DISTINCT
            p.*,
            pa.attribute_value as brand_name
        FROM catalog_product_entity p
        JOIN catalog_product_attribute pa ON p.entity_id = pa.product_entity_id
        WHERE pa.attribute_code = 'brand' AND pa.attribute_value = :brand_name
    ";

    const PRODUCT_FIND_BY_BRAND_PAGINATED = "
        SELECT DISTINCT
            p.*,
            pa.attribute_value as brand_name
        FROM catalog_product_entity p
        JOIN catalog_product_attribute pa ON p.entity_id = pa.product_entity_id
        WHERE pa.attribute_code = 'brand' AND pa.attribute_value = :brand_name
        LIMIT :limit OFFSET :offset
    ";

    const PRODUCT_COUNT_BY_BRAND = "
        SELECT COUNT(DISTINCT p.entity_id)
        FROM catalog_product_entity p
        JOIN catalog_product_attribute pa ON p.entity_id = pa.product_entity_id
        WHERE pa.attribute_code = 'brand' AND pa.attribute_value = :brand_name
    ";

    const PRODUCT_SEARCH = "
        SELECT DISTINCT
            p.*,
            (SELECT attribute_value FROM catalog_product_attribute 
             WHERE product_entity_id = p.entity_id AND attribute_code = 'brand') as brand_name
        FROM catalog_product_entity p 
        WHERE p.name ILIKE :q OR p.description ILIKE :q
    ";

    const PRODUCT_SEARCH_PAGINATED = "
        SELECT DISTINCT
            p.*,
            (SELECT attribute_value FROM catalog_product_attribute 
             WHERE product_entity_id = p.entity_id AND attribute_code = 'brand') as brand_name
        FROM catalog_product_entity p 
        WHERE p.name ILIKE :q OR p.description ILIKE :q
        LIMIT :limit OFFSET :offset
    ";

    const PRODUCT_COUNT_SEARCH = "
        SELECT COUNT(DISTINCT p.entity_id)
        FROM catalog_product_entity p 
        WHERE p.name ILIKE :q OR p.description ILIKE :q
    ";

    const PRODUCT_GET_PRIMARY_IMAGE = "
        SELECT image_path FROM catalog_product_image 
        WHERE product_entity_id = :product_id AND is_primary = true
        LIMIT 1
    ";

    const PRODUCT_SIMILAR_BY_BRAND = "
        SELECT DISTINCT
            p.*,
            pa.attribute_value as brand_name
        FROM catalog_product_entity p
        JOIN catalog_product_attribute pa ON p.entity_id = pa.product_entity_id
        WHERE pa.attribute_code = 'brand' 
        AND pa.attribute_value = :brand_name
        AND p.entity_id != :pid 
        LIMIT :limit
    ";

    const PRODUCT_GET_CATEGORY_ID = "
        SELECT category_entity_id FROM catalog_category_product 
        WHERE product_entity_id = :pid LIMIT 1
    ";

    const PRODUCT_SIMILAR_BY_CATEGORY = "
        SELECT DISTINCT
            p.*,
            (SELECT attribute_value FROM catalog_product_attribute 
             WHERE product_entity_id = p.entity_id AND attribute_code = 'brand') as brand_name
        FROM catalog_product_entity p
        JOIN catalog_category_product cp ON p.entity_id = cp.product_entity_id
        WHERE cp.category_entity_id = :cid AND p.entity_id != :pid 
        LIMIT :limit
    ";

    const PRODUCT_FROM_OTHER_CATEGORIES = "
        SELECT DISTINCT
            p.*,
            (SELECT attribute_value FROM catalog_product_attribute 
             WHERE product_entity_id = p.entity_id AND attribute_code = 'brand') as brand_name
        FROM catalog_product_entity p
        LEFT JOIN catalog_category_product cp ON p.entity_id = cp.product_entity_id
        WHERE (cp.category_entity_id IS NULL OR cp.category_entity_id != :cid) 
        AND p.entity_id != :pid 
        LIMIT :limit
    ";

    // ============================================================================
    // CATEGORY QUERIES
    // ============================================================================

    const CATEGORY_GET_ALL = "
        SELECT * FROM catalog_category_entity 
        WHERE is_active = true 
        ORDER BY position ASC, entity_id ASC
    ";

    const CATEGORY_FIND_BY_ID = "
        SELECT * FROM catalog_category_entity 
        WHERE entity_id = :id
    ";

    const CATEGORY_GET_PRODUCTS = "
        SELECT DISTINCT
            p.*,
            cp.position as category_position,
            (SELECT attribute_value FROM catalog_product_attribute 
             WHERE product_entity_id = p.entity_id AND attribute_code = 'brand') as brand_name
        FROM catalog_product_entity p
        JOIN catalog_category_product cp ON p.entity_id = cp.product_entity_id
        WHERE cp.category_entity_id = :category_id
        ORDER BY cp.position ASC, p.entity_id ASC
    ";

    // ============================================================================
    // CART QUERIES
    // ============================================================================

    const CART_FIND_BY_USER = "
        SELECT * FROM sales_cart 
        WHERE user_id = :user_id AND is_active = true
    ";

    const CART_FIND_BY_SESSION = "
        SELECT * FROM sales_cart 
        WHERE session_id = :session_id AND is_active = true
    ";

    const CART_CREATE = "
        INSERT INTO sales_cart (user_id, session_id, is_active) 
        VALUES (:user_id, :session_id, TRUE) 
        RETURNING cart_id
    ";

    const CART_GET_PRODUCTS = "
        SELECT product_entity_id, quantity 
        FROM sales_cart_product 
        WHERE cart_id = :cart_id
    ";

    const CART_ADD_PRODUCT = "
        INSERT INTO sales_cart_product (cart_id, product_entity_id, quantity)
        VALUES (:cart_id, :product_id, :quantity)
        ON CONFLICT (cart_id, product_entity_id)
        DO UPDATE SET quantity = sales_cart_product.quantity + EXCLUDED.quantity,
                      updated_at = CURRENT_TIMESTAMP
    ";

    const CART_CLEAR_PRODUCTS = "
        DELETE FROM sales_cart_product 
        WHERE cart_id = :cart_id
    ";

    const CART_INACTIVATE = "
        UPDATE sales_cart 
        SET is_active = FALSE, updated_at = CURRENT_TIMESTAMP 
        WHERE cart_id = :cart_id
    ";

    const CART_UPDATE_OWNERSHIP = "
        UPDATE sales_cart 
        SET user_id = :user_id, session_id = NULL, updated_at = CURRENT_TIMESTAMP 
        WHERE cart_id = :cart_id
    ";

    // ============================================================================
    // ORDER QUERIES
    // ============================================================================

    const ORDER_CREATE = "
        INSERT INTO sales_order (
            order_number, user_id, original_cart_id, 
            subtotal, shipping_cost, tax, discount, total, status
        ) VALUES (
            :order_number, :user_id, :cart_id,
            :subtotal, :shipping_cost, :tax, :discount, :total, 'processing'
        ) RETURNING order_id
    ";

    const ORDER_ADD_PRODUCTS_FROM_CART = "
        INSERT INTO sales_order_product (
            order_id, product_entity_id, product_name, product_sku, 
            product_price, quantity, row_total
        )
        SELECT 
            :order_id,
            p.entity_id,
            p.name,
            p.sku,
            p.price,
            cp.quantity,
            (p.price * cp.quantity)
        FROM sales_cart_product cp
        JOIN catalog_product_entity p ON cp.product_entity_id = p.entity_id
        WHERE cp.cart_id = :cart_id
    ";

    const ORDER_FIND_BY_USER = "
        SELECT 
            o.*, 
            op.shipping_method, 
            op.payment_method,
            oa.first_name,
            oa.last_name,
            oa.address_line_one,
            oa.city,
            oa.state,
            oa.postal_code
        FROM sales_order o
        LEFT JOIN sales_order_payment op ON o.order_id = op.order_id
        LEFT JOIN sales_order_address oa ON o.order_id = oa.order_id AND oa.address_type = 'shipping'
        WHERE o.user_id = :user_id AND o.is_archived = :is_archived
        ORDER BY o.created_at DESC
    ";

    const ORDER_ARCHIVE_UPDATE = "
        UPDATE sales_order SET is_archived = :status WHERE order_id = :order_id
    ";

    const ORDER_GET_PRODUCTS = "
        SELECT 
            op.*,
            (SELECT image_path FROM catalog_product_image 
             WHERE product_entity_id = op.product_entity_id AND is_primary = true 
             LIMIT 1) as product_image
        FROM sales_order_product op
        WHERE op.order_id = :order_id
    ";

    const ORDER_FIND_BY_ID = "
        SELECT 
            o.*, 
            op.shipping_method, 
            op.payment_method,
            oa.first_name as ship_first, oa.last_name as ship_last,
            oa.email as ship_email, oa.phone as ship_phone,
            oa.address_line_one as ship_address, oa.city as ship_city,
            oa.state as ship_state, oa.postal_code as ship_zip, oa.country as ship_country
        FROM sales_order o
        LEFT JOIN sales_order_payment op ON o.order_id = op.order_id
        LEFT JOIN sales_order_address oa ON o.order_id = oa.order_id AND oa.address_type = 'shipping'
        WHERE o.order_id = :order_id
    ";

    const ORDER_FIND_BY_NUMBER = "
        SELECT 
            o.*, 
            op.shipping_method, 
            op.payment_method,
            oa.first_name as ship_first, oa.last_name as ship_last,
            oa.email as ship_email, oa.phone as ship_phone,
            oa.address_line_one as ship_address, oa.city as ship_city,
            oa.state as ship_state, oa.postal_code as ship_zip, oa.country as ship_country
        FROM sales_order o
        LEFT JOIN sales_order_payment op ON o.order_id = op.order_id
        LEFT JOIN sales_order_address oa ON o.order_id = oa.order_id AND oa.address_type = 'shipping'
        WHERE o.order_number = :order_number
    ";

    // ============================================================================
    // BRAND QUERIES (Brands are now attributes)
    // ============================================================================

    const BRAND_GET_ALL = "
        SELECT DISTINCT attribute_value as name
        FROM catalog_product_attribute
        WHERE attribute_code = 'brand'
        ORDER BY attribute_value ASC
    ";

    const BRAND_FIND_BY_NAME = "
        SELECT attribute_value as name
        FROM catalog_product_attribute
        WHERE attribute_code = 'brand' AND attribute_value = :name
        LIMIT 1
    ";

    // ============================================================================
    // WISHLIST QUERIES
    // ============================================================================

    const WISHLIST_GET_BY_USER = "SELECT product_entity_id FROM catalog_wishlist WHERE user_id = :user_id";

    const WISHLIST_ADD = "
        INSERT INTO catalog_wishlist (user_id, product_entity_id)
        VALUES (:user_id, :product_id)
        ON CONFLICT (user_id, product_entity_id) DO NOTHING
    ";

    const WISHLIST_REMOVE = "DELETE FROM catalog_wishlist WHERE user_id = :user_id AND product_entity_id = :product_id";

    // ============================================================================
    // DASHBOARD QUERIES
    // ============================================================================

    const DASHBOARD_STATS = "
        SELECT 
            COUNT(order_id) as total_orders,
            COALESCE(SUM(total), 0) as total_spent
        FROM sales_order
        WHERE user_id = :user_id AND status != 'cancelled'
    ";

    const DASHBOARD_CHART_DATA = "
        SELECT 
            DATE(created_at) as order_date,
            SUM(total) as daily_total
        FROM sales_order
        WHERE user_id = :user_id AND status != 'cancelled'
        GROUP BY DATE(created_at)
        ORDER BY order_date ASC
        LIMIT 30
    ";
}
