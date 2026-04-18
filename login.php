<?php                     // hash lenen sifreyi okuyabilmek icin tekrar hash kullanmamız gerek
session_start();
include 'baglanti.php';

if($_SERVER["REQUEST_METHOD"]=="POST") {
    $kullanici=trim($_POST['kullanici_adi']);
    $sifre_ham=$_POST['sifre'];

    $sifre_hash= hash('sha256',$sifre_ham);

    $query= "SELECT kullanici_id, yetki FROM KULLANICILAR WHERE kullanici_adi=? AND sifre=?";
    $stmt=$conn->prepare($query);
    $stmt->bind_param("ss",$kullanici,$sifre_hash);
    $stmt->execute();
    $result=$stmt->get_result();

    if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    $_SESSION['kullanici_id'] = $user['kullanici_id'];
    $_SESSION['kullanici_adi'] = $user['kullanici_adi'];
    $_SESSION['yetki'] = $user['yetki']; // Veritabanından gelen yetki (0 veya 1)

    header("Location: panel.php");
    exit();
}

    if($result->num_rows==1){
        $user= $result->fetch_assoc();
        $_SESSION['kullanici_id']= $user['kullanici_id'];
        $_SESSION['kullanici_adi']=$kullanici;

        echo "<p style='color:green;'>Giris Basarili! Yönlendiriliyorsunuz...</p>";
        header("Refresh:2; url=panel.php");
    } else{
        echo "<p style='color:red;'>Giris Basarisiz! Hatali kullanıcı adı veya şifre girdiniz! </p>";
    }
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="tr">
    <head>
        <meta charset="UTF-8">
        <title> Sisteme Giris</title>
    </head>

    <body>
        <h2>Kullanici Girisi</h2>
        <form method="post" action="login.php">
            Kullanıcı Adı: <input type="text" name="kullanici_adi" required> <br><br>
            Şifre: <input type="password" name="sifre" required> <br><br>
            <button type="submit">Giriş Yap </button>
        </form>
    </body>

    </html>