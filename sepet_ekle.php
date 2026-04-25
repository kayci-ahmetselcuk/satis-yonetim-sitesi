<?php
session_start();

// urun istegi var mı kontrol et
if (isset($_GET['id'])) {
    $urun_id = $_GET['id'];

    // daha önce sepet oluşturulmadıysa bos dizi olustur
    if (!isset($_SESSION['sepet'])) {
        $_SESSION['sepet'] = array();
    }

    // urun zaten sepeete varsa 1 arttır
    if (isset($_SESSION['sepet'][$urun_id])) {
        $_SESSION['sepet'][$urun_id] += 1;
    } else {
        // yoksa yeni ekle ve miktarı 1 yap
        $_SESSION['sepet'][$urun_id] = 1;
    }

    // vitrine geri dön 
    header("Location: urun_vitrini.php?durum=eklendi");
    exit();
} else {
    // urun istegi yoksa vitrine yolla
    header("Location: urun_vitrini.php");
    exit();
}
?>