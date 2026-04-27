<?php
session_start();
include 'baglanti.php';

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kontrol Paneli | Bahaddin Gang</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; background: linear-gradient(135deg, #d57234 0%, #764ba2 100%); padding: 20px; padding-top: 100px !important;}
        .container { background: #e7d2c1; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); max-width: 800px; margin: auto; }
        h1 { color: #333; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
        .menu-section { margin-top: 20px; }
        .menu-section h3 { color: #2c3e50; }
        ul { list-style: none; padding: 0; }
        li { margin-bottom: 10px; }
        a { text-decoration: none; color: #364046; font-weight: bold; }
        a:hover { color: #2980b9; }
        .logout { color: #e74c3c !important; margin-top: 20px; display: inline-block; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <h1>Hesap Yönetimi</h1> 
    <p>Hoş geldin, <strong><?php echo $_SESSION['ad_soyad']; ?></strong>!</p>

    <div class="menu-section">
        <h3>Müşteri İşlemleri</h3>
        <ul>
            <li><a href="urun_vitrini.php">🛒 Ürünleri İncele / Alışveriş Yap</a></li>
            <li><a href="siparislerim.php">📦 Siparişlerimi Takip Et</a></li>
            <li><a href="profil.php">👤 Profil Bilgilerimi Düzenle</a></li>
        </ul>
    </div>

    <?php 
    // SADECE YÖNETİCİLERİN (Yetki 1) göreceği alan
    if ($_SESSION['yetki'] == 1) { 
    ?>
        <div class="menu-section" style="background: #cbb6a5; padding: 15px; border: 1px solid #2f2e2d; border-radius: 5px;">
            <h3>🛠️ Yönetici (Admin) Menüsü</h3>
            <ul>
                <li><a href="urun_ekle.php">➕ Yeni Ürün/Laptop Ekle</a></li>
                <li><a href="urun_listele.php">📋 Ürünleri Yönet (Düzenle/Sil)</a></li>
                <li><a href="stok_yonetimi.php">📉 Stok Durumlarını Güncelle</a></li>
                <li><a href="tum_siparisler.php">💰 Gelen Siparişleri Yönet</a></li>
                <li><a href="kullanici_listesi.php">👥 Müşteri/Kullanıcı Kayıtları</a></li>
            </ul>
        </div>
    <?php 
    } 
    ?>

    <hr>
    <a href="logout.php" class="logout">🚪Çıkış Yap</a>
</div>

</body>
</html>