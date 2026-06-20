<?php
/**
 * Database Seeder Script
 * 
 * Populates the database with initial demo data
 * 
 * Usage: php seed.php
 */

require_once __DIR__ . '/config/config.php';
require_once APP_PATH . '/helpers/Database.php';
require_once APP_PATH . '/helpers/Auth.php';
require_once APP_PATH . '/models/Model.php';
require_once APP_PATH . '/models/User.php';
require_once APP_PATH . '/models/Patient.php';
require_once APP_PATH . '/models/Doctor.php';

$db = new Database();

echo "🌱 Starting database seeding...\n\n";

try {
    // 1. Create demo users
    echo "📝 Creating demo users...\n";

    $adminId = $db->insert(
        "INSERT INTO users (role_id, email, password_hash, first_name, last_name, phone, is_active) 
         VALUES (?, ?, ?, ?, ?, ?, ?)",
        [1, 'admin@healthcare.local', Auth::hashPassword('Demo@1234'), 'Admin', 'User', '555-0001', 1]
    );
    echo "✓ Admin user created (ID: $adminId)\n";

    $doctorUserId = $db->insert(
        "INSERT INTO users (role_id, email, password_hash, first_name, last_name, phone, is_active) 
         VALUES (?, ?, ?, ?, ?, ?, ?)",
        [2, 'doctor@healthcare.local', Auth::hashPassword('Demo@1234'), 'Dr. John', 'Smith', '555-0002', 1]
    );
    echo "✓ Doctor user created (ID: $doctorUserId)\n";

    $receptionistUserId = $db->insert(
        "INSERT INTO users (role_id, email, password_hash, first_name, last_name, phone, is_active) 
         VALUES (?, ?, ?, ?, ?, ?, ?)",
        [3, 'receptionist@healthcare.local', Auth::hashPassword('Demo@1234'), 'Jane', 'Receptionist', '555-0003', 1]
    );
    echo "✓ Receptionist user created (ID: $receptionistUserId)\n";

    $patientUserId = $db->insert(
        "INSERT INTO users (role_id, email, password_hash, first_name, last_name, phone, is_active) 
         VALUES (?, ?, ?, ?, ?, ?, ?)",
        [4, 'patient@healthcare.local', Auth::hashPassword('Demo@1234'), 'Michael', 'Patient', '555-0004', 1]
    );
    echo "✓ Patient user created (ID: $patientUserId)\n\n";

    // 2. Create specialties
    echo "🏥 Creating medical specialties...\n";

    $specialties = [
        'General Practice',
        'Cardiology',
        'Neurology',
        'Orthopedics',
        'Pediatrics',
        'Psychiatry',
        'Dermatology',
        'Ophthalmology'
    ];

    $specialtyIds = [];
    foreach ($specialties as $specialty) {
        $id = $db->insert(
            "INSERT INTO specialties (name) VALUES (?)",
            [$specialty]
        );
        $specialtyIds[] = $id;
        echo "✓ Specialty '$specialty' created\n";
    }
    echo "\n";

    // 3. Create doctor profile
    echo "👨‍⚕️ Creating doctor profile...\n";

    $doctorId = $db->insert(
        "INSERT INTO doctors (user_id, specialty_id, license_number, qualifications, bio, consultation_fee, is_available) 
         VALUES (?, ?, ?, ?, ?, ?, ?)",
        [
            $doctorUserId,
            $specialtyIds[0], // General Practice
            'MED-2024-12345',
            'MD, Board Certified',
            'Experienced general practitioner with 10 years of clinical practice.',
            50.00,
            1
        ]
    );
    echo "✓ Doctor profile created (ID: $doctorId)\n\n";

    // 4. Create doctor schedule
    echo "📅 Creating doctor schedule...\n";

    for ($day = 1; $day <= 5; $day++) { // Monday to Friday
        $db->insert(
            "INSERT INTO doctor_schedules (doctor_id, day_of_week, start_time, end_time, is_active) 
             VALUES (?, ?, ?, ?, ?)",
            [$doctorId, $day, '09:00:00', '17:00:00', 1]
        );
    }
    echo "✓ Doctor schedule created (Mon-Fri, 9:00 AM - 5:00 PM)\n\n";

    // 5. Create patient profile
    echo "👤 Creating patient profile...\n";

    $patientId = $db->insert(
        "INSERT INTO patients (user_id, dob, gender, blood_type) 
         VALUES (?, ?, ?, ?)",
        [$patientUserId, '1990-05-15', 'male', 'O+']
    );
    echo "✓ Patient profile created (ID: $patientId)\n\n";

    // 6. Create sample appointment
    echo "📋 Creating sample appointment...\n";

    $appointmentId = $db->insert(
        "INSERT INTO appointments (patient_id, doctor_id, scheduled_at, duration_minutes, status, created_by) 
         VALUES (?, ?, ?, ?, ?, ?)",
        [
            $patientId,
            $doctorId,
            date('Y-m-d H:i:s', strtotime('+1 week 10:00 AM')),
            30,
            'pending',
            $receptionistUserId
        ]
    );
    echo "✓ Sample appointment created (ID: $appointmentId)\n\n";

    // 7. Create sample invoice
    echo "💰 Creating sample invoice...\n";

    $invoiceNumber = 'INV-' . date('Ym') . '-00001';
    $invoiceId = $db->insert(
        "INSERT INTO invoices (invoice_number, patient_id, appointment_id, total_amount, status) 
         VALUES (?, ?, ?, ?, ?)",
        [$invoiceNumber, $patientId, $appointmentId, 50.00, 'unpaid']
    );
    echo "✓ Sample invoice created (ID: $invoiceId)\n\n";

    // 8. Create sample medical record
    echo "📋 Creating sample medical record...\n";

    $recordId = $db->insert(
        "INSERT INTO medical_records (patient_id, doctor_id, visit_date, diagnosis, treatment) 
         VALUES (?, ?, ?, ?, ?)",
        [
            $patientId,
            $doctorId,
            date('Y-m-d H:i:s'),
            'Routine checkup - No abnormalities detected',
            'Recommend regular exercise and healthy diet'
        ]
    );
    echo "✓ Sample medical record created (ID: $recordId)\n\n";

    echo "✅ Database seeding completed successfully!\n\n";
    echo "📊 Summary:\n";
    echo "   - Users created: 4 (Admin, Doctor, Receptionist, Patient)\n";
    echo "   - Specialties: " . count($specialties) . "\n";
    echo "   - Doctor profiles: 1\n";
    echo "   - Patient profiles: 1\n";
    echo "   - Sample appointment: 1\n";
    echo "   - Sample invoice: 1\n";
    echo "   - Sample medical record: 1\n\n";

    echo "🔑 Default Credentials:\n";
    echo "   Admin:        admin@healthcare.local / Demo@1234\n";
    echo "   Doctor:       doctor@healthcare.local / Demo@1234\n";
    echo "   Receptionist: receptionist@healthcare.local / Demo@1234\n";
    echo "   Patient:      patient@healthcare.local / Demo@1234\n\n";

} catch (Exception $e) {
    echo "❌ Error during seeding: " . $e->getMessage() . "\n";
    exit(1);
}
?>
