<?php
session_start();

// 1. Silinecek ürünün ID'si geldi mi?
if (isset($_GET['id'])) {
    $urun_id = $_GET['id'];

   
    unset($_SESSION['sepet'][$urun_id]); // 1 taneydi zaten, tamamen sil

    // 3. Silme işleminden sonra sepet sayfasına geri dön
    header("Location: sepet.php?durum=silindi");
    exit();
} else {
    // ID gelmediyse de sepete geri dön
    header("Location: sepet.php");
    exit();
}
?>