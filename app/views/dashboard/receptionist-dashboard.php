<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receptionist Dashboard - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-900 text-white p-6">
            <div class="mb-8">
                <h1 class="text-xl font-bold"><?php echo APP_NAME; ?></h1>
                <p class="text-sm text-gray-400">Receptionist Dashboard</p>
            </div>
            
            <nav class="space-y-4">
                <a href="<?php echo APP_URL; ?>/dashboard" class="block px-4 py-2 bg-blue-600 rounded">Dashboard</a>
                <a href="<?php echo APP_URL; ?>/patients" class="block px-4 py-2 hover:bg-gray-800 rounded">Patients</a>
                <a href="<?php echo APP_URL; ?>/appointments" class="block px-4 py-2 hover:bg-gray-800 rounded">Appointments</a>
                <a href="<?php echo APP_URL; ?>/appointments/new" class="block px-4 py-2 hover:bg-gray-800 rounded">New Appointment</a>
                <a href="<?php echo APP_URL; ?>/doctors" class="block px-4 py-2 hover:bg-gray-800 rounded">Doctors</a>
                <hr class="border-gray-700 my-4">
                <a href="<?php echo APP_URL; ?>/logout" class="block px-4 py-2 hover:bg-gray-800 rounded">Logout</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <!-- Header -->
            <div class="bg-white shadow p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-900">Receptionist Dashboard</h2>
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
                        <p class="text-gray-600 text-sm font-semibold">Pending Appointments</p>
                        <p class="text-3xl font-bold text-yellow-600 mt-2"><?php echo $data['stats']['pending_appointments']; ?></p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <p class="text-gray-600 text-sm font-semibold">Today's Appointments</p>
                        <p class="text-3xl font-bold text-blue-600 mt-2"><?php echo $data['stats']['today_appointments']; ?></p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <p class="text-gray-600 text-sm font-semibold">Total Patients</p>
                        <p class="text-3xl font-bold text-green-600 mt-2"><?php echo $data['stats']['total_patients']; ?></p>
                    </div>
                </div>

                <!-- Pending Appointments -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Pending Appointments</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2">Patient</th>
                                    <th class="text-left py-2">Doctor</th>
                                    <th class="text-left py-2">Date/Time</th>
                                    <th class="text-left py-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['pending_appointments'])): ?>
                                    <?php foreach ($data['pending_appointments'] as $apt): ?>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="py-3"><?php echo htmlspecialchars($apt['patient_first'] . ' ' . $apt['patient_last']); ?></td>
                                            <td class="py-3"><?php echo htmlspecialchars($apt['doctor_first'] . ' ' . $apt['doctor_last']); ?></td>
                                            <td class="py-3"><?php echo date('M d, Y h:i A', strtotime($apt['scheduled_at'])); ?></td>
                                            <td class="py-3">
                                                <button class="bg-green-500 text-white px-3 py-1 rounded text-xs hover:bg-green-600 mr-2">Approve</button>
                                                <button class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600">Reject</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="py-4 text-center text-gray-500">No pending appointments</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
