# Laravel Tour Platform API

A robust RESTful API for managing tours, built with Laravel 11. This platform allows tour operators to create, manage, and showcase their tours while providing a secure and efficient way to handle tour-related operations.

## Features

### Tour Management
- ✅ Complete CRUD operations for tours
- ✅ Advanced filtering capabilities:
  - Filter by date range
  - Filter by location
  - Filter by price range
  - Search by name/description
- ✅ Comprehensive validation for tour data

### Authorization & User Management
- ✅ Role-based access control (Admin and Tour Operator roles)
- ✅ Secure authentication using Laravel Sanctum
- ✅ Policy-based authorization for tour operations
- ✅ Protected routes and endpoints

## Requirements

- PHP >= 8.2
- Composer
- MySQL/PostgreSQL
- Laravel 11.x

## Installation

1. Clone the repository:
```bash
git clone [repository-url]
cd laravel-tour-platform
```

2. Install dependencies:
```bash
composer install
```

3. Set up environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure your database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. Run migrations and seeders:
```bash
php artisan migrate --seed
```

## Documentation

### API Documentation
Detailed API documentation is available at `/docs` when running the application:
```
http://your-domain/docs
```

The documentation includes:
- Detailed endpoint descriptions
- Request/Response examples
- Authentication instructions
- Error handling

### Postman Collection
A complete Postman collection is available at `/docs/collection.json`:
```
http://your-domain/docs/collection.json
```

You can import this collection into Postman to test all available endpoints.

## Test Users

The seeder creates the following test users:

1. Admin User
   - Email: admin@example.com
   - Password: password
   - Role: admin

2. Tour Operator
   - Email: operator@example.com
   - Password: password
   - Role: tour_operator

## Authorization Rules

1. Tour Viewing:
   - Anyone can view tours (authenticated or not)
   - Detailed tour information is public

2. Tour Management:
   - Only authenticated tour operators can create tours
   - Tours can only be updated/deleted by:
     - The tour operator who created them
     - Admin users

## Response Format

All API responses follow this structure:
```json
{
    "status": "success|error",
    "message": "Response message",
    "data": {
        // Response data here
    },
    "code": "Error code (only in error responses)"
}
```

## Error Handling

The API uses appropriate HTTP status codes:
- 200: Success
- 201: Created
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found
- 422: Validation Error
- 500: Server Error


## Testing

### Running Tests
```bash
php artisan test
```

### Test Coverage

#### Authentication Tests (`tests/Feature/AuthenticationTest.php`)
- ✅ User Registration
  - Successful registration with valid data
  - Failed registration with invalid data (empty fields, invalid email, etc.)
  - Failed registration with existing email
  - Password confirmation validation

- ✅ User Login
  - Successful login with valid credentials
  - Failed login with invalid credentials
  - Failed login with non-existent email
  - Failed login with empty credentials

- ✅ User Profile
  - Get profile with valid token
  - Cannot access profile without token

#### Tour Management Tests (`tests/Feature/TourManagementTest.php`)
- ✅ Tour Creation
  - Tour operator can create tour with valid data
  - Cannot create tour with invalid data
  - Validates all required fields

- ✅ Tour Updates
  - Tour operator can update own tour
  - Tour operator cannot update others' tours
  - Admin can update any tour
  - Validates update data

- ✅ Tour Deletion
  - Tour operator can delete own tour
  - Tour operator cannot delete others' tours
  - Admin can delete any tour

- ✅ Tour Listing & Filtering
  - List tours with date filters
  - List tours with price range
  - List tours by location
  - Search in tour names and descriptions

- ✅ Authorization Rules
  - Enforces role-based access control
  - Validates tour ownership
  - Admin has full access

- ✅ Data Validation
  - Validates tour name length
  - Validates date logic (start before end)
  - Validates price range
  - Validates required fields

## Development Progress

- ✅ Initial Setup & Authentication
- ✅ Database Design & Models
- ✅ Tour Management API
- ✅ Authorization & User Management
- ✅ Documentation
- 🔄 Testing
- ⏳ Deployment
- ⏳ Optional Enhancements

## Project Structure

```
laravel-tour-platform/
├── app/
│   ├── Api/
│   │   └── V1/
│   │       ├── Controllers/
│   │       │   ├── AuthController.php
│   │       │   ├── BaseController.php
│   │       │   └── TourController.php
│   │       └── Requests/
│   │           ├── CreateTourRequest.php
│   │           └── UpdateTourRequest.php
│   ├── Http/
│   │   ├── Middleware/
│   │   │   └── AdminPanelMiddleware.php
│   │   └── Requests/
│   ├── Models/
│   │   ├── Tour.php
│   │   └── User.php
│   ├── Policies/
│   │   └── TourPolicy.php
│   └── Traits/
│       └── ApiResponse.php
├── database/
│   ├── factories/
│   │   ├── TourFactory.php
│   │   └── UserFactory.php
│   ├── migrations/
│   └── seeders/
│       ├── TourSeeder.php
│       └── UserSeeder.php
├── routes/
│   └── api.php
├── tests/
│   └── Feature/
│       ├── AuthenticationTest.php
│       └── TourManagementTest.php
└── lang/
    └── en/
        └── enum.php
```

### Key Directories

- `app/Api/V1/`: Contains API version 1 related files
  - `Controllers/`: API controllers with versioning support
  - `Requests/`: Form request validation classes
- `app/Models/`: Eloquent models
- `app/Policies/`: Authorization policies
- `app/Traits/`: Reusable traits like ApiResponse
- `database/`: Database related files (migrations, factories, seeders)
- `tests/Feature/`: Feature tests for API endpoints
- `lang/en/`: Language files for response messages

### API Versioning

The API is versioned using directory structure (`V1`) to ensure backward compatibility as the API evolves. All API controllers are placed under `app/Api/V1/Controllers/` directory.
