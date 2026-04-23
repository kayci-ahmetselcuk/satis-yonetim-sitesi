<?php
session_start();
// Listeleme sayfası genellikle herkese açık olabilir ancak 
// Sil/Düzenle butonları için yetki kontrolü ekleyeceğiz.
include 'baglanti.php';
$conn->set_charset("utf8");

// SQL Sorgusu: URUNLER tablosunu MARKA ve KATEGORI ile birleştiriyoruz.
// Ayrıca LAPTOP ve DONANIM tablolarına LEFT JOIN atarak varsa detaylarını alıyoruz.
$sql = "SELECT 
            U.urun_id, 
            U.urun_adi, 
            U.fiyat, 
            M.marka_adi, 
            K.kategori_adi,
            L.islemci,
            D.parca_tipi
        FROM URUNLER U
        LEFT JOIN MARKA M ON U.marka_id = M.marka_id
        LEFT JOIN KATEGORI K ON U.kategori_id = K.kategori_id
        LEFT JOIN LAPTOP L ON U.urun_id = L.urun_id
        LEFT JOIN DONANIM_PARCALARI D ON U.urun_id = D.urun_id
        ORDER BY U.urun_id DESC";

$sonuc = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ürün Listesi</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f4f4f4; }
        tr:hover { background-color: #f9f9f9; }
        .tag { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .laptop { background: #e3f2fd; color: #1976d2; }
        .donanim { background: #f1f8e9; color: #388e3c; }
    </style>
</head>
<body>

    <h2>Sistemdeki Kayıtlı Ürünler</h2>
    
    <?php if (isset($_SESSION['yetki']) && $_SESSION['yetki'] == 1): ?>
        <a href="urun_ekle.php" style="text-decoration:none;">
            <button style="padding:10px; cursor:pointer;">+ Yeni Ürün Ekle</button>
        </a>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Ürün Adı</th>
                <th>Marka</th>
                <th>Kategori</th>
                <th>Fiyat</th>
                <th>Tip / Detay</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $sonuc->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['urun_id'] ?></td>
                    <td>**<?= $row['urun_adi'] ?>**</td>
                    <td><?= $row['marka_adi'] ?></td>
                    <td><?= $row['kategori_adi'] ?></td>
                    <td><?= number_format($row['fiyat'], 2, ',', '.') ?> TL</td>
                    <td>
                        <?php 
                        if (!empty($row['islemci'])) {
                            echo '<span class="tag laptop">Laptop</span> (' . $row['islemci'] . ')';
                        } elseif (!empty($row['parca_tipi'])) {
                            echo '<span class="tag donanim">Donanım</span> (' . $row['parca_tipi'] . ')';
                        } else {
                            echo 'Genel Ürün';
                        }
                        ?>
                    </td>
                    <td>
                        <?php if (isset($_SESSION['yetki']) && $_SESSION['yetki'] == 1): ?>
                            <a href="urun_duzenle.php?id=<?= $row['urun_id'] ?>">Düzenle</a> | 
                            <a href="urun_sil.php?id=<?= $row['urun_id'] ?>" onclick="return confirm('Emin misiniz?')">Sil</a>
                        <?php else: ?>
                            <button disabled>Yetki Yok</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>