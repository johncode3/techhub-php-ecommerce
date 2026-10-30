# TechHub

TechHub is an online shop project built with plain PHP, MySQL, HTML, CSS, and JavaScript.

This is a learning project. It is not Laravel, but it follows a simple MVC-style structure.

## What is included

### Customer pages

- Home
- Products and product details
- About
- Contact
- Account
- Cart
- Checkout
- Register, login, and logout
- Orders

### Admin pages

- Admin login and registration
- Dashboard
- Products
- Categories and sub-categories
- Brands
- Inventory
- Orders
- Users
- Reports
- Admin account

## Project structure

```text
app/
└── http/controllers/       Application controllers

config/
├── config.php              Shared configuration
├── config.local.example.php
└── config.local.php        Local settings (not committed)

database/
└── db.php                  MySQL connection

public/
├── index.php               Application entry point
└── assets/                 CSS, JavaScript, and images

resources/views/
├── pages/                  Customer pages
└── admin/                  Admin pages
```

## Run locally

1. Put the project inside your Laragon `www` folder.
2. Create a MySQL database named `techhub`.
3. Copy `config/config.local.example.php` to `config/config.local.php`.
4. Add your local database password and PayPal sandbox client ID to `config.local.php`.
5. Open the project through Laragon.

The database schema is not included yet, so the required tables must be created separately.

## Note

This project is for learning and is still in development.