# Quick Setup Guide

## Step 1: Install Dependencies
```bash
composer install
```
This downloads all the required PHP packages.

## Step 2: Setup Environment
```bash
# Copy the example environment file
cp .env.example .env

# Generate app encryption key
php artisan key:generate
```

## Step 3: Create Database
This app uses SQLite (simple file-based database):
```bash
# Create the database file
touch database/database.sqlite

# Run migrations (creates all tables)
php artisan migrate
```

## Step 4: Start the Server
```bash
php artisan serve
```
Backend will run at: **http://localhost:8000**

## Test If It's Working
Open browser or Postman and visit:
- http://localhost:8000/api/v1/health

You should see:
```json
{
  "status": "ok",
  "message": "API is running",
  "timestamp": "...",
  "version": "1.0"
}
```

## CORS Fixed! ✅
Your friend's frontend can now connect from ANY port (3000, 5173, etc.).

The config at `@/home/pankaj-singh/Icecream website/ice-cream-backend/config/cors.php` now allows all origins for development.

## API Base URL for Frontend
```
http://localhost:8000/api/v1/
```

## Important Endpoints for Frontend Friend
| Endpoint | Description |
|----------|-------------|
| `GET /api/v1/health` | Test connection |
| `GET /api/v1/distributors` | List all distributors (paginated) |
| `GET /api/v1/distributors/nearby?latitude=28.6139&longitude=77.2090` | Find nearby |
| `GET /api/v1/products` | Product catalog |
| `GET /api/v1/inventory/check-availability?product_id=1&latitude=28.6&longitude=77.2` | Check stock nearby |

## Admin Panel
Visit: http://localhost:8000/admin
- Login with credentials (you need to create an admin user first)
- Manage products, inventory, distributors

## Troubleshooting

### "CORS error" in browser console
✅ Already fixed! The backend now accepts requests from any origin.

### "Connection refused"
Make sure `php artisan serve` is running

### "Database not found"
Run: `touch database/database.sqlite` then `php artisan migrate`

### "Class not found" errors
Run: `composer dump-autoload`
