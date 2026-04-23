<?php
session_start();
if (!isset($_SESSION['yetki']) || $_SESSION['yetki'] != 1) {
    die("Yetkisiz erişim.");
}

include 'baglanti.php';

// Silinecek ürünün ID'sini URL'den alıyoruz
if (isset($_GET['id'])) {
    $urun_id = $_GET['id'];

    /* ON DELETE CASCADE kullandıgım icin burada tek bir sorgu yetiyor. laptop tablosundaki nitelikler otomatik silinecek. eger olmasaydı fazladan 2 tane daha sorgu yapmamız gerekecekti
    */
    $sorgu = $conn->prepare("DELETE FROM URUNLER WHERE urun_id = ?");
    $sorgu->bind_param("i", $urun_id);

    if ($sorgu->execute()) {
        header("Location: urun_listele.php?durum=silindi");
        exit();
    } else {
        echo "Silme işlemi sırasında veritabanı hatası oluştu: " . $conn->error;
    }
} else {
    echo "Geçersiz veya eksik ID.";
}
?>