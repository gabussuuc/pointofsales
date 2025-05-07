<?php
include '../page/controller/db.php';
// Mendeklarasikan tanggal dan bulan
$tanggal = $_POST['tanggal'] ?? '';
$bulan = $_POST['bulan'] ?? '';

// Query untuk laporan
$query = "SELECT p.tanggal_penjualan, p.id_penjualan, pr.nama_produk, dp.jumlah_produk, dp.subtotal
          FROM detail_penjualan dp
          JOIN penjualan p ON dp.id_penjualan = p.id_penjualan
          JOIN produk pr ON dp.id_produk = pr.id_produk";

// Filter untuk tanggal dan bulan
$filters = [];
if ($tanggal) $filters[] = "DATE(p.tanggal_penjualan) = '$tanggal'";
if ($bulan) $filters[] = "MONTH(p.tanggal_penjualan) = '$bulan'";
if ($filters) $query .= " WHERE " . implode(" AND ", $filters);

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan</title>
    <link rel="stylesheet" href="../src/css/laporan.css">
</head>

<body>
    <h2>Laporan Penjualan Danantara</h2>
    <!-- Untuk memillih tanggal atau bulan  -->
    <form method="POST" class="no-print">
        <input type="date" name="tanggal" value="<?= $tanggal ?>">
        <select name="bulan">
            <option value="">Pilih Bulan</option>
            <?php foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'Septeber', 'Oktober', 'November', 'Desember'] as $i => $name): ?>
                <option value="<?= $i + 1 ?> <?= ($bulan == $i + 1) ? 'Selected' : '' ?> ">
                    <?= $name ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Terapkan</button>
    </form>
    <br>
    <br>
    <!-- Tombol untuk kembali ke dashboard, mengeprint dan ekspor ke excel -->
    <div class="actions">
        <a href="index.php">Kembali</a>
        <button onclick="window.print()">Print</button>
        <button onclick="exportToExcel()">Excel</button>
    </div>
    <br>
    <br>
    <!-- Tabel untuk menampilkan laporan penjualan -->
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>ID Penjualan</th>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($r = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $r['tanggal_penjualan'] ?></td>
                        <td><?= $r['id_penjualan'] ?></td>
                        <td><?= $r['nama_produk'] ?></td>
                        <td><?= $r['jumlah_produk'] ?></td>
                        <td>Rp <?= number_format($r['subtotal'], 0, ',', '.') ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else : ?>
                <tr>
                    <td colspan="5">Data Tidak Ada</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
        // Fungsi untuk mengekspor ke excel
        function exportToExcel() {
            const table = document.querySelector("table").outerHTML;
            const link = document.createElement("a");
            link.href = URL.createObjectURL(new Blob([`<head><meta charset="UTF-8">${table}</head>`], {type: `application/vnd.ms-excel`}));
            link.download = "Laporan_Danantara.xls";
            link.click();
        }
    </script>
</body>

</html>