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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontrol Paneli | Bahaddin Gang</title>
    <style>
        /* Genel Sifirlama */
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, sans-serif; }
        
        body { 
            background: linear-gradient(135deg, #d57234 0%, #764ba2 100%); 
            background-attachment: fixed;
            min-height: 100vh; 
            padding: 20px;
            padding-top: 120px !important; /* Navbar boşluğu */
        }

        .container { 
            background: rgba(255, 255, 255, 0.95); 
            padding: 40px; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2); 
            max-width: 900px; 
            margin: auto; 
        }

        .welcome-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 20px;
        }

        .welcome-header h1 { color: #2c3e50; font-size: 28px; }
        .welcome-header p { color: #666; font-size: 16px; margin-top: 5px; }
        .welcome-header strong { color: #764ba2; }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        /* İşlem Kartları */
        .menu-card {
            background: white;
            border: 2px solid #f0f0f0;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .menu-card:hover {
            border-color: #764ba2;
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(118, 75, 162, 0.1);
        }

        /* Admin Bölümü */
        .admin-section {
            margin-top: 40px;
            padding: 25px;
            background: #f8f9fa;
            border-radius: 12px;
            border-left: 5px solid #2c3e50;
        }

        .admin-section h3 { 
            color: #2c3e50; 
            margin-bottom: 20px; 
            display: flex; 
            align-items: center; 
            gap: 10px;
        }

        /* Çıkış Butonu (Kırpılmış Stil) */
        .logout-wrapper {
            text-align: center;
            margin-top: 40px;
        }

        .logout-btn { 
            display: inline-block;
            width: 180px;
            padding: 12px;
            background: #e74c3c; 
            color: white !important; 
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: 0.3s;
        }

        .logout-btn:hover { 
            background: #c0392b; 
            transform: scale(1.05);
        }

        hr { border: 0; border-top: 1px solid #eee; margin: 30px 0; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
    <div class="welcome-header">
        <h1>Hesap Yönetimi</h1> 
        <p>Hoş geldin, <strong><?php echo $_SESSION['ad_soyad']; ?></strong>!</p>
    </div>

    <div class="section-title" style="color: #555; font-weight: bold; margin-bottom: 15px;">🛒 Müşteri İşlemleri</div>
    <div class="menu-grid">
        <a href="urun_vitrini.php" class="menu-card">
            <span>🛒</span>
            <span>Alışverişe Başla</span>
        </a>
        <a href="siparislerim.php" class="menu-card">
            <span>📦</span>
            <span>Siparişlerim</span>
        </a>
        <a href="profil.php" class="menu-card">
            <span>👤</span>
            <span>Profilimi Düzenle</span>
        </a>
    </div>

    <?php if ($_SESSION['yetki'] == 1): ?>
        <div class="admin-section">
            <h3>🛠️ Yönetici Paneli</h3>
            <div class="menu-grid">
                <a href="urun_ekle.php" class="menu-card" style="border-color: #d1d1d1;">
                    <span>➕</span>
                    <span>Ürün Ekle</span>
                </a>
                <a href="urun_listele.php" class="menu-card" style="border-color: #d1d1d1;">
                    <span>📋</span>
                    <span>Ürünleri Yönet</span>
                </a>
                <a href="stok_yonetimi.php" class="menu-card" style="border-color: #d1d1d1;">
                    <span>📉</span>
                    <span>Stok Takibi</span>
                </a>
                <a href="tum_siparisler.php" class="menu-card" style="border-color: #d1d1d1;">
                    <span>💰</span>
                    <span>Sipariş Yönetimi</span>
                </a>
                <a href="kullanici_listesi.php" class="menu-card" style="border-color: #d1d1d1;">
                    <span>👥</span>
                    <span>Müşteri Kayıtları</span>
                </a>
            </div>
        </div>
    <?php endif; ?>

    <div class="logout-wrapper">
        <a href="logout.php" class="logout-btn">🚪 Güvenli Çıkış</a>
    </div>
</div>

</body>
</html>
