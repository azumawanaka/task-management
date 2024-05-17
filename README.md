# Task Management Application

This is a Task Management application built using Laravel for the backend and Vue 3 for the frontend.

## Features

- **User Authentication**: Users can sign up and sign in
- **Task Management**: Users can create, view, update, and delete tasks. Tasks can have sub-tasks.
- **Search and Filtering**: Users can search for tasks and filter them based on various criteria.

## Technologies Used

- **Frontend**: Vue 3, JQuery, Vuex, Vue Router, Axios
- **Backend**: Laravel, Eloquent ORM, Sanctum (for API authentication)
- **Database**: MySQL, SQLite (for testing)
- **Styling**: Bootstrap CSS
- **Development Tools**: Composer, npm

## Getting Started

To get started with the Task Management application, follow these steps:

1. Clone the repository:

   ```bash
   git clone git@github.com:azumawanaka/task-management.git

2. Navigate to the project directory:
   ```bash
   cd task-management

3. Install PHP dependencies using Composer:
   ```bash
   composer install

4. Install JavaScript dependencies using npm:
   ```bash
   npm install

5. Set up your environment variables:
- Copy the .env.example file to .env:
    ```
    cp .env.example .env

- Generate an application key:
    ```bash
    php artisan key:generate

- Update the .env file with your database credentials and other configuration options.

6. Run database migrations and seeders:
   ```bash
   php artisan migrate --seed

7. Compile frontend assets:
   ```bash
   npm run dev

8. Serve the application:
   ```bash
   php artisan serve

9. Visit http://localhost:8000 in your browser to access the Task Management application.

### License
This project is licensed under the MIT License.
