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
     <div>
    <a href="panel.php" class="back-link">← Menü</a>
     </div>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, sans-serif; }
        body { 
            background: linear-gradient(135deg, #d57234 0%, #764ba2 100%); 
            background-attachment: fixed;
            min-height: 100vh; 
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 100px !important;
        }
                .back-link {
            position: fixed !important;
            top: 90px; left: 20px;
            padding: 10px 20px;
            background-color: #2c3e50; 
            color: white; text-decoration: none;
            border-radius: 8px; font-weight: 600;
            z-index: 1000; box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            transition: 0.3s;
        }
        .back-link:hover { background-color: #34495e; transform: scale(1.05); }
        
        .list-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 1100px;
        }

        .header-area {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 20px;
        }

        h2 { color: #333; font-size: 24px; }

        .btn-add {
            background: #27ae60;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 14px;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-add:hover { background: #219150; transform: translateY(-2px); }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f8f9fa; padding: 15px; text-align: left; color: #555; border-bottom: 2px solid #eee; font-size: 14px; }
        td { padding: 15px; border-bottom: 1px solid #eee; color: #444; font-size: 15px; }
        
        tr:hover { background-color: #fcfaff; }

        /* Etiket Tasarımları */
        .tag { padding: 5px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .laptop { background: #ebf8ff; color: #3182ce; border: 1px solid #bee3f8; }
        .donanim { background: #f0fff4; color: #2f855a; border: 1px solid #9ae6b4; }
        .genel { background: #f7fafc; color: #718096; border: 1px solid #edf2f7; }

        .price { font-weight: bold; color: #2d3748; }

        /* İşlem Butonları */
        .action-links a {
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            padding: 5px 10px;
            border-radius: 5px;
            transition: 0.2s;
        }
        .btn-edit { color: #3182ce; background: #ebf8ff; }
        .btn-edit:hover { background: #3182ce; color: white; }
        
        .btn-delete { color: #e53e3e; background: #fff5f5; margin-left: 5px; }
        .btn-delete:hover { background: #e53e3e; color: white; }

        .btn-no-auth { background: #eee; color: #999; border: none; padding: 5px 10px; border-radius: 5px; cursor: not-allowed; }
        </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
<div class="list-card">
    <div class="header-area">
        <h2>📋 Sistemdeki Ürünler</h2>
        <?php if (isset($_SESSION['yetki']) && $_SESSION['yetki'] == 1): ?>
            <a href="urun_ekle.php" class="btn-add">➕ Yeni Ürün Ekle</a>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Ürün Bilgisi</th>
                <th>Marka / Kategori</th>
                <th>Fiyat</th>
                <th>Tip / Detay</th>
                <th style="text-align: right;">İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $sonuc->fetch_assoc()): ?>
                <tr>
                    <td>#<?= $row['urun_id'] ?></td>
                    <td><strong><?= htmlspecialchars($row['urun_adi']) ?></strong></td>
                    <td>
                        <span style="font-size: 13px; color: #777;">
                            <?= $row['marka_adi'] ?> / <?= $row['kategori_adi'] ?>
                        </span>
                    </td>
                    <td class="price"><?= number_format($row['fiyat'], 2, ',', '.') ?> TL</td>
                    <td>
                        <?php 
                        if (!empty($row['islemci'])) {
                            echo '<span class="tag laptop">Laptop</span><br><small style="color:#666;">' . $row['islemci'] . '</small>';
                        } elseif (!empty($row['parca_tipi'])) {
                            echo '<span class="tag donanim">Donanım</span><br><small style="color:#666;">' . $row['parca_tipi'] . '</small>';
                        } else {
                            echo '<span class="tag genel">Genel Ürün</span>';
                        }
                        ?>
                    </td>
                    <td style="text-align: right;" class="action-links">
                        <?php if (isset($_SESSION['yetki']) && $_SESSION['yetki'] == 1): ?>
                            <a href="urun_duzenle.php?id=<?= $row['urun_id'] ?>" class="btn-edit">Düzenle</a>
                            <a href="urun_sil.php?id=<?= $row['urun_id'] ?>" class="btn-delete" onclick="return confirm('Bu ürünü silmek istediğinize emin misiniz?')">Sil</a>
                        <?php else: ?>
                            <button class="btn-no-auth" disabled>Yetki Yok</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>