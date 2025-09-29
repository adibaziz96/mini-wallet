# Mini Wallet Application

A Laravel + Vue 3 + Docker.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Backend Setup](#backend-setup)
- [Frontend Setup](#frontend-setup)
- [Database Seeding](#database-seeding)
- [Docker Setup](#docker-setup)
- [Running the Project](#running-the-project)
- [API Endpoints](#api-endpoints)
- [Notes](#notes)

---

## Features

- User authentication (login/logout)
- Real-time transactions with Laravel Echo & Pusher
- High concurrency support using database transactions & row locking
- Paginated transaction history
- Incoming/outgoing transactions
- Responsive Vue 3 frontend using Composition API

---

## Requirements

- PHP >= 8.2
- Composer
- Node.js >= 23
- NPM / Yarn
- MySQL
- Laravel 12
- Pusher
- Docker

---

## Installation

Clone the repository:

```bash
git clone https://github.com/adibaziz96/mini-wallet.git
cd mini-wallet
```

---

## Backend Setup

Install PHP dependencies:

```bash
cd backend
composer install
```

Copy .env.example to .env:

```bash
cp .env.example .env
```

Set database credentials in .env:

```bash
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=wallet_db
DB_USERNAME=root
DB_PASSWORD=root
```

Generate application key:

```bash
php artisan key:generate
```

Run migrations:

```bash
php artisan migrate --seed
```

## Frontend Setup

Navigate to frontend folder:

```bash
cd frontend
```

Install Node dependencies:

```bash
npm install
```


Update API base URL in axios.js if needed:

```bash
const api = axios.create({
  baseURL: "http://localhost:8000",
  headers: {
    Authorization: `Bearer ${localStorage.getItem("token")}`,
  },
});
```


Start frontend:

```bash
npm run dev
```

## Build and start containers:

```bash
docker-compose up --build -d
```

Containers:

Laravel app → http://localhost:8000

Vue frontend → http://localhost:5173

MySQL database → port 3306

Laravel backend inside Docker:

```bash
docker exec -it wallet-php bash
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

Stop containers:

```bash
docker-compose down
```

## API Endpoints

Method	Endpoint	Description
POST	/api/login	Login user
GET	/api/me	Get current user
GET	/api/users	Get all users
GET	/api/transactions	Get transactions (paginated)
POST	/api/transactions	Make a transfer

## Notes

All transactions are atomic using DB::transaction() and lockForUpdate().

User balance is stored in the users table for fast performance.

Incoming money is green, outgoing money is red.

Transaction timestamps are displayed in US format with AM/PM.