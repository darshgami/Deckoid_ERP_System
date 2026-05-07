# Lead Management ERP System

A simple PHP-based Lead Management ERP system built with MySQL and Tailwind CSS.

## Features

- User authentication with JWT tokens
- Lead management (CRUD operations)
- Dashboard with statistics
- CSV export functionality
- Responsive design with Tailwind CSS

## Requirements

- PHP 8.1 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- XAMPP (recommended for Windows)

## Installation

1. **Clone or download the project** to your web server root directory (e.g., `C:\xampp\htdocs\`)

2. **Configure Environment**
   - Copy `.env` file and update database settings:
   ```
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=lead_management
   DB_USERNAME=root
   DB_PASSWORD=your_password
   APP_KEY=your_secret_key_here
   ```

3. **Setup Database**
   - Start XAMPP MySQL
   - Run the setup script:
   ```bash
   php setup.php
   ```
   This will:
   - Create the database
   - Create all tables
   - Add a sample admin user

4. **Access the Application**
   - Open your browser and go to: `http://localhost/admin/login.php`
   - Login with:
     - Username: `admin`
     - Password: `admin123`

## Project Structure

```
├── admin/          # Admin panel pages
├── api/            # API endpoints
├── assets/         # CSS, JS, images
├── config/         # Configuration files
├── database/       # Database schema
├── docs/           # Documentation
├── exports/        # Exported files
├── includes/       # PHP classes and functions
├── uploads/        # File uploads
├── setup.php       # Database setup script
└── .env            # Environment configuration
```

## API Endpoints

### Authentication
- `POST /api/auth.php/register` - Register new user
- `POST /api/auth.php/login` - User login
- `POST /api/auth.php/logout` - User logout
- `POST /api/auth.php/refresh` - Refresh access token

### Leads
- `GET /api/leads.php` - List leads (with pagination/filters)
- `POST /api/leads.php` - Create new lead
- `PUT /api/leads.php/{id}` - Update lead

### Dashboard
- `GET /api/dashboard.php` - Get dashboard statistics

### Export
- `GET /api/export.php` - Export leads to CSV

## Default Login Credentials

- **Username:** admin
- **Password:** admin123

## Development

### Adding New Features

1. Create PHP files in appropriate directories
2. Update database schema in `database/schema.sql`
3. Run `php setup.php` to apply changes
4. Add frontend pages in `admin/` directory

### Styling

The project uses Tailwind CSS for styling. Custom styles can be added to `assets/css/style.css`.

## Security Notes

- Change the default admin password after first login
- Update `APP_KEY` in `.env` with a secure random string
- Configure proper file permissions for production
- Use HTTPS in production environment

## Troubleshooting

### Database Connection Issues
- Ensure MySQL is running
- Check database credentials in `.env`
- Verify database name exists

### Permission Issues
- Ensure web server has write permissions for `exports/` and `uploads/` directories
- Check PHP error logs for detailed error messages

### Login Issues
- Clear browser cache and cookies
- Check if JWT tokens are properly stored in localStorage
- Verify database has user records

## License

This project is for educational and commercial use.