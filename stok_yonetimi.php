<?php
session_start();
include 'baglanti.php';

// Güvenlik: Sadece admin (yetki=1) girebilir
if (!isset($_SESSION['yetki']) || $_SESSION['yetki'] != 1) {
    die("Bu sayfaya erişim yetkiniz yok!");
}

$mesaj = "";

// Stok Güncelleme İşlemi
if (isset($_POST['stok_kaydet'])) {
    $u_id = $_POST['urun_id'];
    $mik = $_POST['miktar'];
    $konum = $_POST['depo_konumu'];

    // Önce bu ürünün stok tablosunda kaydı var mı kontrol et
    $kontrol = $conn->query("SELECT * FROM stok WHERE urun_id = $u_id");

    if ($kontrol->num_rows > 0) {
        // Varsa Güncelle
        $sql = "UPDATE stok SET miktar = ?, depo_konumu = ? WHERE urun_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isi", $mik, $konum, $u_id);
    } else {
        // Yoksa Yeni Kayıt Oluştur
        $sql = "INSERT INTO stok (urun_id, miktar, depo_konumu) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $u_id, $mik, $konum);
    }
    $stmt->execute();
}

// Ürün adı ana tabloda, miktar ve konum stok tablosunda
$sorgu = $conn->query("
    SELECT u.urun_id, u.urun_adi, s.miktar, s.depo_konumu 
    FROM URUNLER u
    LEFT JOIN stok s ON u.urun_id = s.urun_id
    ORDER BY u.urun_id DESC
");
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Stok Yönetimi(Admin) | Bahaddin Gang</title>
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
    transform: translateX(-3px) scale(1.05); /* Sola kayma efekti ve büyüme*/
    color: white;
}

        .stock-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 800px;
        }

        h2 { text-align: center; color: #333; margin-bottom: 20px; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #f8f9fa; padding: 15px; text-align: left; border-bottom: 2px solid #eee; }
        td { padding: 15px; border-bottom: 1px solid #eee; }

        .stok-input {
            width: 80px; padding: 8px; border: 1px solid #ddd; border-radius: 5px;
        }

        .btn-update {
            background: #27ae60; color: white; border: none;
            padding: 8px 15px; border-radius: 5px; cursor: pointer;
            transition: 0.3s;
        }
        .btn-update:hover { background: #219150; }

        .msg { background: #c6f6d5; color: #2f855a; padding: 10px; border-radius: 5px; text-align: center; margin-bottom: 15px; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<a href="panel.php" class="back-link">← Menü</a>

<div class="stock-card">
    <h2>📉 Stok ve Depo Yönetimi</h2>
    <?= $mesaj ?>

    <table>
        <thead>
            <tr>
                <th>ID</th> <th>Ürün Adı</th> <th>Miktar</th> <th>Depo Konumu</th> <th>İşlem</th> </tr>
        </thead>
        <tbody>
            <?php while($row = $sorgu->fetch_assoc()): ?>
            <tr>
                <td>#<?= $row['urun_id'] ?></td>
                
                <td><strong><?= htmlspecialchars($row['urun_adi']) ?></strong></td>
                
                <form method="POST">
                    <td>
                        <input type="number" name="miktar" value="<?= $row['miktar'] ?? 0 ?>" class="stok-input">
                    </td>
                    
                    <td>
                        <input type="text" name="depo_konumu" value="<?= htmlspecialchars($row['depo_konumu'] ?? '') ?>" class="konum-input" placeholder="Raf/Depo">
                    </td>
                    
                    <td>
                        <input type="hidden" name="urun_id" value="<?= $row['urun_id'] ?>">
                        <button type="submit" name="stok_kaydet" class="btn-update">Kaydet</button>
                    </td>
                </form>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>