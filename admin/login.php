<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#00B4D8',
                        secondary: '#90E0EF',
                        accent: '#80ED99',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-primary via-secondary to-accent min-h-screen flex items-center justify-center p-4">
    <?php
    session_start();
    require_once 'config.php';
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = "Invalid username or password";
        }
    }
    ?>

    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
        <h1 class="text-3xl font-bold text-primary mb-8 text-center">Admin Login</h1>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" id="username" name="username" required
                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                    placeholder="Enter your username">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                    placeholder="Enter your password">
            </div>

            <button type="submit"
                class="w-full bg-primary hover:bg-secondary text-white font-bold py-2 px-4 rounded-md transition duration-300">
                Login
            </button>
        </form>
    </div>
</body>
</html>