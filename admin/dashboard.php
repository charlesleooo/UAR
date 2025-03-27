<?php
session_start();
require_once 'config.php';

// Authentication check
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

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
                    <a href="analytics.php" class="flex items-center px-4 py-3 text-gray-600 rounded-lg transition-all hover:bg-gray-100">
                        <i class='bx bx-line-chart text-xl'></i>
                        <span class="ml-3">Analytics</span>
                    </a>
                    <a href="requests.php" class="flex items-center px-4 py-3 text-gray-600 rounded-lg transition-all hover:bg-gray-100">
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
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Analytics Card -->
                    <a href="analytics.php" class="block bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-all duration-300">
                        <div class="p-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="text-3xl font-semibold text-gray-800">Analytics</h3>
                                    <p class="text-gray-600 mt-1">View system analytics</p>
                                </div>
                                <div class="text-primary">
                                    <i class='bx bx-line-chart text-3xl'></i>
                                </div>
                            </div>
                            <div class="mt-4 text-primary flex items-center justify-between">
                                <span class="text-sm">More info</span>
                                <i class='bx bx-right-arrow-alt'></i>
                            </div>
                        </div>
                    </a>

                    <!-- Requests Card -->
                    <a href="requests.php" class="block bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-all duration-300">
                        <div class="p-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="text-3xl font-semibold text-gray-800">Requests</h3>
                                    <p class="text-gray-600 mt-1">Manage access requests</p>
                                </div>
                                <div class="text-primary">
                                    <i class='bx bxs-message-square-detail text-3xl'></i>
                                </div>
                            </div>
                            <div class="mt-4 text-primary flex items-center justify-between">
                                <span class="text-sm">More info</span>
                                <i class='bx bx-right-arrow-alt'></i>
                            </div>
                        </div>
                    </a>

                    <!-- Approval History Card -->
                    <a href="approval_history.php" class="block bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-all duration-300">
                        <div class="p-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="text-3xl font-semibold text-gray-800">Approval History</h3>
                                    <p class="text-gray-600 mt-1">View past approvals</p>
                                </div>
                                <div class="text-primary">
                                    <i class='bx bx-history text-3xl'></i>
                                </div>
                            </div>
                            <div class="mt-4 text-primary flex items-center justify-between">
                                <span class="text-sm">More info</span>
                                <i class='bx bx-right-arrow-alt'></i>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>