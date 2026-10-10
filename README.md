# inventory-app
Mini Project Inventory Management System built with PHP, PDO, MySQL, and Soft Blue UI.

📦 Inventory Management System (PHP Native & MySQL)
Sistem Informasi Manajemen Inventaris berbasis web yang dibangun menggunakan PHP Native dan database MySQL. Aplikasi ini dirancang untuk membantu pengelolaan produk, pemantauan stok secara real-time, serta pencatatan transaksi barang masuk dan barang keluar secara efisien dan aman.

📸 Fitur Utama

🔐 Autentikasi & Otorisasi Pengguna:
Login multi-role (ADMIN dan STAFF).
Enkripsi password menggunakan password_hash() (Bcrypt).
Proteksi Session untuk keamanan halaman internal.

📊 Dashboard Ringkasan Inventory:
Stat Cards informatif (Total Produk, Nilai Total Inventaris, Produk Stok Rendah, Barang Masuk, Barang Keluar).
Tabel Peringatan Otomatis untuk produk dengan stok di bawah batas minimum (minimum_stock).

📦 Manajemen Data Produk (CRUD):
Tambah, Edit, dan Hapus Produk.
Pencatatan Detail Produk: Kode SKU, Nama Produk, Kategori, Harga, Stok Saat Ini, dan Batas Minimum Stok.
Fitur Pencarian (Search) dan Filter Kategori produk.

🔄 Transaksi Stok (Masuk & Keluar):
Pencatatan barang masuk (IN) dan barang keluar (OUT).
Fitur Database Transaction (BEGIN...COMMIT) untuk menjamin konsistensi data stok.
Riwayat/Histori pencatatan transaksi yang detail.

🛡️ Fitur Keamanan (Security):
Mencegah serangan SQL Injection menggunakan PDO Prepared Statements.
Mencegah serangan XSS (Cross-Site Scripting) menggunakan HTML Escaping.

🛠️ Teknologi yang Digunakan
Language: PHP 8.x
Database: MySQL / MariaDB
Driver Database: PHP Data Objects (PDO)
Frontend / UI: HTML5, CSS3 (Custom Soft Blue & Pastel Theme), Responsive Layout
Environment Development: XAMPP / Laragon

📁 Struktur Direktori Proyek
inventory-app/
├── config/
│   └── database.php        # Koneksi Database menggunakan PDO
├── helpers/
│   └── escape.php          # Function helper pencegah XSS
├── views/
│   ├── header.php          # Header, Navigasi & Styling CSS Global
│   └── footer.php          # Footer halaman
├── login.php               # Halaman Login
├── logout.php              # Process Logout Session
├── dashboard.php           # Halaman Dashboard Ringkasan
├── products.php            # Halaman Manajemen Data Produk (CRUD)
├── product_add.php         # Form Tambah Produk
├── product_edit.php        # Form Edit Produk
├── product_delete.php      # Proses Hapus Produk
├── stock_transaction.php   # Halaman Transaksi Barang Masuk & Keluar
└── README.md               # Dokumentasi Proyek


🚀 Panduan Instalasi & Penggunaan
1. Prasyarat
Pastikan kamu sudah menginstal:
Web Server Local (XAMPP / Laragon)
PHP versi 7.4 atau lebih baru
MySQL Database

2. Langkah Setup Proyek
Clone / Download Proyek
Masukkan folder proyek ini ke dalam direktori htdocs (jika menggunakan XAMPP) atau www (jika menggunakan Laragon).
C:\xampp\htdocs\inventory-app
Import Database MySQL
Buka phpMyAdmin (http://localhost/phpmyadmin).
Buat database baru dengan nama: inventory_db.
Jalankan query SQL berikut untuk membuat tabel dan data awal:

CREATE DATABASE IF NOT EXISTS inventory_db;
USE inventory_db;
-- Tabel Users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff') DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Products
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    stock INT NOT NULL DEFAULT 0,
    minimum_stock INT NOT NULL DEFAULT 5,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Stock Movements
CREATE TABLE stock_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    type ENUM('IN', 'OUT') NOT NULL,
    quantity INT NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- User bawaan (Password: admin123 & staff123)
INSERT INTO users (username, password, role) VALUES
('admin', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1g.A0N1E8bZ0Yy1S3d45F67G8H9I0J.', 'admin'),
('staff', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1g.A0N1E8bZ0Yy1S3d45F67G8H9I0J.', 'staff');


Konfigurasi Database
Pastikan pengaturan pada file config/database.php sudah sesuai dengan environment lokal kamu:

$host = 'localhost';
$db   = 'inventory_db';
$user = 'root';
$pass = ''; // Sesuaikan jika MySQL kamu memakai password


Jalankan Aplikasi
Buka browser dan akses alamat berikut:
http://localhost/inventory-app/login.php


🔑 Akun Default untuk Login
Username
Password
Role
Access Level
admin
admin123
ADMIN
Akses Penuh (CRUD Produk & Transaksi)
staff
staff123
STAFF
Akses Terbatas (View & Transaksi Stok)

📝 Lisensi & Catatan
Proyek ini dikembangkan sebagai Mini Project / Assignment untuk demonstrasi pembuatan aplikasi sistem informasi inventaris berbasis PHP Native dengan arsitektur yang rapi, aman, dan estetik.
