<?php
session_start();
if (!isset($_SESSION['yetki']) || $_SESSION['yetki'] != 1) { 
    die("Yetkisiz erişim."); 
}

include 'baglanti.php';
$conn->set_charset("utf8");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $marka_id = $_POST['marka_id'];
    $kategori_id = $_POST['kategori_id'];
    $urun_adi = $_POST['urun_adi'];
    $fiyat = $_POST['fiyat'];
    $urun_tipi = $_POST['urun_tipi'];

    $conn->begin_transaction();

    try {
        // 1. Süper Tip: URUNLER tablosuna temel bilgileri ekle
        $stmt = $conn->prepare("INSERT INTO URUNLER (marka_id, kategori_id, urun_adi, fiyat) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisd", $marka_id, $kategori_id, $urun_adi, $fiyat);
        $stmt->execute();
        $yeni_id = $conn->insert_id;

        // 2. Alt Tip: Seçime göre ilgili tabloya detayları ekle
        if ($urun_tipi == "laptop") {
            $stmt2 = $conn->prepare("INSERT INTO LAPTOP (urun_id, islemci, ram_gb, ekran_karti, depolama) VALUES (?, ?, ?, ?, ?)");
            $stmt2->bind_param("isisi", $yeni_id, $_POST['islemci'], $_POST['ram_gb'], $_POST['ekran_karti'], $_POST['depolama']);
            $stmt2->execute();
        } 
        elseif ($urun_tipi == "donanim") {
            // Görseldeki DONANIM_PARCALARI sütunları: urun_id, parca_tipi, teknik_detay
            $stmt3 = $conn->prepare("INSERT INTO DONANIM_PARCALARI (urun_id, parca_tipi, teknik_detay) VALUES (?, ?, ?)");
            $stmt3->bind_param("iss", $yeni_id, $_POST['parca_tipi'], $_POST['teknik_detay']);
            $stmt3->execute();
        }

        $conn->commit();
        echo "<p style='color:green;'>Ürün ve detayları başarıyla kaydedildi.</p>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<p style='color:red;'>Hata: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ürün Yönetimi</title>
    <script>
        function formGoster(tip) {
            document.getElementById('laptop_form').style.display = (tip == 'laptop') ? 'block' : 'none';
            document.getElementById('donanim_form').style.display = (tip == 'donanim') ? 'block' : 'none';
        }
    </script>
</head>
<body>
    <h2>Yeni Ürün Ekle</h2>
    <form method="post">
        Ürün Tipi: 
        <select name="urun_tipi" onchange="formGoster(this.value)" required>
            <option value="">Seçiniz...</option>
            <option value="laptop">Laptop</option>
            <option value="donanim">Donanım Parçası</option>
        </select><br><br>

        Ürün Adı: <input type="text" name="urun_adi" required><br><br>
        Fiyat: <input type="number" step="0.01" name="fiyat" required><br><br>
        Marka ID: <input type="number" name="marka_id" required><br><br>
        Kategori ID: <input type="number" name="kategori_id" required><br><br>

        <div id="laptop_form" style="display:none; padding:10px; border:1px solid #ccc;">
            <h3>Laptop Özellikleri</h3>
            İşlemci: <input type="text" name="islemci"><br>
            RAM (GB): <input type="number" name="ram_gb"><br>
            Ekran Kartı: <input type="text" name="ekran_karti"><br>
            Depolama: <input type="number" name="depolama">
        </div>

        <div id="donanim_form" style="display:none; padding:10px; border:1px solid #ccc;">
            <h3>Donanım Parçası Özellikleri</h3>
            Parça Tipi: <input type="text" name="parca_tipi" placeholder="Örn: Ekran Kartı, Anakart"><br><br>
            Teknik Detay (Açıklama):<br>
            <textarea name="teknik_detay" rows="5" cols="40" placeholder="İstediğiniz teknik detayları buraya yazabilirsiniz..."></textarea>
        </div><br>

        <button type="submit">Ürünü Kaydet</button>
    </form>
</body>
</html>