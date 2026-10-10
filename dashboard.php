<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/escape.php';
require_once __DIR__ . '/views/header.php';

// 1. Total Produk
$stmtTotal = $pdo->query("SELECT COUNT(*) AS total_produk FROM products");
$totalProduk = $stmtTotal->fetch()['total_produk'];

// 2. Nilai Total Inventory
$stmtNilai = $pdo->query("SELECT SUM(stock * price) AS nilai_inventory FROM products");
$nilaiInventory = $stmtNilai->fetch()['nilai_inventory'] ?? 0;

// 3. Produk Stok Rendah
$stmtLowStockCount = $pdo->query("SELECT COUNT(*) AS total_low FROM products WHERE stock <= minimum_stock");
$totalLowStock = $stmtLowStockCount->fetch()['total_low'];

// 4. Total Barang Masuk
$stmtIn = $pdo->query("SELECT SUM(quantity) AS total_in FROM stock_movements WHERE type = 'IN'");
$totalIn = $stmtIn->fetch()['total_in'] ?? 0;

// 5. Total Barang Keluar
$stmtOut = $pdo->query("SELECT SUM(quantity) AS total_out FROM stock_movements WHERE type = 'OUT'");
$totalOut = $stmtOut->fetch()['total_out'] ?? 0;

// Daftar Produk Stok Rendah
$stmtLowStockList = $pdo->query("
    SELECT sku, name, stock, minimum_stock 
    FROM products 
    WHERE stock <= minimum_stock 
    ORDER BY stock ASC
");
$lowStockProducts = $stmtLowStockList->fetchAll();
?>

<h3>Dashboard Ringkasan Inventory</h3>

<!-- Grid Kartu Berwarna-Warni Rapi Horizontal -->
<div class="card-container">
    <div class="card card-blue">
        <h5>Total Produk</h5>
        <div class="value"><?= e($totalProduk) ?> <span style="font-size: 14px; font-weight: normal;">Item</span></div>
    </div>
    
    <div class="card card-green">
        <h5>Nilai Total Inventory</h5>
        <div class="value">Rp <?= number_format($nilaiInventory, 0, ',', '.') ?></div>
    </div>
    
    <div class="card card-yellow">
        <h5>Produk Stok Rendah</h5>
        <div class="value"><?= e($totalLowStock) ?> <span style="font-size: 14px; font-weight: normal;">Produk</span></div>
    </div>
    
    <div class="card card-purple">
        <h5>Total Barang Masuk</h5>
        <div class="value" style="color: #2563eb;"><?= e($totalIn) ?> <span style="font-size: 14px; font-weight: normal;">Unit</span></div>
    </div>
    
    <div class="card card-red">
        <h5>Total Barang Keluar</h5>
        <div class="value" style="color: #dc2626;"><?= e($totalOut) ?> <span style="font-size: 14px; font-weight: normal;">Unit</span></div>
    </div>
</div>

<h4 style="margin-top: 25px;">Peringatan Stok Rendah (≤ Minimum Stock)</h4>

<table>
    <thead>
        <tr>
            <th>SKU</th>
            <th>Nama Produk</th>
            <th>Stok Saat Ini</th>
            <th>Batas Minimum</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($lowStockProducts)): ?>
            <tr>
                <td colspan="5" style="text-align: center; color: #666;">Semua stok dalam kondisi aman.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($lowStockProducts as $prod): ?>
                <tr>
                    <td><strong><?= e($prod['sku']) ?></strong></td>
                    <td><?= e($prod['name']) ?></td>
                    <td style="color: #dc2626; font-weight: bold;"><?= e($prod['stock']) ?></td>
                    <td><?= e($prod['minimum_stock']) ?></td>
                    <td><span class="badge-danger">Perlu Restok!</span></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/views/footer.php'; ?>
