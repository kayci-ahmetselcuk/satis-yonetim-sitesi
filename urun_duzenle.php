<?php
session_start();
if (!isset($_SESSION['yetki']) || $_SESSION['yetki'] != 1) { die("Yetkisiz erişim."); }

include 'baglanti.php';
$conn->set_charset("utf8");

// 1. ADIM: Düzenlenecek ürünün ID'sini al ve mevcut verileri çek
if (!isset($_GET['id'])) { die("ID belirtilmedi."); }
$urun_id = $_GET['id'];

$sql = "SELECT U.*, L.islemci, L.ram_gb, L.ekran_karti, L.depolama, D.parca_tipi, D.teknik_detay 
        FROM URUNLER U 
        LEFT JOIN LAPTOP L ON U.urun_id = L.urun_id 
        LEFT JOIN DONANIM_PARCALARI D ON U.urun_id = D.urun_id 
        WHERE U.urun_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $urun_id);
$stmt->execute();
$sonuc = $stmt->get_result();
$urun = $sonuc->fetch_assoc();

if (!$urun) { die("Ürün bulunamadı."); }

// 2. ADIM: Form gönderildiğinde veritabanını güncelle
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $urun_adi = $_POST['urun_adi'];
    $fiyat = $_POST['fiyat'];
    $marka_id = $_POST['marka_id'];
    $kategori_id = $_POST['kategori_id'];

    $conn->begin_transaction();
    try {
        // Ana tabloyu güncelle
        $up1 = $conn->prepare("UPDATE URUNLER SET marka_id=?, kategori_id=?, urun_adi=?, fiyat=? WHERE urun_id=?");
        $up1->bind_param("iisdi", $marka_id, $kategori_id, $urun_adi, $fiyat, $urun_id);
        $up1->execute();

        // Eğer ürün Laptop ise laptop tablosunu güncelle
        if (!empty($urun['islemci'])) {
            $up2 = $conn->prepare("UPDATE LAPTOP SET islemci=?, ram_gb=?, ekran_karti=?, depolama=? WHERE urun_id=?");
            $up2->bind_param("sisii", $_POST['islemci'], $_POST['ram_gb'], $_POST['ekran_karti'], $_POST['depolama'], $urun_id);
            $up2->execute();
        } 
        // Eğer ürün Donanım ise donanım tablosunu güncelle
        elseif (!empty($urun['parca_tipi'])) {
            $up3 = $conn->prepare("UPDATE DONANIM_PARCALARI SET parca_tipi=?, teknik_detay=? WHERE urun_id=?");
            $up3->bind_param("ssi", $_POST['parca_tipi'], $_POST['teknik_detay'], $urun_id);
            $up3->execute();
        }

        $conn->commit();
        header("Location: urun_listele.php?mesaj=guncellendi");
    } catch (Exception $e) {
        $conn->rollback();
        echo "Hata: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürün Düzenle | Bahaddin Gang</title>
    <style>
        /* Genel Sifirlama ve Arka Plan */
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, sans-serif; }
        
        body { 
            background: linear-gradient(135deg, #d57234 0%, #764ba2 100%); 
            background-attachment: fixed;
            min-height: 100vh; 
            display: flex; 
            flex-direction: column;
            align-items: center; 
            padding: 20px;
            /* NAVBAR ÇAKIŞMASINI ÖNLEYEN KRİTİK AYAR */
            padding-top: 120px !important; 
        }

        /* Düzenleme Kartı */
        .edit-card {
            background: white;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 650px;
        }

        .edit-card h2 { color: #333; font-size: 24px; margin-bottom: 20px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; text-align: center; }
        .edit-card h3 { color: #764ba2; font-size: 18px; margin: 25px 0 15px 0; display: flex; align-items: center; gap: 10px; }

        /* Form Elemanları */
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #555; font-size: 14px; }
        
        .form-group input, 
        .form-group textarea, 
        .form-group select { 
            width: 100%; 
            padding: 12px 15px; 
            border: 2px solid #eee; 
            border-radius: 8px; 
            outline: none; 
            transition: 0.3s; 
            font-size: 15px;
        }

        .form-group input:focus, .form-group textarea:focus { 
            border-color: #764ba2; 
            box-shadow: 0 0 8px rgba(118, 75, 162, 0.1); 
        }

        /* Butonlar */
        .btn-container { display: flex; gap: 10px; margin-top: 25px; }

        .btn-save {
            flex: 2;
            padding: 13px;
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-save:hover { background: #219150; transform: translateY(-2px); }

        .btn-cancel {
            flex: 1;
            padding: 13px;
            background: #e74c3c;
            color: white;
            text-decoration: none;
            text-align: center;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-cancel:hover { background: #c0392b; }

        .badge-id { background: #eee; padding: 4px 10px; border-radius: 5px; font-size: 12px; color: #666; }
    </style>
</head>
<body> 
    <?php include 'navbar.php'; ?>

    <div class="edit-card">
        <h2>📦 Ürün Düzenle <span class="badge-id">ID: <?= $urun_id ?></span></h2>
        
        <form method="post">
            <div class="form-group">
                <label>Ürün Adı</label>
                <input type="text" name="urun_adi" value="<?= htmlspecialchars($urun['urun_adi']) ?>" required>
            </div>

            <div style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 1;">
                    <label>Fiyat (TL)</label>
                    <input type="number" step="0.01" name="fiyat" value="<?= $urun['fiyat'] ?>" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Marka ID</label>
                    <input type="number" name="marka_id" value="<?= $urun['marka_id'] ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Kategori ID</label>
                <input type="number" name="kategori_id" value="<?= $urun['kategori_id'] ?>" required>
            </div>

            <?php if (!empty($urun['islemci'])): ?>
                <h3>💻 Laptop Teknik Özellikleri</h3>
                <div class="form-group">
                    <label>İşlemci Modeli</label>
                    <input type="text" name="islemci" value="<?= htmlspecialchars($urun['islemci']) ?>">
                </div>
                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 1;">
                        <label>RAM (GB)</label>
                        <input type="number" name="ram_gb" value="<?= $urun['ram_gb'] ?>">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Depolama (GB)</label>
                        <input type="number" name="depolama" value="<?= $urun['depolama'] ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Ekran Kartı</label>
                    <input type="text" name="ekran_karti" value="<?= htmlspecialchars($urun['ekran_karti']) ?>">
                </div>

            <?php elseif (!empty($urun['parca_tipi'])): ?>
                <h3>🔧 Donanım Parça Bilgileri</h3>
                <div class="form-group">
                    <label>Parça Tipi</label>
                    <input type="text" name="parca_tipi" value="<?= htmlspecialchars($urun['parca_tipi']) ?>">
                </div>
                <div class="form-group">
                    <label>Teknik Detaylar</label>
                    <textarea name="teknik_detay" rows="4"><?= htmlspecialchars($urun['teknik_detay']) ?></textarea>
                </div>
            <?php endif; ?>

            <div class="btn-container">
                <button type="submit" class="btn-save">💾 Değişiklikleri Kaydet</button>
                <a href="urun_listele.php" class="btn-cancel">İptal</a>
            </div>
        </form>
    </div>
</body>
</html>