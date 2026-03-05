-- Question 8: Time Series Aggregation
-- Monthly revenue trends with running totals and year-to-date totals.

SELECT
    month,
    monthly_revenue,

    -- Revenue from previous month
    LAG(monthly_revenue) OVER (
        ORDER BY month
    ) AS previous_month_revenue,

    -- Running total across all months
    SUM(monthly_revenue) OVER (
        ORDER BY month
        ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW
    ) AS running_total,

    -- Year-to-date revenue (reset each year)
    SUM(monthly_revenue) OVER (
        PARTITION BY YEAR(STR_TO_DATE(CONCAT(month,'-01'),'%Y-%m-%d'))
        ORDER BY month
    ) AS ytd_total

FROM (
    -- First aggregate transactions by month
    SELECT
        DATE_FORMAT(transaction_date,'%Y-%m') AS month,
        SUM(amount) AS monthly_revenue
    FROM transactions
    WHERE transaction_date >= DATE_SUB(CURDATE(), INTERVAL 24 MONTH)
    GROUP BY DATE_FORMAT(transaction_date,'%Y-%m')
) t

ORDER BY month;
