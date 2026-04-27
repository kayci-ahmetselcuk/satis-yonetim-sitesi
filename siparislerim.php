<?php
session_start();
include 'baglanti.php';

// Giriş kontrolü
if (!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['kullanici_id'];

// 2. SQL: Siparişleri detay tablosuyla birleştirip toplamı o an hesaplıyoruz
$sql = "SELECT 
            S.siparis_id, 
            S.siparis_tarihi, 
            SUM(SD.adet * SD.birim_fiyat) as hesaplanan_toplam 
        FROM SIPARIS S
        LEFT JOIN SIPARIS_DETAY SD ON S.siparis_id = SD.siparis_id
        WHERE S.musteri_id = ?
        GROUP BY S.siparis_id
        ORDER BY S.siparis_tarihi DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$siparisler = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siparişlerim | Bahaddin Gang</title>
    <div class="header">
        <a href="panel.php" class="btn-panel">← Menü</a>
    </div>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, sans-serif; }
        
        body { 
            background:linear-gradient(135deg, #764ba2 0%, #d57234 100%);
            min-height: 100vh; 
            padding: 40px 20px;
            padding-top: 100px !important;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2); 
            width: 100%;
            max-width: 800px;  
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 0px solid #eee;          
            padding-bottom: 15px;
        }/*border bottomu simdilik 0 yaptım sonra degistircem*/

        .header h2 { color: #333; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            text-align: left;
            background: #f8f9fa;
            color: #555;
            padding: 15px;
            border-bottom: 2px solid #eee;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            color: #444;
            font-size: 15px;
        }

        .status-badge {
            background: #e6fffa;
            color: #2c7a7b;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .price { font-weight: bold; color: #2d3748; }

        .empty-state {
            text-align: center;
            padding: 50px 0;
            color: #777;
        }
            .btn-panel {
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
.btn-panel:hover {
    background-color: #7aa5cf;
    transform: translateX(-3px); /* Sola kayma efekti */
    color: white;
}
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="header">
        <h2>📦 Siparişlerim</h2>
    </div>

    <?php if ($siparisler->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Sipariş No</th>
                    <th>Tarih</th>
                    <th>Toplam Tutar</th>
                    <th>Durum</th>
                </tr>
            </thead>
            <tbody>
                <?php while($s = $siparisler->fetch_assoc()): ?>
                <tr>
                    <td>#<?= $s['siparis_id'] ?></td>
                    <td><?= date('d.m.Y H:i', strtotime($s['siparis_tarihi'])) ?></td>
                    <td class="price"><?= number_format($s['hesaplanan_toplam'], 2, ',', '.') ?> TL</td>
                    <td><span class="status-badge">Hazırlanıyor</span></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state">
            <p>Henüz bir siparişiniz bulunmuyor.</p>
            <br>
            <a href="urun_vitrini.php" style="color: #667eea; font-weight: bold; text-decoration: none;">Alışverişe Başla →</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>