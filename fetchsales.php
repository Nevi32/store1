<?php
$hostname = "localhost";
$database = "store3";
$username = "nevill";
$password = "7683Nev!//";

try {
    $pdo = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch sales data
    $salesQuery = "SELECT sale_id, main_entry_id, quantity_sold, total_price, sale_date
                   FROM sales";

    $salesStmt = $pdo->prepare($salesQuery);
    $salesStmt->execute();
    $salesData = $salesStmt->fetchAll(PDO::FETCH_ASSOC);

    // Encode sales data in JSON format
    $jsonSalesData = json_encode($salesData);

    // Redirect to viewsales.html with sales data as URL parameters
    header('Location: viewsales.html?sales_data=' . urlencode($jsonSalesData));
    exit();

} catch (PDOException $e) {
    // Handle database errors
    echo "Error fetching sales data: " . $e->getMessage();
}
?>

