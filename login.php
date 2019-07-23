<!DOCTYPE html>
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prihlášení</title>
    <link rel="stylesheet" type="text/css" href="styles/login.css">
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

    echo '<div id="Log">';
        echo '<div id="img">';
            echo '<img src="images/img1.png" alt="Login images">'; 
            echo '</div>';     
                echo '<div id="Log_inner">';
                    echo "login";
                    echo '<form method="POST" placeholder="" action="">';
                    echo '<input type="text" name="login_username" placeholder="Přezdívka"><br>';
                    echo '<input type="password" name="login_password" placeholder="Heslo"><br>' . "\n";
                    echo "<br>";
                    echo '<input type="submit" name="login"  value="Přihlásit se">' . "\n";
                    echo '</form>'. "\n";
                    echo '</div>';
       
    echo '</div>';

    echo "<br><br><br>";

    // echo '<div id="Reg">';
    //     echo "Registrace";
    //     echo '<form method="POST" action="">' . "\nJméno";
    //     echo '<input type="text" maxlength="45" name="f_name"><br>' . "\nPřímení";
    //     echo '<input type="text" maxlength="45" name="l_name"><br>' . "\nPřezdívka";
    //     echo '<input type="text" name="username"><br>' . "\nHeslo";
    //     echo '<input type="password" name="password"><br>' . "\n";
    //     echo "<br>";
    //     echo '<input type="submit" name="register"  value="Registrovat">' . "\n";
    //     echo '</form>'. "\n";
    // echo '</div>';
}

if(isset($_POST["register"])){
    if(!(username_exist($conn, $_POST["username"]))){
        add_user($conn, $_POST["f_name"], $_POST["l_name"], $_POST["username"], $_POST["password"]);
        echo "jsi přihlášený";
        $_SESSION["username"] = $_POST["login_username"];
        $_SESSION["password"] = $_POST["login_password"];
        header("Location: /");
    }else{
        echo "Přezdívka už existuje";
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