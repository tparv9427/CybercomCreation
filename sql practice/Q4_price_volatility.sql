-- Question 4: Price Volatility Tracking
-- Analyze product price changes over time.
-- LAG retrieves the previous price and LEAD retrieves the next price.

SELECT *
FROM (
    SELECT
        p.product_name,
        pp.price_date,
        pp.price AS current_price,

        LAG(pp.price) OVER (
            PARTITION BY pp.product_id
            ORDER BY pp.price_date
        ) AS previous_price,

        LEAD(pp.price) OVER (
            PARTITION BY pp.product_id
            ORDER BY pp.price_date
        ) AS next_price,

        -- Percentage change from previous price
        ROUND(
            (pp.price - LAG(pp.price) OVER (
                PARTITION BY pp.product_id
                ORDER BY pp.price_date
            )) /
            LAG(pp.price) OVER (
                PARTITION BY pp.product_id
                ORDER BY pp.price_date
            ) * 100,
            2
        ) AS percent_change

    FROM product_prices pp
    JOIN products p
        ON pp.product_id = p.product_id
    WHERE pp.price_date >= CURDATE() - INTERVAL 90 DAY
) t
WHERE previous_price IS NOT NULL;
