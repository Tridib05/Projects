## Quick Start Guide

### Initial Setup

1. **Database Configuration**
   - Open `config/database.php`
   - Update database credentials
   - Import `config/init.sql` to phpMyAdmin

2. **First Access**
   - Navigate to `http://localhost/Php/`
   - Login with admin credentials (from init.sql)

### Key Features Overview

**Dashboard (/)** 
- View all statistics
- See recent evaluations
- Monitor system overview

**Student Management (/students.php)**
- View all students
- Add new student (/students_form.php)
- Edit student information
- View individual performance
- Search students

**Performance (/performance.php)**
- Add performance records
- View all records with grades
- Automatic calculation

**Reports (/reports.php)**
- Filter by semester/year
- Print reports
- Export to CSV

**Individual Performance (/student_performance.php?id=X)**
- Complete student history
- CGPA calculation
- Grade charts
- Performance analysis

### Grading System

| Grade | Range | GPA |
|-------|-------|-----|
| A+    | 90+   | 4.0 |
| A     | 80-89 | 3.75|
| B     | 70-79 | 3.5 |
| C     | 60-69 | 3.0 |
| D     | 50-59 | 2.0 |
| F     | <50   | 0.0 |

### Mark Components

- Assignment: 10%
- Midterm: 20%
- Practical: 20%
- Final: 50%

### File Structure

- `config/` - Configuration files
- `models/` - Database models
- `includes/` - Shared templates
- `assets/` - CSS, JS, Images
- `*.php` - Main pages

### Required Extensions

- PHP: MySQLi, PDO
- MySQL: 5.7+

### Tips

- Use Chrome/Firefox for best experience
- Backup database regularly
- Test with sample data first
- Review logs for debugging

### Troubleshooting

**Connection Error**: Check database.php credentials
**Data Missing**: Import init.sql properly
**Charts Not Loading**: Enable JavaScript
**Can't Delete**: Check dependencies

### Next Steps

1. Create subjects in database
2. Add student records
3. Add performance evaluations
4. Generate reports
5. Analyze performance

### Contact

For support, check README.md or contact developer.
