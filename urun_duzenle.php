<?php
session_start();
if (!isset($_SESSION['yetki']) || $_SESSION['yetki'] != 1) { die("Yetkisiz erişim."); }

include 'baglanti.php';
$conn->set_charset("utf8");

// 1. ADIM: Düzenlenecek ürünün ID'sini al ve mevcut verileri çek
if (!isset($_GET['id'])) { die("ID belirtilmedi."); }
$urun_id = $_GET['id'];

$sql = "SELECT U.*, L.islemci, L.ram_gb, L.ekran_karti, L.depolama, D.parca_tipi, D.teknik_detay 
        FROM URUNLER U 
        LEFT JOIN LAPTOP L ON U.urun_id = L.urun_id 
        LEFT JOIN DONANIM_PARCALARI D ON U.urun_id = D.urun_id 
        WHERE U.urun_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $urun_id);
$stmt->execute();
$sonuc = $stmt->get_result();
$urun = $sonuc->fetch_assoc();

if (!$urun) { die("Ürün bulunamadı."); }

// 2. ADIM: Form gönderildiğinde veritabanını güncelle
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $urun_adi = $_POST['urun_adi'];
    $fiyat = $_POST['fiyat'];
    $marka_id = $_POST['marka_id'];
    $kategori_id = $_POST['kategori_id'];

    $conn->begin_transaction();
    try {
        // Ana tabloyu güncelle
        $up1 = $conn->prepare("UPDATE URUNLER SET marka_id=?, kategori_id=?, urun_adi=?, fiyat=? WHERE urun_id=?");
        $up1->bind_param("iisdi", $marka_id, $kategori_id, $urun_adi, $fiyat, $urun_id);
        $up1->execute();

        // Eğer ürün Laptop ise laptop tablosunu güncelle
        if (!empty($urun['islemci'])) {
            $up2 = $conn->prepare("UPDATE LAPTOP SET islemci=?, ram_gb=?, ekran_karti=?, depolama=? WHERE urun_id=?");
            $up2->bind_param("sisii", $_POST['islemci'], $_POST['ram_gb'], $_POST['ekran_karti'], $_POST['depolama'], $urun_id);
            $up2->execute();
        } 
        // Eğer ürün Donanım ise donanım tablosunu güncelle
        elseif (!empty($urun['parca_tipi'])) {
            $up3 = $conn->prepare("UPDATE DONANIM_PARCALARI SET parca_tipi=?, teknik_detay=? WHERE urun_id=?");
            $up3->bind_param("ssi", $_POST['parca_tipi'], $_POST['teknik_detay'], $urun_id);
            $up3->execute();
        }

        $conn->commit();
        header("Location: urun_listele.php?mesaj=guncellendi");
    } catch (Exception $e) {
        $conn->rollback();
        echo "Hata: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ürün Düzenle</title>
</head>
<body>
    <h2>Ürün Düzenle (ID: <?= $urun_id ?>)</h2>
    <form method="post">
        Ürün Adı: <input type="text" name="urun_adi" value="<?= $urun['urun_adi'] ?>" required><br><br>
        Fiyat: <input type="number" step="0.01" name="fiyat" value="<?= $urun['fiyat'] ?>" required><br><br>
        Marka ID: <input type="number" name="marka_id" value="<?= $urun['marka_id'] ?>" required><br><br>
        Kategori ID: <input type="number" name="kategori_id" value="<?= $urun['kategori_id'] ?>" required><br><br>

        <?php if (!empty($urun['islemci'])): ?>
            <h3>Laptop Detayları</h3>
            İşlemci: <input type="text" name="islemci" value="<?= $urun['islemci'] ?>"><br>
            RAM (GB): <input type="number" name="ram_gb" value="<?= $urun['ram_gb'] ?>"><br>
            Ekran Kartı: <input type="text" name="ekran_karti" value="<?= $urun['ekran_karti'] ?>"><br>
            Depolama: <input type="number" name="depolama" value="<?= $urun['depolama'] ?>"><br>
        <?php elseif (!empty($urun['parca_tipi'])): ?>
            <h3>Donanım Detayları</h3>
            Parça Tipi: <input type="text" name="parca_tipi" value="<?= $urun['parca_tipi'] ?>"><br>
            Teknik Detay:<br>
            <textarea name="teknik_detay"><?= $urun['teknik_detay'] ?></textarea><br>
        <?php endif; ?>

        <br><button type="submit">Değişiklikleri Kaydet</button>
    </form>
</body>
</html>