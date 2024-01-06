<?php
require_once('config.php');

// Establish a database connection
try {
    $dsn = 'mysql:host=' . $databaseConfig['host'] . ';dbname=' . $databaseConfig['dbname'];
    $pdo = new PDO($dsn, $databaseConfig['user'], $databaseConfig['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Query to retrieve the latest notification for each type
$query = "SELECT MAX(notification_id) AS max_id, subject, MAX(message) AS message FROM notifications GROUP BY subject";
$stmt = $pdo->prepare($query);
$stmt->execute();
$latestNotifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Encode the notifications into a JavaScript variable
$notificationsScript = 'var latestNotifications = ' . json_encode($latestNotifications) . ';';

// Close the database connection
$pdo = null;

// Output the JavaScript script
echo $notificationsScript;
?>

