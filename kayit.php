<?php
include 'baglanti.php';
$conn->set_charset("utf8");

// kayıt islemleri mi olacak yoksa sayfayı mı goruntuleyecegiz bunu kontrol ettik ve kullanıcıdan degerleri aldık sha256 ile ham sifreyi hash e donusturduk sifre artık geri dondurulemeyecek farklı bir sekilde sisteme kaydedildi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kullanici = $_POST['kullanici_adi'];
    $sifre_ham = $_POST['sifre'];

    $sifre_hash = hash('sha256', $sifre_ham);

   //sql sorgusundaki ? yerine gelecek degerleri bind param ile doldururuz ss ise iki tane string girecegimizi soyler 
    $stmt = $conn->prepare("INSERT INTO KULLANICILAR (kullanici_adi, sifre) VALUES (?, ?)");
    $stmt->bind_param("ss", $kullanici, $sifre_hash);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>Kayıt başarılı!</p>";
    } else {
        echo "<p style='color:red;'>Hata: " . $conn->error . "</p>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kullanıcı Kaydı</title>
</head>
<body>
    <h2>Yeni Kullanıcı Kaydı</h2>
    <form method="post" action="kayit.php">
        Kullanıcı Adı: <input type="text" name="kullanici_adi" required><br><br>
        Şifre: <input type="password" name="sifre" required><br><br>
        <button type="submit">Kayıt Ol</button>
    </form>
</body>
</html>