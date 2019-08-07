<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link href="styles/book.css" rel="stylesheet" type="text/css">
    <link href="styles/header.css" rel="stylesheet" type="text/css">
    <link href="styles/footer.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="images/logo.ico">
    <script src="https://kit.fontawesome.com/350205fd30.js"></script>
    <?php
    if (isset($_GET["name"])) {
        echo "<title>Kniha: " . $_GET["name"] . "</title>";
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
session_start();

$book = get_book_by_id($conn, $_GET["id"]);

echo '<div id="header">';
    echo "<a href=\"/\"><image src=\"/images/logo_1.png\" style=\"height: 100px\"></a>";
        echo '<div id="inheader">';
        echo '<div id="monkaS">';
                echo '<form method="POST" action="">' . "\n";
                if(isset($_SESSION["username"]) and isset($_SESSION["password"]) and login($conn, $_SESSION["username"], $_SESSION["password"])){
                    if(login($conn, $_SESSION["username"], $_SESSION["password"], true)){
                        echo '<input type="submit" name="add_book"  value="Přidat knížku">' . "\n";
                        echo '<input type="submit" name="add_author"  value="Přidat autora">' . "\n";
                    }
                    echo '<input type="submit" name="profile"  value="Můj profil">' . "\n";
                    echo '<input type="submit" name="logout"  value="Odhlásit se">' . "\n";
                }else{
                    echo '<input type="submit" name="login"  value="Přihrásit se">' . "\n";
                }
                echo '</form>'. "\n";
            echo '</div>';

            echo '<div id="serch">';
                echo '<form method="GET" action="">' . "\n";
                echo '<input type="text" onfocusout=" " placeholder="Hledate neco?" name="q" autocomplete="off" value="';
                if(isset($_GET["q"])){
                    echo $_GET["q"].'">' . "\n";
                }else{
                    echo '">' . "\n";
                }
                // echo '<button type="submit" name="search"><i class="fa fa-search"></i></button>' . "\n";
                // echo '<input type="submit" name="search"  value="Hledat">' . "\n";
                echo '</form>'. "\n";
            echo '</div>';
        echo '</div>';
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
    header("Location: /add_book.php");
}

if(isset($_POST["add_author"])){
    header("Location: /add_author.php");
}

if(isset($_POST["profile"])){
    header("Location: /profile.php");
}

if(isset($_POST["search"]) and isset($_POST["q"]) and $_POST["q"] != ""){
    header("Location: /index.php?q=".$_POST["q"]);
}

echo "<div class=\"book\">";
    echo "<div class=\"name\">";
    echo $book["name"];
    echo "</div>";
echo "<img src=\"" . $book["img"] . "\">";

    echo '<div id="info";>';

        echo "<div class=\"language\">";
        echo "Jazyk: " . $book["language"];
        echo "</div>";

        echo "<div class=\"class\">";
        echo "Místnost: ".$book["room_name"];
        echo "</div>";

        echo "<div class=\"genres\">";
        $k = mn($conn, "book_has_genres", $book["id"], "book_id", "genres_id");
        $genres = NULL;
        foreach ($k as $id) {
            $genre = get_genre($conn, $id);
            if ($genres != NULL) {
                $genres = $genre . ", " . $genres;
            } else {
                $genres =  $genre;
            }
        }
        echo "Žánr: " . $genres;
        echo "</div>";

        echo "<div class=\"author\">";
        $k = mn($conn, "book_has_author", $book["id"], "book_id", "author_id");
        $authors = NULL;
        foreach ($k as $id) {
            $author = get_author($conn, $id);
            if ($authors != NULL) {
                $authors = $author["f_name"] . " " . $author["l_name"] . ", " . $authors;
            } else {
                $authors =  $author["f_name"] . " " . $author["l_name"];
            }
        }
        echo "Napsal: " . $authors;
        echo "</div>";
    echo "</div>";

    echo "<div class=\"reservation\">";
    echo '<form method="POST" action="">' . "\nZačátek";
    echo '<input type="date" name="s_date"><br>' . "\nKonec   ";
    echo '<input type="date" name="e_date"><br>' . "\n";
    echo '<input type="submit" name="reservation"  value="zarezervovat">' . "\n";
    echo "</form>";
    echo "<br>\nnadcházející rezervace:<br>\n";
    echo "</div>";

    $k = mn($conn, "book_has_reservation", $book["id"], "book_id", "reservation_id");
    $res = NULL;
    foreach($k as $id){
        $reservations = get_reservations($conn, $id);
        foreach ($reservations as $reservation) {
            echo "od: " . substr($reservation["s-reservation"], 0, 10) . " do " . substr($reservation["e-reservation"], 0, 10) . "<br>\n";
        }
    }

    echo "</div>";


if (isset($_POST["reservation"])) {
    if(isset($_SESSION["username"]) and isset($_SESSION["password"]) and login($conn, $_SESSION["username"], $_SESSION["password"])){
        $date = $_POST["s_date"];
        if (strtotime($date) > strtotime('-' . 1 . ' days') and isset($_SESSION["username"]) and isset($_SESSION["password"]) and strtotime($_POST["s_date"]) < strtotime($_POST["e_date"])) {
            if(reservations($conn, $_POST["s_date"], $_POST["e_date"], $_GET["id"])){
                $reservation_id = get_reservation_id($conn, $_POST["s_date"], $_POST["e_date"], $_GET["id"]);
                add_book_has_reservation($conn, (int)$_GET["id"], (int)$reservation_id);
                header("Location: /book.php?id=". $_GET["id"] ."&name=". $_GET["name"]);
            }else{
                echo "Vaše rezervace nenímožná kryje se s jinou";
            }
        } else {
            echo "špatně zadaný datum";
        }
    }else{
        header("Location: /login.php");
    }
}

echo '<div id="footer">';
    echo '<div id="footercon">';
        echo '<div id="social">';
            echo '<a href="https://www.facebook.com/skolavdf/?ref=bookmarks" target="_blank" class="fab fa-facebook-f"></a>';
            echo '<a href="https://www.instagram.com/skolavdf/" target="_blank" class="fab fa-instagram"></a>';
        echo "</div>";
        echo '<div id="splitter"></div>';
        echo '<div id="team">';
        echo'Code: Jan Chlouba <br>';
        echo'Designe: Ibrahim Daghstani';
        echo "</div>";
    echo "</div>";
echo "</div>";
?>
</body>

</html>