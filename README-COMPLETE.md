# HealthCare Pro - Healthcare Management System

A comprehensive, production-ready **Healthcare Management System (HMS)** built with **PHP 8+, MySQL, HTML5, CSS3, and JavaScript**. This application enables seamless management of patients, doctors, appointments, medical records, and billing.

## ✨ Features

### 👥 User Management
- Multi-role authentication (Super Admin, Doctor, Receptionist, Patient)
- Secure login/logout with session management
- Role-based access control (RBAC)
- User profile management
- Password hashing with bcrypt

### 🏥 Patient Management
- Patient registration and profile creation
- Medical history tracking
- Patient search and filtering
- Emergency contact information
- Blood type and demographics

### 👨‍⚕️ Doctor Management
- Doctor profiles with specialties
- Availability scheduling
- Consultation fees management
- Medical licenses and qualifications
- Doctor directory with filtering

### 📅 Appointment System
- Book appointments with doctors
- Appointment status tracking (pending, confirmed, rejected, cancelled, completed)
- Conflict detection for doctor schedules
- Calendar view of appointments
- Appointment reminders

### 📋 Electronic Medical Records (EMR)
- Create and manage patient medical records
- Store diagnoses and treatments
- Prescription management
- Medical history timeline
- Document attachments support

### 💰 Billing System
- Generate professional invoices
- Payment tracking with multiple methods
- Outstanding balance reports
- Monthly and annual revenue analytics
- Financial reports and dashboards

### 📊 Dashboards
- **Admin**: System overview, analytics, user management
- **Doctor**: Schedule, patient list, medical records
- **Receptionist**: Appointment management, patient check-in
- **Patient**: Health records, appointments, billing

### 🔒 Security
- Prepared statements to prevent SQL injection
- Password hashing with bcrypt
- Session timeout protection
- CSRF token support
- Audit logging for sensitive operations
- Role-based access control

## 🛠️ Tech Stack

- **Backend**: PHP 8.0+
- **Database**: MySQL 5.7+ / MariaDB
- **Frontend**: HTML5, CSS3, JavaScript (vanilla)
- **Architecture**: MVC (Model-View-Controller)
- **Server**: Apache (recommended) / Nginx

## 📦 Installation

### Requirements
- PHP 8.0 or higher
- MySQL 5.7 or higher
- Apache with mod_rewrite (or Nginx)
- 20MB disk space minimum

### Quick Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/HealthCare-Pro.git
   cd HealthCare-Pro
   ```

2. **Configure environment**
   ```bash
   cp .env.example .env
   # Edit .env with your database credentials
   ```

3. **Create database**
   ```bash
   mysql -u root -p < database.sql
   ```

4. **Set permissions**
   ```bash
   chmod -R 755 app/ public/ storage/ config/
   ```

5. **Start development server**
   ```bash
   cd public
   php -S localhost:8000
   ```

6. **Access application**
   - URL: `http://localhost:8000`
   - Admin: admin@healthcare.local / Demo@1234

See [INSTALLATION.md](INSTALLATION.md) for detailed setup instructions.

## 📁 Project Structure

```
HealthCare-Pro/
├── app/
│   ├── controllers/          # Business logic controllers
│   ├── models/              # Database models
│   ├── views/               # HTML templates
│   ├── helpers/             # Utility classes
│   └── middleware/          # Authentication & authorization
├── config/                  # Configuration files
├── public/
│   ├── index.php           # Application entry point
│   ├── routes.php          # Route definitions
│   └── assets/             # CSS, JS, images
├── storage/
│   ├── logs/               # Application logs
│   └── uploads/            # File uploads
├── database.sql            # Database schema
├── .env                    # Environment variables
└── INSTALLATION.md         # Setup guide
```

## 🔑 Default Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@healthcare.local | Demo@1234 |
| Doctor | doctor@healthcare.local | Demo@1234 |
| Receptionist | receptionist@healthcare.local | Demo@1234 |
| Patient | patient@healthcare.local | Demo@1234 |

⚠️ **Change these credentials in production!**

## 📖 Database Schema

### Core Tables
- `roles` - User roles
- `users` - User accounts
- `patients` - Patient profiles
- `doctors` - Doctor profiles
- `specialties` - Medical specialties
- `doctor_schedules` - Doctor availability
- `appointments` - Appointment bookings
- `medical_records` - Electronic health records
- `prescriptions` - Medications & prescriptions
- `invoices` - Billing invoices
- `payments` - Payment records
- `audit_logs` - System audit trail

## 🚀 Features Roadmap

- [ ] API endpoints (REST/GraphQL)
- [ ] SMS/Email notifications
- [ ] Video consultations
- [ ] Lab test management
- [ ] Prescription e-delivery
- [ ] Mobile app (React Native)
- [ ] Advanced analytics dashboard
- [ ] Insurance integration
- [ ] Telemedicine support
- [ ] Multi-language support

## 🐛 Troubleshooting

### Database Connection Error
```
Error: SQLSTATE[HY000]: General error: 2006 MySQL server has gone away
```
**Solution**: Verify MySQL is running and check credentials in `.env`

### Permission Denied
```
Permission denied on storage/logs
```
**Solution**: Run `chmod -R 755 storage/`

### Blank Page
**Solution**: Check `storage/logs/error.log` for detailed error messages

See [INSTALLATION.md](INSTALLATION.md) for more troubleshooting tips.

## 🔐 Security Features

- ✅ Password hashing with bcrypt (10 rounds)
- ✅ SQL injection prevention (prepared statements)
- ✅ CSRF protection tokens
- ✅ Session timeout (24 hours default)
- ✅ Role-based access control (RBAC)
- ✅ Audit logging for sensitive operations
- ✅ Secure password requirements (min 8 chars, uppercase, lowercase, number, special char)

## 📝 Code Quality

- Well-documented code with inline comments
- Consistent naming conventions
- Modular and reusable components
- Error handling and logging
- Input validation and sanitization

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see [LICENSE](LICENSE) file for details.

## 📞 Support

For issues, questions, or suggestions:
- 📧 Email: support@healthcare-pro.local
- 💬 GitHub Issues: [Report a bug](../../issues)
- 📚 Documentation: [INSTALLATION.md](INSTALLATION.md)

## 🎉 Acknowledgments

- Built with ❤️ for healthcare professionals
- Inspired by real-world healthcare management needs
- Community contributions welcome

---

**Version**: 1.0.0  
**Last Updated**: June 20, 2026  
**Maintainer**: HealthCare Pro Team  
**License**: MIT

**Made with ❤️ for better healthcare management**
