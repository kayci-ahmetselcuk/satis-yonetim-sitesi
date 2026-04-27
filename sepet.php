<?php
session_start();
include 'baglanti.php';

$toplam_fiyat = 0;
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sepetim</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f4f9; padding: 40px; }
        .sepet-kutu { background: white; max-width: 900px; margin: auto; padding: 25px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); padding-top: 100px !important; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #3498db; color: white; padding: 12px; text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #eee; }
        .toplam-alan { text-align: right; margin-top: 25px; font-size: 22px; font-weight: bold; color: #2c3e50; }
        .btn-devam { color: #3498db; text-decoration: none; font-weight: bold; }
        .btn-onay { background: #27ae60; color: white; padding: 12px 25px; border: none; border-radius: 6px; cursor: pointer; float: right; text-decoration: none; margin-top: 10px; }
        .btn-onay:hover { background: #219150; }
        .bos-sepet { text-align: center; padding: 50px 20px; color: #7f8c8d;}
        .btn-alisveris-don {
         display: inline-block;
         background-color: #3498db;
         color: white;
         padding: 15px 30px;
         text-decoration: none;
         border-radius: 30px; /* Kenarları yuvarlak, modern bir buton */
         font-weight: bold;
         font-size: 18px;
         transition: background 0.3s, transform 0.2s;
         box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3); }
        .btn-alisveris-don:hover {
         background-color: #2980b9;
         transform: scale(1.05); /* Üstüne gelince hafifçe büyüsün */
          color: white; }
        .adet-kontrol {
         display: flex;
         align-items: center;
         gap: 10px;
         background: #f8f9fa;
         border-radius: 5px;
         padding: 5px;
         width: fit-content; }

        .adet-btn {
         display: flex;
         align-items: center;
         justify-content: center;
         width: 25px;
         height: 25px;
         background: white;
         border: 1px solid #ddd;
         color: #333;
         text-decoration: none;
         border-radius: 4px;
         font-weight: bold;
         transition: all 0.2s; }

        .adet-btn:hover {
         background: #3498db;
         color: white;
         border-color: #3498db; }

        .adet-sayi {
         font-weight: bold;
         min-width: 20px;
         text-align: center; }

        .btn-sil-kirmizi {
         color: #e74c3c;
         text-decoration: none;
         font-size: 14px;
         font-weight: bold;
         padding: 5px 10px;
         border: 1px solid #e74c3c;
         border-radius: 4px;
         transition: all 0.3s; }

        .btn-sil-kirmizi:hover {
         background: #e74c3c;
         color: white; }
        
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="sepet-kutu">
    <h2>🛒 Alışveriş Sepetim</h2>


<?php if (empty($_SESSION['sepet'])): ?>
        <div class="bos-sepet">
            <div style="font-size: 50px; margin-bottom: 20px;"><svg xmlns="http://www.w3.org/2000/svg" height="36px" viewBox="0 -960 960 960" width="36px" fill="#000000"><path d="m480-560-56-56 63-64H320v-80h167l-64-64 57-56 160 160-160 160ZM223.5-103.5Q200-127 200-160t23.5-56.5Q247-240 280-240t56.5 23.5Q360-193 360-160t-23.5 56.5Q313-80 280-80t-56.5-23.5Zm400 0Q600-127 600-160t23.5-56.5Q647-240 680-240t56.5 23.5Q760-193 760-160t-23.5 56.5Q713-80 680-80t-56.5-23.5ZM40-800v-80h131l170 360h280l156-280h91L692-482q-11 20-29.5 31T622-440H324l-44 80h480v80H280q-45 0-68.5-39t-1.5-79l54-98-144-304H40Z"/></svg></div>
            <p>Sepetinizde şu an ürün bulunmamaktadır.</p>
            <p>En yeni laptop ve donanım fırsatlarını kaçırma!</p>
            <br>
          <a href="urun_vitrini.php" class="btn-alisveris-don">← Alışverişe Geri Dön</a>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Ürün Adı</th>
                    <th>Adet</th>
                    <th>Birim Fiyat</th>
                    <th>Ara Toplam</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                foreach ($_SESSION['sepet'] as $id => $adet): 
                    // Veritabanından o anki fiyat ve isim bilgisini çekiyoruz
                    $sorgu = "SELECT urun_adi, fiyat FROM URUNLER WHERE urun_id = ?";
                    $stmt = $conn->prepare($sorgu);
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $urun = $result->fetch_assoc();

                    $ara_toplam = $urun['fiyat'] * $adet;
                    $toplam_fiyat += $ara_toplam;
                ?>
                <tr>
                    <td><strong><?= $urun['urun_adi'] ?></strong></td>
                    <td>
    <div class="adet-kontrol">
        <a href="sepet_guncelle.php?id=<?= $id ?>&islem=azalt" class="adet-btn">-</a>
        <span class="adet-sayi"><?= $adet ?></span>
        <a href="sepet_guncelle.php?id=<?= $id ?>&islem=arttir" class="adet-btn">+</a>
    </div>
                   </td>
                    <td><?= number_format($urun['fiyat'], 2, ',', '.') ?> TL</td>
                    <td><strong><?= number_format($ara_toplam, 2, ',', '.') ?> TL</strong></td>
                    <td>
            <a href="sepet_sil.php?id=<?= $id ?>" class="btn-sil-kirmizi">🗑️ Kaldır</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="toplam-alan">
            Genel Toplam: <?= number_format($toplam_fiyat, 2, ',', '.') ?> TL
        </div>
        
        <div style="overflow: hidden;">
            <a href="siparis_onay.php" class="btn-onay">Alışverişi Tamamla ✅</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>