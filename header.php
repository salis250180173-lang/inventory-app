<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Memanggil helper escape agar aman dari XSS
require_once __DIR__ . '/../helpers/escape.php';

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management System</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f7fa; color: #333; }
        
        /* Header Banner Soft Blue */
        .header-banner { background: linear-gradient(135deg, #3b82f6, #1d4ed8); padding: 20px 30px; color: white; }
        .header-banner h2 { font-size: 24px; margin-bottom: 5px; font-weight: 700; }
        .header-banner p { font-size: 14px; opacity: 0.9; }
        
        /* Navigasi Bar */
        .nav-bar { background: #ffffff; padding: 12px 30px; display: flex; gap: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .nav-bar a { text-decoration: none; color: #4b5563; font-weight: 600; font-size: 15px; padding: 6px 12px; border-radius: 6px; transition: all 0.2s; }
        .nav-bar a:hover { background: #eff6ff; color: #2563eb; }
        .nav-bar a.logout { color: #ef4444; margin-left: auto; }
        .nav-bar a.logout:hover { background: #fef2f2; }
        
        .container { padding: 0 30px 40px 30px; }

        /* Card Container Horizontal */
        .card-container { display: flex; flex-wrap: wrap; gap: 15px; margin-top: 15px; margin-bottom: 30px; }
        
        /* Style Kartu Ringkasan (Pastel Rainbow) */
        .card { 
            flex: 1; 
            min-width: 170px; 
            padding: 18px; 
            border-radius: 10px; 
            box-shadow: 0 3px 8px rgba(0,0,0,0.04); 
            border: 1px solid rgba(0,0,0,0.05);
        }
        .card h5 { font-size: 13px; margin-bottom: 8px; color: #555; text-transform: uppercase; letter-spacing: 0.5px; }
        .card .value { font-size: 22px; font-weight: bold; }

        /* Variasi Warna Tiap Kartu */
        .card-blue { background-color: #e0f2fe; border-left: 5px solid #0284c7; color: #0369a1; }
        .card-green { background-color: #dcfce7; border-left: 5px solid #16a34a; color: #15803d; }
        .card-yellow { background-color: #fef9c3; border-left: 5px solid #ca8a04; color: #a16207; }
        .card-purple { background-color: #f3e8ff; border-left: 5px solid #9333ea; color: #7e22ce; }
        .card-red { background-color: #ffe4e6; border-left: 5px solid #e11d48; color: #be123c; }

        /* Styling Tabel dengan Header Biru Soft */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            background: #ffffff; 
            border-radius: 8px; 
            overflow: hidden; 
            box-shadow: 0 3px 10px rgba(0,0,0,0.05); 
            margin-top: 15px; 
            border: 1px solid #dbeaff;
        }
        
        /* Warna Biru Soft Khusus Header Tabel */
        th { 
            background-color: #dbeafe; /* Biru Soft / Ice Blue */
            color: #1e40af; /* Teks Biru Gelap Kontras */
            padding: 14px 16px; 
            text-align: left; 
            font-size: 13px; 
            font-weight: 700; 
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #bfdbfe; 
        }
        
        td { 
            padding: 12px 16px; 
            border-bottom: 1px solid #f1f5f9; 
            font-size: 14px; 
            color: #334155;
        }
        
        tr:nth-child(even) { background-color: #f8fafc; } /* Zebra striping tipis */
        tr:hover { background-color: #eff6ff; } /* Highlight biru saat di-hover */
        
        /* Badge Custom */
        .badge-danger { background-color: #fee2e2; color: #dc2626; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .badge-success { background-color: #dcfce7; color: #15803d; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header-banner">
        <h2>Inventory Management System</h2>
        <p>Halo, <strong><?= e($user['username']) ?></strong> (Role: <?= strtoupper(e($user['role'])) ?>)</p>
    </div>
    
    <div class="nav-bar">
        <a href="dashboard.php">Dashboard</a>
        <a href="products.php">Data Produk</a>
        <a href="stock_transaction.php">Transaksi Stok</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
    
    <div class="container">
        