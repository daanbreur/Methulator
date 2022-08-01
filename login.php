<?php
require_once "config.php";

session_start();
if (!isset($_SESSION['user'])) $_SESSION['user'] = new Account();
if ($_SESSION['user']->authenticated) {
        header('Location: home.php');
        exit;
}

if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
    if (!isset($_POST['username']) ) {
        $error = ErrorCodes::LOGIN_USERNAME_NOT_ENTERED;
    } else if (!isset($_POST['password']) ) {
        $error = ErrorCodes::LOGIN_PASSWORD_NOT_ENTERED;
    } else {
        $result = $_SESSION['user']->login($_POST['username'],$_POST['password']);
        if (!$result['success']) {
            $error = $result['message'];
        }
        if ($result['success']) {
            header('Location: home.php');
        }
    }
}


?>

<!DOCTYPE html>
<html>
        <head>
                <meta charset="utf-8">
                <title>Methulator | Login</title>
                <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
                <link href="assets/style.css" rel="stylesheet" type="text/css">
        </head>
        <body>
                <div class="login">
                        <h1>Methulator Login</h1>
                        <div class="error">
                                <p id="error-msg"><?php echo $error ?? ''; ?></p>
                        </div>
                        <form action="login.php" method="post">
                                <label for="username">
                                        <i class="fas fa-user"></i>
                                </label>
                                <input type="text" name="username" placeholder="Username" id="username" required>
                                <label for="password">
                                        <i class="fas fa-lock"></i>
                                </label>
                                <input type="password" name="password" placeholder="Password" id="password" required>
                                <input type="submit" value="Login">
                        </form>
                </div>
        </body>
</html>