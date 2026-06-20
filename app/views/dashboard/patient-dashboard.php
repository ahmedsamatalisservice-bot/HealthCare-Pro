<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-900 text-white p-6">
            <div class="mb-8">
                <h1 class="text-xl font-bold"><?php echo APP_NAME; ?></h1>
                <p class="text-sm text-gray-400">Patient Dashboard</p>
            </div>
            
            <nav class="space-y-4">
                <a href="<?php echo APP_URL; ?>/dashboard" class="block px-4 py-2 bg-blue-600 rounded">Dashboard</a>
                <a href="<?php echo APP_URL; ?>/appointments" class="block px-4 py-2 hover:bg-gray-800 rounded">My Appointments</a>
                <a href="<?php echo APP_URL; ?>/medical-records" class="block px-4 py-2 hover:bg-gray-800 rounded">Medical Records</a>
                <a href="<?php echo APP_URL; ?>/invoices" class="block px-4 py-2 hover:bg-gray-800 rounded">Billing & Payments</a>
                <a href="<?php echo APP_URL; ?>/profile" class="block px-4 py-2 hover:bg-gray-800 rounded">My Profile</a>
                <hr class="border-gray-700 my-4">
                <a href="<?php echo APP_URL; ?>/logout" class="block px-4 py-2 hover:bg-gray-800 rounded">Logout</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <!-- Header -->
            <div class="bg-white shadow p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-900">My Health Dashboard</h2>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Welcome, <?php echo htmlspecialchars($data['user']['first_name']); ?></p>
                        <p class="text-xs text-gray-500"><?php echo date('l, F j, Y'); ?></p>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                <!-- Stats Grid -->
                <div class="grid grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow p-6">
                        <p class="text-gray-600 text-sm font-semibold">Upcoming Appointments</p>
                        <p class="text-3xl font-bold text-blue-600 mt-2"><?php echo $data['stats']['upcoming_appointments']; ?></p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <p class="text-gray-600 text-sm font-semibold">Pending Invoices</p>
                        <p class="text-3xl font-bold text-red-600 mt-2"><?php echo $data['stats']['pending_invoices']; ?></p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <p class="text-gray-600 text-sm font-semibold">Outstanding Balance</p>
                        <p class="text-3xl font-bold text-orange-600 mt-2">$<?php echo number_format($data['stats']['outstanding_balance'], 2); ?></p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <!-- Recent Appointments -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4">Recent Appointments</h3>
                        <div class="space-y-3">
                            <?php if (!empty($data['recent_appointments'])): ?>
                                <?php foreach ($data['recent_appointments'] as $apt): ?>
                                    <div class="border-l-4 border-blue-500 pl-4 py-2">
                                        <p class="font-semibold text-sm">Dr. <?php echo htmlspecialchars($apt['doctor_last']); ?></p>
                                        <p class="text-xs text-gray-600"><?php echo date('M d, Y h:i A', strtotime($apt['scheduled_at'])); ?></p>
                                        <p class="text-xs text-gray-500">Status: <span class="font-semibold"><?php echo ucfirst($apt['status']); ?></span></p>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-gray-500 text-sm">No appointments</p>
                            <?php endif; ?>
                        </div>
                        <a href="<?php echo APP_URL; ?>/appointments/new" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">Book Appointment</a>
                    </div>

                    <!-- Recent Invoices -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4">Recent Invoices</h3>
                        <div class="space-y-3">
                            <?php if (!empty($data['recent_invoices'])): ?>
                                <?php foreach ($data['recent_invoices'] as $inv): ?>
                                    <div class="border-l-4 border-green-500 pl-4 py-2">
                                        <p class="font-semibold text-sm"><?php echo htmlspecialchars($inv['invoice_number']); ?></p>
                                        <p class="text-xs text-gray-600">$<?php echo number_format($inv['total_amount'], 2); ?></p>
                                        <p class="text-xs text-gray-500">Status: <span class="font-semibold"><?php echo ucfirst($inv['status']); ?></span></p>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-gray-500 text-sm">No invoices</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
