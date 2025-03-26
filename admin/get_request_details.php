<?php
session_start();
require_once 'config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Check if request ID is provided
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Request ID is required']);
    exit();
}

try {
    // Verify database connection
    if (!isset($pdo)) {
        throw new Exception('Database connection not established');
    }

    // Fetch request details with modified query to match actual table structure
    $sql = "SELECT * FROM access_requests WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    
    // Add error handling for prepare
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . print_r($pdo->errorInfo(), true));
    }
    
    $stmt->execute(['id' => $_GET['id']]);
    
    // Add error handling for execute
    if ($stmt->errorCode() !== '00000') {
        throw new Exception('Failed to execute statement: ' . print_r($stmt->errorInfo(), true));
    }
    
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$request) {
        http_response_code(404);
        echo json_encode([
            'error' => 'Request not found',
            'id' => $_GET['id']
        ]);
        exit();
    }

    // Set proper content type header
    header('Content-Type: application/json');
    header('Cache-Control: no-cache, must-revalidate');
    
    // Return request details as JSON
    echo json_encode($request);

} catch (PDOException $e) {
    error_log("Database error in get_request_details.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error occurred',
        'message' => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("General error in get_request_details.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'An error occurred while fetching request details',
        'message' => $e->getMessage()
    ]);
} 