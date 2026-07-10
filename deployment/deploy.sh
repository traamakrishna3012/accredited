#!/bin/bash
# Accredited Website - OCI Ubuntu 24.04 Deployment Script
# Run as: sudo bash deploy.sh

set -e

echo "=========================================="
echo "Accredited Website Deployment Script"
echo "Target: OCI Ubuntu 24.04 (1 OCPU, 1GB RAM)"
echo "=========================================="

# Variables
DOMAIN="accredited.co.in"
WEB_ROOT="/var/www/accredited"
DB_NAME="agency_db"
DB_USER="agency_user"
DB_PASS="${1:-CHANGE_THIS_PASSWORD}"

# Check if running as root
if [[ $EUID -ne 0 ]]; then
   echo "This script must be run as root (use sudo)"
   exit 1
fi

echo ""
echo "Step 1: System Update"
echo "-------------------------------------------"
apt update && apt upgrade -y

echo ""
echo "Step 2: Install Nginx, PHP, MariaDB"
echo "-------------------------------------------"
apt install -y nginx mariadb-server \
    php8.3-fpm php8.3-mysql php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-gd php8.3-opcache

echo ""
echo "Step 3: Configure MariaDB"
echo "-------------------------------------------"
# Copy optimized MariaDB config
cp deployment/mariadb.cnf /etc/mysql/mariadb.conf.d/99-optimized.cnf

# Secure MariaDB and create database
mysql -e "CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

# Import schema
mysql ${DB_NAME} < database/schema.sql
echo "Database created and schema imported."

# Restart MariaDB
systemctl restart mariadb
systemctl enable mariadb

echo ""
echo "Step 4: Configure PHP-FPM"
echo "-------------------------------------------"
# Backup and replace FPM config
cp /etc/php/8.3/fpm/pool.d/www.conf /etc/php/8.3/fpm/pool.d/www.conf.backup
cp deployment/php-fpm.conf /etc/php/8.3/fpm/pool.d/www.conf

# Copy OPcache config
cp deployment/opcache.ini /etc/php/8.3/mods-available/opcache.ini

# Create PHP log directory
mkdir -p /var/log/php-fpm
chown www-data:www-data /var/log/php-fpm

# Restart PHP-FPM
systemctl restart php8.3-fpm
systemctl enable php8.3-fpm

echo ""
echo "Step 5: Setup Web Directory"
echo "-------------------------------------------"
# Create web root
mkdir -p ${WEB_ROOT}

# Copy application files (excluding deployment and sensitive files)
rsync -av --exclude='deployment' --exclude='.git' --exclude='*.key' --exclude='*.key.pub' --exclude='env.example' ./ ${WEB_ROOT}/

# Create .env file from example
cp env.example ${WEB_ROOT}/.env

# Update .env with production values
sed -i "s/ENVIRONMENT=production/ENVIRONMENT=production/" ${WEB_ROOT}/.env
sed -i "s/DB_USER=agency_user/DB_USER=${DB_USER}/" ${WEB_ROOT}/.env
sed -i "s/CHANGE_THIS_TO_SECURE_PASSWORD/${DB_PASS}/" ${WEB_ROOT}/.env

# Create cache directory
mkdir -p ${WEB_ROOT}/cache
chown -R www-data:www-data ${WEB_ROOT}/cache

# Set permissions
chown -R www-data:www-data ${WEB_ROOT}
find ${WEB_ROOT} -type d -exec chmod 755 {} \;
find ${WEB_ROOT} -type f -exec chmod 644 {} \;
chmod 600 ${WEB_ROOT}/.env

echo ""
echo "Step 6: Configure Nginx"
echo "-------------------------------------------"
# Copy Nginx config
cp deployment/nginx.conf /etc/nginx/sites-available/accredited

# Remove default site
rm -f /etc/nginx/sites-enabled/default

# Enable accredited site
ln -sf /etc/nginx/sites-available/accredited /etc/nginx/sites-enabled/accredited

# Test Nginx config
nginx -t

# Restart Nginx
systemctl restart nginx
systemctl enable nginx

echo ""
echo "Step 7: Setup Firewall"
echo "-------------------------------------------"
ufw allow 22/tcp
ufw allow 80/tcp
ufw allow 443/tcp
ufw --force enable

echo ""
echo "Step 8: Install SSL Certificate (Let's Encrypt)"
echo "-------------------------------------------"
apt install -y certbot python3-certbot-nginx

# Get SSL certificate (requires domain to be pointing to this server)
certbot --nginx -d ${DOMAIN} -d www.${DOMAIN} --non-interactive --agree-tos --email admin@${DOMAIN} || {
    echo "SSL certificate setup failed. Make sure your domain DNS is pointing to this server."
    echo "Run manually later: sudo certbot --nginx -d ${DOMAIN} -d www.${DOMAIN}"
}

# Setup auto-renewal
systemctl enable certbot.timer
systemctl start certbot.timer

echo ""
echo "Step 9: Verify Installation"
echo "-------------------------------------------"
systemctl status nginx --no-pager
systemctl status php8.3-fpm --no-pager
systemctl status mariadb --no-pager

echo ""
echo "=========================================="
echo "Deployment Complete!"
echo "=========================================="
echo ""
echo "IMPORTANT: Update these settings in ${WEB_ROOT}/.env:"
echo "  - DB_PASS (database password)"
echo "  - SMTP settings (for email functionality)"
echo ""
echo "Admin Panel: https://${DOMAIN}/admin/login.php"
echo "Default Login: admin / admin123 (CHANGE THIS IMMEDIATELY!)"
echo ""
echo "To clear OPcache after code updates:"
echo "  sudo systemctl restart php8.3-fpm"
echo ""
