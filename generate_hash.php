<?php
require_once __DIR__ . '/config/database.php';

// Generate hash password123
$password = 'password123';
$hash = password_hash($password, PASSWORD_BCRYPT);

try {
    // Reset dan isi ulang user admin & staff
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $pdo->exec("TRUNCATE TABLE users;");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (:user, :hash, :role)");
    
    // Insert Admin
    $stmt->execute(['user' => 'admin', 'hash' => $hash, 'role' => 'admin']);
    // Insert Staff
    $stmt->execute(['user' => 'staff', 'hash' => $hash, 'role' => 'staff']);

    echo "<h2 style='color: green;'>BERHASIL! User admin dan staff sudah di-reset.</h2>";
    echo "<p>Password untuk keduanya adalah: <strong>password123</strong></p>";
    echo "<a href='login.php'>Klik di sini untuk ke Halaman Login</a>";

} catch (PDOException $e) {
    echo "<h2 style='color: red;'>Gagal: " . $e->getMessage() . "</h2>";
}
