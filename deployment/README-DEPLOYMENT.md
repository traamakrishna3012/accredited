# Accredited Website - OCI Deployment Guide

Deploy the Accredited Inspection Agency website on Oracle Cloud Infrastructure (OCI) with Ubuntu 24.04, Nginx, PHP-FPM, and MariaDB.

## Prerequisites

- OCI Instance: VM.Standard.E2.1.Micro (1 OCPU, 1 GB RAM)
- OS: Ubuntu 24.04
- Domain: accredited.co.in (managed via Hostinger)
- SSH access to the instance

## Instance Details

| Setting | Value |
|---------|-------|
| Public IP | 68.233.112.2 |
| Username | ubuntu |
| Region | ap-hyderabad-1 |

---

## Step 1: Configure DNS (Hostinger)

1. Log in to [Hostinger](https://hpanel.hostinger.com)
2. Go to **Domains** → **accredited.co.in** → **DNS / Nameservers**
3. Add these DNS records:

| Type | Name | Points to | TTL |
|------|------|-----------|-----|
| A | @ | 68.233.112.2 | 14400 |
| A | www | 68.233.112.2 | 14400 |

4. Wait 10-30 minutes for DNS propagation
5. Verify: `ping accredited.co.in` should resolve to 68.233.112.2

---

## Step 2: Connect to OCI Instance

```bash
# From the project directory
ssh -i ssh-key-2025-12-18.key ubuntu@68.233.112.2
```

If permission denied:
```bash
chmod 400 ssh-key-2025-12-18.key
```

---

## Step 3: Upload Files to Server

From your local machine (PowerShell):

```powershell
# Create tar archive (exclude sensitive files)
tar -cvf accredited.tar --exclude="*.key" --exclude="*.key.pub" --exclude=".git" .

# Upload to server
scp -i ssh-key-2025-12-18.key accredited.tar ubuntu@68.233.112.2:~/
```

On the server:
```bash
mkdir -p ~/accredited
cd ~/accredited
tar -xvf ~/accredited.tar
```

---

## Step 4: Run Deployment Script

```bash
cd ~/accredited

# Make script executable
chmod +x deployment/deploy.sh

# Run with your desired database password
sudo bash deployment/deploy.sh "YourSecureDBPassword123!"
```

The script will:
- ✅ Install Nginx, PHP 8.3, MariaDB
- ✅ Configure optimized settings for 1GB RAM
- ✅ Create database and import schema
- ✅ Setup SSL with Let's Encrypt
- ✅ Configure firewall rules

---

## Step 5: Configure Application

Edit the production environment file:

```bash
sudo nano /var/www/accredited/.env
```

Update these values:
```ini
DB_PASS=YourSecureDBPassword123!
SMTP_USER=your-actual-email@gmail.com
SMTP_PASS=your-gmail-app-password
ADMIN_EMAIL=your-email@example.com
```

---

## Step 6: Verify Installation

```bash
# Check services are running
sudo systemctl status nginx
sudo systemctl status php8.3-fpm
sudo systemctl status mariadb

# Check application logs
sudo tail -f /var/log/nginx/accredited_error.log
```

Visit: https://accredited.co.in

---

## Post-Deployment

### Change Admin Password

1. Go to https://accredited.co.in/admin/login.php
2. Login with: `admin` / `admin123`
3. Go to Settings and change password immediately

### Clear Cache After Updates

```bash
# Clear PHP OPcache (after code changes)
sudo systemctl restart php8.3-fpm

# Clear application cache
sudo rm -rf /var/www/accredited/cache/*
```

### SSL Certificate Renewal

Certbot auto-renews. To manually renew:
```bash
sudo certbot renew
```

### Monitor Memory Usage

```bash
free -m
htop
```

---

## Troubleshooting

### 502 Bad Gateway
```bash
sudo systemctl restart php8.3-fpm
sudo systemctl restart nginx
```

### Database Connection Error
```bash
sudo systemctl status mariadb
sudo mysql -u agency_user -p agency_db
```

### Permission Issues
```bash
sudo chown -R www-data:www-data /var/www/accredited
sudo chmod 600 /var/www/accredited/.env
```

---

## Configuration Files Reference

| File | Server Location |
|------|-----------------|
| Nginx config | `/etc/nginx/sites-available/accredited` |
| PHP-FPM config | `/etc/php/8.3/fpm/pool.d/www.conf` |
| MariaDB config | `/etc/mysql/mariadb.conf.d/99-optimized.cnf` |
| OPcache config | `/etc/php/8.3/mods-available/opcache.ini` |
| Application | `/var/www/accredited/` |
| App config | `/var/www/accredited/.env` |
