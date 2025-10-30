# Cinema Management System API Documentation

## Overview
Complete API for managing cinema operations including movies, studios, and schedules.

## Authentication
Admin endpoints require authentication using Laravel Sanctum tokens and admin role.

## Base URLs
- Admin: `/api/admin/`
- Public: `/api/`

## Movie Management

### Admin Movie Endpoints

#### 1. List All Movies (Admin)
**GET** `/api/admin/movies`

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Movie Title",
            "description": "Movie description",
            "poster_image": "movies/poster.jpg",
            "duration_minutes": 120,
            "genre": "Action",
            "rating": "PG-13",
            "director": "Director Name",
            "production_team": "Production Team",
            "trailer_type": "url",
            "trailer_url": "https://youtube.com/watch?v=...",
            "trailer_file": null,
            "status": "now_showing",
            "price": 50000
        }
    ]
}
```

#### 2. Create Movie (Admin)
**POST** `/api/admin/movies`

**Request Body (multipart/form-data):**
```
title: "Movie Title"
description: "Movie description"
poster_image: [file]
duration_minutes: 120
genre: "Action"
rating: "PG-13"
director: "Director Name"
production_team: "Production Team"
trailer_type: "upload"
trailer_file: [video file]
status: "now_showing"
price: 50000
```

**Validation Rules:**
- `title`: required, string, max:255
- `description`: required, string
- `poster_image`: nullable, image, mimes:jpeg,png,jpg, max:2048KB
- `duration_minutes`: required, integer, min:1
- `genre`: required, string, max:100
- `rating`: required, in:G,PG,PG-13,R,NC-17
- `director`: required, string, max:255
- `production_team`: nullable, string
- `trailer_type`: required, in:url,upload
- `trailer_url`: required_if:trailer_type,url, nullable, url
- `trailer_file`: required_if:trailer_type,upload, nullable, file, mimes:mp4,avi,mov, max:50MB
- `status`: required, in:now_showing,coming_soon
- `price`: required, numeric, min:1, max:999999

### Public Movie Endpoints

#### 1. List Public Movies
**GET** `/api/movies`

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Movie Title",
            "genre": "Action",
            "duration_minutes": 120,
            "rating": "PG-13",
            "poster_image": "movies/poster.jpg",
            "price": 50000
        }
    ]
}
```

#### 2. Get Movie Details with Schedules
**GET** `/api/movies/{id}`

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "title": "Movie Title",
        "description": "Movie description",
        "genre": "Action",
        "duration_minutes": 120,
        "rating": "PG-13",
        "director": "Director Name",
        "production_team": "Production Team",
        "trailer_type": "url",
        "trailer_url": "https://youtube.com/watch?v=...",
        "poster_image": "movies/poster.jpg",
        "schedules": [
            {
                "id": 1,
                "show_date": "2024-01-15",
                "show_time": "14:30",
                "price": 50000,
                "studio": {
                    "id": 1,
                    "name": "Studio A",
                    "type": "Regular"
                }
            }
        ]
    }
}
```

## Schedule Management

### Admin Schedule Endpoints

#### 1. List All Schedules (Admin)
**GET** `/api/admin/schedules`

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "movie_id": 1,
            "studio_id": 1,
            "show_date": "2024-01-15",
            "show_time": "14:30",
            "price": 50000,
            "movie": {
                "id": 1,
                "title": "Movie Title",
                "genre": "Action",
                "poster_image": "movies/poster.jpg"
            },
            "studio": {
                "id": 1,
                "name": "Studio A",
                "type": "Regular",
                "total_seats": 140
            }
        }
    ]
}
```

#### 2. Create Schedule (Admin)
**POST** `/api/admin/schedules`

**Request Body:**
```json
{
    "movie_id": 1,
    "studio_id": 1,
    "show_date": "2024-01-15",
    "show_time": "14:30",
    "price": 50000
}
```

**Validation Rules:**
- `movie_id`: required, exists:movies,id
- `studio_id`: required, exists:studios,id
- `show_date`: required, date, after_or_equal:today
- `show_time`: required, date_format:H:i
- `price`: required, numeric, min:1, max:999999

**Unique Constraint:** studio_id + show_date + show_time must be unique

#### 3. Update Schedule (Admin)
**PUT** `/api/admin/schedules/{id}`

#### 4. Delete Schedule (Admin)
**DELETE** `/api/admin/schedules/{id}`

### Public Schedule Endpoints

#### 1. Get Schedules by Movie
**GET** `/api/schedules/movie/{movieId}`

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "show_date": "2024-01-15",
            "show_time": "14:30",
            "price": 50000,
            "studio": {
                "id": 1,
                "name": "Studio A",
                "type": "Regular"
            }
        }
    ]
}
```

## File Storage

### Upload Directories
- **Movie Posters:** `storage/app/public/movies/`
- **Trailer Videos:** `storage/app/public/trailers/`

### File Limits
- **Images:** Max 2MB, formats: jpeg, png, jpg
- **Videos:** Max 50MB, formats: mp4, avi, mov

### Access URLs
- **Images:** `http://domain.com/storage/movies/filename.jpg`
- **Videos:** `http://domain.com/storage/trailers/filename.mp4`

## Business Rules

### Schedule Constraints
1. **No Double Booking:** Same studio cannot have overlapping schedules
2. **Future Dates Only:** Schedule date must be today or future
3. **Active Studios Only:** Can only schedule in active studios
4. **Soft Delete:** Schedules are soft deleted, not permanently removed

### Movie Constraints
1. **Trailer Requirements:** Must provide either URL or upload file
2. **Status Management:** Movies can be 'now_showing' or 'coming_soon'
3. **File Cleanup:** Old files are deleted when updated

## Error Responses

### 422 Validation Error
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "movie_id": ["The selected movie id is invalid."],
        "show_date": ["The show date must be a date after or equal to today."]
    }
}
```

### 409 Conflict (Double Booking)
```json
{
    "message": "The studio is already booked for this date and time.",
    "errors": {
        "studio_id": ["Studio already has a schedule at this time."]
    }
}
```

## Database Schema

### Updated Movies Table
```sql
ALTER TABLE movies ADD COLUMN rating VARCHAR(10) AFTER genre;
ALTER TABLE movies ADD COLUMN director VARCHAR(255) AFTER rating;
ALTER TABLE movies ADD COLUMN production_team TEXT AFTER director;
ALTER TABLE movies ADD COLUMN trailer_type ENUM('url', 'upload') DEFAULT 'url' AFTER production_team;
ALTER TABLE movies ADD COLUMN trailer_url TEXT AFTER trailer_type;
ALTER TABLE movies ADD COLUMN trailer_file VARCHAR(255) AFTER trailer_url;
```

### Schedules Table
```sql
CREATE TABLE schedules (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    movie_id BIGINT UNSIGNED NOT NULL,
    studio_id BIGINT UNSIGNED NOT NULL,
    show_date DATE NOT NULL,
    show_time TIME NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (studio_id) REFERENCES studios(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_schedule (studio_id, show_date, show_time),
    INDEX idx_schedules_movie_date (movie_id, show_date),
    INDEX idx_schedules_studio_date (studio_id, show_date)
);
```

### Performance Indexes
```sql
CREATE INDEX idx_movies_status ON movies(status);
CREATE INDEX idx_schedules_movie_date ON schedules(movie_id, show_date);
CREATE INDEX idx_schedules_studio_date ON schedules(studio_id, show_date);
```