<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-900 text-white p-6">
            <div class="mb-8">
                <h1 class="text-xl font-bold"><?php echo APP_NAME; ?></h1>
                <p class="text-sm text-gray-400">Admin Dashboard</p>
            </div>
            
            <nav class="space-y-4">
                <a href="<?php echo APP_URL; ?>/dashboard" class="block px-4 py-2 bg-blue-600 rounded">Dashboard</a>
                <a href="<?php echo APP_URL; ?>/patients" class="block px-4 py-2 hover:bg-gray-800 rounded">Patients</a>
                <a href="<?php echo APP_URL; ?>/doctors" class="block px-4 py-2 hover:bg-gray-800 rounded">Doctors</a>
                <a href="<?php echo APP_URL; ?>/appointments" class="block px-4 py-2 hover:bg-gray-800 rounded">Appointments</a>
                <a href="<?php echo APP_URL; ?>/invoices" class="block px-4 py-2 hover:bg-gray-800 rounded">Billing</a>
                <hr class="border-gray-700 my-4">
                <a href="<?php echo APP_URL; ?>/logout" class="block px-4 py-2 hover:bg-gray-800 rounded">Logout</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <!-- Header -->
            <div class="bg-white shadow p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-900">Dashboard</h2>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Welcome, <?php echo htmlspecialchars($data['user']['first_name']); ?></p>
                        <p class="text-xs text-gray-500"><?php echo date('l, F j, Y'); ?></p>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                <!-- Stats Grid -->
                <div class="grid grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow p-6">
                        <p class="text-gray-600 text-sm font-semibold">Total Patients</p>
                        <p class="text-3xl font-bold text-blue-600 mt-2"><?php echo $data['stats']['total_patients']; ?></p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <p class="text-gray-600 text-sm font-semibold">Total Doctors</p>
                        <p class="text-3xl font-bold text-green-600 mt-2"><?php echo $data['stats']['total_doctors']; ?></p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <p class="text-gray-600 text-sm font-semibold">Total Appointments</p>
                        <p class="text-3xl font-bold text-purple-600 mt-2"><?php echo $data['stats']['total_appointments']; ?></p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <p class="text-gray-600 text-sm font-semibold">Total Revenue</p>
                        <p class="text-3xl font-bold text-orange-600 mt-2">$<?php echo number_format($data['stats']['total_revenue'], 2); ?></p>
                    </div>
                </div>

                <!-- Recent Data -->
                <div class="grid grid-cols-2 gap-6">
                    <!-- Today's Appointments -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4">Today's Appointments</h3>
                        <div class="space-y-3">
                            <?php if (!empty($data['today_appointments'])): ?>
                                <?php foreach (array_slice($data['today_appointments'], 0, 5) as $apt): ?>
                                    <div class="border-l-4 border-blue-500 pl-4 py-2">
                                        <p class="font-semibold text-sm"><?php echo htmlspecialchars($apt['patient_first'] . ' ' . $apt['patient_last']); ?></p>
                                        <p class="text-xs text-gray-600"><?php echo htmlspecialchars($apt['doctor_first'] . ' ' . $apt['doctor_last']); ?></p>
                                        <p class="text-xs text-gray-500"><?php echo date('h:i A', strtotime($apt['scheduled_at'])); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-gray-500 text-sm">No appointments today</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Pending Appointments -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4">Pending Appointments</h3>
                        <div class="space-y-3">
                            <?php if (!empty($data['pending_appointments'])): ?>
                                <?php foreach (array_slice($data['pending_appointments'], 0, 5) as $apt): ?>
                                    <div class="border-l-4 border-yellow-500 pl-4 py-2">
                                        <p class="font-semibold text-sm"><?php echo htmlspecialchars($apt['patient_first'] . ' ' . $apt['patient_last']); ?></p>
                                        <p class="text-xs text-gray-600"><?php echo date('M d, Y h:i A', strtotime($apt['scheduled_at'])); ?></p>
                                        <button class="text-xs bg-green-500 text-white px-2 py-1 rounded mt-1 hover:bg-green-600">Approve</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-gray-500 text-sm">No pending appointments</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
