<?php
session_start();
header('Content-Type: application/json'); // Tarayıcıya JSON gönderdiğimizi söylüyoruz

$cevap = array();

if (empty($_SESSION['sepet'])) {
    $cevap['sepetBosMu'] = true;
} else {
    $cevap['sepetBosMu'] = false;
}

echo json_encode($cevap); // Veriyi JSON formatına çevirip basıyoruz
?>
