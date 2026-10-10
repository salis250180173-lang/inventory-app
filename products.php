<?php
require_once 'config/database.php';
require_once 'views/header.php';

// 1. Ambil Parameter Search, Sorting, dan Pagination dari URL
$search  = trim($_GET['search'] ?? '');
$sort_by = $_GET['sort'] ?? 'name';
$page    = max(1, (int)($_GET['page'] ?? 1));
$limit   = 5; // Jumlah data per halaman
$offset  = ($page - 1) * $limit;

// 2. Allowlist Sorting Kolom (Keamanan: Mencegah SQL Injection di ORDER BY)
$allowed_sort = [
    'name'  => 'p.name',
    'price' => 'p.price',
    'stock' => 'p.stock'
];
$orderBy = $allowed_sort[$sort_by] ?? 'p.name';

// 3. Menyusun Filter WHERE
$whereClause = 'WHERE 1=1';
$params = [];

if ($search !== '') {
    $whereClause .= ' AND (p.name LIKE :search OR p.sku LIKE :search)';
    $params['search'] = '%' . $search . '%';
}

// 4. Hitung Total Data Produk untuk Pagination
$countSql = "SELECT COUNT(*) FROM products p $whereClause";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalRows = $countStmt->fetchColumn();
$totalPages = ceil($totalRows / $limit);

// 5. Fetch Data Produk dengan JOIN ke Tabel Categories
$sql = "SELECT p.*, c.name AS category_name 
        FROM products p 
        JOIN categories c ON c.id = p.category_id 
        $whereClause 
        ORDER BY $orderBy ASC 
        LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);

// Bind parameter pencarian
foreach ($params as $key => $val) {
    $stmt->bindValue(":$key", $val);
}
// Bind limit dan offset sebagai integer
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$products = $stmt->fetchAll();
?>

<h3>Daftar Produk Inventory</h3>

<!-- Form Search & Sorting -->
<form method="GET" action="products.php" style="margin-bottom: 20px;">
    <input type="text" name="search" placeholder="Cari nama produk atau SKU..." value="<?= e($search) ?>" style="padding: 6px; width: 250px;">
    
    <select name="sort" style="padding: 6px;">
        <option value="name" <?= $sort_by === 'name' ? 'selected' : '' ?>>Urutkan Nama</option>
        <option value="price" <?= $sort_by === 'price' ? 'selected' : '' ?>>Urutkan Harga</option>
        <option value="sort_by" <?= $sort_by === 'stock' ? 'selected' : '' ?>>Urutkan Stok</option>
    </select>
    
    <button type="submit" style="padding: 6px 12px; cursor: pointer;">Cari / Filter</button>
    <?php if ($search !== ''): ?>
        <a href="products.php" style="margin-left: 10px;">Reset Filter</a>
    <?php endif; ?>
</form>

<!-- Tabel Data Produk -->
<table>
    <thead>
        <tr>
            <th>SKU</th>
            <th>Nama Produk</th>
            <th>Kategori</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Status Stok</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $row): ?>
            <tr>
                <td><strong><?= e($row['sku']) ?></strong></td>
                <td><?= e($row['name']) ?></td>
                <td><?= e($row['category_name']) ?></td>
                <td>Rp <?= number_format($row['price'], 0, ',', '.') ?></td>
                <td><?= $row['stock'] ?></td>
                <td>
                    <?php if ($row['stock'] <= $row['minimum_stock']): ?>
                        <span class="badge-danger">Stok Rendah!</span>
                    <?php else: ?>
                        <span class="badge-success">Aman</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align: center;">Data produk tidak ditemukan.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Navigasi Pagination -->
<?php if ($totalPages > 1): ?>
    <div style="margin-top: 15px;">
        <span>Halaman: </span>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort_by) ?>" 
               style="margin-right: 5px; padding: 3px 8px; border: 1px solid #ccc; text-decoration: none; <?= $i === $page ? 'background: #0066cc; color: white;' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
<?php endif; ?>

<?php require_once 'views/footer.php'; ?>
