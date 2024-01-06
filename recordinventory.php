<?php
// Include the configuration file
require_once 'config.php';

try {
    // Use the database configuration from the included file
    $pdo = new PDO("mysql:host={$databaseConfig['host']};dbname={$databaseConfig['dbname']}", $databaseConfig['user'], $databaseConfig['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If there is an error connecting to the database, terminate the script and display an error message
    die("Connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $productName = $_POST['productName'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];
    $quantityDescription = $_POST['quantityDescription'];
    $price = $_POST['price'];

    // Check if any required fields are empty
    if (empty($productName) || empty($quantity) || empty($quantityDescription)) {
        echo "Product Name, Quantity, and Quantity Description are required fields.";
        exit();
    }

    try {
        // Check if the product already exists in the main_entry table
        $existingRecord = $pdo->prepare("SELECT * FROM main_entry WHERE product_name = ? AND category = ?");
        $existingRecord->execute([$productName, $category]);
        $existingData = $existingRecord->fetch(PDO::FETCH_ASSOC);

        if ($existingData !== false) {
            // Product already exists as a main entry, insert a new individual entry
            $mainEntryId = $existingData['main_entry_id'];

            // Insert the individual entry
            $insertStmt = $pdo->prepare("INSERT INTO inventory (main_entry_id, quantity, quantity_description, price, record_date) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)");
            $insertStmt->execute([$mainEntryId, $quantity, $quantityDescription, $price]);

            // Update the total inventory in the main entry
            $updateTotalStmt = $pdo->prepare("UPDATE main_entry SET total_quantity = total_quantity + ? WHERE main_entry_id = ?");
            $updateTotalStmt->execute([$quantity, $mainEntryId]);

            echo "Individual entry added successfully!";
        } else {
            // Product does not exist as a main entry, insert a new main entry
            $insertMainStmt = $pdo->prepare("INSERT INTO main_entry (product_name, category, total_quantity, quantity_description) VALUES (?, ?, ?, ?)");
            $insertMainStmt->execute([$productName, $category, $quantity, $quantityDescription]);

            // Retrieve the main entry_id for the newly inserted main entry
            $mainEntryId = $pdo->lastInsertId();

            // Insert the individual entry
            $insertStmt = $pdo->prepare("INSERT INTO inventory (main_entry_id, quantity, quantity_description, price, record_date) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)");
            $insertStmt->execute([$mainEntryId, $quantity, $quantityDescription, $price]);

            echo "Main entry and individual entry added successfully!";
        }
    } catch (PDOException $e) {
        // Handle any PDO exceptions and display an error message
        echo "Error: " . $e->getMessage();
    }
}

// Fetch and display inventory data
try {
    $inventoryData = $pdo->query("SELECT * FROM inventory")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle any PDO exceptions when fetching data and display an error message
    die("Error fetching inventory data: " . $e->getMessage());
}
?>

