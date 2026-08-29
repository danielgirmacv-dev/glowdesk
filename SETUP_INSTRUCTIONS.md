# Internal E-Commerce Setup Instructions

Follow these steps to finish setting up the newly upgraded Internal E-commerce system on your LAN.

## 1. Complete Laravel Setup
Your system was just upgraded into a modern Laravel skeleton in this directory. 
Since `composer create-project` was initially interrupted by GitHub API limits, please run:

```bash
composer install
cp .env.example .env
php artisan key:generate
```

## 2. Environment Variables
Edit your new `.env` file to add the custom Telegram Bot configuration and Admin Auth settings:

```env
# Add your Telegram Bot info (required for notifications)
TELEGRAM_BOT_TOKEN="your_bot_token_here"
TELEGRAM_CHAT_ID="your_chat_id_here"

# Hardcoded Simple Admin Auth Password
ADMIN_PASSWORD="secret_password"
```

## 3. Database
We configured new standard migrations for Products, Orders, and OrderItems.
I have pre-configured `.env` for your XAMPP setup to use a MySQL database named `glowdesk` with the default `root` user and no password. **Please ensure you have created an empty database named `glowdesk` within your phpMyAdmin first.**

```bash
php artisan migrate
```

*(Optional) You might want to manually insert some products or use the `/admin/products/create` route after logging in.*

## 4. How to run locally on LAN
If you want to run this application such that other computers on the 192.168.x.x LAN can access it:
```bash
php artisan serve --host=0.0.0.0 --port=8000
```
This forces Laravel's built-in server to listen on all interfaces.
Computers on the LAN can access it via your PC's local IP (e.g., `http://192.168.1.50:8000`).

## 5. Security & Access
- **Company LAN Restriction:** The `RestrictLan` middleware automatically blocks any IP outside the `192.168.*` or `10.*` range and `localhost`.
- **Admin Access:** Visit `/admin` or `/admin/login`, and use the `ADMIN_PASSWORD` (defaults to `secret123` if missing from `.env`) to manage Products and view all Internal Orders.

## Summary of Upgrades
- **Frontend**: Upgraded to a Tailwind CSS aesthetic and responsive mobile-first design.
- **Order Flow**: Added a slick Alpine.js animated modal for purchases with validation routines.
- **Telegram Notifications**: A background-safe service integrates automatically.
- **Security Checkers**: Basic Basic Auth substitute for fast LAN-administration.
