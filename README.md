🤖 N8N AI Assistant - Admin API

A secure, multi-tenant Laravel 11 API backend for the n8n AI Call Assistant.

This project provides the complete administrative backend for managing client companies, users, roles, and services. It's built on a clean, service-oriented architecture, secured by spatie/laravel-permission, and ready to be consumed by a dashboard or frontend application.

📋 Table of Contents

🚀 Key Features

💻 Tech Stack

🛠️ Getting Started

Prerequisites

Installation

Database Setup

Default Super-Admin

▶️ Running the Application

📖 API Documentation (Swagger)

🧪 Postman

📄 License

🚀 Key Features

🔑 Authentication: Token-based API auth with Laravel Sanctum.

🛡️ Authorization: Powerful Role-Based Access Control (RBAC) via spatie/laravel-permission (Super-Admin, Admin, Observer roles).

🏗️ Architecture: Clean, scalable Service-Oriented Architecture (Controllers, Services, Policies, Form Requests, API Resources).

🏢 Multi-Tenancy:

Full, protected CRUD for Companies (Super-Admin only).

Full, protected CRUD for Service Types (scoped to a company).

👥 User Management:

Full CRUD for Users.

Assign roles and a company_id to users upon creation/update.

📄 Documentation: Automatic OpenAPI (Swagger) documentation via l5-swagger.

💻 Tech Stack

Framework: Laravel 11

PHP: 8.2+

Database: MySQL / MariaDB

Authentication: Laravel Sanctum

Authorization: spatie/laravel-permission

API Documentation: l5-swagger

🛠️ Getting Started

Follow these steps to get the project running on your local machine.

Prerequisites

PHP 8.2+

Composer

A MySQL/MariaDB database (e.g., via Laragon, XAMPP, Docker)

Installation

# 1. Clone the repository
git clone [https://github.com/your-username/your-repo-name.git](https://github.com/your-username/your-repo-name.git)

# 2. Enter the new directory
cd your-repo-name

# 3. Install all PHP dependencies
composer install

# 4. Copy the environment file
cp .env.example .env

# 5. Generate your unique application key
php artisan key:generate


Database Setup

Configure .env
Open your .env file and set up your database credentials.

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=n8n-laravel
DB_USERNAME=root
DB_PASSWORD=


Run Migrations & Seeders
This is the most important step. It will build your database schema and create the default roles, permissions, and the Super-Admin user.

# This command wipes and rebuilds the database
php artisan migrate:fresh --seed


🔑 Default Super-Admin

After seeding, a Super-Admin user is created for you:

Email: ali.melmedas1383@gmail.com
Password: password

Use these credentials at the POST /api/v1/login endpoint to get your API token.

▶️ Running the Application

Start the local development server:

# Start the server on [http://127.0.0.1:8000](http://127.0.0.1:8000)
php artisan serve


All API endpoints are prefixed with /api/v1.
Base URL: http://127.0.0.1:8000/api/v1

📖 API Documentation (Swagger)

This project uses l5-swagger to generate interactive API documentation from the code.

Generate the Docs
(Required any time you update the @OA annotations in the code)

php artisan l5-swagger:generate


View the Docs
Open the Swagger UI in your browser:
http://127.0.0.1:8000/api/documentation

🧪 Postman

A Postman collection (n8n-api.postman_collection.json) is included in the root of this repository.

You can import it directly into Postman. It is pre-configured with all available endpoints and uses collection variables for {{baseUrl}} and {{apiToken}} to make testing easy.

📄 License

This project is licensed under the MIT License. See the LICENSE file for details.
