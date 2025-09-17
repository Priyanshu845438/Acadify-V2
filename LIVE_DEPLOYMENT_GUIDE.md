# Live Deployment Guide - Hostinger SMTP Email Integration

## Overview
This guide provides step-by-step instructions for deploying the new professional email system to your live website. The changes include transitioning from the existing system to a production-ready Hostinger SMTP email service with PostgreSQL database integration.

## ⚠️ **IMPORTANT - READ FIRST**
- **Backup your live database** before making any changes
- **Test on staging environment** if available
- **Deploy during low-traffic hours** to minimize impact
- **Have rollback plan ready** (documented below)

---

## 📋 Pre-Deployment Checklist

### 1. Environment Requirements
- [ ] Hostinger SMTP credentials available
- [ ] Database credentials (MySQL/PostgreSQL)
- [ ] PHP 7.4+ with OpenSSL and cURL modules
- [ ] Composer installed for dependency management
- [ ] FTP/SFTP access to live server
- [ ] Ability to set environment variables on server

### 2. Critical Security Update - Database Configuration
**IMPORTANT**: The database configuration has been updated to use environment variables instead of hardcoded credentials for security.

**Required Environment Variables**:
```bash
# Database Configuration
DB_HOST=your_database_host
DB_NAME=your_database_name
DB_USER=your_database_username
DB_PASS=your_database_password
DB_PORT=3306  # or 5432 for PostgreSQL

# SMTP Configuration  
SMTP_HOST=smtp.hostinger.com
SMTP_PORT=465
SMTP_USERNAME=your_email@yourdomain.com
SMTP_PASSWORD=your_smtp_password
SMTP_FROM_NAME=Your Company Name
ADMIN_EMAIL=admin@yourdomain.com
```

### 3. Backup Current System
```bash
# Backup database
mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql

# Backup website files
tar -czf website_backup_$(date +%Y%m%d).tar.gz /path/to/website/
```

---

## 🔧 File Changes Required

### 1. Replace `config/email.php`
**Action**: Replace entire file with the new version

**Current file**: Uses basic PHP mail() or simple SMTP
**New file**: Professional PHPMailer-based SMTP system

**Key changes**:
- Secure SMTP authentication
- Professional email templates
- Configurable via environment variables
- Proper error handling and logging

### 2. Install Dependencies
**Action**: Add PHPMailer dependency

**If using Composer** (Recommended):
```bash
cd /path/to/your/website
composer require phpmailer/phpmailer
```

**If not using Composer**:
1. Download PHPMailer files from the project
2. Copy the `vendor/` directory to your website root
3. Ensure the autoload.php file is present

### 3. Update Handler Files
**Files to update**:
- `handlers/contact_handler.php`
- `handlers/partner_handler.php` 
- `handlers/quote_handler.php`

**Changes needed**:
- Replace `new ReplitMail()` with `new EmailService()`
- No other code changes required (backward compatible)

**CRITICAL**: Ensure all handlers use the correct class name `EmailService` as defined in config/email.php

---

## 🗄️ Database Migration

### For PostgreSQL Users
```sql
-- Create required tables
CREATE TABLE IF NOT EXISTS contact_entries (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS partner_entries (
    id SERIAL PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    business_type VARCHAR(255),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS quote_requests (
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
```

### For MySQL Users (Current System)
```sql
-- Create required tables
CREATE TABLE IF NOT EXISTS contact_entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS partner_entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    business_type VARCHAR(255),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS quote_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
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
```

---

## 🔑 Environment Variables Setup

### Method 1: Using .env file (Recommended)
Create `.env` file in your website root:
```env
# SMTP Configuration
SMTP_HOST=smtp.hostinger.com
SMTP_PORT=465
SMTP_ENCRYPTION=ssl
SMTP_USERNAME=your-email@yourdomain.com
SMTP_PASSWORD=your-email-password
ADMIN_EMAIL=your-admin@yourdomain.com
FROM_NAME=Your Company Name

# Database Configuration (if using PostgreSQL)
DATABASE_URL=postgresql://username:password@host:port/database
```

### Method 2: Server Environment Variables
**cPanel/Hosting Panel**:
1. Go to "Environment Variables" or "PHP Configuration"
2. Add the variables listed above

**Via .htaccess** (Alternative):
```apache
# Add to .htaccess file
SetEnv SMTP_HOST "smtp.hostinger.com"
SetEnv SMTP_PORT "465"
SetEnv SMTP_ENCRYPTION "ssl"
SetEnv SMTP_USERNAME "your-email@yourdomain.com"
SetEnv SMTP_PASSWORD "your-email-password"
SetEnv ADMIN_EMAIL "your-admin@yourdomain.com"
SetEnv FROM_NAME "Your Company Name"
```

---

## 📧 Email Configuration Details

### Hostinger SMTP Settings
```
Host: smtp.hostinger.com
Port: 465 (SSL) or 587 (TLS)
Encryption: SSL or TLS
Authentication: Required
```

### Email Account Setup
1. **Create email account** in Hostinger control panel
2. **Use authenticated email** - SMTP username must match sender email
3. **Enable SMTP** in email account settings
4. **Test credentials** before deployment

---

## 🚀 Step-by-Step Deployment

