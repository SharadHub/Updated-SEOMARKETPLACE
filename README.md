# SEOMarketplace

A PHP-based platform connecting businesses with SEO professionals. Clients post jobs, workers apply, and applications are managed through a simple dashboard.

## Tech Stack

- **Backend**: PHP 8.x
- **Database**: MySQL
- **Frontend**: TailwindCSS
- **Server**: Built-in PHP server (production) / Apache (local)

## Quick Start

### Local Development

```bash
# Clone the repository
git clone https://github.com/SharadHub/Updated-SEOMARKETPLACE.git
cd Updated-SEOMARKETPLACE

# Start the server
php -S localhost:8000
```

### Environment Variables

Create a `.env` file or set these in your hosting panel:

```
DB_HOST=your_database_host
DB_USER=your_database_user
DB_PASS=your_database_password
DB_NAME=your_database_name
```

### Database Schema

Required tables:
- `client` - Client accounts
- `worker` - Worker accounts
- `jobs` - Job postings (linked to clients)
- `applications` - Job applications (linked to workers and jobs)

## Deployment

### Render (Recommended)

1. Push code to GitHub
2. Create new Web Service on Render
3. Set environment variables
4. Deploy

**Build Command**: (none - PHP is native)

**Start Command**: `bash start.sh`

## Project Structure

```
├── includes/
│   ├── auth.php          # Session authentication
│   └── db_connect.php    # Database connection
├── css/                  # Stylesheets
├── js/                   # JavaScript
├── images/               # Static assets
├── *.php                 # Application pages
└── start.sh              # Production server script
```

## Security

- All protected routes require authentication via `includes/auth.php`
- Role-based access control (client vs worker)
- Ownership verification on destructive operations (delete job, edit job)
- Prepared statements for database queries
- Environment-based configuration (no hardcoded credentials)

## License

MIT
