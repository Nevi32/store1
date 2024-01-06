<?php
include 'config.php';

try {
    $pdo = new PDO("mysql:host={$databaseConfig['host']};dbname={$databaseConfig['dbname']}", $databaseConfig['user'], $databaseConfig['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $salesQuery = "SELECT s.sale_id, s.main_entry_id, s.quantity_sold, s.total_price, s.sale_date,
                          m.product_name, m.total_quantity, m.quantity_description
                   FROM sales s
                   LEFT JOIN main_entry m ON s.main_entry_id = m.main_entry_id";

    $salesStmt = $pdo->prepare($salesQuery);
    $salesStmt->execute();
    $salesData = $salesStmt->fetchAll(PDO::FETCH_ASSOC);

    // Encode sales data in JSON format
    $jsonSalesData = json_encode($salesData);

    // Redirect to viewsales.html with sales data as URL parameters
    header('Location: viewsales.html?sales_data=' . urlencode($jsonSalesData));
    exit();
} catch (PDOException $e) {
    echo "Error fetching sales data: " . $e->getMessage();
}
?>

