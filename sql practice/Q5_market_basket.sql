-- Question 5: Market Basket Analysis
-- Identify product pairs purchased together in the same order.

SELECT
    oi1.product_name AS product_1,
    oi2.product_name AS product_2,
    COUNT(*) AS times_bought_together,

    -- Percentage of total orders containing this pair
    ROUND(
        COUNT(*) * 100.0 /
        (SELECT COUNT(DISTINCT order_id) FROM orders),
        2
    ) AS percent_of_orders

FROM order_items oi1
JOIN order_items oi2
    ON oi1.order_id = oi2.order_id
    AND oi1.product_name < oi2.product_name   -- prevents duplicate pairs

GROUP BY
    oi1.product_name,
    oi2.product_name

HAVING COUNT(*) > 10
ORDER BY times_bought_together DESC;
