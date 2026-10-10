<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Mengecek apakah user sudah login
function checkLogin() {
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
}

// Mengecek apakah user adalah Admin
function checkAdmin() {
    checkLogin();
    if ($_SESSION['user']['role'] !== 'admin') {
        http_response_code(403);
        die('Akses Ditolak: Halaman ini khusus untuk Admin.');
    }
}
