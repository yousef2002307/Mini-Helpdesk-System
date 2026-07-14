# Mini Helpdesk System

A clean, modern Helpdesk and Ticketing System built using **Laravel 11**, **Inertia.js**, **React**, and **TypeScript**, styled with premium custom Vanilla CSS variables for a beautiful, responsive Light Mode user experience. The application runs fully containerized via Docker.

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

---

## Accessing the Application

Once the containers are up and running, you can access the application at:
👉 **[http://localhost:8000](http://localhost:8000)**

### Default Credentials
- **Admin User**:
  - **Email**: `test@admin.com`
  - **Password**: `12345678`
- **Standard User**:
  - You can sign up with a new account directly via the user interface, or log in as the default admin to view the ticket queues.

---

## Running Tests
Run the PHP Unit / Feature tests inside the container using:
```bash
docker-compose exec backend php artisan test
```
