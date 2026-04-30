# API Documentation for Frontend

## Base URL
All endpoints are prefixed with `/api/v1/`

---

## DISTRIBUTOR ENDPOINTS

### 1. List Distributors (Paginated + Search)
```
GET /api/v1/distributors
```

**Query Parameters:**
- `search` - Search by name, address, or service area
- `service_area` - Filter by specific service area
- `latitude` + `longitude` + `radius` - Search within radius (km)
- `per_page` - Items per page (default: 15)

**Example:**
```
GET /api/v1/distributors?search=delhi&per_page=10
GET /api/v1/distributors?latitude=28.6139&longitude=77.2090&radius=20
```

**Response:**
```json
{
  "success": true,
  "data": [...],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 75
  },
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": "..."
  }
}
```

### 2. Get Single Distributor (with Inventory)
```
GET /api/v1/distributors/{id}
```

Returns distributor details + products in stock at that location.

### 3. Find Nearby Distributors
```
GET /api/v1/distributors/nearby?latitude=28.6139&longitude=77.2090&radius=10
```

Returns distributors sorted by distance from given coordinates.

---

## PRODUCT & INVENTORY ENDPOINTS

### 4. List Products (Paginated + Filter)
```
GET /api/v1/products
```

**Query Parameters:**
- `category` - Filter by category
- `search` - Search by product name
- `per_page` - Items per page (default: 15)

**Response:**
```json
{
  "success": true,
  "data": [...],
  "categories": ["Ice Cream", "Sorbet", "Frozen Yogurt"],
  "meta": {...}
}
```

### 5. Get Single Product (with Stock at All Distributors)
```
GET /api/v1/products/{id}
```

Returns product details + stock availability at each distributor location.

### 6. Get Inventory at Specific Distributor
```
GET /api/v1/distributors/{id}/inventory
```

Returns all products in stock at that distributor with availability info.

### 7. Check Product Availability Nearby
```
GET /api/v1/inventory/check-availability?product_id=1&latitude=28.6139&longitude=77.2090&radius=10&quantity=5
```

Finds distributors within radius that have the requested product in stock.

**Query Parameters:**
- `product_id` (required) - Product to search for
- `latitude` + `longitude` (required) - User's location
- `radius` (optional) - Search radius in km (default: 10)
- `quantity` (optional) - Minimum quantity needed (default: 1)

**Response:**
```json
{
  "success": true,
  "product": {...},
  "requested_quantity": 5,
  "nearby_distributors_with_stock": [
    {
      "distributor_id": 1,
      "distributor_name": "...",
      "available_quantity": 50,
      "product_price": 250.00,
      "distance_km": 3.2
    }
  ],
  "total_available": 150
}
```

### 8. Get Product Categories
```
GET /api/v1/products/categories
```

Returns list of all unique product categories.

---

## INQUIRY ENDPOINT (Existing)

### 9. Submit Customer Inquiry
```
POST /api/v1/inquiries
```

**Body:**
```json
{
  "name": "John Doe",
  "business_name": "ABC Store",
  "email": "john@example.com",
  "requirement": "Looking for wholesale ice cream supply"
}
```

---

## Admin Panel Routes

Access at `/admin` with these new sections:

- `/admin/products` - Product management (CRUD + images)
- `/admin/inventory` - Stock management by distributor
- `/admin/inventory-history` - Stock movement audit trail
- `/admin/low-stock` - Low stock alerts

---

## Setup Instructions

1. Install dependencies:
```bash
composer install
```

2. Run migrations:
```bash
php artisan migrate
```

3. Create storage link for images:
```bash
php artisan storage:link
```

4. Seed sample data (optional):
```bash
php artisan db:seed
```
