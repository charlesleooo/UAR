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
            'submission_date' => date('Y-m-d H:i:s')
        ];

        // Add System Application specific data if applicable
        if ($_POST['access_type'] === 'system_application') {
            $form_data['system_type'] = $_POST['system_type'];
            if (in_array('other', $_POST['system_type']) && !empty($_POST['other_system_type'])) {
                $form_data['other_system_type'] = $_POST['other_system_type'];
            }
            $form_data['role_access_type'] = $_POST['role_access_type'] ?? '';
            $form_data['duration_type'] = $_POST['duration_type'];
            if ($_POST['duration_type'] === 'temporary') {
                $form_data['start_date'] = $_POST['start_date'];
                $form_data['end_date'] = $_POST['end_date'];
            }
        }

        // TODO: Add your database storage logic here
        // For now, we'll just simulate successful submission
        $response['success'] = true;
        $response['message'] = 'Form submitted successfully';
        $response['data'] = $form_data;
    } else {
        $response['message'] = implode(', ', $errors);
    }
} else {
    $response['message'] = 'Invalid request method';
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT);