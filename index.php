<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>Knihovna</title>
</head>
<body>
<?php
include "functions.php";
ini_set('max_execution_time', 0);
$configs = include('config.php');
$conn = connect_to_db($configs["servername"], $configs["dbname"], $configs["username"], $configs["password"]);
session_start();

echo "<br><br><br>";
echo '<form method="POST" action="">' . "\n";
if(isset($_SESSION["username"]) and isset($_SESSION["password"]) and login($conn, $_SESSION["username"], $_SESSION["password"])){
    echo '<input type="submit" name="logout"  value="Odhlásit se">' . "\n";
}else{
    echo '<input type="submit" name="login"  value="Přihrásit se">' . "\n";
}
echo '</form>'. "\n";

if(isset($_POST["logout"])){
    unset($_SESSION["username"]);
    unset($_SESSION["password"]);
    header("Location: /index.php");
}

if(isset($_POST["login"])){
    header("Location: /login.php");
}

    
?>
</body>
</html>