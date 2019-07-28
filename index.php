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
$configs = include('config.php');
$conn = connect_to_db($configs["servername"], $configs["dbname"], $configs["username"], $configs["password"]);
session_start();

echo '<div id="header">';
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


$books = get_table($conn, "book");

echo "<div class=\"products\">";
    echo '<div id="side">';

    echo '</div>';
    echo '<div id="bookcon">';
        foreach($books as $book){
            echo "<div class=\"book\">";

            echo "<div class=\"name\">";
                echo "<a href=\"/book.php?id=". $book["id"] ."&name=". $book["name"] ."\">".$book["name"]."</a>";
            echo "</div>";


            echo '<div id="img">';
                echo "<a href=\"/book.php?id=". $book["id"] ."&name=". $book["name"] ."\"><img src=\"". $book["img"] ."\"></a>";
            echo "</div>";

            echo '<div id="info">';
                echo "<div class=\"language\">";
                echo "Jazyk: ".$book["language"];
                echo "</div>";

                echo "<div class=\"genres\">";
                    $k = mn($conn, "book_has_genres", $book["id"], "book_id", "genres_id");
                    $genres = NULL;
                    foreach($k as $id){
                        $genre = get_genre($conn, $id);
                        if($genres != NULL){
                            $genres = $genre . ", " . $genres;
                        }else{
                            $genres =  $genre;
                        }
                    }
                    echo "Žánr: ".$genres;
                echo "</div>";

                echo "<div class=\"author\">";
                    $k = mn($conn, "book_has_author", $book["id"], "book_id", "author_id");
                    $authors = NULL;
                    foreach($k as $id){
                        $author = get_author($conn, $id);
                        if($authors != NULL){
                            $authors = $author["f_name"] . " " . $author["l_name"] . ", " . $authors;
                        }else{
                            $authors =  $author["f_name"] . " " . $author["l_name"];
                        }
                    }
                    echo "Napsal: ".$authors;
                echo "</div>";
            echo "</div>";

    echo "</div>";

            // echo "</div>";
}
echo "</div>";
    
?>
</body>
</html>