# Mini Wallet Application

A Laravel + Vue 3 + Docker.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Host Setup (Optional)](#host)
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

## Host Setup (Optional)

If you want to use custom url (on MacOS):

```bash
nano /etc/hosts
```
Add your prefer domain
127.0.0.1 wallet.local.com
127.0.0.1 api-wallet.local.com

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
DB_DATABASE=wallet
DB_USERNAME=wallet_user
DB_PASSWORD=wallet_pass
```

Generate application key:

```bash
php artisan key:generate
```

Run migrations:

```bash
php artisan migrate --seed
```

Start Queue Worker:

```bash
php artisan queue:work
```

---

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
  baseURL: "http://api-wallet.local.com",
  headers: {
    Authorization: `Bearer ${localStorage.getItem("token")}`,
  },
});
```

Start frontend:

```bash
npm run dev
```

---

## Build and start containers:

```bash
docker-compose up --build -d
```

Containers:

Laravel app → http://api-wallet.local.com

Vue frontend → http://wallet.local.com

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

---

## API Endpoints

| Method | Endpoint                | Description                   |
|--------|------------------------|------------------------------- |
| POST   | `/api/login`            | Login user                    |
| GET    | `/api/me`               | Get current authenticated user|
| GET    | `/api/users`            | Get all users                 |
| GET    | `/api/transactions`     | Get transactions (paginated)  |
| POST   | `/api/transactions`     | Make a transfer               |

---

## Notes

All transactions are atomic using DB::transaction() and lockForUpdate().

User balance is stored in the users table for fast performance.

Incoming money is green, outgoing money is red.

Transaction timestamps are displayed in US format with AM/PM.