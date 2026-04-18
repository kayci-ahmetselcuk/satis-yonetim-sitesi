<?php
session_start();
if (!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yönetim Paneli</title>
</head>
<body>
    <h1>Lojistik Yönetim Paneli</h1>
    <p>Hoş geldin, <?php echo $_SESSION['kullanici_adi']; ?></p>

    <div class="menu">
        <a href="kargo_sorgula.php">Kargo Sorgula</a>

        <?php 
        // Sadece adminlerin göreceği özel alanlar
        if ($_SESSION['yetki'] == 1) { 
        ?>
            <hr>
            <h3>Yönetici Menüsü</h3>
            <ul>
                <li><a href="urun_ekle.php">Yeni Ürün Tanımla</a></li>
                <li><a href="stok_duzenle.php">Stok Güncelleme</a></li>
                <li><a href="kullanici_listesi.php">Kullanıcı Yönetimi</a></li>
            </ul>
        <?php 
        } 
        ?>

        <hr>
        <a href="logout.php" style="color:red;">Güvenli Çıkış</a>
    </div>
</body>
</html>