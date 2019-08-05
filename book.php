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
echo "</div>";

echo "<div class=\"book\">";

echo "<img src=\"" . $book["img"] . "\">";

    echo '<div id="info";>';
        echo "<div class=\"name\">";
        echo $book["name"];
        echo "</div>";

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
            if(reservations($conn, $_POST["s_date"], $_POST["e_date"])){
                $reservation_id = get_reservation_id($conn, $_POST["s_date"], $_POST["e_date"]);
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