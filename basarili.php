<?php
session_start();
include 'baglanti.php';

 if (!isset($_GET['siparis_id'])) {
    header("Location: urun_vitrini.php");
    exit();
 }
$siparis_no = htmlspecialchars($_GET['siparis_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Başarılı!</title>
    <style>
    body { font-family: sans-serif; text-align: center; padding: 50px; background: #f9f9f9; }
        .mesaj-kutusu { background: white; padding: 30px; border-radius: 10px; display: inline-block; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { color: #27ae60; }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="mesaj-kutusu">
        <h1>✅ Siparişiniz Alındı!</h1>
    <p>Teşekkürler <strong><?php echo htmlspecialchars($_SESSION['ad_soyad']); ?></strong>, siparişin başarıyla sisteme kaydedildi.</p>
    <p><strong>Sipariş Numaran:</strong> #<?php echo $siparis_no; ?></p>
    <br>
    <a href="urun_vitrini.php">Alışverişe Geri Dön</a>
    </div>
</body>
</html>