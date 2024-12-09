# A Dynamic Blog Management Application

This is a dynamic blog management system for managing and publishing blog posts. It is built using PHP and supports database migrations and an integrated development server.

## Features
- Blog creation, editing, and management.
- Database migration for easy setup.
- Integrated local development server.

---

## Prerequisites

- PHP 7.4 or higher
- Composer (Dependency Manager for PHP)
- A MySQL database (or compatible)

---

## Installation Instructions

Follow these steps to set up and run the application on your local environment:

### Step 1: Clone the Repository
Clone the repository using the following command:
```bash
git clone https://github.com/mallikarjun92/blog-management.git
```

Navigate to the repository root:
```bash
cd blog-management
```

### Step 2: Configure Database Credentials

Edit the `config.php` file (or equivalent configuration file) in the repository root. Set the following values:

- `host`: Your database host (e.g., localhost).
- `dbname`: The name of your database.
- `user`: Your database username.
- `password`: Your database password.
- `charset`: Charset e.g. `utf8mb4`

### Step 3: Run Database Migrations
Run the following command to set up the database schema:
```bash
composer run migrate
```

### Step 4: Start the Development Server
Launch the development server with:
```bash
composer run server
```

The application will be available at `http://localhost:8000`
