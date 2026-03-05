-- Question 6: Multi-Dimensional Sales Reporting
-- Sales breakdown by category and quarter with conditional aggregation.

SELECT
    c.category_name,
    CONCAT('Q', QUARTER(o.order_date)) AS quarter,

    SUM(CASE WHEN o.status='completed' THEN oi.quantity*oi.price ELSE 0 END) AS completed_amount,
    COUNT(DISTINCT CASE WHEN o.status='completed' THEN o.order_id END) AS completed_orders,

    SUM(CASE WHEN o.status='pending' THEN oi.quantity*oi.price ELSE 0 END) AS pending_amount,
    COUNT(DISTINCT CASE WHEN o.status='pending' THEN o.order_id END) AS pending_orders,

    SUM(CASE WHEN o.status='cancelled' THEN oi.quantity*oi.price ELSE 0 END) AS cancelled_amount,
    COUNT(DISTINCT CASE WHEN o.status='cancelled' THEN o.order_id END) AS cancelled_orders

FROM orders o
JOIN order_items oi ON o.order_id = oi.order_id
JOIN products p ON oi.product_id = p.product_id
JOIN categories c ON p.category_id = c.category_id

GROUP BY
    c.category_name,
    CONCAT('Q', QUARTER(o.order_date))

ORDER BY
    c.category_name,
    quarter;
