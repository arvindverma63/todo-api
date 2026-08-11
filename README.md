# todo-api

A lightweight PHP REST API connecting to a remote MySQL database for the My-Task helper, ironing, and appliance tracker application.

## Prerequisites

- **PHP 7.4+** or **PHP 8.0+**
- **PDO MySQL Extension** enabled in your `php.ini`

## How to Run Locally

To spin up a local development server, run the following command in this directory:

```bash
php -S localhost:8000
```

The API will be accessible at: `http://localhost:8000/api`

## MySQL Remote Connection Configuration

The remote database connection is configured in [db.php](file:///d:/github/todo-api/db.php). Update the configuration variables at the top of that file to point to your target MySQL host, database name, user, and password.

## API Endpoints

- **Employees**: `/api/employees` (GET, POST, PUT, DELETE)
- **Attendance**: `/api/attendance` (GET, POST, DELETE)
- **Ironing Workers**: `/api/ironing-workers` (GET, POST, DELETE)
- **Ironing Rates**: `/api/ironing-workers/{workerId}/rates` (GET, POST)
- **Ironing Records**: `/api/ironing-records` (GET, POST, DELETE)
- **Ironing Payments**: `/api/ironing-payments` (GET, POST, DELETE)
- **Appliances**: `/api/appliances` (GET, POST, PUT, DELETE)
- **Service Records**: `/api/service-records` (GET, POST, DELETE)
- **Image/Invoice Uploads**: `/api/upload` (POST)
