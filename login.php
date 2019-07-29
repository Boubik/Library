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

    echo '<div id="main">';
        echo '<div id="img">';
            echo '<img src="images/img1.png" alt="Login images">'; 
            echo '</div>';   echo   
                '<div id="Log">';
                    echo "login";
                    echo '<form method="POST" placeholder="" action="">';
                    echo '<input type="text" name="login_username" placeholder="Přezdívka"><br>';
                    echo '<input type="password" name="login_password" placeholder="Heslo"><br>' . "\n";
                    echo "<br>";
                    echo '<input type="submit" name="login"  value="Přihlásit se">' . "\n";
                    echo '</form>'. "\n";
                    echo '</div>';

                    echo '<div id="swap">';
                        echo'mate učet?<button onclick="myFunction()">Zaregistruj se!</button>';
                    echo '</div>'; 
                        
    
                    echo '<div id="2" style="display:none;">
                    My Dynamic Content
                    </div>';
 

    echo '<div id="Reg" style="display:none;" >';
        echo "Registrace";
        echo '<form method="POST" action="">';
        echo '<input type="text" maxlength="45" name="f_name" placeholder="Jméno"><br>';
        echo '<input type="text" maxlength="45" name="l_name" placeholder="Přímení"><br>';
        echo '<input type="text" name="username" placeholder="Přezdívka"><br>';
        echo '<input type="password" name="password" placeholder="Heslo"><br>';
        echo "<br>";
        echo '<input type="submit" name="register"  value="Registrovat">' . "\n";
        echo '</form>'. "\n";
    echo '</div>';
    echo '</div>';   


}

if(isset($_POST["register"])){
    if(!(username_exist($conn, $_POST["username"]))){
        add_user($conn, $_POST["f_name"], $_POST["l_name"], $_POST["username"], $_POST["password"]);
        echo "jsi přihlášený";
        $_SESSION["username"] = $_POST["username"];
        $_SESSION["password"] = $_POST["password"];
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
<script>
function myFunction() {
  var x = document.getElementById("Log");
  var y = document.getElementById("Reg");
  if (x.style.display === "none") {
    x.style.display = "block";
    y.style.display = "none";
  } else {
    x.style.display = "none";
    y.style.display = "block";
  }
}
</script>
</html>
