<?php
// Include the configuration file
include 'config.php';

session_start();

try {
    // Create a PDO connection using the configuration from config.php
    $pdo = new PDO("mysql:host={$databaseConfig['host']};dbname={$databaseConfig['dbname']}", $databaseConfig['user'], $databaseConfig['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch summarized main entry data
    $mainEntryQuery = "SELECT main_entry_id, product_name, category, total_quantity, quantity_description
                      FROM main_entry";

    $mainEntryStmt = $pdo->prepare($mainEntryQuery);
    $mainEntryStmt->execute();
    $_SESSION['main_entry_data'] = $mainEntryStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch detailed individual entry data from inventory
    $detailedEntryQuery = "SELECT main_entry_id, quantity, quantity_description, price, record_date
                           FROM inventory";

    $detailedEntryStmt = $pdo->prepare($detailedEntryQuery);
    $detailedEntryStmt->execute();
    $_SESSION['detailed_entry_data'] = $detailedEntryStmt->fetchAll(PDO::FETCH_ASSOC);

    // Redirect to viewinventory.html
    header('Location: viewinventory.php');
    exit();

} catch (PDOException $e) {
    // Handle database errors
    echo "Error fetching inventory data: " . $e->getMessage();
}
?>

