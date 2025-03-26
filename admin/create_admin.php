<?php
require_once 'config.php';

$username = 'admin';
$password = 'admin123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    // First, clear existing admin user if any
    $stmt = $pdo->prepare("DELETE FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    
    // Insert new admin user
    $stmt = $pdo->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
    $stmt->execute([$username, $hashed_password]);
    
    echo "Admin user created successfully!\n";
    echo "Username: " . $username . "\n";
    echo "Password: " . $password . "\n";
    
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
