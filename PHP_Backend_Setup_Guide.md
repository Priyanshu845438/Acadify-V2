# Complete PHP Backend Setup Guide for Local Development

This guide will help you set up the TechSite PHP backend system on your local laptop for development. Follow these steps in order to get everything working.

## 📋 Prerequisites

Before starting, ensure you have administrative access to your computer to install software.

## 🛠️ Step 1: Install Required Software

### 1.1 Install XAMPP (Recommended for Beginners)

XAMPP provides Apache, PHP, and MySQL in one package.

**For Windows:**
1. Download XAMPP from: https://www.apachefriends.org/download.html
2. Choose the latest PHP 8.x version
3. Run the installer as Administrator
4. Select these components:
   - ✅ Apache
   - ✅ MySQL  
   - ✅ PHP
   - ✅ phpMyAdmin
5. Install to default location: `C:\xampp`

**For macOS:**
1. Download XAMPP for macOS from the same link
2. Open the `.dmg` file and drag XAMPP to Applications
3. Open Terminal and run: `sudo /Applications/XAMPP/xamppfiles/xampp start`

**For Linux (Ubuntu/Debian):**
```bash
# Download XAMPP
wget https://www.apachefriends.org/xampp-files/8.2.12/xampp-linux-x64-8.2.12-0-installer.run

# Make it executable
chmod +x xampp-linux-x64-8.2.12-0-installer.run

# Install
sudo ./xampp-linux-x64-8.2.12-0-installer.run

# Start services
sudo /opt/lampp/lampp start
```

### 1.2 Alternative: Manual Installation

If you prefer separate installations:

**Install PHP:**
- Windows: Download from https://windows.php.net/download/
- macOS: `brew install php` (requires Homebrew)
- Linux: `sudo apt install php php-mysql php-curl php-json`

**Install Apache:**
- Windows: Download from https://httpd.apache.org/
- macOS: `brew install httpd`
- Linux: `sudo apt install apache2`

**Install PostgreSQL:**
- Windows: Download from https://www.postgresql.org/download/
- macOS: `brew install postgresql`
- Linux: `sudo apt install postgresql postgresql-contrib`

## 🗂️ Step 2: Set Up Project Directory

### 2.1 Choose Project Location

**For XAMPP:**
- Windows: `C:\xampp\htdocs\techsite`
- macOS: `/Applications/XAMPP/xamppfiles/htdocs/techsite`
- Linux: `/opt/lampp/htdocs/techsite`

**For Manual Installation:**
- Create folder: `C:\projects\techsite` (Windows) or `~/projects/techsite` (Mac/Linux)

### 2.2 Copy Project Files

1. Create the main project directory
2. Copy all project files maintaining this structure:

```
techsite/
├── index.html
├── assets/
│   ├── css/
│   ├── js/
│   └── img/
├── components/
├── pages/
├── config/
│   ├── database.php
│   └── email.php
├── handlers/
│   ├── contact_handler.php
│   ├── partner_handler.php
│   └── quote_handler.php
└── admin/
    ├── login.php
    └── dashboard.php
```

## 🗄️ Step 3: Set Up Database

### 3.1 Start Database Service

**XAMPP:**
1. Open XAMPP Control Panel
2. Click "Start" next to MySQL
3. Wait for green "Running" status

**Manual PostgreSQL:**
- Windows: Start PostgreSQL service from Services
- macOS/Linux: `sudo systemctl start postgresql`

### 3.2 Create Database

**Using phpMyAdmin (XAMPP MySQL):**
1. Open browser: http://localhost/phpmyadmin
2. Click "New" to create database
3. Database name: `techsite_db`
4. Click "Create"

**Using PostgreSQL:**
```sql
-- Connect to PostgreSQL
psql -U postgres

-- Create database
CREATE DATABASE techsite_db;

-- Create user (optional)
CREATE USER techsite_user WITH PASSWORD 'your_password';
GRANT ALL PRIVILEGES ON DATABASE techsite_db TO techsite_user;

-- Exit
\q
```

