<?php
require_once 'config/database.php';
require_once 'helpers/validation.php';
require_once 'views/header.php';

$message = '';
$error   = '';

// 1. Proses Form Transaksi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $type       = $_POST['type'] ?? '';
    $quantity   = validatePositiveInt($_POST['quantity'] ?? 0);
    $user_id    = $_SESSION['user']['id'];

    if (!$product_id || !$quantity || !in_array($type, ['IN', 'OUT'])) {
        $error = 'Input data transaksi tidak valid atau jumlah harus lebih besar dari 0.';
    } else {
        try {
            // Memulai Database Transaction
            $pdo->beginTransaction();

            // Lock baris produk untuk membaca stok terbaru (FOR UPDATE)
            $stmt = $pdo->prepare('SELECT stock FROM products WHERE id = :id FOR UPDATE');
            $stmt->execute(['id' => $product_id]);
            $product = $stmt->fetch();

            if (!$product) {
                throw new Exception('Produk tidak ditemukan!');
            }

            $current_stock = (int)$product['stock'];

            // Aturan Bisnis: Stok tidak boleh menjadi negatif saat barang keluar
            if ($type === 'OUT' && $quantity > $current_stock) {
                throw new Exception("Stok tidak mencukupi! Stok tersedia saat ini hanya: {$current_stock}");
            }

            // Hitung stok baru
            $new_stock = ($type === 'IN') ? $current_stock + $quantity : $current_stock - $quantity;

            // Update stok produk
            $updateStmt = $pdo->prepare('UPDATE products SET stock = :stock WHERE id = :id');
            $updateStmt->execute([
                'stock' => $new_stock,
                'id'    => $product_id
            ]);

            // Catat ke riwayat stock_movements
            $logStmt = $pdo->prepare('INSERT INTO stock_movements (product_id, user_id, type, quantity) VALUES (:product_id, :user_id, :type, :quantity)');
            $logStmt->execute([
                'product_id' => $product_id,
                'user_id'    => $user_id,
                'type'       => $type,
                'quantity'   => $quantity
            ]);

            // Jika semua query berhasil, simpan perubahan secara permanen
            $pdo->commit();
            $message = 'Transaksi stok berhasil diproses!';
        } catch (Exception $e) {
            // Batalkan semua transaksi jika terjadi kesalahan
            $pdo->rollBack();
            $error = $e->getMessage();
        }
    }
}

// 2. Ambil Daftar Produk untuk Dropdown Form
$products = $pdo->query('SELECT id, sku, name, stock FROM products ORDER BY name ASC')->fetchAll();

// 3. Ambil Riwayat Transaksi Terakhir (JOIN products & users)
$historySql = "SELECT sm.*, p.name AS product_name, p.sku, u.username 
               FROM stock_movements sm
               JOIN products p ON p.id = sm.product_id
               JOIN users u ON u.id = sm.user_id
               ORDER BY sm.created_at DESC LIMIT 10";
$history = $pdo->query($historySql)->fetchAll();
?>

<h3>Pencatatan Transaksi Stok</h3>

<?php if ($error): ?>
    <div class="alert-error"><?= e($error) ?></div>
<?php endif; ?>

<?php if ($message): ?>
    <div class="alert-success"><?= e($message) ?></div>
<?php endif; ?>

<!-- Form Transaksi Stok -->
<form method="POST" action="stock_transaction.php" style="background: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 25px;">
    <div style="margin-bottom: 10px;">
        <label for="product_id"><strong>Pilih Produk:</strong></label><br>
        <select name="product_id" id="product_id" required style="padding: 8px; width: 100%; max-width: 400px; margin-top: 5px;">
            <option value="">-- Pilih Produk --</option>
            <?php foreach ($products as $p): ?>
                <option value="<?= $p['id'] ?>">
                    [<?= e($p['sku']) ?>] <?= e($p['name']) ?> (Stok: <?= $p['stock'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="margin-bottom: 10px;">
        <label for="type"><strong>Jenis Transaksi:</strong></label><br>
        <select name="type" id="type" required style="padding: 8px; width: 100%; max-width: 400px; margin-top: 5px;">
            <option value="IN">Barang Masuk (IN)</option>
            <option value="OUT">Barang Keluar (OUT)</option>
        </select>
    </div>

    <div style="margin-bottom: 15px;">
        <label for="quantity"><strong>Jumlah (Quantity):</strong></label><br>
        <input type="number" name="quantity" id="quantity" min="1" required style="padding: 8px; width: 100%; max-width: 382px; margin-top: 5px;">
    </div>

    <button type="submit" style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
        Proses Transaksi
    </button>
</form>

<!-- Riwayat Transaksi Terakhir -->
<h4>10 Transaksi Terakhir</h4>
<table>
    <thead>
        <tr>
            <th>Waktu</th>
            <th>SKU</th>
            <th>Nama Produk</th>
            <th>Tipe</th>
            <th>Jumlah</th>
            <th>Petugas</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($history) > 0): ?>
            <?php foreach ($history as $log): ?>
            <tr>
                <td><?= $log['created_at'] ?></td>
                <td><strong><?= e($log['sku']) ?></strong></td>
                <td><?= e($log['product_name']) ?></td>
                <td>
                    <?php if ($log['type'] === 'IN'): ?>
                        <span class="badge-success">MASUK (IN)</span>
                    <?php else: ?>
                        <span class="badge-danger">KELUAR (OUT)</span>
                    <?php endif; ?>
                </td>
                <td><strong><?= $log['quantity'] ?></strong></td>
                <td><?= e($log['username']) ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align: center;">Belum ada riwayat transaksi.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once 'views/footer.php'; ?>
