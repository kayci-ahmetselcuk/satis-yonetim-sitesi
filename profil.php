<?php
session_start();
include 'baglanti.php';

// Giriş kontrolü
if (!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['kullanici_id'];
$mesaj = "";
$mesaj_turu = "";

// 1. Mevcut bilgileri çek (Formun içine önceden yazmak için)
$sorgu = $conn->prepare("SELECT ad_soyad, eposta, telefon, adres FROM KULLANICILAR WHERE kullanici_id = ?");
$sorgu->bind_param("i", $user_id);
$sorgu->execute();
$user = $sorgu->get_result()->fetch_assoc();

// 2. Güncelleme işlemi tetiklendi mi?
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ad = trim($_POST['ad_soyad']);
    $eposta = trim($_POST['email']);
    $tel = trim($_POST['telefon']);
    $adr = trim($_POST['adres']);

    $guncelle = $conn->prepare("UPDATE KULLANICILAR SET ad_soyad = ?, eposta = ?, telefon = ?, adres = ? WHERE kullanici_id = ?");
    $guncelle->bind_param("ssssi", $ad, $eposta, $tel, $adr, $user_id);
    
    if ($guncelle->execute()) {
        $mesaj = "Bilgileriniz başarıyla güncellendi!";
        $mesaj_turu = "success";
        // Session'daki ismi de güncelleyelim ki paneldeki "Hoş geldin" yazısı değişsin
        $_SESSION['ad_soyad'] = $ad;
        // Güncel veriyi tekrar çekelim
        $user['ad_soyad'] = $ad;
        $user['eposta'] = $eposta;
        $user['telefon'] = $tel;
        $user['adres'] = $adr;
    } else {
        $mesaj = "Güncelleme sırasında hata oluştu!";
        $mesaj_turu = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilim | Bahaddin Gang </title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, sans-serif; }
        
        body { 
            background: linear-gradient(135deg, #d57234 0%, #764ba2 100%); 
            background-attachment: fixed;
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center;
            padding: 40px 20px;
            padding-top: 120px !important;
        }

        .profile-card {
            background: white;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 550px;
        }

        .profile-card h2 { color: #333; font-size: 26px; margin-bottom: 5px; text-align: center; }
        .profile-card p.subtitle { color: #777; font-size: 14px; margin-bottom: 25px; text-align: center; }

        .form-group { text-align: left; margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #555; font-size: 14px; }
        .form-group input, .form-group textarea { 
            width: 100%; padding: 11px 15px; border: 2px solid #eee; border-radius: 8px; 
            outline: none; transition: 0.3s; font-size: 15px;
        }
        .form-group input:focus, .form-group textarea:focus { 
            border-color: #d57234; 
            box-shadow: 0 0 8px rgba(213, 114, 52, 0.15); 
        }

        .btn-update {
            width: 100%; padding: 13px; background: #667eea; color: white; border: none; 
            border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; 
            transition: 0.3s; margin-top: 10px;
        }
        .btn-update:hover { background: #5a67d8; }

        .msg { padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; text-align: center; }
        .error { background: #fed7d7; color: #c53030; border: 1px solid #feb2b2; }
        .success { background: #c6f6d5; color: #2f855a; border: 1px solid #9ae6b4; }

        .back-link {
    position: fixed !important;
    top: 90px;
    left: 20px;
    display: inline-block;
    padding: 10px 18px;
    background-color: #2c3e50; 
    color: #ffffff;
    text-decoration: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    transition: background 0.3s ease, transform 0.2s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.back-link:hover {
    background-color: #7aa5cf;
    transform: translateX(-3px) scale(1.05); /* Sola kayma efekti */
    color: white;
}
.profile-actions {
    display: flex;
    flex-direction: column; /* Butonları alt alta dizmek için */
    gap: 10px;
    margin-top: 20px;
}

.btn-logout {
    display: block;
    width: 50%;
    margin: 10px auto; 
    padding: 10px 15px;
    background: #e74c3c; 
    color: white;
    text-align: center;
    text-decoration: none;
    border-radius: 8px;
    font-size: 15px;
    font-weight: bold;
    transition: 0.3s;
}

.btn-logout:hover {
    background: #c0392b;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(231, 76, 60, 0.2);
}

    </style>
</head>
<body>  
<?php include 'navbar.php'; ?>    
<a href="panel.php" class="back-link">← Menü</a>

<div class="profile-card">
    <h2>👤 Profil Bilgilerim</h2>
    <p class="subtitle">Bilgilerinizi buradan güncelleyebilirsiniz.</p>

    <?php if($mesaj != ""): ?>
        <div class="msg <?= $mesaj_turu ?>"><?= $mesaj ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Ad Soyad</label>
            <input type="text" name="ad_soyad" value="<?= htmlspecialchars($user['ad_soyad'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>E-posta Adresi</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['eposta'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>Telefon Numarası</label>
            <input type="text" name="telefon" value="<?= htmlspecialchars($user['telefon'] ?? '') ?>" placeholder="05xx xxx xx xx">
        </div>

        <div class="form-group">
            <label>Teslimat Adresi</label>
            <textarea name="adres" rows="3" placeholder="Siparişlerinizin gönderileceği açık adres..."><?= htmlspecialchars($user['adres'] ?? '') ?></textarea>
        </div>
            
<div class="profile-actions">
    <button type="submit" class="btn-update">Değişiklikleri Kaydet</button>
    <a href="logout.php" class="btn-logout">🚪 Çıkış</a>
</div>
    </form>
</div>

</body>
</html>