<?php

try {
    $dbHost = getenv('APP_DB_HOST') ?: '127.0.0.1';
    $dbName = getenv('APP_DB_NAME') ?: 'project01';
    $dbUser = getenv('APP_DB_USER') ?: 'root';
    $dbPass = getenv('APP_DB_PASS') ?: 'password';

    $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $stmt = $pdo->query("SELECT * FROM test");
    $results = $stmt->fetchAll();

    if ($results) {
        echo "<h2>Test Table Contents:</h2>";
        echo "<table border='1'>";
        echo "<tr>";
        foreach (array_keys($results[0]) as $column) {
            echo "<th>$column</th>";
        }
        echo "</tr>";
        
        foreach ($results as $row) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No records found in the test table.";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$pdo = null;
