<?php
$hostname = "localhost";
$database = "store3";
$username = "nevill";
$password = "7683Nev!//";

try {
    $pdo = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch summarized main entry data
    $mainEntryQuery = "SELECT main_entry_id, product_name, category, total_quantity, quantity_description
                      FROM main_entry";

    $mainEntryStmt = $pdo->prepare($mainEntryQuery);
    $mainEntryStmt->execute();
    $mainEntryData = $mainEntryStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch detailed individual entry data from inventory
    $detailedEntryQuery = "SELECT main_entry_id, quantity, quantity_description, price, record_date
                           FROM inventory";

    $detailedEntryStmt = $pdo->prepare($detailedEntryQuery);
    $detailedEntryStmt->execute();
    $detailedEntryData = $detailedEntryStmt->fetchAll(PDO::FETCH_ASSOC);

    // Encode both sets of data in JSON format
    $jsonMainEntryData = json_encode($mainEntryData);
    $jsonDetailedEntryData = json_encode($detailedEntryData);

    // Redirect to viewinventory.html with the data as URL parameters
    header('Location: viewinventory.html?main_entry_data=' . urlencode($jsonMainEntryData) . '&detailed_entry_data=' . urlencode($jsonDetailedEntryData));
    exit();

} catch (PDOException $e) {
    // Handle database errors
    echo "Error fetching inventory data: " . $e->getMessage();
}
?>