### 3.3 Create Required Tables

Run these SQL commands in your database:

```sql
-- Contact entries table
CREATE TABLE contact_entries (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Partner entries table
CREATE TABLE partner_entries (
    id SERIAL PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    business_type VARCHAR(255),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Quote requests table
CREATE TABLE quote_requests (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    company VARCHAR(255),
    service_type VARCHAR(255),
    project_budget VARCHAR(100),
    project_timeline VARCHAR(100),
    project_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin users table
CREATE TABLE admin_users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user
INSERT INTO admin_users (email, password_hash) VALUES 
('acadify.online@gmail.com', '$2y$10$sicHE8V/CfyTU3vDBtPguOTeVjyunprOi38KqqHtlvADRWsKotGYO');
```

## ⚙️ Step 4: Configure Database Connection

### 4.1 Update config/database.php

Edit the database configuration file:

```php
<?php
// Start session for admin functionality
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function getDatabaseConnection() {
    // Database configuration
    $host = 'localhost';
    $dbname = 'techsite_db';
    $username = 'your_username';  // Update this
    $password = 'your_password';  // Update this
    $port = '5432'; // For PostgreSQL, use '3306' for MySQL
    
    try {
        // For PostgreSQL
        $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
        
        // For MySQL (alternative)
        // $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die('Database connection failed: ' . $e->getMessage());
    }
}
?>
```

### 4.2 Database Connection Parameters

**For MySQL (XAMPP default):**
```php
$host = 'localhost';
$dbname = 'techsite_db';
$username = 'root';
$password = '';  // Empty for default XAMPP
$port = '3306';

// Connection string
$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
```

**For PostgreSQL:**
```php
$host = 'localhost';
$dbname = 'techsite_db';
$username = 'postgres';
$password = 'your_password';
$port = '5432';

// Connection string
$pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
```

## 🌐 Step 5: Configure Web Server

### 5.1 XAMPP Configuration

**Apache Document Root:**
1. Open XAMPP Control Panel
2. Click "Config" next to Apache
3. Select "Apache (httpd.conf)"
4. Find: `DocumentRoot "C:/xampp/htdocs"`
5. Change to your project path if needed

**Virtual Host (Optional):**
1. Edit: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
2. Add:
```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/techsite"
    ServerName techsite.local
    <Directory "C:/xampp/htdocs/techsite">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```
3. Edit hosts file: `C:\Windows\System32\drivers\etc\hosts`
4. Add: `127.0.0.1 techsite.local`

### 5.2 Manual Apache Configuration

Edit Apache configuration file (`httpd.conf`):

```apache
# Enable PHP
LoadModule php_module modules/libphp.so
AddHandler php-script php
AddType text/html php

# Document root
DocumentRoot "/path/to/your/techsite"

# Directory permissions
<Directory "/path/to/your/techsite">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

## 📧 Step 6: Email Configuration (Optional)

The system uses Replit's email service by default. For local testing without email:

### 6.1 Disable Email Features (Testing Only)

In `config/email.php`, modify the constructor:

```php
public function __construct() {
    try {
        $this->authToken = $this->getAuthToken();
    } catch (Exception $e) {
        // For local testing, email will be disabled
        error_log("Email disabled in local environment: " . $e->getMessage());
        $this->authToken = null;
    }
}
```

### 6.2 Alternative: Use Local SMTP

Install and configure a local SMTP server:

**For Testing (Fake SMTP):**
- Install MailHog: https://github.com/mailhog/MailHog
- Configure PHP to use localhost:1025

## 🚀 Step 7: Start and Test

### 7.1 Start Services

**XAMPP:**
1. Open XAMPP Control Panel
2. Start Apache
3. Start MySQL
4. Check for green "Running" status

**Manual Installation:**
```bash
# Start Apache
sudo systemctl start apache2

# Start PostgreSQL
sudo systemctl start postgresql

