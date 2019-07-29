<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>Přidání autora</title>
    <link rel="stylesheet" type="text/css" href="styles/book_to_author.css">
</head>
<body>
<?php
include "functions.php";
ini_set('max_execution_time', 0);
$configs = include('config.php');
$conn = connect_to_db($configs["servername"], $configs["dbname"], $configs["username"], $configs["password"]);
session_start();

echo '<div id="header">';
    echo "<a href=\"/\"><image src=\"/images/logo.png\" style=\"height: 100px\"></a>";
echo "</div>";

if(isset($_SESSION["username"]) and isset($_SESSION["password"]) and login($conn, $_SESSION["username"], $_SESSION["password"], true)){
}else{
    header("Location: /login.php");
}
echo '<div id="main">';
    echo '<div id="inner">';
        echo "Přiřadte autora k \"" . $_GET["name"] . "\"<br>\n";

        echo '<form method="POST" action="">';
        echo '<input type="text" maxlength="45" name="f_name" placeholder="Jméno"><br>';
        echo '<input type="text" maxlength="45" name="l_name" placeholder="Přímen"><br>';
        echo '<input type="number" name="bday" placeholder="Rok narozen"><br>' . "\n";
        echo '<input type="text" maxlength="2" name="country" placeholder="Země narození"><br>';
        echo '<input type="submit" name="submit1"  value="Spojit">';
        echo '</form>'. "\n";

        echo '<br><br><form method="POST" action="">' . "\n";
        echo '<select id="sel" name="authors">' . "\n";
        foreach(get_table($conn, "author") as $author){
            $bday = explode("-", $author["bday"]);
            $bday = $bday[0];
            echo '<option>'. $author["f_name"] . ", " . $author["l_name"] . ", " . $bday . ", " . $author["country"] .'</option>' . "\n";
        }
        echo '</select><br>'. "\n";
        echo '<input type="submit" name="submit2"  value="Spojit">' . "\n";
        echo '</form>'. "\n";
    echo '<div>';
echo '<div>';

if(isset($_POST['submit1'])){
    add_author($conn, $_POST["f_name"], $_POST["l_name"], $_POST["bday"], $_POST["country"]);
    foreach(get_table($conn, "author") as $items){
        $bday = explode("-", $items["bday"]);
        $bday = $bday[0];
        if($_POST["f_name"] == $items["f_name"] and $_POST["l_name"] == $items["l_name"] and $_POST["bday"] == $bday and $_POST["country"] == $items["country"]){
            echo $items["id"];
            $id_author = $items["id"];
            break;
        }
    }
}

if(isset($_POST['submit2'])){
    $author = explode(", ", $_POST['authors']);

    echo "<br><br><br>";
    foreach(get_table($conn, "author") as $items){
        $bday = explode("-", $items["bday"]);
        $bday = $bday[0];
        if($author[0] == $items["f_name"] and $author[1] == $items["l_name"] and $author[2] == $bday and $author[3] == $items["country"]){
            $id_author = $items["id"];
            break;
        }
    }
}

if(isset($_POST['submit1']) or isset($_POST['submit2'])){
    echo "<br><br><br>";
    foreach(get_table($conn, "book") as $items){
        if($_GET["name"] == $items["name"] and $_GET["relase"] == $items["relase"] and $_GET["language"] == $items["language"] and $_GET["ISBN"] == $items["ISBN"] and $_GET["room_name"] == $items["room_name"] and $_GET["pages"] == $items["pages"]){
            $id_book = $items["id"];
            break;
        }
    }

    $genres = explode(",", $_GET["genres"]);
    foreach($genres as $id_genres){
        add_book_has_genres($conn, (int)$id_book, (int)$id_genres);
    }
    add_book_has_author($conn, (int)$id_book, (int)$id_author);
    header("Location: /add.php");
}

/*add_book($conn, "Stopařův průvodce po Galaxii", 1991, "CZ", "80-207-0229-6", "14", 304);
add_author($conn, "Jan", "Chlouba", 2001, "CZ");*/



?>
</body>
</html>