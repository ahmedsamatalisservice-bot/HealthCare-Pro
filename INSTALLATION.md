# HealthCare Pro - Installation & Setup Guide

## 📋 Requirements

- **PHP**: 8.0 or higher
- **MySQL**: 5.7 or higher (8.0 recommended)
- **Web Server**: Apache with mod_rewrite enabled or Nginx
- **Composer**: (Optional, for dependency management)

## 🚀 Quick Start

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/HealthCare-Pro.git
cd HealthCare-Pro
```

### 2. Set Up Environment Variables

```bash
cp .env.example .env
```

Edit `.env` with your database credentials:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=healthcare_pro
DB_USER=root
DB_PASS=your_password
APP_URL=http://localhost:8000
```

### 3. Create Database

#### Option A: Using MySQL Command Line

```bash
mysql -u root -p < database.sql
```

#### Option B: Using phpMyAdmin

1. Open phpMyAdmin
2. Create a new database: `healthcare_pro`
3. Import `database.sql` file

### 4. Set File Permissions

```bash
chmod -R 755 app/
chmod -R 755 public/
chmod -R 755 storage/
chmod -R 755 config/
```

### 5. Configure Web Server

#### Apache (.htaccess)

Create `/public/.htaccess`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?/$1 [L]
</IfModule>
```

#### Nginx

Add to your server block:

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 6. Start Development Server (PHP Built-in)

```bash
cd public
php -S localhost:8000
```

Access the application at: **http://localhost:8000**

## 🔐 Default Login Credentials

**Note**: These should be changed in production!

### Super Admin
- **Email**: admin@healthcare.local
- **Password**: Demo@1234

### Demo Doctor
- **Email**: doctor@healthcare.local
- **Password**: Demo@1234

### Demo Patient
- **Email**: patient@healthcare.local
- **Password**: Demo@1234

### Demo Receptionist
- **Email**: receptionist@healthcare.local
- **Password**: Demo@1234

## 📁 Project Structure

```
HealthCare-Pro/
├── app/
│   ├── controllers/          # Request handlers
│   ├── models/              # Database models
│   ├── views/               # HTML templates
│   ├── helpers/             # Utility classes
│   └── middleware/          # Authentication & authorization
├── config/
│   ├── config.php          # App configuration
│   └── database.php        # Database settings
├── public/
│   ├── index.php           # Entry point
│   └── assets/
│       ├── css/
│       ├── js/
│       └── images/
├── storage/
│   ├── logs/               # Application logs
│   └── uploads/            # User uploads
├── database.sql            # Database schema
├── .env                    # Environment variables
└── README.md              # This file
```

## 🛠️ Module Overview

### 1. **Authentication & Authorization**
- User registration and login
- Role-based access control (RBAC)
- Session management
- Password hashing with bcrypt

### 2. **Patient Management**
- Patient registration and profiles
- Medical history tracking
- Search and filter patients
- Emergency contact information

### 3. **Doctor Management**
- Doctor profiles and specialties
- Availability schedules
- Consultation fees
- Medical licenses and qualifications

### 4. **Appointment System**
- Book appointments
- Appointment status tracking
- Conflict detection
- Doctor availability verification

### 5. **Electronic Medical Records (EMR)**
- Create and manage medical records
- Diagnosis and treatment tracking
- Prescription management
- Medical history timeline

### 6. **Billing System**
- Generate invoices
- Payment tracking
- Outstanding balance reports
- Monthly revenue reports
- Multiple payment methods support

### 7. **Dashboard**
- Admin: System overview, analytics, user management
- Doctor: Schedule, patient list, medical records
- Receptionist: Appointments, patient management
- Patient: Health records, appointments, invoices

## 🔑 User Roles & Permissions

| Role | Features |
|------|----------|
| **Super Admin** | Full system access, user management, reports |
| **Doctor** | View appointments, medical records, prescriptions |
| **Receptionist** | Manage appointments, patient check-in |
| **Patient** | Book appointments, view medical records, pay bills |

## 📝 Database Schema

### Core Tables

- `roles` - User roles definition
- `users` - User accounts (all roles)
- `patients` - Patient profiles
- `doctors` - Doctor profiles
- `specialties` - Medical specialties
- `doctor_schedules` - Doctor availability
- `appointments` - Appointment bookings
- `medical_records` - EMR records
- `prescriptions` - Medication prescriptions
- `invoices` - Billing invoices
- `payments` - Payment records
- `audit_logs` - System audit trail

## 🔒 Security Features

- ✅ Password hashing with bcrypt
- ✅ SQL injection prevention (prepared statements)
- ✅ CSRF protection (implement tokens)
- ✅ Session timeout (1440 minutes)
- ✅ Role-based access control
- ✅ Audit logging for sensitive operations
- ✅ Secure password requirements

## 📊 API Endpoints (Future)

The following endpoints will be implemented:

```
POST   /api/auth/login
POST   /api/auth/register
POST   /api/auth/logout
GET    /api/patients
POST   /api/patients
GET    /api/patients/{id}
PUT    /api/patients/{id}
GET    /api/doctors
GET    /api/appointments
POST   /api/appointments
PUT    /api/appointments/{id}
GET    /api/invoices
GET    /api/medical-records
```

## 🐛 Troubleshooting

### Database Connection Error

```
Error: SQLSTATE[HY000]: General error: 2006 MySQL server has gone away
```

**Solution**: Verify MySQL is running and credentials in `.env` are correct.

### Permission Denied Error

```
Permission denied on storage/logs
```

**Solution**: Run `chmod -R 755 storage/`

### Blank Page

**Solution**: 
1. Check `.env` file exists and is properly configured
2. Ensure PHP extensions are enabled (PDO, MySQL)
3. Check `storage/logs/error.log` for detailed errors

## 📧 Email Configuration

To enable email notifications:

1. Update `.env` with your SMTP details
2. Implement email service in `app/helpers/Email.php`
3. Use it in appropriate controllers

## 🚀 Production Deployment

### Pre-deployment Checklist

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Generate strong passwords for demo accounts or remove them
- [ ] Enable HTTPS/SSL
- [ ] Set proper file permissions (644 for files, 755 for directories)
- [ ] Run database backups
- [ ] Set up error logging
- [ ] Configure firewall rules
- [ ] Enable CORS if needed

### Deployment Steps

```bash
# 1. Upload files via FTP/Git
# 2. Create database on production server
# 3. Import database.sql
# 4. Create .env file with production credentials
# 5. Set appropriate permissions
# 6. Test all functionality
# 7. Set up automated backups
# 8. Monitor error logs
```

## 📞 Support & Contribution

For bugs and feature requests, please open an issue or submit a pull request.

## 📄 License

This project is licensed under the MIT License - see LICENSE file for details.

---

**Version**: 1.0.0
**Last Updated**: 2026-06-20
**Maintainer**: HealthCare Pro Team
