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

function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $fullName = $_POST['full-name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phone-number'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];
    $storeName = $_POST['store-name'];
    $role = $_POST['role'];
    $gender = $_POST['gender'];

    // Check if any required fields are empty
    if (empty($fullName) || empty($username) || empty($email) || empty($phoneNumber) || empty($password) || empty($confirmPassword) || empty($storeName) || empty($role) || empty($gender)) {
        echo "All fields are required.";
        exit();
    }

    // Check if passwords match
    if ($password !== $confirmPassword) {
        echo "Passwords do not match.";
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email address.";
        exit();
    }

    // Implement additional validation as needed

    // Hash the password
    $hashedPassword = hashPassword($password);

    // Prepare and execute the SQL query
    $stmt = $pdo->prepare("INSERT INTO users (full_name, username, email, phone_number, password_hash, role, store_name, gender) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    try {
        $stmt->execute([$fullName, $username, $email, $phoneNumber, $hashedPassword, $role, $storeName, $gender]);
        echo "Registration successful!";
    } catch (PDOException $e) {
        // Log the error details (in a production environment, do not expose details to the user)
        error_log("Error: " . $e->getMessage(), 0);
        echo "An error occurred. Please try again later.";
    }
}
?>

