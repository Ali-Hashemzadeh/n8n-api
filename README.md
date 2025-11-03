N8N AI Assistant - Admin API
This is the official Laravel 11 backend API for the n8n AI Call Assistant. It is designed to store call reports (future) and handle the multi-tenant administration of client companies (e.g., clinics, barbershops), their users, and their services.

This API is built using a clean, service-oriented architecture and is secured with role-based access control.

🚀 Key Features
Authentication: Token-based API authentication using Laravel Sanctum.

Authorization: Role-Based Access Control (RBAC) via spatie/laravel-permission.

Roles: Super-Admin, Admin, Observer.

Architecture: Service-Oriented Architecture (Services, Policies, Form Requests, API Resources).

Multi-Tenancy:

Full CRUD for Companies (Super-Admin only).

Full CRUD for Service Types (scoped to a company).

User Management:

Full CRUD for Users.

Assign roles and a company_id to users.

API Documentation: Automatic OpenAPI (Swagger) documentation via l5-swagger.

💻 Technology Stack
Framework: Laravel 11

PHP: 8.2+

Database: MySQL / MariaDB

Authentication: Laravel Sanctum

Authorization: spatie/laravel-permission

API Documentation: l5-swagger

🛠️ Local Installation & Setup
Follow these steps to get the project running on your local machine.

1. Clone the Repository
   Bash

git clone https://github.com/your-username/your-repo-name.git
cd your-repo-name
2. Install Dependencies
   Bash

composer install
3. Environment Setup
   Bash

# Copy the example .env file
cp .env.example .env

# Generate your application key
php artisan key:generate
4. Configure Your Database
   Open your .env file and set up your database credentials. The database name from your SQL file is n8n-laravel.

Code snippet

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=n8n-laravel
DB_USERNAME=root
DB_PASSWORD=
5. Run Migrations & Seeders
   This is the most important step. The migrate:fresh command will build your database, and the --seed flag will run all seeders to create default permissions, roles, and the Super-Admin user.

Bash

php artisan migrate:fresh --seed
🔑 Default Admin User
After seeding the database, a Super-Admin user is created for you:

Email: ali.melmedas1383@gmail.com

Password: password

You can use these credentials at the POST /api/v1/login endpoint to get your API token.

🏃 Running the Application
1. Start the Server
   Bash

php artisan serve
By default, the API will be available at http://127.0.0.1:8000.

2. Access the API
   All API endpoints are prefixed with /api/v1.

Base URL: http://127.0.0.1:8000/api/v1

📖 API Documentation (Swagger)
This project uses l5-swagger to generate API documentation from the controller annotations.

1. Generate the Docs
   Any time you update the @OA annotations, you must re-generate the documentation:

Bash

php artisan l5-swagger:generate
2. View the Docs
   Access the Swagger UI in your browser: http://127.0.0.1:8000/api/documentation

🧪 Postman
A Postman collection (n8n-api.postman_collection.json) is included in the root of this repository.

You can import it directly into Postman. It is pre-configured with all available endpoints and uses collection variables for {{baseUrl}} and {{apiToken}} to make testing easy.

🚓 Running Tests
To run the application's feature tests (if any):

Bash

php artisan test
📄 License
This project is licensed under the MIT License.
