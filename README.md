# RGC System

> **A full-stack church management system** built with React (TypeScript) for the frontend and Laravel (PHP) for the backend. This system helps manage churches, districts, regions, members, offerings, attendance, expenses, and user roles efficiently.

---

## Table of Contents

* [Features](#features)
* [Tech Stack](#tech-stack)
* [Project Structure](#project-structure)
* [Installation & Setup](#installation--setup)
* [Usage](#usage)
* [Folder Structure](#folder-structure)
* [Contributing](#contributing)
* [License](#license)

---

## Features

* User authentication & role management (admin, pastor, member)
* Dashboard with KPIs and summaries
* Manage regions, districts, churches, and branches
* Member and pastor management
* Attendance tracking
* Offerings and expenses management
* Responsive frontend with intuitive navigation
* RESTful API with Laravel backend
* Context-based state management using React Context API

---

## Tech Stack

**Frontend:**

* React 18 + TypeScript
* React Router
* CSS modules
* Context API for state management
* Axios for API calls

**Backend:**

* Laravel 10
* MySQL / MariaDB
* Composer for dependency management
* REST API design
* Middleware for authentication & authorization

**Other Tools:**

* PHPUnit for backend testing
* Vite for frontend bundling

---

## Project Structure

### Frontend (`frontend/`)

```
src/
├── components/      # Reusable UI components (Navbar, Sidebar, Forms, Loader)
├── context/         # React Context API for authentication & state management
├── pages/           # Feature pages (Dashboard, Churches, Members, Attendance)
├── services/        # API services (auth, resources)
├── styles/          # Page-specific and component CSS
├── App.tsx          # Main app component
├── index.tsx        # Entry point
```

### Backend (`backend/`)

```
app/            # Controllers, Models, and core logic
bootstrap/      # Laravel bootstrap files
config/         # Configuration files
database/       # Migrations and seeders
public/         # Public assets & index.php
routes/         # API routes
tests/          # PHPUnit tests
artisan         # Laravel CLI
composer.json   # PHP dependencies
```

---

## Installation & Setup

### Backend

1. Clone the repository:

```bash
git clone https://github.com/yourusername/rgc-system.git
cd rgc-system/backend
```

2. Install PHP dependencies:

```bash
composer install
```

3. Copy `.env.example` to `.env` and configure your database credentials:

```bash
cp .env.example .env
php artisan key:generate
```

4. Run migrations & seeders:

```bash
php artisan migrate --seed
```

5. Start the backend server:

```bash
php artisan serve
```

### Frontend

1. Navigate to the frontend folder:

```bash
cd ../frontend
```

2. Install npm dependencies:

```bash
npm install
```

3. Start the frontend dev server:

```bash
npm run dev
```

4. Open the app in your browser at `http://localhost:5173`

---

## Usage

* Login with admin credentials to manage churches, members, pastors, and districts.
* Navigate through the sidebar to access different modules (Dashboard, Regions, Districts, Churches, Members, Offerings, Expenses, etc.)
* Add, update, or delete records via the provided forms.
* Track attendance and manage offerings with intuitive UI.

---

## Contributing

1. Fork the repository
2. Create a new feature branch (`git checkout -b feature/xyz`)
3. Commit your changes (`git commit -m "Add new feature"`)
4. Push to the branch (`git push origin feature/xyz`)
5. Open a Pull Request

---

## License

This project is **MIT licensed**.
