-- Question 7: Completeness Check with Correlated Subqueries
-- Find customers who purchased at least one product in every category.

SELECT c.customer_id, c.customer_name
FROM customers c
WHERE NOT EXISTS (

    -- For each category, check if the customer has NOT purchased from it
    SELECT *
    FROM categories cat
    WHERE NOT EXISTS (

        SELECT *
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN products p ON oi.product_id = p.product_id
        WHERE o.customer_id = c.customer_id
        AND p.category_id = cat.category_id

    )

);
