<?php
include './controller/db.php';
date_default_timezone_set('Asia/Jakarta');

if ($_POST) {
    $id = $_POST['id_produk'];
    $jumlah = $_POST['jumlah'];
    $diskon = isset($_POST['diskon']) ? $_POST['diskon'] : 0;
    $uang_dibayar = isset($_POST['uang_dibayar']) ? $_POST['uang_dibayar'] : 0;

    $produk = $conn->query("SELECT * FROM produk WHERE id_produk=$id")->fetch_assoc();

    if ($jumlah > $produk['stok']) {
        echo "<script>alert('Stoknya Habis')</script>";
    } else {
        $subtotal = $produk['harga'] * $jumlah;
        $diskon_total = ($diskon / 100) * $subtotal;
        $total_bayar = $subtotal - $diskon_total;

        if ($uang_dibayar < $total_bayar) {
            echo "<script>alert('Uangnya Kurang')</script>";
        } else {
            $kembalian = $uang_dibayar - $total_bayar;
            $conn->query("INSERT INTO penjualan (tanggal_penjualan, total_harga) VALUES (NOW(), $total_bayar)");
            $conn->query("INSERT INTO detail_penjualan (id_penjualan, id_produk, jumlah_produk, subtotal) 
        VALUES (LAST_INSERT_ID(), $id, $jumlah, $total_bayar)");
            $conn->query("UPDATE produk SET stok = stok - $jumlah WHERE id_produk=$id");

            //Kode untuk struk transaksi
            $struk = [
                'produk' => $produk['nama_produk'],
                'harga' => $produk['harga'],
                'jumlah' => $jumlah,
                'subtotal' => $subtotal,
                'diskon' => $diskon,
                'uang_dibayar' => $uang_dibayar,
                'total_bayar' => $total_bayar,
                'kembalian' => $kembalian,
                'tanggal' => date('Y-m-d H:i:s'),
            ];
        }
    }
}
$produk = $conn->query("SELECT * FROM produk");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi</title>
    <link rel="stylesheet" href="../src/css/transaksi.css">
</head>

<body>
    <h2>Transaksi Toko Danantara</h2>
    <!-- Inputan untuk transaksi -->
    <div class="input">
        <form method="POST">
            <select name="id_produk" onchange="updateDetails(this)">
                <option value="">Pilih Produk</option>
                <?php while ($p = mysqli_fetch_assoc($produk)): ?>
                    <option value="<?= $p['id_produk'] ?>"
                        data-nama="<?= $p['nama_produk'] ?>"
                        data-harga="<?= $p['harga'] ?>"
                        data-stok="<?= $p['stok'] ?>">
                        <?= $p['id_produk'] ?>-<?= $p['nama_produk'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <!-- Menampilkan data produk yang dipilih -->
            <div>
                <p>Nama Produk : <span id="nama_produk">-</span> </p>
                <p>Harga : Rp<span id="harga">-</span></p>
                <p>Stok : <span id="stok">-</span> </p>
                
                <!-- Menginput berapa jumlah barang yang dibeli -->
                <label for="jumlah">Jumlah Beli :</label>
                <input type="number" name="jumlah" id="jumlah" oninput="updateSubtotal()" min="0" required>
                <p>Subtotal : <span id="subtotal">-</span></p>

                <!-- Menginput berapa diskon yang ingin diberikan -->
                <label for="diskon">Diskon :</label>
                <input type="number" name="diskon" id="diskon" oninput="updateSubtotal()" min="0" value="0" required>

                <!-- Menginput berapa uang yang dikasih pembeli -->
                <label for="uang_dibayar">Uang Dibayar :</label>
                <input type="number" name="uang_dibayar" min="0" required>

                <button>Transaksi</button>
                <br>
                <br>
                <span class="back"><a href="index.php">Kembali</a></span>
        </form>
    </div>
    <h2>Struk Pembelian</h2>
    <!-- Menampilkan struk pembelian -->
    <div id="struk">
        <?php if (isset($struk)) : ?>
            <p><?= $struk['produk'] ?> | <?= $struk['jumlah'] ?> x Rp <?= number_format($struk['harga'], 0, ',', '.') ?></p>
            <p>Total: Rp<?= number_format($struk['subtotal'], 0, ',', '.') ?></p>
            <p>Diskon: <?= $struk['diskon'] ?>%</p>
            <p>Subtotal: Rp<?= number_format($struk['total_bayar'], 0, ',', '.') ?> </p>
            <p>Uang Dibayar: Rp<?= number_format($struk['uang_dibayar'], 0, ',', '.') ?> </p>
            <p>kembalian: Rp<?= number_format($struk['kembalian'], 0, ',', '.') ?> </p>
            <p>Tanggal: <?= date('Y-m-d H:i:s') ?></p>
            <button onclick="printStruk()">Cetak</button>
        <?php endif; ?>
    </div>
    <script>
        let harga = 0;
        //Program fungsi untuk update detail
        function updateDetails(select) {
            const option = select.selectedOptions[0];
            document.getElementById('nama_produk').textContent = option.getAttribute('data-nama') || '-';
            harga = option.getAttribute('data-harga') || 0;
            document.getElementById('harga').textContent = harga;
            document.getElementById('stok').textContent = option.getAttribute('data-stok') || '-';  
            updateSubtotal();
        }
        //Program fungsi untuk fungsi update subtotal
        function updateSubtotal() {
            const jumlah = parseInt(document.getElementById('jumlah').value) || 0;
            const diskon = parseFloat(document.getElementById('diskon').value) || 0;
            const subtotal = harga * jumlah;
            const diskonValue = (diskon / 100) * subtotal;
            const total = subtotal - diskonValue;
            document.getElementById('subtotal').textContent = subtotal > 0 ? `Rp${subtotal.toLocaleString('id-ID')}` : '-';

        }
        //Untuk mengeprint struk transaksi
        function printStruk() {
            const strukContent = document.getElementById('struk').cloneNode(true);
            const button = strukContent.querySelector('button');
            if (button) {
                button.remove();
            }
            const win = window.open('', '', 'width="12000", height="12000"');
            win.document.write(strukContent.innerHTML);
            win.document.close();
            win.print();
        }
    </script>
</body>
</html>