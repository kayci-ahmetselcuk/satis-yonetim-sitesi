<?php
session_start();

if (isset($_GET['id']) && isset($_GET['islem'])) {
    $id = $_GET['id'];
    $islem = $_GET['islem'];

    if (isset($_SESSION['sepet'][$id])) {
        if ($islem == 'arttir') {
            $_SESSION['sepet'][$id]++;
        } elseif ($islem == 'azalt') {
            $_SESSION['sepet'][$id]--;
            // Eğer adet 0'a düşerse ürünü sepetten tamamen kaldır
            if ($_SESSION['sepet'][$id] < 1) {
                unset($_SESSION['sepet'][$id]);
            }
        }
    }
}

// Her işlemden sonra sepet sayfasına geri dön
header("Location: sepet.php");
exit();