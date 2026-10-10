<?php
$host = 'localhost';
$db   = 'inventory_db';
$user = 'root';
$pass = ''; // Biarkan kosong jika kamu menggunakan XAMPP standar

try {
    // Menghubungkan ke database MySQL menggunakan PDO
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Menampilkan error jika ada masalah query
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Mengembalikan data dalam bentuk array asosiatif
    ]);
} catch (PDOException $e) {
    // Tampilkan pesan jika koneksi gagal
    die("Koneksi database gagal: " . $e->getMessage());
}
