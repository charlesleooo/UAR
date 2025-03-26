<?php
session_start();
require_once 'config.php';

// Authentication check
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch dashboard data
function getDashboardStats($pdo) {
    // Get total requests
    $stmt = $pdo->query("SELECT COUNT(*) FROM requests");
    $totalRequests = $stmt->fetchColumn();
    
    // Get total approved requests
    $stmt = $pdo->query("SELECT COUNT(*) FROM requests WHERE status = 'approved'");
    $totalApproved = $stmt->fetchColumn();
    
    // Get total declined requests
    $stmt = $pdo->query("SELECT COUNT(*) FROM requests WHERE status = 'declined'");
    $totalDeclined = $stmt->fetchColumn();
    
    // Calculate approval rate
    $approvalRate = $totalRequests > 0 ? round(($totalApproved / $totalRequests) * 100, 2) : 0;
    
    // Calculate decline rate
    $declineRate = $totalRequests > 0 ? round(($totalDeclined / $totalRequests) * 100, 2) : 0;
    
    return [
        [
            'title' => 'Total Requests',
            'value' => number_format($totalRequests),
            'change' => '+59.3%',
            'color' => 'text-amber-500'
        ],
        [
            'title' => 'Approved Requests',
            'value' => number_format($totalApproved),
            'change' => '+27.4%',
            'color' => 'text-emerald-500'
        ],
        [
            'title' => 'Approval Rate',
            'value' => $approvalRate . '%',
            'change' => '+27.4%',
            'color' => 'text-blue-500'
        ],
        [
            'title' => 'Decline Rate',
            'value' => $declineRate . '%',
            'change' => '+12.8%',
            'color' => 'text-red-500'
        ]
    ];
}

// Fetch recent activity
function getRecentActivity($pdo) {
    $stmt = $pdo->query("SELECT r.*, au.username 
                        FROM requests r 
                        LEFT JOIN admin_users au ON r.user_id = au.id 
                        ORDER BY r.created_at DESC 
                        LIMIT 5");
    return $stmt->fetchAll();
}

$dashboardStats = getDashboardStats($pdo);
$recentActivity = getRecentActivity($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UAR Dashboard</title>

    <!-- External CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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
                    <a href="#" class="flex items-center px-4 py-3 text-primary bg-indigo-50 rounded-lg transition-all hover:bg-indigo-100">
                        <i class='bx bxs-dashboard text-xl'></i>
                        <span class="ml-3">Dashboard</span>
                    </a>
                    <a href="reports.php" class="flex items-center px-4 py-3 text-gray-600 rounded-lg transition-all hover:bg-gray-100">
                        <i class='bx bxs-report text-xl'></i>
                        <span class="ml-3">Reports Analysis</span>
                    </a>
                    <a href="requests.php" class="flex items-center px-4 py-3 text-gray-600 rounded-lg transition-all hover:bg-gray-100">
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
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <?php foreach ($dashboardStats as $stat): ?>
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-gray-500 text-sm"><?php echo $stat['title']; ?></h3>
                                <span class="<?php echo $stat['color']; ?> text-xs"><?php echo $stat['change']; ?></span>
                            </div>
                            <p class="text-2xl font-semibold text-gray-800"><?php echo $stat['value']; ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Request Overview Chart -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-gray-800 font-semibold">Request Overview</h3>
                            <div class="flex gap-2">
                                <button class="px-3 py-1 text-sm bg-blue-50 text-primary rounded">Month</button>
                                <button class="px-3 py-1 text-sm text-gray-500 hover:bg-gray-50 rounded">Week</button>
                            </div>
                        </div>
                        <canvas id="visitorChart" height="300"></canvas>
                    </div>

                    <!-- Request Statistics Chart -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="text-gray-800 font-semibold">Request Statistics</h3>
                                <p class="text-gray-500 text-sm">This Week's Statistics</p>
                            </div>
                        </div>
                        <canvas id="incomeChart" height="300"></canvas>
                    </div>
                </div>

                <!-- Recent Activity Table -->
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h5 class="text-xl font-semibold text-gray-800">Recent Activity</h5>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Request ID</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">User</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Type</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php foreach ($recentActivity as $activity): 
                                        $statusColor = match($activity['status']) {
                                            'approved' => 'bg-emerald-100 text-emerald-800',
                                            'pending' => 'bg-amber-100 text-amber-800',
                                            default => 'bg-red-100 text-red-800'
                                        };
                                    ?>
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 text-sm text-gray-600">#<?php echo htmlspecialchars($activity['request_id']); ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($activity['username']); ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($activity['report_type']); ?></td>
                                            <td class="px-6 py-4 text-sm">
                                                <span class="px-3 py-1 rounded-full <?php echo $statusColor; ?>">
                                                    <?php echo htmlspecialchars($activity['status']); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                <?php echo date('M j, Y', strtotime($activity['created_at'])); ?>
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
    </div>

    <!-- Charts Initialization -->
    <script>
        // Request Overview Chart
        const visitorChartCtx = document.getElementById('visitorChart').getContext('2d');
        new Chart(visitorChartCtx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Page Views',
                    data: [30, 40, 35, 50, 49, 60, 70],
                    borderColor: 'rgb(99, 102, 241)',
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                },
                {
                    label: 'Sessions',
                    data: [20, 25, 30, 35, 40, 35, 45],
                    borderColor: 'rgb(74, 222, 128)',
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(74, 222, 128, 0.1)',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Request Statistics Chart
        const incomeChartCtx = document.getElementById('incomeChart').getContext('2d');
        new Chart(incomeChartCtx, {
            type: 'bar',
            data: {
                labels: ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],
                datasets: [{
                    label: 'Requests',
                    data: [65, 59, 80, 81, 56, 55, 40],
                    backgroundColor: 'rgb(99, 102, 241)',
                    borderRadius: 5,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>