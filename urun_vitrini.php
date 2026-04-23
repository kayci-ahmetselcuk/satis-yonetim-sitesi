<?php
session_start();
include 'baglanti.php';
$conn->set_charset("utf8");

// SQL: Tüm ürünleri alt detaylarıyla birlikte çekiyoruz
$sql = "SELECT 
            U.urun_id, U.urun_adi, U.fiyat, U.urun_resim, U.urun_resim_hover,
            L.islemci, L.ram_gb, 
            D.parca_tipi
        FROM URUNLER U
        LEFT JOIN LAPTOP L ON U.urun_id = L.urun_id
        LEFT JOIN DONANIM_PARCALARI D ON U.urun_id = D.urun_id
        ORDER BY U.urun_id DESC";

$sonuc = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ürün Vitrini | Teknoloji Mağazası</title>
    <style>
        /* display: grid sayesinde ürünler oto dizilcek */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .vitrin-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; max-width: 1200px; margin: auto; } 
        
        /* Ürün Kartı Tasarımı */
        .urun-kart { background: white; border-radius: 10px; padding: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); display: flex; flex-direction: column; transition: transform 0.2s; }
        .urun-kart:hover { transform: translateY(-5px); }
        
        .urun-resim-alanı { height: 200px; background: white; border-radius: 8px; margin-bottom: 10px; display: flex; align-items: center; justify-content: center; color: white;  overflow: hidden; position: relative;}

        .urun-resim-alanı img {width: 100%; height: 100%; object-fit: cover; /* resmi kutuya sıgdırır */
        position: absolute; /* Resimleri üst üste bindirir */top: 0;left: 0; transition: opacity 0.5s ease; /*geçiş yumuşak olur */}

        .img-hover {opacity: 0; z-index: 2;}  /* 2.resim başta gözükmesin */

        .urun-kart:hover .img-main {opacity: 0;}  /* kartın üzerin gelince 1.yi göster 2.yi gizle */
        .urun-kart:hover .img-hover {opacity: 1;}
        
        .urun-adi { font-size: 18px; font-weight: bold; color: #333; margin: 10px 0; }
        .urun-fiyat { font-size: 20px; color: #2ecc71; font-weight: bold; margin-bottom: 10px; }
        
        .teknik-ozellik { font-size: 13px; color: #666; background: #f9f9f9; padding: 8px; border-radius: 5px; margin-bottom: 15px; flex-grow: 1; }
        
        .btn-sepet { background: #3498db; color: white; border: none; padding: 10px; border-radius: 5px; cursor: pointer; text-decoration: none; text-align: center; font-weight: bold; }
        .btn-sepet:hover { background: #2980b9; }
        
        .badge { display: inline-block; padding: 3px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; }
        .badge-laptop { background: #e3f2fd; color: #1976d2; }
        .badge-donanim { background: #f1f8e9; color: #388e3c; }
    </style>
</head>
<body>

<h1 style="text-align:center; color: #2c3e50;">Teknoloji Vitrini</h1>
<p style="text-align:center;"><a href="panel.php">← Panele Dön</a></p>

<div class="vitrin-container">
    <?php while($urun = $sonuc->fetch_assoc()): ?>
        <div class="urun-kart">
            <div class="urun-resim-alanı">
    <?php 
        // 1. Veritabanı verisini kontrol et
        // Eğer sütun boşsa 'no-image.jpg' adında genel bir dosya kullan
        $ana_resim = !empty($urun['urun_resim']) ? $urun['urun_resim'] : 'no-image.jpg';
        $hover_resim = !empty($urun['urun_resim_hover']) ? $urun['urun_resim_hover'] : 'no-image.jpg';
        
        // 2. Yol ayarı (Mükerrer php/ klasörünü engellemek için)
        $yol = "projeresim/"; 
    ?>
    
    <img src="<?= $yol . $ana_resim ?>" 
         class="img-main" 
         onerror="this.src='https://via.placeholder.com/300x200?text=Resim+Yok'">
    
    <img src="<?= $yol . $hover_resim ?>" 
         class="img-hover" 
         onerror="this.src='https://via.placeholder.com/300x200?text=Resim+Yok'">
</div> <?php if (!empty($urun['islemci'])): ?>
                <span class="badge badge-laptop">Laptop</span>
            <?php else: ?>
                <span class="badge badge-donanim">Donanım</span>
            <?php endif; ?>

            <div class="urun-adi"><?= htmlspecialchars($urun['urun_adi']) ?></div>
            
            <div class="teknik-ozellik">
                <?php if (!empty($urun['islemci'])): ?>
                    💻 <?= $urun['islemci'] ?> İşlemci <br>
                    🚀 <?= $urun['ram_gb'] ?> GB RAM
                <?php else: ?>
                    🔧 Parça Tipi: <?= $urun['parca_tipi'] ?>
                <?php endif; ?>
            </div>

            <div class="urun-fiyat"><?= number_format($urun['fiyat'], 2, ',', '.') ?> TL</div>
            
            <a href="sepet_ekle.php?id=<?= $urun['urun_id'] ?>" class="btn-sepet">Sepete Ekle</a>
            
        </div> <?php endwhile; ?>
</div>

</body>
</html>