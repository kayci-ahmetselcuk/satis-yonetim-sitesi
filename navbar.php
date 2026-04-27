<?php

$kategori_sorgu = $conn->query("SELECT * FROM KATEGORI");
?>
<style>
    /* Navbar Genel Stili */
    .main-nav {
        background: #fffffff2;
        backdrop-filter: blur(10px);
        height: 75px;
        width: 100%;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 9999;
        box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        display: block; 
    }
    .nav-container {
        max-width: 1200px; /* İçeriğin yayılacağı maksimum genişlik */
        margin: 0 auto; /* Konteynırı sayfada ortalar */
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between; /* Elemanları bu 1200px içinde dağıtır */
        padding: 0 20px; /* Mobilde kenarlara yapışmaması için */
    }


    .nav-brand {
        font-size: 24px;
        font-weight: 800;
        color: #764ba2;
        text-decoration: none;
        letter-spacing: -1px;
        margin-right: 50px;
    }

    .nav-right {
        margin-left: auto; /* Kendisinden önceki her şeyi sola iter, kendisi sağa yapışır */
    }

    .nav-links {
        display: flex;
        gap: 25px;
        align-items: center;
    }

    .nav-item {
        text-decoration: none;
        color: #555;
        font-weight: 600;
        font-size: 15px;
        transition: 0.3s;
    }

    .nav-item:hover { color: #d57234; }

    /* Kategori Dropdown (Açılır Menü) */
    .dropdown { position: relative; display: inline-block; }
    .dropbtn { background: none; border: none; font-weight: 600; cursor: pointer; color: #555; font-size: 15px; padding: 20px 10px; }
    
.dropdown-content {
    display: none;
    position: absolute;
    background-color: white;
    min-width: 200px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    border-radius: 8px;
    margin-top: 0px;
    z-index: 10000 ; 
    border: 1px solid #eee;
}
.dropdown:hover .dropdown-content {
        display: block;
    }

    .dropdown-content a {
        color: #333;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
        font-size: 14px;
        transition: 0.2s;
    }

    .dropdown-content a:hover { background-color: #f8f9fa; color: #764ba2; }
    .dropdown:hover .dropdown-content { display: block; }

    /* Profil Butonu */
    .nav-profile-btn {
        background: linear-gradient(to right, #764ba2, #667eea);
        color: white;
        padding: 8px 20px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: 0.3s;
    }

    .nav-profile-btn:hover { transform: scale(1.05); box-shadow: 0 4px 10px rgba(118, 75, 162, 0.3); }

    @media (max-width: 768px) {
        .main-nav { padding: 0 20px; }
        .nav-links { gap: 15px; }
    }
    .nav-brand img {
    height: 50px; /* Navbar yüksekliğine (70px) göre ideal boyut */
    width: auto;  /* Oranı korur */
    display: block;
    transition: transform 0.3s ease;
}
.nav-brand:hover img {
    transform: scale(1.05);
}
.nav-brand {
    display: flex;
    align-items: center;
    padding: 0;
    margin-right: 40px;
}
</style>

<nav class="main-nav">
    <div class="nav-container">
        <a href="urun_vitrini.php" class="nav-brand">
        <img src="projeresim/navbar.png" alt="Bahaddin Gang Logo">
        </a>

        <div class="nav-links">
            <div class="dropdown">
                <button class="dropbtn">☰ Kategoriler ▾</button>
                <div class="dropdown-content">
                    <a href="urun_vitrini.php"> <strong>Tüm Ürünler</strong></a>
                    <?php while($kat = $kategori_sorgu->fetch_assoc()): ?>
                        <a href="urun_vitrini.php?kategori=<?= $kat['kategori_id'] ?>">
                            <?= htmlspecialchars($kat['kategori_adi']) ?>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
            <a href="urun_vitrini.php" class="nav-item">Mağaza</a>
            <a href="panel.php" class="nav-item nav-admin-link">Menü</a>
        </div>
        <div class="nav-right">
            <a href="profil.php" class="nav-profile-btn">👤 Profilim</a>
        </div>
    </div>
</nav>

