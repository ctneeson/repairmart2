<p align="center"><img src="https://github.com/ctneeson/repairmart2/blob/main/public/img/RepairMart-logo.png" width="400" alt="RepairMart Logo"></p>

## About RepairMart

RepairMart is an online marketplace-style web application where Customers can submit requests for repair of their electronic goods, and Repair Specialists can review requests and submit quotes for repair.
Customers may review submitted quotes and accept in order to create a repair order, which will then be managed until completion through the RepairMart system.

## Installation Instructions

### Prerequisites

-   PHP 8.2 or higher
-   Composer
-   MySQL 8.0 or SQLite (for development)
-   Node.js and npm
-   Laravel requirements: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML

### Step 1: Clone the Repository

```bash
git clone https://github.com/yourusername/repairmart2.git
cd repairmart2
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

### Step 3: Install JavaScript Dependencies

```bash
npm install
```

### Step 4: Configure Environment

1. Create a copy of the environment file:

```bash
cp .env.example .env
```

2. Generate application key:

```bash
php artisan key:generate
```

3. Configure your database in the .env file:

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=repairmart
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

4. Configure OAuth settings for social authentication:

```bash
FACEBOOK_CLIENT_ID=your_facebook_client_id
FACEBOOK_CLIENT_SECRET=your_facebook_client_secret
FACEBOOK_REDIRECT=http://localhost:8000/auth/facebook/callback

GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT=http://localhost:8000/auth/google/callback
```

### Step 5: Set Up the Database

1. Run migrations to create database tables:

```bash
php artisan migrate
```

2. Seed the database with initial data:

```bash
php artisan db:seed
```

### Step 6: Storage and File Uploads Configuration

1. Create symbolic link for storage:

```bash
php artisan storage:link
```

2. Ensure the proper directory permissions:

```bash
chmod -R 775 storage bootstrap/cache
```

### Step 7: Build Assets

```bash
npm run build
```

### Step 8: Start Development Server

```bash
php artisan serve
```

The application will be available at http://localhost:8000

### Step 9: Schedule Cron Job (Production)

For production environments, add the Laravel scheduler to your crontab:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

This enables automated tasks like listing expiration and email notifications.

Default Admin User
After seeding, you can log in with the default admin account:

Email: admin@repairmart.com
Password: password

## Testing

To run the test suite:

```bash
php artisan test
```

For development with SQLite:

```bash
php artisan test --env=testing
```

## Additional Configuration

Email: Configure your mail driver in the .env file for notification emails
Queue: For production, configure a queue driver like Redis or database
Search: Full-text search is configured automatically in development; see documentation for production settings
