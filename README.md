# Project Installation Guide

Follow these steps to set up the project on your local machine:

## Prerequisites

- PHP (version 7.4 or later)
- Composer
- MySQL
- phpMyAdmin

## Installation Steps

1. **Clone the Repository**

   ```bash
   git clone https://github.com/E-commerce-CS22/laravel-api.git
   cd laravel-api
   ```

2. **Install Dependencies**

   Run the following command to install the necessary PHP packages:

   ```bash
   composer install
   ```

3. **Environment Setup**

   - Copy the `.env.example` file to a new `.env` file:

     ```bash
     cp .env.example .env
     ```

   - Open the `.env` file and set the `DB_DATABASE` variable to your database name.

4. **Generate Application Key**

   Run the following command to generate the application key:

   ```bash
   php artisan key:generate
   ```

5. **Create Database**

   - Open phpMyAdmin.
   - Create a new database with the name you specified in the `.env` file.

6. **Run Migrations**

   Execute the following command to set up the database tables:

   ```bash
   php artisan migrate
   ```

7. **Seed the Database** (Optional)

   If you want to seed the database with sample data, run:

   ```bash
   php artisan db:seed
   ```

## Running the Application

Start the local development server using:

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser to view the application.

## Additional Notes

- Ensure your PHP and Composer versions are compatible with Laravel.
- Adjust any other environment variables in the `.env` file as necessary for your setup.