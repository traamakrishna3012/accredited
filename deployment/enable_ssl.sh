#!/bin/bash
# Script to enable SSL for accredited.co.in

# Stop Nginx
sudo systemctl stop nginx

# Run Certbot
sudo certbot certonly --standalone -d accredited.co.in -d www.accredited.co.in --agree-tos --email admin@accredited.co.in

# Link Production SSL Config
sudo cp /var/www/accredited/deployment/nginx.conf /etc/nginx/sites-available/accredited
sudo ln -sf /etc/nginx/sites-available/accredited /etc/nginx/sites-enabled/accredited

# Start Nginx
sudo systemctl start nginx

echo "SSL Enabled! Verification needed."
