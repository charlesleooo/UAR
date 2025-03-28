<?php
session_start();
require_once 'config.php';

// Add the generateRequestNumber function at the top
function generateRequestNumber($pdo) {
    try {
        $pdo->beginTransaction();
        
        // Get the highest request number from both tables
        $sql = "SELECT MAX(CAST(SUBSTRING_INDEX(access_request_number, '-', -1) AS UNSIGNED)) as max_num 
               FROM (
                   SELECT access_request_number FROM access_requests
                   UNION ALL
                   SELECT access_request_number FROM approval_history
               ) combined";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Get the next number (current max + 1)
        $nextNumber = ($result['max_num'] ?? 0) + 1;
        
        // Format the request number (REQ2025-003 format)
        $year = date('Y');
        $requestNumber = sprintf("REQ%d-%03d", $year, $nextNumber);
        
        $pdo->commit();
        return $requestNumber;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw new Exception("Failed to generate request number: " . $e->getMessage());
    }
}

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Handle approve/decline actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['request_id'])) {
    $action = $_POST['action'];
    $request_id = (int)$_POST['request_id'];
    $admin_id = $_SESSION['admin_id'];
    $review_notes = $_POST['review_notes'] ?? '';
    
    try {
        // Start transaction
        $pdo->beginTransaction();

        // 1. First get the request details before updating
        $sql = "SELECT * FROM access_requests WHERE id = :request_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['request_id' => $request_id]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$request) {
            throw new Exception('Request not found');
        }

        // 2. Insert into approval_history table first
        $sql = "INSERT INTO approval_history (
                    access_request_number,
                    action,
                    requestor_name,
                    business_unit,
                    department,
                    access_type,
                    admin_id,
                    comments,
                    system_type,
                    duration_type,
                    start_date,
                    end_date,
                    justification,
                    email,
                    contact_number
                ) VALUES (
                    :access_request_number,
                    :action,
                    :requestor_name,
                    :business_unit,
                    :department,
                    :access_type,
                    :admin_id,
                    :comments,
                    :system_type,
                    :duration_type,
                    :start_date,
                    :end_date,
                    :justification,
                    :email,
                    :contact_number
                )";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            'access_request_number' => $request['access_request_number'],
            'action' => ($action === 'approve') ? 'approved' : 'rejected',
            'requestor_name' => $request['requestor_name'],
            'business_unit' => $request['business_unit'],
            'department' => $request['department'],
            'access_type' => $request['access_type'],
            'admin_id' => $admin_id,
            'comments' => $review_notes,
            'system_type' => $request['system_type'],
            'duration_type' => $request['duration_type'],
            'start_date' => $request['start_date'],
            'end_date' => $request['end_date'],
            'justification' => $request['justification'],
            'email' => $request['email'],
            'contact_number' => $request['contact_number']
        ]);

        if (!$result) {
            throw new Exception('Failed to insert into approval history');
        }

        // 3. Delete from access_requests table
        $sql = "DELETE FROM access_requests WHERE id = :request_id";  // Changed from access_request_number
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['request_id' => $request_id]);  // Changed parameter name to match

        // Send email notification to requestor
        $subject = "Access Request " . ($action === 'approve' ? 'Approved' : 'Declined');
        $message = "Dear " . $request['requestor_name'] . ",\n\n";
        $message .= "Your access request has been " . ($action === 'approve' ? 'approved' : 'declined') . ".\n\n";
        if (!empty($review_notes)) {
            $message .= "Notes: " . $review_notes . "\n\n";
        }
        $message .= "Request Details:\n";
        $message .= "Access Type: " . $request['access_type'] . "\n";
        $message .= "Business Unit: " . $request['business_unit'] . "\n";
        $message .= "Department: " . $request['department'] . "\n";
        
        mail($request['email'], $subject, $message);

        // Commit transaction
        $pdo->commit();

        $_SESSION['success_message'] = "Request successfully " . ($action === 'approve' ? 'approved' : 'declined');
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        $_SESSION['error_message'] = "Error updating request: " . $e->getMessage();
    }
    
    header('Location: requests.php');
    exit();
}

