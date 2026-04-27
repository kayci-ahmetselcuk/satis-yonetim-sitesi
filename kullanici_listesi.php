<?php
session_start();
include 'baglanti.php';

if (!isset($_SESSION['yetki']) || $_SESSION['yetki'] != 1) {
    die("Bu sayfaya erişim yetkiniz yok!");
}

$mesaj = "";

// Yetki Güncelleme İşlemi
if (isset($_POST['yetki_degistir'])) {
    $kullanici_id = $_POST['kullanici_id'];
    $yeni_yetki = $_POST['yeni_yetki'];

    // Kendini adminlikten çıkarmayı engellemek için küçük bir kontrol
    if ($kullanici_id == $_SESSION['kullanici_id'] && $yeni_yetki == 0) {
        $mesaj = "<p class='msg error'>Kendi yetkinizi alamazsınız!</p>";
    } else {
        $stmt = $conn->prepare("UPDATE KULLANICILAR SET yetki = ? WHERE kullanici_id = ?");
        $stmt->bind_param("ii", $yeni_yetki, $kullanici_id);
        if ($stmt->execute()) {
            $mesaj = "<p class='msg success'>Kullanıcı yetkisi güncellendi.</p>";
        }
    }
}

// Tüm kullanıcıları çek
$sorgu = $conn->query("SELECT kullanici_id, ad_soyad, eposta, yetki FROM KULLANICILAR ORDER BY kullanici_id ASC");
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kullanıcı Yönetimi(Admin) |Bahaddin Gang</title>
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
    transform: translateX(-3px) scale(1.05);
    color: white;
}

        .users-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 900px;
        }

        h2 { text-align: center; color: #333; margin-bottom: 25px; }

        table { width: 100%; border-collapse: collapse; }
        th { background: #f8f9fa; padding: 15px; text-align: left; border-bottom: 2px solid #eee; color: #555; }
        td { padding: 15px; border-bottom: 1px solid #eee; color: #444; }

        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-admin { background: #fff5f5; color: #c53030; border: 1px solid #feb2b2; }
        .badge-user { background: #f0fff4; color: #2f855a; border: 1px solid #9ae6b4; }

        .btn-action {
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: 0.3s;
        }
        .btn-make-admin { background-color: #e53e3e; color: white; }
        .btn-make-user { background-color: #3182ce; color: white; }
        .btn-action:hover { opacity: 0.8; transform: translateY(-2px); }

        .msg { padding: 10px; border-radius: 5px; text-align: center; margin-bottom: 15px; }
        .success { background: #c6f6d5; color: #2f855a; }
        .error { background: #fed7d7; color: #c53030; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<a href="panel.php" class="back-link">← Menü</a>

<div class="users-card">
    <h2>👥 Kayıtlı Kullanıcılar</h2>
    <?= $mesaj ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Ad Soyad</th>
                <th>E-posta</th>
                <th>Statü</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php while($user = $sorgu->fetch_assoc()): ?>
            <tr>
                <td>#<?= $user['kullanici_id'] ?></td>
                <td><strong><?= htmlspecialchars($user['ad_soyad']) ?></strong></td>
                <td><?= htmlspecialchars($user['eposta']) ?></td>
                <td>
                    <?php if($user['yetki'] == 1): ?>
                        <span class="badge badge-admin">Admin</span>
                    <?php else: ?>
                        <span class="badge badge-user">Müşteri</span>
                    <?php endif; ?>
                </td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="kullanici_id" value="<?= $user['kullanici_id'] ?>">
                        <?php if($user['yetki'] == 1): ?>
                            <input type="hidden" name="yeni_yetki" value="0">
                            <button type="submit" name="yetki_degistir" class="btn-action btn-make-user">Yetkisini Al</button>
                        <?php else: ?>
                            <input type="hidden" name="yeni_yetki" value="1">
                            <button type="submit" name="yetki_degistir" class="btn-action btn-make-admin">Yetkilendir</button>
                        <?php endif; ?>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>