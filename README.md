# Accredited Inspection Agency Website

Professional PHP website for testing, inspection, and certification services.

## Quick Start (Localhost)

### Prerequisites
- XAMPP with Apache and MySQL running
- PHP 8.x

### Setup Steps

1. **Create Database**
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Import the schema: `database/schema.sql`
   - Or run in MySQL CLI:
     ```sql
     source C:/xampp/htdocs/accredited/database/schema.sql
     ```

2. **Configure Database** (if needed)
   - Edit `includes/config.php`
   - Update DB credentials if not using XAMPP defaults

3. **Access the Website**
   - Frontend: `http://localhost/accredited/`
   - Admin Panel: `http://localhost/accredited/admin/login.php`
   - Default login: `admin` / `admin123`

## Project Structure

```
accredited/
├── index.php           # Home page
├── about.php           # About page
├── services.php        # Services (dynamic from DB)
├── contact.php         # Contact form
├── sitemap.xml         # SEO sitemap
├── robots.txt          # Crawler rules
├── .htaccess           # Apache config
├── assets/
│   ├── css/style.css   # Custom styles
│   ├── js/script.js    # Custom scripts
│   └── images/         # Image assets
├── includes/
│   ├── config.php      # Site configuration
│   ├── db.php          # Database connection
│   ├── header.php      # Header template
│   └── footer.php      # Footer template
├── admin/
│   ├── login.php       # Admin login
│   ├── dashboard.php   # Dashboard
│   ├── inquiries.php   # Manage inquiries
│   ├── services.php    # Manage services
│   └── logout.php      # Logout
├── api/
│   ├── submit_inquiry.php  # Form handler
│   └── send_email.php      # Email helper
└── database/
    └── schema.sql      # Database schema
```

## Deployment to Hostinger

1. Upload all files to `public_html/`
2. Import `database/schema.sql` to MySQL
3. Update `includes/config.php`:
   - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
   - SMTP credentials
4. Change admin password after first login

## Security Features

- PDO prepared statements
- CSRF token protection
- Password hashing (bcrypt)
- Input sanitization
- Session regeneration
- .htaccess protection

## License

Copyright © 2024 Accredited Inspection Agency Private Limited
