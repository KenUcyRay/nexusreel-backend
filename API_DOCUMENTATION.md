# Cinema Management System API Documentation

## Setup Instructions

### 1. Database Setup
```bash
# Create MySQL database
CREATE DATABASE nexuscinema;

# Update .env file with your database credentials
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nexuscinema
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 2. Run Migrations and Seeders
```bash
php artisan migrate
php artisan db:seed
```

### 3. Start Development Server
```bash
php artisan serve
```

## Authentication

### Cookie-Based Authentication Setup
The system uses Laravel Sanctum for stateful authentication with cookies.

**Frontend Setup (React/Vue):**
```javascript
// Get CSRF token before making requests
await axios.get('http://localhost:8000/sanctum/csrf-cookie', {
    withCredentials: true
});

// Configure axios
const api = axios.create({
    baseURL: 'http://localhost:8000',
    withCredentials: true,
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
    }
});
```

## API Endpoints

### Authentication
- `POST /api/register` - Register new user
- `POST /api/login` - Login user
- `POST /api/logout` - Logout user
- `GET /api/user` - Get authenticated user

### Public Movie Endpoints
- `GET /api/movies` - List all movies
- `GET /api/movies/{id}` - Get movie details
- `GET /api/movies/{id}/showtimes` - Get movie showtimes

### User Booking Endpoints (Authenticated)
- `POST /api/bookings` - Create booking
- `GET /api/bookings/{id}` - Get booking details
- `GET /api/user/bookings` - Get user booking history
- `GET /api/showtimes/{id}/seats` - Get available seats

### Admin Endpoints (Admin Role Required)
- `GET /api/admin/dashboard` - Admin dashboard stats
- `GET|POST|PUT|DELETE /api/admin/movies` - Movie CRUD
- `GET|POST|PUT|DELETE /api/admin/users` - User management
- `GET|POST|PUT|DELETE /api/admin/showtimes` - Showtime management
- `GET|POST|PUT|DELETE /api/admin/foods` - Food management

### Owner Endpoints (Owner Role Required)
- `GET /api/owner/dashboard` - Owner dashboard
- `GET /api/owner/reports/income` - Income reports
- `GET /api/owner/reports/expenses` - Expense reports

### Kasir Endpoints (Kasir Role Required)
- `GET /api/kasir/dashboard` - Kasir dashboard
- `POST /api/kasir/bookings` - Create offline booking
- `GET /api/kasir/bookings/{id}/print` - Print ticket
- `PUT /api/kasir/bookings/{id}/process` - Process online booking
- `POST /api/kasir/food-orders` - Create food order

## Default Users

After running seeders, you can login with:

- **Admin**: admin@cinema.com / password
- **Owner**: owner@cinema.com / password
- **Kasir**: kasir@cinema.com / password
- **User**: user@cinema.com / password

## Database Schema

### Users Table
- id, name, email, password, phone, role (user|admin|owner|kasir)
- email_verified_at, remember_token, created_at, updated_at

### Movies Table
- id, title, description, poster_image, duration_minutes, genre, rating
- status (now_showing|coming_soon), price, created_at, updated_at

### Showtimes Table
- id, movie_id, cinema_hall, show_date, show_time, available_seats
- created_at, updated_at

### Seats Table
- id, showtime_id, seat_number, row_letter, is_booked, created_at, updated_at

### Bookings Table
- id, user_id, showtime_id, total_amount, booking_status (pending|confirmed|cancelled)
- payment_method (qris|cash), invoice_number, created_at, updated_at

### Booking_Seats Table (Pivot)
- id, booking_id, seat_id, created_at, updated_at

### Foods Table
- id, name, description, price, image, stock, created_at, updated_at

### Food_Orders Table
- id, booking_id, food_id, quantity, subtotal, created_at, updated_at

## Security Features

- HttpOnly cookies (prevent XSS)
- CSRF protection
- Session regeneration on login
- Role-based access control
- Rate limiting for API endpoints

## Example Requests

### Login
```javascript
const response = await api.post('/api/login', {
    email: 'admin@cinema.com',
    password: 'password'
});
```

### Create Booking
```javascript
const booking = await api.post('/api/bookings', {
    showtime_id: 1,
    seat_ids: [1, 2, 3],
    payment_method: 'qris'
});
```

### Get Movies
```javascript
const movies = await api.get('/api/movies');
```