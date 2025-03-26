<?php
session_start();
require_once 'config.php';

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
        $sql = "UPDATE access_requests SET 
                status = :status,
                reviewed_by = :admin_id,
                review_date = NOW(),
                review_notes = :review_notes
                WHERE id = :request_id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'status' => ($action === 'approve') ? 'approved' : 'rejected',
            'admin_id' => $admin_id,
            'review_notes' => $review_notes,
            'request_id' => $request_id
        ]);

        // Send email notification to requestor
        $sql = "SELECT * FROM access_requests WHERE id = :request_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['request_id' => $request_id]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

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

        $_SESSION['success_message'] = "Request successfully " . ($action === 'approve' ? 'approved' : 'declined');
    } catch (PDOException $e) {
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
    <title>UAR Requests</title>

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
                    <a href="approval-history.php" class="flex items-center px-4 py-3 text-gray-600 rounded-lg transition-all hover:bg-gray-100">
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
                        <h2 class="text-2xl font-bold text-gray-800">User Access Request System</h2>
                        <p class="text-gray-800 text-sm mt-1">
                            Welcome back, <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?>
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <input type="text" 
                                   placeholder="Search..." 
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
                                <button class="px-3 py-1 text-sm bg-blue-50 text-primary rounded">All</button>
                                <button class="px-3 py-1 text-sm text-gray-500 hover:bg-gray-50 rounded">Pending</button>
                                <button class="px-3 py-1 text-sm text-gray-500 hover:bg-gray-50 rounded">Approved</button>
                                <button class="px-3 py-1 text-sm text-gray-500 hover:bg-gray-50 rounded">Rejected</button>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Number</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requestor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Business Unit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Access Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submission Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($requests as $request): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($request['access_request_number']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($request['requestor_name']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($request['email']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo htmlspecialchars($request['business_unit']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($request['department']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo htmlspecialchars($request['access_type']); ?></div>
                                        <?php if ($request['access_type'] === 'system_application'): ?>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($request['system_type']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php
                                            switch ($request['status']) {
                                                case 'pending':
                                                    echo 'bg-yellow-100 text-yellow-800';
                                                    break;
                                                case 'approved':
                                                    echo 'bg-green-100 text-green-800';
                                                    break;
                                                case 'rejected':
                                                    echo 'bg-red-100 text-red-800';
                                                    break;
                                            }
                                            ?>">
                                            <?php echo ucfirst($request['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('M d, Y H:i', strtotime($request['submission_date'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <?php if ($request['status'] === 'pending'): ?>
                                            <button onclick="showActionModal(<?php echo $request['id']; ?>, 'approve')" class="text-primary hover:text-primary-dark mr-2">
                                                <i class='bx bx-check text-xl'></i>
                                            </button>
                                            <button onclick="showActionModal(<?php echo $request['id']; ?>, 'decline')" class="text-red-600 hover:text-red-700">
                                                <i class='bx bx-x text-xl'></i>
                                            </button>
                                        <?php else: ?>
                                            <button onclick="showDetailsModal(<?php echo $request['id']; ?>)" class="text-gray-600 hover:text-gray-900">
                                                <i class='bx bx-info-circle text-xl'></i>
                                            </button>
                                        <?php endif; ?>
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
    <div id="actionModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden">
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

    <script>
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

        // Close modal when clicking outside
        document.getElementById('actionModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideActionModal();
            }
        });
    </script>
</body>
</html>
