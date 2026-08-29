#!/bin/bash

# GlowDesk Auto-Setup Script for Ubuntu 22.04+
# This script installs Nginx, PHP 8.2, Composer and configures the app.

set -e

echo "🚀 Starting GlowDesk Server Setup..."

# 1. Update System
sudo apt update && sudo apt upgrade -y

# 2. Install PHP 8.2 and Extensions
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2-fpm php8.2-curl php8.2-xml php8.2-zip php8.2-sqlite3 php8.2-mbstring php8.2-gd nginx unzip git

# 3. Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# 4. Prepare Directory
# Assuming current dir is the project root
APP_PATH=$(pwd)
sudo chown -R www-data:www-data $APP_PATH/storage $APP_PATH/bootstrap/cache $APP_PATH/database
sudo chmod -R 775 $APP_PATH/storage $APP_PATH/bootstrap/cache $APP_PATH/database

# 5. Configure Nginx
cat <<EOF | sudo tee /etc/nginx/sites-available/glowdesk
server {
    listen 80;
    server_name _;
    root $APP_PATH/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

sudo ln -sf /etc/nginx/sites-available/glowdesk /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl restart nginx

# 6. Laravel Setup
if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate
fi

composer install --no-dev --optimize-autoloader
touch database/database.sqlite
php artisan migrate --force

echo "✅ GlowDesk is now LIVE at your Server IP!"
echo "Remember to update your .env with your Telegram Token and BOT name."
