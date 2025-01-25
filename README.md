# Student Management System

## Prerequisites
- PHP 7.4+
- MySQL
- Web Server (Apache/Nginx)

## Setup Instructions

### Database Configuration
1. Create a MySQL database:
```sql
CREATE DATABASE student_management;
```

### Configuration
- Open the PHP file
- Modify database connection settings if needed:
```php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'student_management';
```

### Installation
1. Clone the repository
2. Place the file in your web server's document root
3. Ensure MySQL is running
4. Open in browser

### Features
- Add students
- Edit student details
- Delete students
- View student list

### Troubleshooting
- Verify database connection
- Check PHP and MySQL versions
- Ensure web server is configured correctly

## Security Note
This is a basic implementation. For production, implement:
- Input validation
- Prepared statements (already used)
- User authentication
