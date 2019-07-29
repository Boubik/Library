<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <link href="styles/index.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="images/logo.ico">
    <title>Knihovna</title>
</head>
<body>
<?php
include "functions.php";
ini_set('max_execution_time', 0);
generate_db();
$configs = include('config.php');
$conn = connect_to_db($configs["servername"], $configs["dbname"], $configs["username"], $configs["password"]);
session_start();
if(isset($_GET["q"])){
    $books = book($conn, $_GET["q"]);
}else{
    $books = book($conn);
}

echo '<div id="header">';
    echo "<a href=\"/\"><image src=\"/images/logo.png\" style=\"height: 100px\"></a>";
    echo '<form method="POST" action="">' . "\n";
    if(isset($_SESSION["username"]) and isset($_SESSION["password"]) and login($conn, $_SESSION["username"], $_SESSION["password"])){
        if(login($conn, $_SESSION["username"], $_SESSION["password"], true)){
            echo '<input type="submit" name="add_book"  value="Přidat knížku">' . "\n";
        }
        echo '<input type="submit" name="profile"  value="Moje rezervace">' . "\n";
        echo '<input type="submit" name="logout"  value="Odhlásit se">' . "\n";
    }else{
        echo '<input type="submit" name="login"  value="Přihrásit se">' . "\n";
    }
    echo '</form>'. "\n";

    echo '<form method="POST" action="">' . "\n";
    echo '<input type="text" name="q" value="';
    if(isset($_GET["q"])){
        echo $_GET["q"].'">' . "\n";
    }else{
        echo '">' . "\n";
    }
    echo '<input type="submit" name="search"  value="Hledat">' . "\n";
    echo '</form>'. "\n";
echo '</div>';

if(isset($_POST["logout"])){
    unset($_SESSION["username"]);
    unset($_SESSION["password"]);
    header("Location: /index.php");
}

if(isset($_POST["login"])){
    header("Location: /login.php");
}

if(isset($_POST["add_book"])){
    header("Location: /add.php");
}

if(isset($_POST["profile"])){
    header("Location: /profile.php");
}

if(isset($_POST["search"]) and isset($_POST["q"]) and $_POST["q"] != ""){
    header("Location: /index.php?q=".$_POST["q"]);
}



echo "<div class=\"products\">";

    echo '<div id="side">';

        echo '<div id="author">';
        $genres = get_table($conn, "genres");
        foreach($genres as $item){
            echo "<a href=\"/index.php?q=". $item["name"] ."\">".$item["name"]."</a><br>\n";
        }
        echo "</div>";
        echo '<div id="author">';
        $author = get_table($conn, "author");
        foreach($author as $item){
            echo "<a href=\"/index.php?q=". $item["f_name"]. " ". $item["l_name"] ."\">".$item["f_name"]. " ". $item["l_name"]."</a><br>\n";
        }
        echo "</div>";
        echo '<div id="language">';
        $language = get_table($conn, "book");
        foreach($language as $item){
            echo "<a href=\"/index.php?q=". $item["language"] ."\">".$item["language"]."</a><br>\n";
        }
        echo "</div>";
        echo '<div id="room">';
        $room = get_table($conn, "room");
        foreach($room as $item){
            echo "<a href=\"/index.php?q=". $item["name"] ."\">".$item["name"]."</a><br>\n";
        }
        echo "</div>";

    echo '</div>';

    echo '<div id="bookcon">';
        
    if(!isset($books[0])){
        echo "Žádná taková knížka tu není<br>\n";
    }
    foreach($books as $book){
        echo "<a href=\"/book.php?id=". $book["book_id"] ."&name=". $book["book_name"] ."\"><div class=\"book\">";

        echo "<div class=\"name\">";
            echo $book["book_name"];
        echo "</div>";


        $status = "volna";
        $k = get_table($conn, "reservation");
        foreach($k as $reservation){
            if($reservation["user_id"] == $book["book_id"]){
                if(strtotime($reservation["e-reservation"]) > strtotime('-' . 1 . ' days') and strtotime($reservation["s-reservation"]) < strtotime('-'. 0 . ' days')){
                    $status = "pucena";
                    break;
                }
            }
        }
        echo "<div class=\"status\" id=\"" . $status . "\"></div>";


        echo '<div id="img">';
            echo "<img src=\"". $book["img"] ."\">";
        echo "</div>";

        echo '<div id="info">';
            echo "<div class=\"class\">";
            echo "Místnost: ".$book["room_name"];
            echo "</div>";

            echo "<div class=\"language\">";
            echo "Jazyk: ".$book["language"];
            echo "</div>";

            echo "<div class=\"genres\">";
            $genres = NULL;
            foreach($book["genres_name"] as $value){
                if($genres == NULL){
                    $genres = $value;
                }else{
                    $genres = $genres. ", ". $value;
                }
            }
                echo "Žánr: ".$genres;
            echo "</div>";

            echo "<div class=\"author\">";
            $author = NULL;
            foreach($book["author"] as $value){
                if($author == NULL){
                    $author = $value;
                }else{
                    $author = $author. ", ". $value;
                }
            }
                echo "Napsal: ".$author;
            echo "</div>";
        echo "</div>";

echo "</div></a>";
}
echo "</div>";
    
?>
</body>
</html>