// Get all requests
try {
    $sql = "SELECT r.*, a.username as reviewed_by_name 
            FROM access_requests r 
            LEFT JOIN admin_users a ON r.reviewed_by = a.id 
            ORDER BY r.submission_date DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Error fetching requests: " . $e->getMessage();
    $requests = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Requests</title>

    <!-- External CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    
    <!-- Tailwind Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4F46E5',
                        secondary: '#1F2937',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="fixed h-full w-64 bg-white border-r border-gray-200">
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="text-center">
                    <img src="../logo.png" alt="Alsons Agribusiness Logo" class="mt-1 w-60 h-auto mx-auto">
                </div><br>
                
                <!-- Navigation -->
                <nav class="flex-1 p-4 space-y-2">
                    <a href="dashboard.php" class="flex items-center px-4 py-3 text-gray-600 rounded-lg transition-all hover:bg-gray-100">
                        <i class='bx bxs-dashboard text-xl'></i>
                        <span class="ml-3">Dashboard</span>
                    </a>
                    <a href="reports.php" class="flex items-center px-4 py-3 text-gray-600 rounded-lg transition-all hover:bg-gray-100">
                        <i class='bx bxs-report text-xl'></i>
                        <span class="ml-3">Reports Analysis</span>
                    </a>
                    <a href="requests.php" class="flex items-center px-4 py-3 text-primary bg-indigo-50 rounded-lg transition-all hover:bg-indigo-100">
                        <i class='bx bxs-message-square-detail text-xl'></i>
                        <span class="ml-3">Requests</span>
                    </a>
                    <a href="approval_history.php" class="flex items-center px-4 py-3 text-gray-600 rounded-lg transition-all hover:bg-gray-100">
                        <i class='bx bx-history text-xl'></i>
                        <span class="ml-3">Approval History</span>
                    </a>
                </nav>
                
                <!-- Logout -->
                <div class="p-4 border-t border-gray-200">
                    <a href="logout.php" class="flex items-center px-4 py-3 text-red-600 rounded-lg transition-all hover:bg-red-50">
                        <i class='bx bx-log-out text-xl'></i>
                        <span class="ml-3">Logout</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 ml-64">
            <!-- Header -->
            <div class="bg-white border-b border-gray-200">
                <div class="flex justify-between items-center px-8 py-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Access Requests</h2>
                        <p class="text-gray-600 text-sm mt-1">
                            Manage and review user access requests
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <input type="text" 
                                   id="searchInput"
                                   placeholder="Search requests..." 
                                   class="pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20">
                            <i class='bx bx-search absolute left-3 top-2.5 text-gray-400'></i>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                            <i class='bx bx-user text-xl text-gray-600'></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="p-8">
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6" role="alert">
                        <?php 
                        echo $_SESSION['success_message'];
                        unset($_SESSION['success_message']);
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
                        <?php 
                        echo $_SESSION['error_message'];
                        unset($_SESSION['error_message']);
                        ?>
                    </div>
                <?php endif; ?>

                <div class="bg-white rounded-xl shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <div class="flex justify-between items-center">
                            <h3 class="text-xl font-semibold text-gray-800">Access Requests</h3>
                            <div class="flex gap-2">
                                <button onclick="filterRequests('all')" class="px-3 py-1 text-sm bg-blue-50 text-primary rounded">All</button>
                                <button onclick="filterRequests('pending')" class="px-3 py-1 text-sm text-gray-500 hover:bg-gray-50 rounded">Pending</button>
                                <button onclick="filterRequests('approved')" class="px-3 py-1 text-sm text-gray-500 hover:bg-gray-50 rounded">Approved</button>
                                <button onclick="filterRequests('rejected')" class="px-3 py-1 text-sm text-gray-500 hover:bg-gray-50 rounded">Rejected</button>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request No.</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requestor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Business Unit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Access Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($requests as $request): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($request['access_request_number']); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($request['requestor_name']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($request['email']); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900"><?php echo htmlspecialchars($request['business_unit']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($request['department']); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900"><?php echo htmlspecialchars($request['access_type']); ?></div>
                                        <?php if ($request['system_type']): ?>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($request['system_type']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php echo $request['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                ($request['status'] === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                                            <?php echo ucfirst($request['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('M d, Y', strtotime($request['submission_date'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-2">
                                            <button onclick="showDetailsModal(<?php echo $request['id']; ?>)" 
                                                    class="inline-flex items-center px-3 py-1 bg-gray-50 text-gray-700 rounded-lg hover:bg-gray-100">
                                                <i class='bx bx-info-circle'></i>
                                                <span class="ml-1">View</span>
                                            </button>
                                            <?php if ($request['status'] === 'pending'): ?>
                                            <button onclick="showActionModal(<?php echo $request['id']; ?>, 'approve')" 
                                                    class="inline-flex items-center px-3 py-1 bg-green-50 text-green-700 rounded-lg hover:bg-green-100">
                                                <i class='bx bx-check'></i>
                                                <span class="ml-1">Approve</span>
                                            </button>
                                            <button onclick="showActionModal(<?php echo $request['id']; ?>, 'decline')" 
                                                    class="inline-flex items-center px-3 py-1 bg-red-50 text-red-700 rounded-lg hover:bg-red-100">
                                                <i class='bx bx-x'></i>
                                                <span class="ml-1">Decline</span>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Modal -->
    <div id="actionModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden z-50">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded-xl p-8 max-w-md w-full mx-4">
                <h3 id="modalTitle" class="text-xl font-semibold text-gray-800 mb-4"></h3>
                <form id="actionForm" method="POST">
                    <input type="hidden" name="request_id" id="request_id">
                    <input type="hidden" name="action" id="action">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Review Notes</label>
                        <textarea name="review_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20"></textarea>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="hideActionModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-white bg-primary rounded-lg hover:bg-primary-dark">
                            Confirm
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Details Modal -->
    <div id="detailsModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl w-[90%] max-w-7xl mx-auto shadow-xl">
                <div class="flex items-center px-6 py-4 border-b border-gray-200">
                    <div class="w-1/4">
                        <p class="text-sm font-medium text-gray-500">Request Number</p>
                        <p id="detail_request_number" class="text-lg font-semibold text-gray-900"></p>
                    </div>
                    <div class="flex-1 text-center">
                        <h3 class="text-xl font-semibold text-gray-800">Access Request Details</h3>
                    </div>
                    <div class="w-1/4 flex justify-end">
                        <button onclick="hideDetailsModal()" class="text-gray-500 hover:text-gray-700">
                            <i class='bx bx-x text-2xl'></i>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <div id="modalContent">
                        <!-- Modal content will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Existing functions
        function showActionModal(requestId, action) {
            document.getElementById('request_id').value = requestId;
            document.getElementById('action').value = action;
            document.getElementById('modalTitle').textContent = 
                action === 'approve' ? 'Approve Access Request' : 'Decline Access Request';
            document.getElementById('actionModal').classList.remove('hidden');
        }

        function hideActionModal() {
            document.getElementById('actionModal').classList.add('hidden');
        }

        function showDetailsModal(requestId) {
            document.getElementById('detailsModal').classList.remove('hidden');
            const modalContainer = document.getElementById('modalContent');
            
            // Show loading state
            modalContainer.innerHTML = `
                <div class="flex justify-center items-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                    <span class="ml-2">Loading details...</span>
                </div>
            `;
            
            // Fetch request details via AJAX
            fetch(`get_request_details.php?id=${requestId}`)
                .then(async response => {
                    if (!response.ok) {
                        const errorText = await response.text();
                        throw new Error(errorText || 'Failed to load request details');
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data || typeof data !== 'object') {
                        throw new Error('Invalid response data');
                    }
                    
                    // Update the request number in the header
                    document.getElementById('detail_request_number').textContent = data.access_request_number;
                    
                    // Render the modal content with the same design as approval history
                    modalContainer.innerHTML = `
                        <div class="grid grid-cols-3 gap-6">
                            <!-- Left Column -->
                            <div class="col-span-1 space-y-6">
                                <!-- Requestor Information -->
                                <div class="bg-white rounded-lg border border-gray-100 shadow-sm">
                                    <div class="px-6 py-4 border-b border-gray-100">
                                        <h4 class="text-lg font-semibold text-gray-800">Requestor Information</h4>
                                    </div>
                                    <div class="p-6 space-y-4">
                                        <div>
                                            <p class="text-sm font-medium text-gray-500 mb-1">Full Name</p>
                                            <p class="text-gray-900">${data.requestor_name}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500 mb-1">Email Address</p>
                                            <p class="text-gray-900">${data.email}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500 mb-1">Contact Number</p>
                                            <p class="text-gray-900">${data.contact_number}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500 mb-1">Business Unit</p>
                                            <p class="text-gray-900">${data.business_unit}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500 mb-1">Department</p>
                                            <p class="text-gray-900">${data.department}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Information -->
                                <div class="bg-white rounded-lg border border-gray-100 shadow-sm">
                                    <div class="px-6 py-4 border-b border-gray-100">
                                        <h4 class="text-lg font-semibold text-gray-800">Status Information</h4>
                                    </div>
                                    <div class="p-6 space-y-4">
                                        <div>
                                            <p class="text-sm font-medium text-gray-500 mb-1">Status</p>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium 
                                                ${data.status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                (data.status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')}">
                                                ${data.status.toUpperCase()}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500 mb-1">Submission Date</p>
                                            <p class="text-gray-900">${new Date(data.submission_date).toLocaleString()}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-span-2 space-y-6">
                                <!-- Access Details -->
                                <div class="bg-white rounded-lg border border-gray-100 shadow-sm">
                                    <div class="px-6 py-4 border-b border-gray-100">
                                        <h4 class="text-lg font-semibold text-gray-800">Access Details</h4>
                                    </div>
                                    <div class="p-6">
                                        <div class="grid grid-cols-2 gap-6">
                                            <div>
                                                <p class="text-sm font-medium text-gray-500 mb-1">Access Type</p>
                                                <p class="text-gray-900">${data.access_type}</p>
                                            </div>
                                            ${data.system_type ? `
                                            <div>
                                                <p class="text-sm font-medium text-gray-500 mb-1">System Type</p>
                                                <p class="text-gray-900">${data.system_type}</p>
                                            </div>
                                            ` : ''}
                                            <div>
                                                <p class="text-sm font-medium text-gray-500 mb-1">Duration Type</p>
                                                <p class="text-gray-900">${data.duration_type ? data.duration_type.charAt(0).toUpperCase() + data.duration_type.slice(1) : 'Not specified'}</p>
                                            </div>
                                            ${data.duration_type === 'temporary' ? `
                                            <div>
                                                <p class="text-sm font-medium text-gray-500 mb-1">Duration Period</p>
                                                <p class="text-gray-900">${new Date(data.start_date).toLocaleDateString()} to ${new Date(data.end_date).toLocaleDateString()}</p>
                                            </div>
                                            ` : ''}
                                        </div>
                                    </div>
                                </div>

                                <!-- Justification -->
                                <div class="bg-white rounded-lg border border-gray-100 shadow-sm">
                                    <div class="px-6 py-4 border-b border-gray-100">
                                        <h4 class="text-lg font-semibold text-gray-800">Justification</h4>
                                    </div>
                                    <div class="p-6">
                                        <p class="text-gray-900 bg-gray-50 p-4 rounded-lg min-h-[100px]">${data.justification || 'No justification provided'}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalContainer.innerHTML = `
                        <div class="text-center py-8">
                            <div class="text-red-600 mb-2">
                                <i class='bx bx-error-circle text-3xl'></i>
                            </div>
                            <p class="text-red-600 font-medium">Error loading request details</p>
                            <p class="text-gray-500 text-sm mt-1">${error.message}</p>
                            <button onclick="hideDetailsModal()" 
                                    class="mt-4 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                                Close
                            </button>
                        </div>
                    `;
                });
        }

        function hideDetailsModal() {
            document.getElementById('detailsModal').classList.add('hidden');
        }

        // New filter function
        function filterRequests(status) {
            const buttons = document.querySelectorAll('.flex.gap-2 button');
            buttons.forEach(button => {
                if (button.textContent.toLowerCase() === status || (status === 'all' && button.textContent === 'All')) {
                    button.classList.add('bg-blue-50', 'text-primary');
                    button.classList.remove('text-gray-500', 'hover:bg-gray-50');
                } else {
                    button.classList.remove('bg-blue-50', 'text-primary');
                    button.classList.add('text-gray-500', 'hover:bg-gray-50');
                }
            });

            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const statusCell = row.querySelector('td:nth-child(5) span');
                const rowStatus = statusCell.textContent.trim().toLowerCase();
                if (status === 'all' || rowStatus === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchText = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchText) ? '' : 'none';
            });
        });

        // Close modals when clicking outside
        document.getElementById('actionModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideActionModal();
            }
        });

        document.getElementById('detailsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideDetailsModal();
            }
        });
    </script>
</body>
</html>