<?php
$config = require 'config/database.php';

try {
    $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";
    $pdo = new PDO($dsn, $config['user'], $config['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $report = [];

    // 1. Get all tables and their descriptions
    $tablesStmt = $pdo->query("
        SELECT 
            relname as table_name,
            obj_description(c.oid, 'pg_class') as description
        FROM pg_class c
        JOIN pg_namespace n ON n.oid = c.relnamespace
        WHERE n.nspname = 'public' AND c.relkind = 'r'
        ORDER BY table_name
    ");
    $tablesData = $tablesStmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Pre-fetch all foreign keys to build bidirectional dependency map
    $fkStmt = $pdo->query("
        SELECT
            tc.table_name, 
            kcu.column_name, 
            ccu.table_name AS foreign_table_name,
            ccu.column_name AS foreign_column_name,
            tc.constraint_name
        FROM 
            information_schema.table_constraints AS tc 
            JOIN information_schema.key_column_usage AS kcu
              ON tc.constraint_name = kcu.constraint_name
              AND tc.table_schema = kcu.table_schema
            JOIN information_schema.constraint_column_usage AS ccu
              ON ccu.constraint_name = tc.constraint_name
              AND ccu.table_schema = tc.table_schema
        WHERE tc.constraint_type = 'FOREIGN KEY' AND tc.table_schema = 'public'
    ");
    $allFks = $fkStmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Pre-fetch all primary keys
    $pkStmt = $pdo->query("
        SELECT 
            tc.table_name,
            kcu.column_name
        FROM 
            information_schema.table_constraints AS tc 
            JOIN information_schema.key_column_usage AS kcu
              ON tc.constraint_name = kcu.constraint_name
              AND tc.table_schema = kcu.table_schema
        WHERE tc.constraint_type = 'PRIMARY KEY' AND tc.table_schema = 'public'
    ");
    $allPks = [];
    while ($row = $pkStmt->fetch(PDO::FETCH_ASSOC)) {
        $allPks[$row['table_name']][] = $row['column_name'];
    }

    foreach ($tablesData as $t) {
        $tableName = $t['table_name'];
        $tableInfo = [
            'name' => $tableName,
            'description' => $t['description'] ?: 'No description available.',
            'columns' => [],
            'pk' => $allPks[$tableName] ?? [],
            'fks' => [],
            'dependencies' => [], // What this table depends on
            'dependents' => [],   // What depends on this table
            'recommended_changes' => 'Standardize ID naming, ensure updated_at triggers exist.'
        ];

        // Get Columns
        $colsStmt = $pdo->prepare("
            SELECT column_name, data_type, is_nullable, column_default
            FROM information_schema.columns
            WHERE table_name = ?
            ORDER BY ordinal_position
        ");
        $colsStmt->execute([$tableName]);
        $tableInfo['columns'] = $colsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Get FKs for this table
        foreach ($allFks as $fk) {
            if ($fk['table_name'] === $tableName) {
                $tableInfo['fks'][] = $fk;
                $tableInfo['dependencies'][] = $fk['foreign_table_name'];
            }
            if ($fk['foreign_table_name'] === $tableName) {
                $tableInfo['dependents'][] = $fk['table_name'];
            }
        }

        $tableInfo['dependencies'] = array_unique($tableInfo['dependencies']);
        $tableInfo['dependents'] = array_unique($tableInfo['dependents']);

        $report[] = $tableInfo;
    }

    echo json_encode($report, JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
