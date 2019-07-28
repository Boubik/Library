<!DOCTYPE html>
<html lang="cz">

<head>
    <meta charset="UTF-8">
    <link href="styles/book.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="images/logo.ico">
    <?php
    session_start();
    if (isset($_SESSION["username"])) {
        echo "<title>Učat: " . $_SESSION["username"] . "</title>";
    } else {
        header("Location: /");
    }
    ?>
</head>

<body>
<?php
include "functions.php";
ini_set('max_execution_time', 0);
$configs = include('config.php');
$conn = connect_to_db($configs["servername"], $configs["dbname"], $configs["username"], $configs["password"]);

echo "Aktivní rezervace:<br>\n";

$old = array();
$user_id = get_user_id($conn, $_SESSION["username"]);
$k = get_table($conn, "reservation");
foreach($k as $reservation){
    if($reservation["user_id"] == $user_id){
        if(strtotime($reservation["e-reservation"]) > strtotime('-' . 1 . ' days')){
            echo "od: " . substr($reservation["s-reservation"], 0, 10) . " do " . substr($reservation["e-reservation"], 0, 10) . "<br>\n";
        }else{
            $old[] = "od: " . substr($reservation["s-reservation"], 0, 10) . " do " . substr($reservation["e-reservation"], 0, 10) . "<br>\n";
        }
    }
}
echo "Staré rezervace:<br>\n";
foreach($old as $item){
    echo $item;
}

?>
</body>
</html>