### Phase 1: Preparation
1. **Backup current system** (database + files)
2. **Create staging environment** (if possible)
3. **Prepare new files** on local development
4. **Test email credentials** locally

### Phase 2: Database Updates
1. **Connect to database** (phpMyAdmin/command line)
2. **Run table creation scripts** (see Database Migration section)
3. **Verify tables created** successfully
4. **Test database connectivity** from PHP

### Phase 3: File Updates
1. **Upload new `config/email.php`**
2. **Upload PHPMailer dependencies** (`vendor/` folder)
3. **Update handler files** (replace ReplitMail with HostingerMail)
4. **Set environment variables** or create `.env` file
5. **Update file permissions** if needed

### Phase 4: Testing
1. **Test website loading** (ensure no PHP errors)
2. **Test contact form** submission
3. **Test partnership form** 
4. **Test quote request** modal
5. **Verify emails received** (both admin notifications and user confirmations)

### Phase 5: Monitoring
1. **Monitor server error logs** for 24-48 hours
2. **Check email delivery** success rates
3. **Monitor form submissions** in database
4. **Test from different devices/browsers**

---

## 🧪 Testing Procedures

### Pre-Deployment Testing
```bash
# Test SMTP connection
php -r "
require_once 'vendor/autoload.php';
require_once 'config/email.php';
\$mail = new EmailService();
echo 'SMTP configuration loaded successfully';
"
```

### Live Testing Checklist
- [ ] Website loads without errors
- [ ] Contact form submits successfully  
- [ ] Email received by admin
- [ ] User confirmation email sent
- [ ] Database entries created correctly
- [ ] All form types work (contact/partner/quote)
- [ ] Email formatting appears professional
- [ ] Mobile devices work correctly

---

## 🔍 Troubleshooting Guide

### Common Issues & Solutions

**1. "Class 'HostingerMail' not found"**
```bash
# Solution: Verify autoload.php exists and is included
ls -la vendor/autoload.php
# Check email.php includes autoload
grep -n "require.*autoload" config/email.php
```

**2. "SMTP Authentication Failed"**  
- Verify email credentials in environment variables
- Check if email account exists in Hostinger
- Ensure SMTP is enabled for the email account
- Try port 587 with TLS instead of 465 with SSL

**3. "Database connection failed"**
- Verify database credentials
- Check if required tables exist
- Test database connection separately

**4. "Emails not sending"**
```bash
# Check PHP error logs
tail -f /path/to/php/error.log

# Test SMTP connectivity
telnet smtp.hostinger.com 465
```

**5. "Permission denied errors"**
```bash
# Fix file permissions
chmod 644 config/email.php
chmod -R 644 vendor/
chmod 755 handlers/
```

---

## 📊 Monitoring & Maintenance

### Log Files to Monitor
- **PHP Error Log**: `/path/to/php/error.log`  
- **Apache/Nginx Access Log**: Monitor form submissions
- **Email Queue**: Check hosting panel for email status

### Performance Monitoring
- **Form submission success rate**
- **Email delivery time** 
- **Database query performance**
- **Error frequency** in logs

### Regular Maintenance
- **Weekly**: Check error logs for email failures
- **Monthly**: Verify email deliverability 
- **Quarterly**: Update PHPMailer dependencies

---

## 🔄 Rollback Plan

### If Issues Occur

**Immediate Rollback (< 1 hour)**:
1. **Restore old `config/email.php`**
2. **Revert handler files** to use `ReplitMail`
3. **Remove new environment variables**
4. **Test basic functionality**

**Full Rollback (if needed)**:
```bash
# Restore database backup
mysql -u username -p database_name < backup_YYYYMMDD.sql

# Restore website files
tar -xzf website_backup_YYYYMMDD.tar.gz -C /path/to/website/

# Clear any caches
# (hosting-specific cache clearing steps)
```

### Rollback Testing
- [ ] Website loads correctly
- [ ] Contact forms work
- [ ] Emails send successfully
- [ ] Database connections work
- [ ] No PHP errors in logs

---

## 📞 Support & Resources

### Hostinger Support
- **Email SMTP Documentation**: Hostinger Knowledge Base
- **Support Ticket**: If SMTP issues persist
- **Email Account Management**: Hostinger Control Panel

### Technical Support
- **PHP/Database Issues**: Check hosting provider documentation
- **Email Deliverability**: Monitor with tools like Mail Tester
- **SSL/Security**: Verify certificate validity

---

## 📝 Post-Deployment Checklist

### 24 Hours After Deployment
- [ ] No PHP errors in logs
- [ ] Email delivery working consistently  
- [ ] All form submissions successful
- [ ] Database entries complete and accurate
- [ ] User experience remains smooth
- [ ] Mobile functionality verified

### 1 Week After Deployment  
- [ ] Email deliverability rates acceptable
- [ ] No user complaints about forms
- [ ] Server performance stable
- [ ] Error logs clean
- [ ] Backup procedures updated

---

## 🎯 Success Metrics

### Key Performance Indicators
- **Form Submission Success Rate**: >99%
- **Email Delivery Rate**: >95%
- **Page Load Time**: No degradation
- **Error Rate**: <0.1% of requests
- **User Experience**: No functionality lost

---

This guide ensures a smooth transition to the new professional email system while maintaining your website's reliability and user experience. Always test thoroughly before deploying to production!