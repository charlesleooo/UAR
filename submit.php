<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'data' => []
];

// Database connection
$host = 'localhost';
$dbname = 'uar_db';
$username = 'root'; // Change if different
$password = ''; // Change if different

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Set response header
    header('Content-Type: application/json');

    // Generate access request number (REQ2025-XXX format)
    $year = date('Y');
    
    // Check both tables to find the highest request number
    $sql = "SELECT MAX(request_num) as max_num FROM (
        SELECT CAST(SUBSTRING_INDEX(access_request_number, '-', -1) AS UNSIGNED) as request_num 
        FROM access_requests 
        WHERE access_request_number LIKE :year_prefix
        UNION
        SELECT CAST(SUBSTRING_INDEX(access_request_number, '-', -1) AS UNSIGNED) as request_num 
        FROM approval_history 
        WHERE access_request_number LIKE :year_prefix
    ) combined";
    
    $stmt = $pdo->prepare($sql);
    $year_prefix = "REQ$year-%";
    $stmt->execute(['year_prefix' => $year_prefix]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $next_num = ($result['max_num'] ?? 0) + 1;
    $access_request_number = sprintf("REQ%d-%03d", $year, $next_num);

    // Verify the generated number doesn't exist in either table
    $check_sql = "SELECT 1 FROM (
        SELECT access_request_number FROM access_requests
        UNION
        SELECT access_request_number FROM approval_history
    ) combined WHERE access_request_number = :request_number";
    
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute(['request_number' => $access_request_number]);
    
    if ($check_stmt->rowCount() > 0) {
        throw new Exception('Generated request number already exists');
    }

    // Prepare the SQL statement
    $sql = "INSERT INTO access_requests (
        requestor_name,
        business_unit,
        access_request_number,
        department,
        email,
        contact_number,
        access_type,
        system_type,
        other_system_type,
        role_access_type,
        duration_type,
        start_date,
        end_date,
        justification,
        submission_date,
        status
    ) VALUES (
        :requestor_name,
        :business_unit,
        :access_request_number,
        :department,
        :email,
        :contact_number,
        :access_type,
        :system_type,
        :other_system_type,
        :role_access_type,
        :duration_type,
        :start_date,
        :end_date,
        :justification,
        NOW(),
        'pending'
    )";

    $stmt = $pdo->prepare($sql);

    // Handle system type array if present
    $system_type = null;
    if (isset($_POST['system_type']) && is_array($_POST['system_type'])) {
        $system_type = implode(', ', $_POST['system_type']);
    }

    // Execute with parameters
    $success = $stmt->execute([
        'requestor_name' => $_POST['requestor_name'],
        'business_unit' => $_POST['business_unit'],
        'access_request_number' => $access_request_number,
        'department' => $_POST['department'],
        'email' => $_POST['email'],
        'contact_number' => $_POST['contact_number'],
        'access_type' => $_POST['access_type'],
        'system_type' => $system_type,
        'other_system_type' => $_POST['other_system_type'] ?? null,
        'role_access_type' => $_POST['role_access_type'] ?? null,
        'duration_type' => $_POST['duration_type'],
        'start_date' => $_POST['duration_type'] === 'temporary' ? $_POST['start_date'] : null,
        'end_date' => $_POST['duration_type'] === 'temporary' ? $_POST['end_date'] : null,
        'justification' => $_POST['justification']
    ]);

    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => "Access request submitted successfully! Your request number is $access_request_number"
        ]);
    } else {
        throw new Exception('Failed to insert record');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}