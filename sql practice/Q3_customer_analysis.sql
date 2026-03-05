-- Question 3: Customer Analysis with Advanced Filtering
-- Find customers whose spending in the last 30 days exceeds the average customer spending.

WITH customer_spending AS (
    SELECT
        c.customer_id,
        c.customer_name,
        COUNT(o.order_id) AS purchase_count,
        SUM(o.total_amount) AS total_spending
    FROM customers c
    JOIN orders o
        ON c.customer_id = o.customer_id
    WHERE o.order_date >= CURDATE() - INTERVAL 30 DAY
    GROUP BY c.customer_id, c.customer_name
)

SELECT
    customer_name,
    purchase_count,
    total_spending,
    total_spending - avg_spending AS above_average
FROM (
    SELECT
        cs.*,
        AVG(total_spending) OVER() AS avg_spending
    FROM customer_spending cs
) t
WHERE total_spending > avg_spending;
