# ToDoApp

## About the Project

**ToDoApp** is a productivity-focused task management web application designed to help users organize their daily activities efficiently. The platform supports task creation, prioritization, due dates, drag & drop, attachments, and subtasks, all within a user-friendly and interactive interface.

---

## Technologies Used

The project is built using a modern full-stack architecture:

### **Frontend**

- **Vue 3** – Progressive JavaScript framework.
- **Tailwind CSS** – Utility-first CSS framework for styling.
- **PrimeVue** – Rich UI component library.
- **Inertia.js** – Provides SPA-like experience using Laravel + Vue.
- **Vite** – Fast frontend build tool.

### **Backend**

- **Laravel 10+** – Robust PHP framework for the API and logic.
- **Sanctum** – API authentication for secure access.
- **MySQL** – Relational database system.

### **Testing**

- **PHPUnit** – Framework for unit and feature testing.

---

## Project Structure

The project follows a modular organization to facilitate maintenance and scalability.

````
TODOAPP
│── app
│   ├── Http
│   │   ├── Controllers
│   │   │   ├── Auth
│   │   │   ├── Settings
│   │   │   ├── AttachmentController.php
│   │   │   ├── ProfileController.php
│   │   │   ├── SubTaskController.php
│   │   │   ├── TaskController.php
│   │   ├── Middleware
│   │   ├── Requests
│   ├── Models
│   │   ├── Attachment.php
│   │   ├── SubTask.php
│   │   ├── Task.php
│   │   ├── User.php
│   ├── Providers
│── bootstrap
│── config
│── database
│   ├── factories
│   │   ├── SubTaskFactory.php
│   │   ├── TaskFactory.php
│   │   ├── UserFactory.php
│   ├── migrations
│   ├── seeders
│   │   ├── DatabaseSeeder.php
│   │   ├── TaskSeeder.php
│   │   ├── UserSeeder.php
│── node_modules
│── public
│── resources
│   ├── css
│   │   ├── app.css
│   ├── js
│   │   ├── components
│   │   ├── composables
│   │   ├── directives
│   │   ├── layouts
│   │   ├── lib
│   │   ├── pages
│   │   ├── stores
│   │   ├── types
│   │   ├── app.js
│   │   ├── app.ts
│   │   ├── axios.ts
│   │   ├── bootstrap.js
│   │   ├── ssr.ts
│   ├── views
│── routes
│── storage
│── tests
│── vendor
│── editorconfig
│── .env
│── .env.example
│── .gitattributes
│── .gitingnore
│── .prettierignore
│── .prettierrc
│── artisan
│── components.json
│── composer.json
│── composer.lock
│── eslint.config.js
│── jsconfig.json
│── package-lock.json
│── package.json
│── phpunit.xml
│── postcss.config.js
│── README.md
│── tailwind.config.js
│── tsconfig.json
│── vite.config.js
│── vite.config.ts

---

## Core Features

- Authenticated user access
- Create, edit and delete tasks
- Prioritize tasks (low, medium, high)
- Set due dates (with past-date validation)
- Add rich-text descriptions
- Upload and remove file attachments
- Toggle and manage subtasks
- Recycle bin and task recovery
- AI Assistant (Chatbot) for instant support, quick links, and motivational messages
- Help & Motivation Page with FAQs, productivity tips, a mood switcher, and an interactive quiz
- Pomodoro Timer** to enhance focus with work/break intervals
- Fully tested with PHPUnit

---

## API Endpoints

These endpoints provide CRUD operations for all core resources:

### **Tasks**
- `GET /tasks` – List user tasks
- `POST /tasks` – Create a new task
- `PATCH /tasks/{task}` – Update task
- `DELETE /tasks/{task}` – Soft delete task
- `PATCH /tasks/restore/{id}` – Restore from recycle bin

### **SubTasks**
- `POST /tasks/{task}/subtasks` – Create multiple subtasks
- `PATCH /subtasks/{subtask}` – Update subtask
- `PATCH /subtasks/{subtask}/toggle` – Toggle completion
- `DELETE /subtasks/{subtask}` – Delete subtask

### **Attachments**
- `POST /tasks/{task}/attachments/upload` – Upload files
- `DELETE /tasks/{task}/attachments/{attachment}` – Delete attachment

---

## Installation & Setup

### **Prerequisites**

Before starting, make sure you have installed:

- **PHP 8.2+**
- **Composer** – PHP dependency manager
- **MySQL** – Relational database system
- **Node.js (v18+) + npm (v9+)** – JavaScript runtime and package manager

This project also uses modern frontend tools and frameworks, which will be installed via `npm`:

- **Vite** – Lightning-fast frontend build tool
- **Vue 3** – Progressive JavaScript framework
- **TypeScript** – Strongly typed JavaScript (used via `vue-tsc`)
- **Tailwind CSS** – Utility-first CSS framework
- **Inertia.js (Vue 3 adapter)** – Bridges Laravel with a Vue SPA
- **PrimeVue** – UI component library for Vue
- **Vue Final Modal**, **Vuedraggable**, **Lucide Icons**, **Flatpickr**, and others

> ℹ️ Development and linting tools like **ESLint**, **Prettier**, and **Tailwind CSS plugins** are also included and can be run via `npm scripts`.

### **Step-by-Step Guide**
1. Clone the repository:
   ```bash
   git clone https://github.com/diogopaulino-trainee/ToDoApp.git
   cd ToDoApp
````

2. Install backend dependencies:
    ```bash
    composer install
    ```
3. Install frontend dependencies:
    ```bash
    npm install
    ```
4. Configure the environment:

    ```bash
    cp .env.example .env
    ```

    Update the `.env` file with database credentials.

5. Generate the application key:
    ```bash
    php artisan key:generate
    ```
6. Run migrations and seeders:
    ```bash
    php artisan migrate --seed
    ```
7. Start the Laravel server:
    ```bash
    php artisan serve
    ```
8. Start the frontend (Vite)
    ```bash
    npm run dev
    ```

Now, the application will be available at `http://todoapp.test`.

---

## Testing

To ensure code quality, automated tests have been implemented with **PHPUnit**.

To run the tests:

```bash
php artisan test
```

# Run specific tests

```bash
php artisan test --filter=TaskTest
```

```bash
php artisan test --filter=SubTaskTest
```

```bash
php artisan test --filter=AttachmentTest
```

---

### Contact

If you have any questions or suggestions, feel free to contact via [LinkedIn](https://www.linkedin.com/in/diogo-paulino/), [Personal GitHub](https://github.com/diogopaulin0) or [InovCorp GitHub](https://github.com/diogopaulino-trainee).

---
