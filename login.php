<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $rememberMe = isset($_POST['remember-me']);

    if (empty($username) || empty($password)) {
        echo "Username and password are required.";
        exit();
    }

    try {
        $pdo = new PDO("mysql:host={$databaseConfig['host']};dbname={$databaseConfig['dbname']}", $databaseConfig['user'], $databaseConfig['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT user_id, username, role, store_name, password_hash, remember_token FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            // Login successful

            // Generate a new token and update the database
            $token = bin2hex(random_bytes(16)); // Generate a random token
            $expiryTime = $rememberMe ? time() + (30 * 24 * 60 * 60) : 0; // Token expires in 30 days if "Remember me" is checked
            $pdo->prepare("UPDATE users SET remember_token = ?, token_expiry = ? WHERE user_id = ?")->execute([$token, $expiryTime, $user['user_id']]);

            // Set a cookie if "Remember me" is checked
            if ($rememberMe) {
                setcookie('remember_token', $token, $expiryTime, '/'); // Cookie expires in 30 days
            } else {
                // Clear any existing remember token
                setcookie('remember_token', '', time() - 3600, '/');
            }

            // Redirect to home.html with user details as URL parameters
            header("Location: home.html?user_id={$user['user_id']}&username={$user['username']}&role={$user['role']}&store_name={$user['store_name']}");
            exit();
        } else {
            echo "Invalid username or password.";
            exit();
        }
    } catch (PDOException $e) {
        // Log the error details
        error_log("Database Error: " . $e->getMessage(), 0);
        echo "An error occurred. Please try again later.";
    }
}
?>

