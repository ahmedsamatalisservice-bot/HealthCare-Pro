<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900"><?php echo APP_NAME; ?></h1>
                <p class="text-gray-600 text-sm mt-2">Healthcare Management System</p>
            </div>

            <!-- Error Message -->
            <?php if (isset($_SESSION['login_error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($_SESSION['login_error']); ?>
                </div>
                <?php unset($_SESSION['login_error']); ?>
            <?php endif; ?>

            <!-- Success Message -->
            <?php if (isset($_SESSION['registration_success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($_SESSION['registration_success']); ?>
                </div>
                <?php unset($_SESSION['registration_success']); ?>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="<?php echo APP_URL; ?>/login" class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="your@email.com"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="••••••••"
                    >
                </div>

                <button 
                    type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200"
                >
                    Sign In
                </button>
            </form>

            <!-- Divider -->
            <div class="my-6 text-center text-gray-500 text-sm">
                Don't have an account? 
                <a href="<?php echo APP_URL; ?>/register" class="text-blue-600 hover:text-blue-700 font-semibold">Sign Up</a>
            </div>

            <!-- Demo Credentials -->
            <div class="bg-blue-50 border border-blue-200 rounded p-4 text-sm text-gray-700">
                <strong>Demo Credentials:</strong><br>
                <small>Email: admin@healthcare.local<br>Password: Demo@1234</small>
            </div>
        </div>
    </div>
</body>
</html>
