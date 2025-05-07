<?php
include '../page/controller/db.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_user'];
    $nama = $_POST['nama_kasir'];
    $email = $_POST['email'];
    $tanggal = $_POST['tgl_lahir'];
    $alamat = $_POST['alamat'];
    // Query untuk mengupdate dan menginsert data produk  
    $query = "UPDATE akun SET nama_kasir='$nama', email='$email', tgl_lahir='$tanggal', alamat='$alamat' WHERE id_user=$id";
    $conn->query($query);
    header('Location: profil.php');
    exit;
}

$edit = isset($_GET['edit']) ? $conn->query("SELECT * FROM akun WHERE id_user=" . $_GET['edit'])->fetch_assoc() : null;
$akun = $conn->query("SELECT * FROM akun");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link rel="stylesheet" href="../src/css/profil.css">
</head>

<body>

    <center>
        <div class="card">
            <h2>Profil Danantara</h2>
            <div class="logo"><img src="../src/img/user-icon.png" alt=""></div>
            <?php while ($n = mysqli_fetch_assoc($akun)): ?>
                <p>Nama Kasir : <?= $n['nama_kasir'] ?></p>
                <p>Email : <?= $n['email'] ?></p>
                <p>Tanggal Lahir : <?= date($n['tgl_lahir']) ?></p>
                <p>Alamat : <?= $n['alamat'] ?></p>
                <center><a href="?edit=<?= $n['id_user'] ?>">Edit</a></center>
                <br>
                <center><a href="index.php">Kembali</a></center>
            <?php endwhile; ?>
        </div>
    </center>

    <form method="POST">
        <h2>Edit Profil</h2>
        <input type="hidden" name="id_user" value="<?= $edit['id_user'] ?? '' ?>">
        <input name="nama_kasir" value="<?= $edit['nama_kasir'] ?? '' ?>" placeholder="Nama kasir"><br>
        <input name="email" value="<?= $edit['email'] ?? '' ?>" placeholder="Email"><br>
        <input type="date" name="tgl_lahir" value="<?= $edit['tgl_lahir'] ?? '' ?>" placeholder="Tgl lahir"><br>
        <input name="alamat" value="<?= $edit['alamat'] ?? '' ?>" placeholder="Alamat"><br>
        <button type="submit"><?= $edit ? 'Edit' : '' ?>Edit</button><br>
        <?php if ($edit): ?><a href="profil.php">Kembali</a><?php endif; ?>
    </form>
    <br>
    <br>
</body>

</html>