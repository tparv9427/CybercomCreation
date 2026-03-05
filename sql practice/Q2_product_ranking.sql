-- Question 2: Product Ranking by Category
-- Rank products by revenue within each category.
-- DENSE_RANK ensures ties receive the same rank with no gaps.

SELECT *
FROM (
    SELECT 
        product_id,
        category_id,
        product_name,
        revenue,
        DENSE_RANK() OVER (
            PARTITION BY category_id
            ORDER BY revenue DESC
        ) AS rank_in_category
    FROM products
) ranked_products
WHERE rank_in_category <= 3;
