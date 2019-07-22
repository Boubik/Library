<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>Prihlášení</title>
</head>
<body>
<?php
include "functions.php";
ini_set('max_execution_time', 0);
$configs = include('config.php');
$conn = connect_to_db($configs["servername"], $configs["dbname"], $configs["username"], $configs["password"]);
session_start();

if(isset($_SESSION["username"]) and isset($_SESSION["password"]) and login($conn, $_SESSION["username"], $_SESSION["password"])){
    header("Location: /");
}else{
    echo "Registrace";
    echo '<form method="POST" action="">' . "\nJméno";
    echo '<input type="text" maxlength="45" name="f_name"><br>' . "\nPřímení";
    echo '<input type="text" maxlength="45" name="l_name"><br>' . "\nPřezdka";
    echo '<input type="text" name="username"><br>' . "\nHeslo";
    echo '<input type="password" name="password"><br>' . "\n";
    echo '<input type="submit" name="register"  value="Registrovat">' . "\n";
    echo '</form>'. "\n";

    echo "<br><br><br>";
    echo "login";
    echo '<form method="POST" action="">' . "\nPřezdka";
    echo '<input type="text" name="login_username"><br>' . "\nHeslo";
    echo '<input type="password" name="login_password"><br>' . "\n";
    echo '<input type="submit" name="login"  value="Přihlásit se">' . "\n";
    echo '</form>'. "\n";
}

if(isset($_POST["register"])){
    if(!(username_exist($conn, $_POST["username"]))){
        add_user($conn, $_POST["f_name"], $_POST["l_name"], $_POST["username"], $_POST["password"]);
        echo "jsi přihlášený";
        $_SESSION["username"] = $_POST["login_username"];
        $_SESSION["password"] = $_POST["login_password"];
        header("Location: /");
    }else{
        echo "Přezdka už existuje";
    }
}

if(isset($_POST["login"])){
    if(login($conn, $_POST["login_username"], $_POST["login_password"])){
        echo "jsi přihlášený";
        $_SESSION["username"] = $_POST["login_username"];
        $_SESSION["password"] = $_POST["login_password"];
        header("Location: /");
    }else{
        echo "Špatné přihlašovací údaje";
        unset($_SESSION["username"]);
        unset($_SESSION["password"]);
    }
}
?>
</body>
</html>