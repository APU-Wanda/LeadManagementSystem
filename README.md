# Lead Management System

## Overview
The Lead Management System is a web-based application that allows employees to import, manage, and export leads. It provides essential features such as lead CRUD operations, filtering, searching, role-based access control (RBAC), and Excel file handling.

## Features
### 1. Lead Import
- Upload Excel files to import leads.
- Validate required fields: `name`, `email`, `phone`, `status`.
- Ensure unique emails and valid phone numbers.
- Display import summary (successful and failed records).

### 2. Lead Management
- List all leads in a paginated table.
- Edit, update, and delete leads.
- Search leads by name or email.
- Filter leads by status (`New`, `In Progress`, `Closed`).
- Multi-delete functionality (Bulk deletion).
- Role-based access control (RBAC):
    - Admins can manage all leads.
    - Employees can manage only assigned leads.

### 3. Lead Export
- Export filtered leads to an Excel file.
- Include columns: `name`, `email`, `phone`, `status`, `date_added`.

## Technologies Used
- **Backend**: PHP, Laravel 11
- **Frontend**: Vue.js, Tailwind CSS
- **Database**: PostgreSQL
- **Authentication**: Laravel Breeze
- **Excel Handling**: PhpSpreadsheet
- **API Communication**: Laravel HTTP Client

## Installation Steps
### 1. Clone the Repository
```sh
git clone https://github.com/your-username/lead-management-system.git
cd lead-management-system
```

### 2. Install Dependencies
```sh
composer install
npm install
```

### 3. Set Up Environment Variables
Copy the `.env.example` file to `.env`:
```sh
cp .env.example .env
```
Update the `.env` file with your database credentials:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Run Migrations
```sh
php artisan migrate
```

### 6. Start the Development Server
```sh
php artisan serve
```

### 7. Run Vite for Frontend Development
```sh
npm run dev
```

## API Endpoints
### Lead Management
| Method | Endpoint            | Description                  |
|--------|---------------------|------------------------------|
| GET    | `/api/leads`        | Get all leads                |
| POST   | `/api/leads`        | Create a new lead            |
| PUT    | `/api/leads/{id}`   | Update a lead                |
| DELETE | `/api/leads/{id}`   | Delete a lead                |
| POST   | `/api/leads/import` | Import leads from Excel      |
| GET    | `/api/leads/export` | Export leads to Excel        |

## Role-Based Access Control (RBAC)
- **Admin**: Full access (view, edit, delete, import/export leads).
- **Employee**: Can only manage assigned leads.
- Middleware protects routes to ensure proper access control.

## Testing
Run PHPUnit tests:
```sh
php artisan test
```

## Example Excel File for Testing
An example `leads_sample.xlsx` file is included in the `/storage/app/public/` directory for testing lead imports.

## TODO
- Implement dashboard with lead statistics.
- Add email notifications for lead updates.
- Track lead history (e.g., status changes).

## License
This project is licensed under the MIT License.

