<?php
session_start();
if (!isset($_SESSION['yetki']) || $_SESSION['yetki'] != 1) { 
    die("Yetkisiz erişim."); 
}
$mesaj = "";
include 'baglanti.php';
$conn->set_charset("utf8");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $marka_id = $_POST['marka_id'];
    $kategori_id = $_POST['kategori_id'];
    $urun_adi = $_POST['urun_adi'];
    $fiyat = $_POST['fiyat'];
    $urun_tipi = $_POST['urun_tipi'];

    $conn->begin_transaction();

    try {
        // 1. Süper Tip: URUNLER tablosuna temel bilgileri ekle
        $stmt = $conn->prepare("INSERT INTO URUNLER (marka_id, kategori_id, urun_adi, fiyat) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisd", $marka_id, $kategori_id, $urun_adi, $fiyat);
        $stmt->execute();
        $yeni_id = $conn->insert_id;

        // 2. Alt Tip: Seçime göre ilgili tabloya detayları ekle
        if ($urun_tipi == "laptop") {
            $stmt2 = $conn->prepare("INSERT INTO LAPTOP (urun_id, islemci, ram_gb, ekran_karti, depolama) VALUES (?, ?, ?, ?, ?)");
            $stmt2->bind_param("isisi", $yeni_id, $_POST['islemci'], $_POST['ram_gb'], $_POST['ekran_karti'], $_POST['depolama']);
            $stmt2->execute();
        } 
        elseif ($urun_tipi == "donanim") {
            // Görseldeki DONANIM_PARCALARI sütunları: urun_id, parca_tipi, teknik_detay
            $stmt3 = $conn->prepare("INSERT INTO DONANIM_PARCALARI (urun_id, parca_tipi, teknik_detay) VALUES (?, ?, ?)");
            $stmt3->bind_param("iss", $yeni_id, $_POST['parca_tipi'], $_POST['teknik_detay']);
            $stmt3->execute();
        }

        $conn->commit();
        echo "<p style='color:green;'>Ürün ve detayları başarıyla kaydedildi.</p>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<p style='color:red;'>Hata: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ürün Yönetimi</title>
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

        .form-card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 650px;
        }

        h2 { text-align: center; color: #333; margin-bottom: 30px; font-size: 24px; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px; }

        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; font-size: 14px; }
        
        input[type="text"], 
        input[type="number"], 
        select, 
        textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #eee;
            border-radius: 8px;
            font-size: 15px;
            transition: 0.3s;
            outline: none;
        }

        input:focus, select:focus, textarea:focus { border-color: #764ba2; box-shadow: 0 0 8px rgba(118, 75, 162, 0.2); }

        .sub-form {
            background: #fcfaff;
            border: 1px dashed #764ba2;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            display: none;
        }

        .sub-form h3 { font-size: 16px; color: #764ba2; margin-bottom: 15px; display: flex; align-items: center; gap: 8px; }

        .btn-submit {
            width: 100%;
            padding: 15px;
            background: #d57234;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 20px;
        }
        .btn-submit:hover { background: #bf6128; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(213, 114, 52, 0.3); }

        /* Mesaj Kutuları */
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: 600; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        .row { display: flex; gap: 15px; }
        .row .form-group { flex: 1; }
    </style>
    <script>
        function formGoster(tip) {
            document.getElementById('laptop_form').style.display = (tip == 'laptop') ? 'block' : 'none';
            document.getElementById('donanim_form').style.display = (tip == 'donanim') ? 'block' : 'none';
        }
    </script>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="ust-menu">
    <a href="panel.php" class="back-link">← Menü</a>
<div class="form-card">
    <h2>📦 Yeni Ürün Ekleme Sistemi</h2>
    
    <?= $mesaj ?>

    <form method="post">
        <div class="form-group">
            <label>Ürün Tipi</label>
            <select name="urun_tipi" onchange="formGoster(this.value)" required>
                <option value="">Ürün Tipini Seçiniz...</option>
                <option value="laptop">💻 Laptop</option>
                <option value="donanim">🔧 Donanım Parçası</option>
            </select>
        </div>

        <div class="form-group">
            <label>Ürün Adı</label>
            <input type="text" name="urun_adi" placeholder="Örn: MSI Katana 15" required>
        </div>

        <div class="row">
            <div class="form-group">
                <label>Fiyat (TL)</label>
                <input type="number" step="0.01" name="fiyat" placeholder="0.00" required>
            </div>
            <div class="form-group">
                <label>Marka ID</label>
                <input type="number" name="marka_id" required>
            </div>
            <div class="form-group">
                <label>Kategori ID</label>
                <input type="number" name="kategori_id" required>
            </div>
        </div>

        <div id="laptop_form" class="sub-form">
            <h3>💻 Laptop Detayları</h3>
            <div class="row">
                <div class="form-group">
                    <label>İşlemci</label>
                    <input type="text" name="islemci" placeholder="i7-13700H">
                </div>
                <div class="form-group">
                    <label>RAM (GB)</label>
                    <input type="number" name="ram_gb" placeholder="16">
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <label>Ekran Kartı</label>
                    <input type="text" name="ekran_karti" placeholder="RTX 4060">
                </div>
                <div class="form-group">
                    <label>Depolama (GB)</label>
                    <input type="number" name="depolama" placeholder="512">
                </div>
            </div>
        </div>

        <div id="donanim_form" class="sub-form">
            <h3>🔧 Donanım Parçası Detayları</h3>
            <div class="form-group">
                <label>Parça Tipi</label>
                <input type="text" name="parca_tipi" placeholder="Örn: Ekran Kartı, Anakart, İşlemci">
            </div>
            <div class="form-group">
                <label>Teknik Detay (Açıklama)</label>
                <textarea name="teknik_detay" rows="4" placeholder="Teknik özellikleri buraya giriniz..."></textarea>
            </div>
        </div>

        <button type="submit" class="btn-submit">💾 Ürünü Veritabanına Kaydet</button>
    </form>
</div>

</body>
</html>