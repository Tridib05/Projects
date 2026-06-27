<?php
// README - Setup Instructions
?>
# Student Academic Performance Evaluation System

## Overview
A comprehensive PHP-based system for managing and evaluating student academic performance with advanced features including:
- Student Management
- Performance Tracking
- Grade Calculation
- GPA Management
- Reporting & Analytics
- Data Visualization

## Project Structure

```
/Php/
├── config/
│   ├── database.php          # Database configuration
│   └── init.sql              # Database initialization script
├── models/
│   ├── Database.php          # Database wrapper class
│   ├── Student.php           # Student model
│   ├── Performance.php       # Performance model
│   ├── Subject.php           # Subject model
├── includes/
│   ├── header.php            # Common header
│   ├── footer.php            # Common footer
├── controllers/              # (Future expansion)
├── assets/
│   ├── css/
│   │   └── style.css         # Main stylesheet
│   ├── js/
│   │   └── script.js         # JavaScript utilities
│   └── img/                  # Images folder
├── views/
│   └── (View files)
├── index.php                 # Dashboard
├── students.php              # Student management
├── students_form.php         # Student form
├── performance.php           # Performance management
├── student_performance.php   # Individual student performance
├── reports.php               # Performance reports
└── db.php                    # (Legacy)
```

## Installation & Setup

### Step 1: Database Setup
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create new database or run the init.sql script
3. Update config/database.php with your credentials:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'student_performance_db');
```

### Step 2: Access the Application
- Navigate to: http://localhost/Php/
- Default Admin Credentials:
  - Username: `admin`
  - Email: `admin@school.com`
  - Password: (Check init.sql for default password - use hashed password generator)

### Step 3: Configure Base URL
Update BASE_URL in config/database.php if needed.

## Database Schema

### Tables Created:
1. **users** - Admin/Teacher accounts
2. **students** - Student information
3. **subjects** - Subject/Course information
4. **performance** - Student performance records
5. **attendance** - Attendance tracking
6. **reports** - Generated reports
7. **audit_log** - System activity logs

## Features

### Dashboard
- Student statistics
- Performance overview
- Grade distribution charts
- Recent evaluations

### Student Management
- Add/Edit/Delete students
- Search functionality
- Student profiles
- Contact information tracking

### Performance Management
- Add evaluation records
- Automatic grade calculation
- Mark components:
  - Assignment (10%)
  - Midterm (20%)
  - Practical (20%)
  - Final (50%)
- Grade scale: A+ (90+), A (80+), B (70+), C (60+), D (50+), F (<50)

### Reports
- Performance reports with filters
- Semester-wise reports
- Academic year reports
- Print & Export functionality (CSV)

### Individual Performance
- Student-specific performance view
- CGPA calculation
- Grade distribution
- Performance charts
- Historical records

## Grade Calculation
Total Marks = (Assignment × 0.1) + (Midterm × 0.2) + (Practical × 0.2) + (Final × 0.5)

## Technologies Used
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, Bootstrap 5
- **JavaScript**: Chart.js, jQuery, DataTables
- **Icons**: Font Awesome 6.4.0

## API/Libraries Used
- Bootstrap 5.3.0
- Chart.js for analytics
- jQuery 3.6.0
- DataTables 1.13.4
- Font Awesome Icons

## Security Features
- SQL injection prevention (Prepared Statements)
- Session management
- Password hashing (bcrypt)
- Input validation & sanitization

## File Permissions
Ensure write permissions on:
- `/config/` (for database configuration)
- `/assets/` (for uploads - if enabled)

## Troubleshooting

### Database Connection Error
- Check database credentials in config/database.php
- Ensure MySQL is running
- Verify database exists

### Charts Not Loading
- Check if Chart.js is loaded
- Verify CDN access
- Check browser console for errors

### Data Not Displaying
- Run init.sql to create tables
- Check database permissions
- Verify SQL queries in browser console

## Future Enhancements
- User authentication system
- Email notifications
- SMS alerts
- Student portal
- Parent portal
- Attendance management
- Advanced analytics
- API development

## Support
For issues or questions, please contact the development team.

---
Last Updated: 2026
