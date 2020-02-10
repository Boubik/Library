<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link href="styles/frontend.css" rel="stylesheet" type="text/css">
    <link rel="shortcut icon" href="images/fav.png" type="image/x-icon" />
    <link rel="icon" href="images/logo.ico">
    <?php
    if (isset($_GET["name"])) {
        echo "<title>Kniha: " . filter_input(INPUT_GET, "name") . "</title>";
    } else {
        header("Location: index.php");
    }
    ?>
    <style>
        table,
        th,
        td {
            border: 1px solid black;
            border-collapse: collapse;
        }

        .none {
            display: none;
        }
    </style>
</head>

<body>
    <?php
    include "functions.php";
    ini_set('max_execution_time', 0);
    $configs = include 'config.php';
    date_default_timezone_set('Europe/Prague');
    $conn = connect_to_db($configs["servername"], $configs["dbname"], $configs["username"], $configs["password"]);
    session_start();

    $book = get_book_by_id($conn, filter_input(INPUT_GET, "id"));
    echo '<div class="container">';

    if (isset($_POST["logout"])) {
        unset($_SESSION["username"]);
        unset($_SESSION["password"]);
        header("Location: index.php");
    }

    if (isset($_POST["login"])) {
        header("Location: login.php");
    }

    if (isset($_POST["add_book"])) {
        header("Location: add_book.php");
    }

    if (isset($_POST["add_author"])) {
        header("Location: add_author.php");
    }

    if (isset($_POST["profile"])) {
        header("Location: profile.php");
    }

    if (isset($_POST["delete_book"])) {
        hide_book($conn, filter_input(INPUT_GET, "id"));
        header("Location: index.php");
    }

    if (isset($_POST["users"])) {
        header("Location: users.php");
    }

    if (isset($_GET["ulozeno"]) and $_GET["ulozeno"] == "true") {
        echo "<div class\"warning\">Uloženo</div>";
    }

    if (isset($_POST["save"])) {
        update_book($conn, filter_input(INPUT_POST, "id"), filter_input(INPUT_POST, "name"), filter_input(INPUT_POST, "relase"), filter_input(INPUT_POST, "language"), filter_input(INPUT_POST, "ISBN"), filter_input(INPUT_POST, "pages"), $_POST["img"], filter_input(INPUT_POST, "room_name"));
        update_book_has_author($conn, filter_input(INPUT_POST, "id"), filter_input(INPUT_POST, "author"));
        header("Location: edit_book.php?id=" . $_GET["id"] . "&ulozeno=true&name=$" . $_GET["name"]);
    }

    if (isset($_POST["reservations"])) {
        header("Location: reservations.php");
    }

    echo "<div id='logincon'>";
    echo '<form method="POST" action="">';
    echo '<input class="none" type="text" name="id" value="' . $book["id"] . '">';
    echo '<input type="text" name="name" placeholder="Nazev" value="' . $book["name"] . '">';
    echo '<input type="number" name="relase" placeholder="Rok vydání" value="' . $book["relase"] . '">';
    echo '<input type="text" name="language" placeholder="Jazyk " value="' . $book["language"] . '">';
    echo '<input type="text" name="ISBN" placeholder="ISBN" value="' . $book["ISBN"] . '">';
    echo '<input type="text" name="pages" placeholder="Pocet Stránek" value="' . $book["pages"] . '">';
    echo '<input type="text" name="img"  placeholder="Obrázek" value="' . $book["img"] . '">';
    echo '<input type="text" name="room_name"  placeholder="Místnost " value="' . $book["room_name"] . '">';

    $book_has_author = get_table($conn, "book_has_author");
    $authors = get_table($conn, "author");
    echo '<select name="author" id="sel">' . "\n";
    foreach ($authors as $author) {
        foreach ($book_has_author as $key => $item) {
            if ($item["author_id"] == $author["id"]) {
                break;
            } else {
                $key = null;
            }
        }
        if ($book["id"] == $book_has_author[$key]["book_id"]) {
            echo '<option selected>';
        } else {
            echo '<option>';
        }
        echo $author["f_name"] . " " . $author["l_name"] . '</option>' . "\n";
    }
    echo '</select>' . "<br>\n";
    
    echo '<input type="submit" name="save"  value="Uložit">';
    echo "</form>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    ?>
    <footer>
        <div id="footer" style="margin-top:150px !important;">
            <div id="footercon">
                <div id="social">
                    <a href="http://www.skolavdf.cz" target="_blank"><img src="images/skola_logo_color.png" alt="logo"></a>
                    <a href="https://www.facebook.com/skolavdf/?ref=bookmarks"><img src="images/facebook.png" alt="logo"></a>
                    <a href="https://www.instagram.com/skolavdf/" target="_blank"><img src="images/instagram.png" alt="logo"></a>
                </div>
                <div id="splitter"></div>+
                <div id="kontakt">
                    kontakty:<br><br>
                    <a href="mailto:kristina.petrackova@skolavdf.cz">Kristina Petráčková</a>: 412 315 049<br>
                    <a href="mailto:andrea.skodova@skolavdf.cz">Andrea Škodová</a>: 412 315 049<br>
                </div>
                <div id="team">
                    <a href="https://github.com/Boubik" target="_blank">Coder: Jan Chlouba</a><br>
                    <a href="https://github.com/JINXisHERE" target="_blank">Designer: Ibrahim Daghstani</a>
                </div>
            </div>
        </div>
    </footer>
</body>


</html>