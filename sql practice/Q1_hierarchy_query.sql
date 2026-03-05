-- Question 1: Hierarchical Organization Structure
-- Schema: employee(emp_id, emp_name, manager_id)
-- Uses a recursive CTE to walk the reporting hierarchy from CEO downward.

WITH RECURSIVE org AS (

    -- Base case: start from top-level employee(s) with no manager (CEO)
    SELECT emp_id, emp_name, manager_id, 1 AS level
    FROM employee
    WHERE manager_id IS NULL

    UNION ALL

    -- Recursive step: find employees whose manager is already discovered
    SELECT e.emp_id, e.emp_name, e.manager_id, o.level + 1
    FROM employee e
    JOIN org o ON e.manager_id = o.emp_id
)

SELECT * FROM org;
