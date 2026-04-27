
<?php
include 'baglanti.php';
$conn->set_charset("utf8");

$mesaj = "";
$mesaj_turu = "";
// kayıt islemleri mi olacak yoksa sayfayı mı goruntuleyecegiz bunu kontrol ettik ve kullanıcıdan degerleri aldık sha256 ile ham sifreyi hash e donusturduk sifre artık geri dondurulemeyecek farklı bir sekilde sisteme kaydedildi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ad_soyad = trim($_POST['ad_soyad']);
    $eposta = trim($_POST['eposta']);
    $sifre = $_POST['sifre'];
    $sifre_tekrar = $_POST['sifre_tekrar'];

    // 1. Kontrol: Şifreler aynı mı?
    if ($sifre !== $sifre_tekrar) {
        $mesaj = "<p style='color:red;'>Hata: Şifreler eşleşmiyor!</p>";
    } else {
        // 2. Kontrol: Bu e-posta zaten var mı?
        $stmt = $conn->prepare("SELECT kullanici_id FROM KULLANICILAR WHERE eposta = ?");
        $stmt->bind_param("s", $eposta);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $mesaj = "<p style='color:red;'>Hata: Bu e-posta zaten kayıtlı!</p>";
        } else {
            // 3. SHA-256 ile şifreleme
            $sifre_hash = hash('sha256', $sifre);

            // 4. Kayıt
            // INSERT sorgusuna kullanici_adi sütununu da ekledik
         $kayit = $conn->prepare("INSERT INTO KULLANICILAR (ad_soyad, eposta, kullanici_adi, sifre, yetki) VALUES (?, ?, ?, ?, 0)");


         $kayit->bind_param("ssss", $ad_soyad, $eposta, $eposta, $sifre_hash);

            if ($kayit->execute()) {
                $mesaj = "<p style='color:green;'>Kayıt başarılı! Giriş sayfasına yönlendiriliyorsunuz...</p>";
                header("Refresh: 2; url=login.php");
            } else {
                $mesaj = "<p style='color:red;'>Hata: " . $conn->error . "</p>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Ol | Bahaddin Gang</title>
    <style>

        body { 
          padding-top: 100px !important;
          font-family: Arial;
          background:linear-gradient(135deg, #764ba2 0%, #d57234 100%); 
          height : 100vh;
          display: flex; 
          align-items: center; 
          justify-content: center; }

        .form-kutu {
          background: white;
          padding: 35px; 
          border-radius: 15px; 
          box-shadow: 0 10 25px rgba(0,0,0,0.2); 
          width: 100%; 
          max-width: 450px;
        text-align: center; }

        .register-card h2 { color: #333; font-size: 26px; margin-bottom: 8px; }
        .register-card p { color: #777; font-size: 14px; margin-bottom: 25px; }
        .form-group { text-align: left; margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #555; font-size: 14px; }
        .form-group input { 
            width: 100%; padding: 11px 15px; border: 2px solid #eee; border-radius: 8px; 
            outline: none; transition: 0.3s; font-size: 15px;
        }
        .form-group input:focus { border-color: #667eea; box-shadow: 0 0 8px rgba(102, 126, 234, 0.15); }

        .btn-register {
            width: 100%; padding: 13px; background: #667eea; color: white; border: none; 
            border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; 
            transition: 0.3s; margin-top: 10px;
        }
        .btn-register:hover { background: #5a67d8; }

    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
    <div class="form-kutu">
        <h2>Kayıt Ol</h2>
        <p>💠 Bahaddin Gang ayrıcalıkları sizi bekliyor 💠</p>
        <?php if($mesaj != ""): ?>
        <div class="msg <?= $mesaj_turu ?>"><?= $mesaj ?></div>
    <?php endif; ?>
     <form action="kayit.php" method="post">
            <div class="form-group">
            <label>Ad Soyad</label>
            <input type="text" name="ad_soyad" placeholder="Adınız ve Soyadınız" required>
        </div>
            
            <div class="form-group">
            <label>E-posta Adresi</label>
            <input type="eposta" name="eposta" placeholder="ornek@mail.com" required>
        </div>
            <div class="form-group">
                <label>Şifre</label>
                <input type="password" name="sifre" placeholder="••••••" required>
            </div>
           <div class="form-group">
                <label>Şifre Tekrar</label>
                <input type="password" name="sifre_tekrar" placeholder="••••••" required>
            </div>
       <button type="submit" class="btn-register">Kayıt Ol</button>
    </form><br>
    <div class="footer-links">
        Zaten hesabın var mı? <a href="login.php">Giriş Yap</a>
    </div>
</div>
</body>
</html>