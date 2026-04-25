<?php
session_start();
include 'baglanti.php';

// giriş yapmamışsa login'e gönder
if (!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php");
    exit();
}

// sepet boşsa vitrine gönder
if (empty($_SESSION['sepet'])) {
    header("Location: urun_vitrini.php");
    exit();
}

$musteri_id = $_SESSION['kullanici_id'];
$toplam_tutar = 0;

// ya hepsi kaydedilir ya da hiçbiri kaydedilmez olarak islem baslattık
$conn->begin_transaction();

try {
    // 1. Önce Toplam Tutarı Hesaplayalım (Güvenlik için veritabanından çekerek)
    foreach ($_SESSION['sepet'] as $id => $adet) {
        $sorgu = $conn->prepare("SELECT fiyat FROM URUNLER WHERE urun_id = ?");
        $sorgu->bind_param("i", $id);
        $sorgu->execute();
        $fiyat = $sorgu->get_result()->fetch_assoc()['fiyat'];
        $toplam_tutar += ($fiyat * $adet);
    }

    // 2. Ana Sipariş Kaydını Atalım
    $sql_ana = "INSERT INTO siparis (musteri_id, siparis_tarihi, toplam_tutar) VALUES (?, NOW(), ?)";
    $stmt_ana = $conn->prepare($sql_ana);
    $stmt_ana->bind_param("id", $musteri_id, $toplam_tutar);
    $stmt_ana->execute();

    // 3. Yeni oluşan Sipariş ID'sini alalım (En kritik nokta!)
    $yeni_siparis_id = $conn->insert_id;

    // 4. Sepetteki her ürünü detay tablosuna tek tek ekleyelim
    $sql_detay = "INSERT INTO siparis_detay (siparis_id, urun_id, adet, birim_fiyat) VALUES (?, ?, ?, ?)";
    $stmt_detay = $conn->prepare($sql_detay);

    foreach ($_SESSION['sepet'] as $id => $adet) {
        // kullanıcı sepet sayfasındayken veritabanından ürünün fiyatını değiştirmek zorunda kalırsak(zam gelirse vb.) ürünü güncel fiyattan satmamızı sağlar
        $sorgu_fiyat = $conn->prepare("SELECT fiyat FROM URUNLER WHERE urun_id = ?");
        $sorgu_fiyat->bind_param("i", $id);
        $sorgu_fiyat->execute();
        $birim_fiyat = $sorgu_fiyat->get_result()->fetch_assoc()['fiyat'];

        // Detay tablosuna ekle
        $stmt_detay->bind_param("iiid", $yeni_siparis_id, $id, $adet, $birim_fiyat);
        $stmt_detay->execute();
    }

    // Her şey yolundaysa veritabanına onayı ver!
    $conn->commit();

    // Sepeti temizle
    unset($_SESSION['sepet']);

    // Başarılı sayfasına yönlendir
    header("Location: basarili.php?siparis_id=" . $yeni_siparis_id);

} catch (Exception $e) {
    // Bir hata oluşursa yapılan tüm işlemleri geri al (Rollback)
    $conn->rollback();
    echo "Sipariş sırasında bir hata oluştu: " . $e->getMessage();
}
?>