# Or for development
php -S localhost:8000
```

### 7.2 Test Installation

1. **Access Website:**
   - XAMPP: http://localhost/techsite
   - Manual: http://localhost:8000
   - Virtual Host: http://techsite.local

2. **Test Admin Panel:**
   - URL: http://localhost/techsite/admin
   - Login: acadify.online@gmail.com
   - Password: LoveDay@1103

3. **Test Forms:**
   - Fill out contact form
   - Try partnership form
   - Submit quote request
   - Check admin panel for entries

### 7.3 Common URLs

```
Homepage:        http://localhost/techsite/
Admin Login:     http://localhost/techsite/admin/
Admin Dashboard: http://localhost/techsite/admin/dashboard.php
Contact Form:    http://localhost/techsite/pages/about/contact.html
Partnership:     http://localhost/techsite/pages/about/partners-alliances.html
```

## 🐛 Step 8: Troubleshooting

### 8.1 Common Issues

**"Database connection failed":**
- Check database credentials in `config/database.php`
- Ensure database service is running
- Verify database name exists

**"404 Not Found":**
- Check Apache document root
- Verify file permissions
- Ensure `.htaccess` is readable

**"500 Internal Server Error":**
- Check Apache error logs
- Enable PHP error display:
```php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

**Forms not submitting:**
- Check file permissions on handlers/ directory
- Ensure PHP can write to log files
- Verify database connection

### 8.2 Useful Commands

**Check PHP Version:**
```bash
php --version
```

**Test PHP Configuration:**
```bash
php -m  # Show loaded modules
php --ini  # Show configuration files
```

**Check Apache Status:**
```bash
# Linux/macOS
sudo systemctl status apache2

# Windows
net start apache
```

### 8.3 Log Files Locations

**XAMPP:**
- Apache: `C:\xampp\apache\logs\error.log`
- PHP: `C:\xampp\php\logs\php_error_log`

**Manual Installation:**
- Apache: `/var/log/apache2/error.log`
- PHP: `/var/log/php_errors.log`

## 🔧 Step 9: Development Tips

### 9.1 Enable Debug Mode

Add to beginning of PHP files during development:
```php
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

### 9.2 Useful Tools

**Database Management:**
- phpMyAdmin (MySQL): http://localhost/phpmyadmin
- Adminer: http://localhost/adminer.php
- pgAdmin (PostgreSQL): https://www.pgadmin.org/

**Code Editor Extensions:**
- PHP Intelephense
- PHP Debug
- HTML/CSS/JS support

### 9.3 File Permissions

**Linux/macOS:**
```bash
# Make files readable
chmod -R 644 /path/to/techsite

# Make directories executable
chmod -R 755 /path/to/techsite

# Make handlers executable
chmod -R 755 /path/to/techsite/handlers
```

## ✅ Step 10: Verification Checklist

Before going live, verify:

- [ ] Website loads at http://localhost/techsite
- [ ] Admin login works with provided credentials
- [ ] Contact form saves to database
- [ ] Partnership form saves to database  
- [ ] Quote modal opens and submits successfully
- [ ] Admin dashboard shows all form submissions
- [ ] No PHP errors in error logs
- [ ] Database connections work properly
- [ ] All static assets (CSS, JS, images) load correctly

## 🔒 Security Notes

**For Production Deployment:**

1. **Change Default Passwords:**
   - Database user password
   - Admin panel password

2. **Secure File Permissions:**
   - Make config files non-readable from web
   - Set proper directory permissions

3. **Enable HTTPS:**
   - Install SSL certificate
   - Force HTTPS redirects

4. **Database Security:**
   - Use environment variables for credentials
   - Enable database firewall rules
   - Regular backups

## 📞 Support

If you encounter issues during setup:

1. Check error logs first
2. Verify all dependencies are installed
3. Ensure correct file permissions
4. Test database connectivity separately
5. Review this guide step-by-step

## 📝 Final Notes

- Keep backups of your configuration files
- Test thoroughly before deploying to production
- Monitor error logs regularly
- Update PHP and dependencies regularly for security

This guide covers the complete setup process for local PHP development. Follow each step carefully and refer back to this documentation when troubleshooting issues.