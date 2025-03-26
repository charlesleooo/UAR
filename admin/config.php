<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'uar_db');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

    // Check if the access_requests table exists, if not create it
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'access_requests'");
    if ($tableCheck->rowCount() == 0) {
        $sql = "CREATE TABLE access_requests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            requestor_name VARCHAR(255) NOT NULL,
            business_unit VARCHAR(50) NOT NULL,
            access_request_number INT NOT NULL,
            department VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            contact_number VARCHAR(50) NOT NULL,
            access_type VARCHAR(50) NOT NULL,
            justification TEXT NOT NULL,
            status VARCHAR(20) DEFAULT 'pending',
            submission_date DATETIME NOT NULL,
            system_type VARCHAR(255) NULL,
            other_system_type VARCHAR(255) NULL,
            role_access_type TEXT NULL,
            duration_type VARCHAR(20) NULL,
            start_date DATE NULL,
            end_date DATE NULL,
            reviewed_by INT NULL,
            review_date DATETIME NULL,
            review_notes TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $pdo->exec($sql);
    }

    error_log("Database connection successful");
} catch(PDOException $e) {
    // Check if database exists, if not create it
    if ($e->getCode() == 1049) {
        try {
            $tempPdo = new PDO(
                "mysql:host=" . DB_HOST,
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            
            $tempPdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            error_log("Database created successfully");
            
            // Reconnect to the newly created database
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            
            // Create the table
            $sql = "CREATE TABLE access_requests (
                id INT AUTO_INCREMENT PRIMARY KEY,
                requestor_name VARCHAR(255) NOT NULL,
                business_unit VARCHAR(50) NOT NULL,
                access_request_number INT NOT NULL,
                department VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                contact_number VARCHAR(50) NOT NULL,
                access_type VARCHAR(50) NOT NULL,
                justification TEXT NOT NULL,
                status VARCHAR(20) DEFAULT 'pending',
                submission_date DATETIME NOT NULL,
                system_type VARCHAR(255) NULL,
                other_system_type VARCHAR(255) NULL,
                role_access_type TEXT NULL,
                duration_type VARCHAR(20) NULL,
                start_date DATE NULL,
                end_date DATE NULL,
                reviewed_by INT NULL,
                review_date DATETIME NULL,
                review_notes TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $pdo->exec($sql);
            error_log("Table created successfully");
        } catch(PDOException $e2) {
            error_log("Database creation failed: " . $e2->getMessage());
            die("Database creation failed: " . $e2->getMessage());
        }
    } else {
        error_log("Database connection failed: " . $e->getMessage());
        die("Connection failed: " . $e->getMessage());
    }
}
?>
