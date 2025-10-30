# Studio Management API Documentation

## Overview
This API provides endpoints for managing cinema studios with authentication and role-based access control.

## Authentication
All endpoints require authentication using Laravel Sanctum tokens and admin role.

## Base URL
```
/api/studios
```

## Endpoints

### 1. List All Studios
**GET** `/api/studios`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Studio A",
            "type": "Regular",
            "status": "active",
            "rows": 10,
            "columns": 14,
            "total_seats": 140,
            "created_at": "2024-01-01T00:00:00Z",
            "updated_at": "2024-01-01T00:00:00Z"
        }
    ]
}
```

### 2. Create New Studio
**POST** `/api/studios`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "name": "Studio B",
    "type": "Premium",
    "status": "active",
    "rows": 8,
    "columns": 12
}
```

**Validation Rules:**
- `name`: required, string, max:255, unique
- `type`: required, in:Regular,Premium,IMAX,4DX
- `status`: required, in:active,inactive
- `rows`: required, integer, min:1, max:20
- `columns`: required, integer, min:1, max:20

**Response (201):**
```json
{
    "success": true,
    "data": {
        "id": 2,
        "name": "Studio B",
        "type": "Premium",
        "status": "active",
        "rows": 8,
        "columns": 12,
        "total_seats": 96,
        "created_at": "2024-01-01T00:00:00Z",
        "updated_at": "2024-01-01T00:00:00Z"
    }
}
```

### 3. Get Studio by ID
**GET** `/api/studios/{id}`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Studio A",
        "type": "Regular",
        "status": "active",
        "rows": 10,
        "columns": 14,
        "total_seats": 140,
        "created_at": "2024-01-01T00:00:00Z",
        "updated_at": "2024-01-01T00:00:00Z"
    }
}
```

### 4. Update Studio
**PUT** `/api/studios/{id}`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "name": "Studio A Updated",
    "type": "IMAX",
    "status": "active",
    "rows": 12,
    "columns": 16
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Studio A Updated",
        "type": "IMAX",
        "status": "active",
        "rows": 12,
        "columns": 16,
        "total_seats": 192,
        "created_at": "2024-01-01T00:00:00Z",
        "updated_at": "2024-01-01T01:00:00Z"
    }
}
```

### 5. Delete Studio
**DELETE** `/api/studios/{id}`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Response (Success):**
```json
{
    "success": true,
    "message": "Studio deleted successfully"
}
```

**Response (Error - Studio in use):**
```json
{
    "success": false,
    "message": "Cannot delete studio that has scheduled showtimes"
}
```

## Error Responses

### 401 Unauthorized
```json
{
    "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
    "message": "Forbidden"
}
```

### 422 Validation Error
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "name": ["The name field is required."],
        "type": ["The selected type is invalid."]
    }
}
```

### 404 Not Found
```json
{
    "message": "No query results for model [App\\Models\\Studio] {id}"
}
```

## Features

### Auto-calculated Fields
- `total_seats` is automatically calculated as `rows * columns`

### Soft Delete
- Studios are soft deleted, not permanently removed
- Cannot delete studios that have scheduled showtimes

### Validation
- Unique studio names
- Valid studio types and statuses
- Row and column limits (1-20)

## Database Schema

```sql
CREATE TABLE studios (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    type ENUM('Regular', 'Premium', 'IMAX', '4DX') NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    rows INT NOT NULL CHECK (rows >= 1 AND rows <= 20),
    columns INT NOT NULL CHECK (columns >= 1 AND columns <= 20),
    total_seats INT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);
```