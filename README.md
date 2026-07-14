# Mini Helpdesk System

A clean, modern Helpdesk and Ticketing System built using **Laravel 13**, **Inertia.js**, **React**, and **TypeScript**, styled with premium custom Vanilla CSS variables for a beautiful, responsive Light Mode user experience. The application runs fully containerized via Docker.

---

## Features

- **User Dashboard**: Standard users can submit tickets, view their tickets in a paginated table, and reply to their own tickets.
- **Admin Dashboard**: Administrators can view all system tickets, filter them by status, change ticket statuses (`open`, `in_progress`, `closed`), and reply to any ticket.
- **Type Safety**: Built with TypeScript on the frontend and strict PHP 8.3 Data Transfer Objects (DTOs) on the backend.
- **Clean Architecture**: Standardized multi-layered architecture: Controllers ➔ Requests ➔ DTOs ➔ Services ➔ Repositories.

---

## Installation & Setup

Follow these steps to run the application locally using Docker:

### 1. Prerequisites
Ensure you have the following installed on your machine:
- [Git](https://git-scm.com/)
- [Docker & Docker Compose](https://www.docker.com/products/docker-desktop/)

### 2. Clone the Repository
```bash
git clone https://github.com/yousef2002307/Mini-Helpdesk-System.git
cd Mini-Helpdesk-System
```

### 3. Configure the Environment
Copy the example environment file:
```bash
cp .env.example .env
```
Example database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=helpdesk
DB_USERNAME=helpdesk_user
DB_PASSWORD=secret_password
```

*(Note: Inside the Docker container, the database credentials are pre-configured to automatically match the MySQL service defined in `docker-compose.yml`.)*

### 4. Build and Start the Containers
Start the DB and Backend services:
```bash
docker-compose up -d --build
```
This command automatically installs PHP dependencies (via Composer) and Node dependencies (via npm), starts the Vite dev server, and spins up the PHP artisan server.

### 5. Generate the Application Key
```bash
docker-compose exec backend php artisan key:generate
```

### 6. Run Migrations & Seed the Database
Migrating is handled automatically during container startup. Run the database seeders to populate initial admin, user, and ticket records:
```bash
docker-compose exec backend php artisan db:seed
```

> [!IMPORTANT]
> **Verify Application Key Generation:** Before attempting to log in or run tests, check your `.env` file on the host machine. Ensure the `APP_KEY` variable is present and populated. If it is empty or missing, run the following command to generate it:
> ```bash
> docker-compose exec backend php artisan key:generate
> ```

---

## Accessing the Application

Once the containers are up and running, you can access the application at:
👉 **[http://localhost:8000](http://localhost:8000)**

### Default Credentials
- **Admin User**:
  - **Email**: `test@admin.com`
  - **Password**: `12345678`
- **Standard User**:
  - **Email**: `test@user.com`
  - **Password**: `12345678`

---

## Running Tests
Run the PHP Unit / Feature tests inside the container using:
```bash
docker-compose exec backend php artisan test
```

---

## API Documentation
Interactive API docs are built using **Knuckles Scribe**.

### Generate Documentation
Generate or update the API documentation static and blade files by running:
```bash
docker-compose exec backend php artisan scribe:generate
```

### Access Documentation
Once generated, you can view the documentation locally at:
👉 **[http://localhost:8000/docs](http://localhost:8000/docs)**

