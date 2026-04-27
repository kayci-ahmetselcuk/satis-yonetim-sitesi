<?php
session_start();
include 'baglanti.php';

if (!isset($_SESSION['yetki']) || $_SESSION['yetki'] != 1) {
    die("Bu sayfaya erişim yetkiniz yok!");
}

// Siparişleri ve müşterileri birleştirerek çekiyoruz
// SIPARIS tablosundaki musteri_id ile KULLANICILAR tablosundaki kullanici_id eşleşiyor
$sql = "SELECT s.siparis_id, s.siparis_tarihi, s.toplam_tutar, k.ad_soyad 
        FROM SIPARIS s 
        JOIN KULLANICILAR k ON s.musteri_id = k.kullanici_id 
        ORDER BY s.siparis_tarihi DESC";

$sorgu = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sipariş Yönetim(Admin)| Bahaddin Gang</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, sans-serif; }
        
        body { 
            background: linear-gradient(135deg, #d57234 0%, #764ba2 100%); 
            background-attachment: fixed;
            min-height: 100vh; 
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            padding-top: 100px !important;
        }

        .back-link {
    position: fixed !important;
    top: 90px;
    left: 20px;
    display: inline-block;
    padding: 10px 18px;
    background-color: #2c3e50; 
    color: #ffffff;
    text-decoration: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    transition: background 0.3s ease, transform 0.2s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.back-link:hover {
    background-color: #7aa5cf;
    transform: translateX(-3px) scale(1.05); /* Sola kayma efekti */
    color: white;
}

        .orders-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 1000px;
        }

        h2 { text-align: center; color: #333; margin-bottom: 25px; }

        table { width: 100%; border-collapse: collapse; }
        th { background: #f8f9fa; padding: 15px; text-align: left; border-bottom: 2px solid #eee; color: #555; }
        td { padding: 15px; border-bottom: 1px solid #eee; color: #444; font-size: 15px; }

        .price-tag { font-weight: bold; color: #2d3748; }
        
        .status-badge {
            background: #ebf8ff;
            color: #3182ce;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            border: 1px solid #bee3f8;
        }

        tr:hover { background-color: #fcfcfc; }
        
        .empty-state { text-align: center; padding: 40px; color: #777; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<a href="panel.php" class="back-link">← Menü</a>

<div class="orders-card">
    <h2>📋 Tüm Müşteri Siparişleri</h2>

    <?php if ($sorgu->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Sipariş No</th>
                    <th>Müşteri Adı</th>
                    <th>Tarih</th>
                    <th>Toplam Tutar</th>
                    <th>Durum</th>
                </tr>
            </thead>
            <tbody>
           <?php while($s = $sorgu->fetch_assoc()): 
                    // Sipariş tarihi ile şu anki tarih arasındaki farkı hesaplıyoruz
                    $siparis_tarihi = new DateTime($s['siparis_tarihi']);
                    $bugun = new DateTime();
                    $fark = $bugun->diff($siparis_tarihi);
                    $gun_farki = $fark->days; // Geçen gün sayısı

                   // Durum ve Renk belirleme
                if ($gun_farki >= 2) {
                 $durum_metni = "Eski Sipariş";
                 $badge_stili = "background: #724420; color: #ffffff; border-color: #774a23;"; // Gri tonları
                 } else {
                  $durum_metni = "Yeni Sipariş";
                $badge_stili = "background: #ebf8ff; color: #3182ce; border-color: #bee3f8;"; // Mavi tonları
           }?>
<tr>
    <td>#<?= $s['siparis_id'] ?></td>
    <td><strong><?= htmlspecialchars($s['ad_soyad']) ?></strong></td>
    <td><?= date('d.m.Y H:i', strtotime($s['siparis_tarihi'])) ?></td>
    <td class="price-tag"><?= number_format($s['toplam_tutar'], 2, ',', '.') ?> TL</td>
    <td>
        <span class="status-badge" style="<?= $badge_stili ?>">
            <?= $durum_metni ?> (<?= $gun_farki ?> gün)
        </span>
    </td>
</tr>
<?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state">
            <p>Henüz sistemde kayıtlı bir sipariş bulunmuyor.</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>