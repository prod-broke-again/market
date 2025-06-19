# VSEC Marketplace

<div align="center">
    <a href="README.md">Русский</a> | <b>English</b>
</div>
<br>

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php)
![Filament](https://img.shields.io/badge/Filament-3-FF8A00?style=for-the-badge)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql)

A modern marketplace built on Laravel, designed for interaction between buyers and sellers through a request and response system.

---

## 🚀 About The Project

This project is a platform where buyers can post requests for goods or services, and sellers can send their offers (responses). All further communication takes place in isolated chats.

**Key Features:**
-   **Request/Response System**: The core business logic is built around buyers creating requests and sellers submitting responses.
-   **Role-Based Model**: Clear separation of users into Buyers (`buyer`), Sellers (`seller`), and Administration (`super_admin`).
-   **Shops and Products**: Sellers can create their own storefronts (shops) and manage their products.
-   **Admin Panel**: A powerful and flexible admin panel based on **Filament** for managing all aspects of the platform.

## 🛠 Tech Stack

-   **Backend**: Laravel 12, PHP 8.3
-   **Admin Panel**: Filament 3
-   **Database**: MySQL
-   **Authorization & Permissions**: `spatie/laravel-permission` & `bezhansalleh/filament-shield`
-   **Frontend (Planned)**: Vue.js, Vite, TailwindCSS
-   **Real-time (Planned)**: Laravel Echo, Pusher / Soketi

---

## ✅ Current Progress (What's Done)

1.  **Architecture Design**:
    -   Developed the database structure based on business logic analysis.
    -   Created all necessary migrations for entities: `users`, `users_info`, `shops`, `products`, `categories`, `customer_requests`, `responses`, `chats`, `chat_messages`, etc.

2.  **Eloquent Models**:
    -   Created all models with correctly configured relationships (`hasMany`, `belongsTo`, `morphMany`, etc.).
    -   Models are typed using modern PHP features.

3.  **Filament Admin Panel**:
    -   Installed and configured the Filament admin panel.
    -   Created resources for all key models (`User`, `Shop`, `Product`, `Category`, `CustomerRequest`, `Response`).

4.  **Role-Based Access Control (RBAC)**:
    -   Integrated `spatie/laravel-permission` package for role management.
    -   Integrated `filament-shield` plugin for convenient permission management through the admin panel.
    -   Configured policies to restrict access:
        -   Buyers (`buyer`) do not have access to the admin panel.
        -   Sellers (`seller`) can only see and manage **their own** data (shops, products, etc.).
        -   Administrators (`super_admin`) have full access.

5.  **Seeders for Initial Data**:
    -   Created `RolesAndAdminSeeder` to automatically create roles (`seller`, `buyer`) and users.
    -   Configured `ShieldSeeder` to create the `super_admin` role and all necessary permissions.

---

## ⚙️ Installation and Setup

1.  **Clone the repository**:
    ```bash
    git clone https://github.com/prod-broke-again/market.git
    cd market
    ```

2.  **Install dependencies**:
    ```bash
    composer install
    npm install
    ```

3.  **Set up the environment**:
    -   Copy `.env.example` to `.env`: `cp .env.example .env`
    -   Generate an application key: `php artisan key:generate`
    -   Configure your database connection in the `.env` file.

4.  **Run migrations and seeders**:
    > **Warning!** This command will completely wipe your database and fill it with test data.
    ```bash
    php artisan migrate:fresh --seed
    ```

5.  **Run the development server**:
    ```bash
    php artisan serve
    ```

6.  **Log in to the admin panel**:
    -   **URL**: `http://localhost:8000/admin`
    -   **Login**: `admin@admin.com`
    -   **Password**: `password`

---

## 🗺️ Roadmap

-   [ ] **Frontend Implementation** with Vue.js, Vite, and TailwindCSS.
-   [ ] **API Development** for backend-frontend interaction.
-   [ ] **Real-time Feature Implementation**: chats and notifications.
-   [ ] **Refine Access Policies** for all remaining resources.
-   [ ] **Implement Core Business Logic**: full cycle from request creation to completion.
-   [ ] **Cover Code with Tests** (Unit and Feature).

---

## 🤝 Contributing

Contributions are what make the open-source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

1.  Fork the Project.
2.  Create your Feature Branch (`git checkout -b feature/AmazingFeature`).
3.  Commit your Changes (`git commit -m 'Add some AmazingFeature'`).
4.  Push to the Branch (`git push origin feature/AmazingFeature`).
5.  Open a Pull Request. 