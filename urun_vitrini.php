<?php 
session_start();
include_once 'baglanti.php';
$conn->set_charset("utf8");

// 1. URL'den kategori ID'sini al (Eğer yoksa 0 ata)
$kategori_filtre = isset($_GET['kategori']) ? intval($_GET['kategori']) : 0;

// 2. SQL Sorgusunu parçalar halinde oluştur (Boşluklara dikkat!)
$sql = "SELECT 
            U.urun_id, U.urun_adi, U.fiyat, U.urun_resim, U.urun_resim_hover,
            L.islemci, L.ram_gb, 
            D.parca_tipi, D.teknik_detay,
            S.miktar
        FROM URUNLER U
        LEFT JOIN LAPTOP L ON U.urun_id = L.urun_id
        LEFT JOIN DONANIM_PARCALARI D ON U.urun_id = D.urun_id
        LEFT JOIN stok S ON U.urun_id = S.urun_id";

// 3. Filtre varsa WHERE ekle (Başına mutlaka bir boşluk bırak " WHERE")
if ($kategori_filtre > 0) {
    $sql .= " WHERE U.kategori_id = $kategori_filtre";
}

// 4. Sıralamayı ekle (Başına mutlaka bir boşluk bırak " ORDER")
$sql .= " ORDER BY U.urun_id DESC";

// --- HATA AYIKLAMA (DEBUG) ---
// Eğer hala çalışmıyorsa aşağıdaki satırın başındaki // işaretini kaldır.
// Ekranda oluşan SQL sorgusunu göreceksin, onu kopyalayıp bana atabilirsin.
 //echo $sql; 
 //-----------------------------

$sonuc = $conn->query($sql);

