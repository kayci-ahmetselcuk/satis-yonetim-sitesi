<?php                      //hash ile korudugumuz sifreyi alabilmek için tekrar hash kullanıuyoruz
session_start();
include 'baglanti.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $sifre_ham = $_POST['sifre'];
    
    // Girilen şifreyi SHA-256'ya çevir
    $sifre_hash = hash('sha256', $sifre_ham);

    $stmt = $conn->prepare("SELECT * FROM KULLANICILAR WHERE eposta = ? AND sifre = ?");
    $stmt->bind_param("ss", $email, $sifre_hash);
    $stmt->execute();
    $sonuc = $stmt->get_result();

    if ($user = $sonuc->fetch_assoc()) {
        $_SESSION['kullanici_id'] = $user['kullanici_id'];
        $_SESSION['ad_soyad'] = $user['ad_soyad']; // Panelde görünecek isim
        $_SESSION['yetki'] = $user['yetki'];
        
        header("Location: panel.php");
    } else {
        $hata= "E-posta veya Şifre hatalı!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap | Bahaddin Gang</title>
    <style>
        /* Temel Sıfırlama */
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        body { 
            background: linear-gradient(135deg, #d57234 0%, #764ba2 100%); 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding-top: 100px !important;
        }

        .login-card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-card h2 {
            margin-bottom: 10px;
            color: #333;
            font-size: 28px;
        }

        .login-card p {
            color: #777;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #eee;
            border-radius: 8px;
            outline: none;
            transition: all 0.3s ease;
            font-size: 16px;
        }

        .form-group input:focus {
            border-color: #d57234;
            box-shadow: 0 0 8px rgba(102, 126, 234, 0.2);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: #5a67d8;
        }

        .footer-links {
            margin-top: 25px;
            font-size: 14px;
            color: #777;
        }

        .footer-links a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }
        
        .error-msg {
            background: #fed7d7;
            color: #c53030;
            padding: 10px;
            border-radius: 5px;
            margin:-5px 0 15px 0;
            font-size: 14px;
            text-align: center;
            border: 1px solid #feb2b2;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="login-card">
    <h2>Hoş Geldin!</h2>
    <p>Lütfen e-posta ve şifren ile giriş yap.</p>

    <?php if(isset($hata)): ?>
        <div class="error-msg"><?= $hata ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="form-group">
            <label>E-posta Adresi</label>
            <input type="email" name="email" placeholder="ornek@mail.com" required>
        </div>

        <div class="form-group">
            <label>Şifre</label>
            <input type="password" name="sifre" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn-login">Giriş Yap</button>
    </form>

    <div class="footer-links">
        Hesabın yok mu?<a href="kayit.php"> Hemen Kaydol</a><br><br>
        <a href="index.php">← Mağazaya Dön</a>
    </div>
</div>

</body>
</html>