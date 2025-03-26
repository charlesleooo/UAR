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

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic required fields for all access types
    $required_fields = [
        'requestor_name',
        'business_unit',
        'access_request_number',
        'department',
        'email',
        'contact_number',
        'justification',
        'access_type'
    ];

    $errors = [];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
        }
    }

    // Validate email format
    if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }

    // Additional validation for System Application type
    if ($_POST['access_type'] === 'system_application') {
        // Validate system type selection
        if (empty($_POST['system_type'])) {
            $errors[] = 'At least one System/Application Type must be selected';
        }

        // Validate duration type
        if (empty($_POST['duration_type'])) {
            $errors[] = 'Access Duration is required for System Application';
        } else {
            if ($_POST['duration_type'] === 'temporary') {
                if (empty($_POST['start_date']) || empty($_POST['end_date'])) {
                    $errors[] = 'Start date and end date are required for temporary access';
                } else {
                    // Validate date format (mm/dd/yyyy)
                    $date_pattern = '/^(0[1-9]|1[0-2])\/(0[1-9]|[12][0-9]|3[01])\/\d{4}$/';
                    if (!preg_match($date_pattern, $_POST['start_date'])) {
                        $errors[] = 'Start date must be in mm/dd/yyyy format';
                    }
                    if (!preg_match($date_pattern, $_POST['end_date'])) {
                        $errors[] = 'End date must be in mm/dd/yyyy format';
                    }

                    // Validate date range
                    if (empty($errors)) {
                        $start_date = DateTime::createFromFormat('m/d/Y', $_POST['start_date']);
                        $end_date = DateTime::createFromFormat('m/d/Y', $_POST['end_date']);
                        
                        if (!$start_date || !$end_date) {
                            $errors[] = 'Invalid date format';
                        } elseif ($end_date <= $start_date) {
                            $errors[] = 'End date must be after start date';
                        }
                    }
                }
            }
        }
    }

    // If no errors, process the form
    if (empty($errors)) {
        // Include database configuration
        require_once 'admin/config.php';

        try {
            // Prepare data for storage
            $form_data = [
                'requestor_name' => $_POST['requestor_name'],
                'business_unit' => $_POST['business_unit'],
                'access_request_number' => $_POST['access_request_number'],
                'department' => $_POST['department'],
                'email' => $_POST['email'],
                'contact_number' => $_POST['contact_number'],
                'access_type' => $_POST['access_type'],
                'justification' => $_POST['justification'],
                'status' => 'pending',
                'submission_date' => date('Y-m-d H:i:s'),
                'system_type' => null,
                'other_system_type' => null,
                'role_access_type' => null,
                'duration_type' => null,
                'start_date' => null,
                'end_date' => null
            ];

            // Add System Application specific data if applicable
            if ($_POST['access_type'] === 'system_application') {
                $form_data['system_type'] = isset($_POST['system_type']) ? implode(',', $_POST['system_type']) : null;
                $form_data['other_system_type'] = isset($_POST['other_system_type']) ? $_POST['other_system_type'] : null;
                $form_data['role_access_type'] = isset($_POST['role_access_type']) ? $_POST['role_access_type'] : null;
                $form_data['duration_type'] = isset($_POST['duration_type']) ? $_POST['duration_type'] : null;
                
                if (isset($_POST['duration_type']) && $_POST['duration_type'] === 'temporary') {
                    $form_data['start_date'] = isset($_POST['start_date']) ? date('Y-m-d', strtotime($_POST['start_date'])) : null;
                    $form_data['end_date'] = isset($_POST['end_date']) ? date('Y-m-d', strtotime($_POST['end_date'])) : null;
                }
            }

            error_log("Form data: " . print_r($form_data, true));

            // Insert into database
            $sql = "INSERT INTO access_requests (
                requestor_name, business_unit, access_request_number, 
                department, email, contact_number, access_type,
                justification, status, submission_date, system_type,
                other_system_type, role_access_type, duration_type,
                start_date, end_date
            ) VALUES (
                :requestor_name, :business_unit, :access_request_number,
                :department, :email, :contact_number, :access_type,
                :justification, :status, :submission_date, :system_type,
                :other_system_type, :role_access_type, :duration_type,
                :start_date, :end_date
            )";

            $stmt = $pdo->prepare($sql);
            
            // Log the SQL query with values for debugging
            error_log("SQL Query: " . $sql);
            error_log("Executing with values: " . print_r($form_data, true));
            
            $stmt->execute($form_data);

            // Send email notification to admin
            $admin_email = "admin@example.com"; // Replace with actual admin email
            $subject = "New Access Request from " . $form_data['requestor_name'];
            $message = "A new access request has been submitted:\n\n";
            $message .= "Requestor: " . $form_data['requestor_name'] . "\n";
            $message .= "Business Unit: " . $form_data['business_unit'] . "\n";
            $message .= "Department: " . $form_data['department'] . "\n";
            $message .= "Access Type: " . $form_data['access_type'] . "\n";
            $message .= "Submission Date: " . $form_data['submission_date'] . "\n\n";
            $message .= "Please login to the admin dashboard to review this request.";

            $headers = "From: " . $form_data['email'] . "\r\n";
            $headers .= "Reply-To: " . $form_data['email'] . "\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();

            mail($admin_email, $subject, $message, $headers);

            $response['success'] = true;
            $response['message'] = 'Your access request has been submitted successfully. You will be notified once it has been reviewed.';
        } catch (PDOException $e) {
            $response['message'] = 'Database Error: ' . $e->getMessage();
            error_log("Database Error: " . $e->getMessage());
        } catch (Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
            error_log("Error: " . $e->getMessage());
        }
    } else {
        $response['message'] = implode(', ', $errors);
    }
} else {
    $response['message'] = 'Invalid request method';
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);