if (!$sonuc) {
    die("Sorgu Hatası: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ana Sayfa | Bahaddin Gang</title>
    <div>
    <a href="panel.php" class="btn-panel">← Menü</a>
     </div>
    <style>
        /* display: grid sayesinde ürünler oto dizilcek */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background: linear-gradient(135deg, #d57234 0%, #764ba2 100%); padding: 20px; background-attachment: fixed; min-height: 100vh; padding-top: 120px !important;}
        .vitrin-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; max-width: 1200px; margin: auto; } 

        
        /* Ürün Kartı Tasarımı */
        .urun-kart { background: white; border-radius: 10px; padding: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); display: flex; flex-direction: column; transition: transform 0.2s; }
        .urun-kart:hover { transform: translateY(-5px); }
        
        .urun-resim-alanı {  height: 200px; background: white; border-radius: 8px; margin-bottom: 10px; display: flex; align-items: center; justify-content: center; color: white;  overflow: hidden; position: relative;}

        .urun-resim-alanı img {width: 100%; height: 100%; object-fit: cover; /* resmi kutuya sıgdırır */
        position: absolute; /* Resimleri üst üste bindirir */top: 0;left: 0; transition: opacity 0.5s ease; /*geçiş yumuşak olur */}

        .img-hover {opacity: 0; z-index: 2;}  /* 2.resim başta gözükmesin */

        .urun-kart:hover .img-main {opacity: 0;}  /* kartın üzerin gelince 1.yi göster 2.yi gizle */
        .urun-kart:hover .img-hover {opacity: 1;}
        
        .urun-adi { font-size: 18px; font-weight: bold; color: #333; margin: 10px 0; }
        .urun-fiyat { font-size: 20px; color: #1b436a; font-weight: bold; margin-bottom: 10px; }
        
        .teknik-ozellik { font-size: 13px; color: #666; background: #f9f9f9; padding: 8px; border-radius: 5px; margin-bottom: 15px; flex-grow: 1; }
        
        .btn-panel {
    display: inline-block;

    padding: 8px 18px; 
    background-color: #2c3e50; 
    color: #ffffff;
    text-decoration: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    transition: background 0.3s ease, transform 0.2s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    align-items: center;
    gap: 6px;
    /* Eğer bu butonun da her zaman görünmesini istiyorsan position: fixed eklemelisin */
}
.btn-panel:hover {
    background-color: #7aa5cf;
    transform: translateX(-3px); /* Sola kayma efekti */
    color: white;
}
        .btn-sepet { background: #d57234; color: white; border: none; padding: 10px; border-radius: 5px; cursor: pointer; text-decoration: none; text-align: center; font-weight: bold; }
        .btn-sepet:hover { background: #8e4111; }
        
        .badge { display: inline-block; padding: 3px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; }
        .badge-laptop { background: #34346190; color: #ffffff; }
        .badge-donanim { background: #e15f5f; color: #ffffff; }
        /* Sağ Üst Köşedeki Sepet Butonu */
       .sepet-link {
    position: fixed;
    /* Navbar'ın (70px) hemen altına, profil butonundan uzağa alıyoruz */
    top: 90px; 
    right: 30px; 
    /* Boyutları küçültüyoruz */
    padding: 8px 18px; 
    background-color: #2ecc71;
    color: white;
    text-decoration: none;
    border-radius: 8px; /* Daha köşeli ve modern bir görünüm */
    font-weight: 600;
    font-size: 13px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    z-index: 10001; /* Her şeyin üstünde kalmaya devam etsin */
    display: flex;
    align-items: center;
    gap: 6px;
    transition: 0.3s;
}
         .sepet-link:hover {
    background-color: #27ae60;
    transform: scale(1.05);
    color: white;
}

/* Ürün Sayısı Rozeti (Badge) */
          .sepet-adet-rozet {
    background-color: #e74c3c; /* Kırmızı */
    color: white;
    border-radius: 50%;
    padding: 2px 8px;
    font-size: 12px;
    min-width: 12px;
    text-align: center;
}
/* Konteynırın yüksekliğinin bozulmaması için göreceli konumlandırma */
         .donanim-bilgi-wrapper {
    position: relative;
    min-height: 40px; /* İçerik yüksekliğine göre ayarlanabilir */
}

/* Hover'da görünecek detay metni başlangıçta gizli ve üst üste binmiş durumda */
         .hover-detay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease-in-out;
    color: #2c3e50;
    font-style: italic;
}

/* Varsayılan metin */
         .varsayilan-bilgi {
    opacity: 1;
    transition: opacity 0.3s ease-in-out;
}

/* Kart hover olduğunda yapılacak değişim */
         .urun-kart:hover .varsayilan-bilgi {
    opacity: 0;
    visibility: hidden;
}

      .urun-kart:hover .hover-detay {
    opacity: 1;
    visibility: visible;
}

    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
<?php
// Sepetteki toplam ürün adedini hesapla
$toplam_adet = 0;
if (isset($_SESSION['sepet'])) {
    // Array_sum kullanarak tüm adetleri topluyoruz
    $toplam_adet = array_sum($_SESSION['sepet']);
}
?>

<a href="sepet.php" class="sepet-link">
    <span>🛒 Sepetim</span>
    <?php if ($toplam_adet > 0): ?>
        <span class="sepet-adet-rozet"><?= $toplam_adet ?></span>
    <?php endif; ?>
</a>

<h1 style="text-align:center; color: #2c3e50;">Ana Sayfa</h1>

<div class="vitrin-container">
    <?php while($urun = $sonuc->fetch_assoc()): 
        $stok = $urun['miktar'] ?? 0; 
    ?>
        <div class="urun-kart">
            <div class="urun-resim-alanı">
                <?php 
                    $ana_resim = !empty($urun['urun_resim']) ? $urun['urun_resim'] : 'no-image.jpg';
                    $hover_resim = !empty($urun['urun_resim_hover']) ? $urun['urun_resim_hover'] : 'no-image.jpg';
                    $yol = "projeresim/"; 
                ?>
                <img src="<?= $yol . $ana_resim ?>" class="img-main" onerror="this.src='https://via.placeholder.com/300x200?text=Resim+Yok'">
                <img src="<?= $yol . $hover_resim ?>" class="img-hover" onerror="this.src='https://via.placeholder.com/300x200?text=Resim+Yok'">
            </div>

            <?php if (!empty($urun['islemci'])): ?>
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
                    <div class="donanim-bilgi-wrapper">
                        <div class="varsayilan-bilgi">
                            🔧 Parça Tipi: <?= htmlspecialchars($urun['parca_tipi']) ?>
                        </div>
                        <div class="hover-detay">
                            📝 <?= htmlspecialchars($urun['teknik_detay']) ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="stock-info" style="margin-top: 10px; font-weight: bold; font-size: 13px;">
                <?php if($stok > 0): ?>
                    <span style="color: #27ae60;">✅ Stokta: <?= $stok ?> Adet</span>
                <?php else: ?>
                    <span style="color: #e74c3c;">❌ Tükendi</span>
                <?php endif; ?>
            </div>

            <div class="urun-fiyat" style="margin-top: 10px;">
                <?= number_format($urun['fiyat'], 2, ',', '.') ?> TL
            </div>
            
            <?php if($stok > 0): ?>
                <a href="sepet_ekle.php?id=<?= $urun['urun_id'] ?>" class="btn-sepet">Sepete Ekle</a>
            <?php else: ?>
                <button class="btn-sepet" style="background: #ccc; cursor: not-allowed;" disabled>Stokta Yok</button>
            <?php endif; ?>
            
        </div> 
    <?php endwhile; ?> 
    </div>

</body>
</html>  