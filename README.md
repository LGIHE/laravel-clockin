# ClockIn Laravel Application

A comprehensive employee attendance and leave management system built with Laravel 11.x, Livewire 3.x, Alpine.js, and Tailwind CSS.

## Features

- **Authentication & Authorization**: Role-based access control (Admin, Supervisor, User)
- **Attendance Tracking**: Clock in/out functionality with worked hours calculation
- **Leave Management**: Apply, approve, and track leave requests
- **User Management**: Complete CRUD operations for employee management
- **Department & Designation Management**: Organize employees by organizational structure
- **Project Management**: Assign employees to projects and track allocations
- **Holiday Management**: Define public holidays
- **Notice Board**: Company-wide announcements
- **Notifications**: Real-time notifications for important events
- **Reporting & Analytics**: Generate attendance and leave reports
- **Role-based Dashboards**: Personalized dashboards for different user roles

## Technology Stack

### Backend
- **Laravel 11.x** - PHP Framework
- **PHP 8.2+** - Programming Language
- **MySQL 8.0+** - Database
- **Laravel Sanctum** - API Authentication
- **Eloquent ORM** - Database Interactions

### Frontend
- **Laravel Livewire 3.x** - Full-stack framework
- **Alpine.js** - Lightweight JavaScript framework
- **Tailwind CSS 3.x** - Utility-first CSS framework
- **Vite** - Frontend build tool

### Development Tools
- **Composer** - PHP dependency management
- **NPM** - JavaScript package management
- **Laravel Pint** - Code formatting
- **PHPUnit** - Testing framework

## Requirements

- PHP >= 8.2
- Composer
- Node.js >= 18.x
- NPM or Yarn
- MySQL >= 8.0

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd laravel-clockin
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install JavaScript Dependencies

```bash
npm install
```

### 4. Environment Configuration

Copy the example environment file and configure your database:

```bash
cp .env.example .env
```

Update the following variables in `.env`:

```env
APP_NAME="ClockIn Laravel"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=clockin
DB_USERNAME=root
DB_PASSWORD=your_password

SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1
SANCTUM_TOKEN_EXPIRATION=1440
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Run Database Migrations

**Note**: This application uses an existing database schema. Ensure your `clockin` database exists with the required tables.

```bash
php artisan migrate
```

### 7. Build Frontend Assets

For development:
```bash
npm run dev
```

For production:
```bash
npm run build
```

### 8. Start the Development Server

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## Project Structure

```
laravel-clockin/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # API and Web Controllers
│   │   ├── Middleware/      # Custom Middleware
│   │   └── Requests/        # Form Request Validation
│   ├── Models/              # Eloquent Models
│   ├── Services/            # Business Logic Layer
│   └── Livewire/            # Livewire Components
├── config/                  # Configuration Files
├── database/
│   ├── migrations/          # Database Migrations
│   └── seeders/             # Database Seeders
├── resources/
│   ├── css/                 # Stylesheets
│   ├── js/                  # JavaScript Files
│   └── views/               # Blade Templates
│       ├── components/      # Reusable UI Components
│       ├── livewire/        # Livewire Component Views
│       └── layouts/         # Layout Templates
├── routes/
│   ├── api.php              # API Routes
│   └── web.php              # Web Routes
└── tests/                   # Test Files
```

## Configuration

### Sanctum API Authentication

Sanctum is configured for API token authentication. Token expiration is set to 24 hours (1440 minutes) by default.

### CORS Configuration

CORS is configured to allow requests from localhost and 127.0.0.1. Update `config/cors.php` for production environments.

### Tailwind CSS

Tailwind is configured with:
- Custom primary color palette
- @tailwindcss/forms plugin for better form styling
- Support for Livewire components

### Alpine.js

Alpine.js is globally available for interactive components like modals, dropdowns, and tooltips.

## Development

### Code Formatting

Format your code using Laravel Pint:

```bash
./vendor/bin/pint
```

### Running Tests

Run the test suite:

```bash
php artisan test
```

Run tests with coverage:

```bash
php artisan test --coverage
```

### Watching Assets

For development with hot module replacement:

```bash
npm run dev
```

## API Documentation

API endpoints are organized under `/api` prefix and require authentication via Sanctum tokens.

### Authentication Endpoints

- `POST /api/auth/login` - User login
- `POST /api/auth/logout` - User logout
- `POST /api/auth/refresh` - Refresh token
- `GET /api/auth/me` - Get authenticated user
- `POST /api/auth/forgot-password` - Request password reset
- `POST /api/auth/reset-password` - Reset password

### Protected Endpoints

All other endpoints require authentication. Include the token in the Authorization header:

```
Authorization: Bearer {token}
```

## Database Schema

The application uses the existing ClockIn database schema with the following main tables:

- `users` - User accounts
- `user_levels` - User roles (Admin, Supervisor, User)
- `departments` - Organizational departments
- `designations` - Job titles
- `attendances` - Clock in/out records
- `leaves` - Leave requests
- `leave_categories` - Types of leave
- `leave_statuses` - Leave status (Pending, Approved, Rejected)
- `projects` - Project information
- `holidays` - Public holidays
- `notices` - Company announcements
- `notifications` - User notifications

All tables use UUID (CHAR 36) as primary keys and include soft delete support.

## Security

- Passwords are hashed using bcrypt
- CSRF protection enabled for web routes
- API rate limiting configured
- Input validation on all forms
- SQL injection prevention via Eloquent ORM
- XSS protection via Blade templating

## Performance Optimization

- Eager loading to prevent N+1 queries
- Database query caching
- Asset minification and compression
- Pagination on all list views
- Optimized database indexes

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes with descriptive messages
4. Push to your branch
5. Create a Pull Request

## License

This project is proprietary software.

## Support

For support, please contact the development team.
