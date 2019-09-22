<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link href="styles/bookedit.css" rel="stylesheet" type="text/css">
    <link href="styles/book.css" rel="stylesheet" type="text/css">
    <link href="styles/header.css" rel="stylesheet" type="text/css">
    <link href="styles/footer.css" rel="stylesheet" type="text/css">
    <link rel="shortcut icon" href="/images/fav.png" type="image/x-icon" />
    <link rel="icon" href="images/logo.ico">
    <script src="js/350205fd30.js"></script>
    <?php
    if (isset($_GET["name"])) {
        echo "<title>Kniha: " . $_GET["name"] . "</title>";
    } else {
        header("Location: /");
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
    $configs = include('config.php');
    date_default_timezone_set('Europe/Prague');
    $conn = connect_to_db($configs["servername"], $configs["dbname"], $configs["username"], $configs["password"]);
    session_start();

    $book = get_book_by_id($conn, $_GET["id"]);

    echo '<div id="header">';
    echo "<a href=\"/\"><image src=\"/images/logo_1.png\" style=\"height: 100px\"></a>";
    echo '<div id="inheader">';
    echo '<div id="monkaS">';
    echo '<form method="POST" action="">' . "\n";
    if (isset($_SESSION["username"]) and isset($_SESSION["password"]) and login($conn, $_SESSION["username"], $_SESSION["password"])) {
        if (login($conn, $_SESSION["username"], $_SESSION["password"], true)) {
            echo '<input id="reservations" type="submit" name="reservations"  value="Rezervace">' . "\n";
            echo '<input id="addbook" type="submit" name="users"  value="Uživatelé">' . "\n";
            echo '<input id="addbook" type="submit" name="add_book"  value="Přidat knížku">' . "\n";
            echo '<input id="addautor" type="submit" name="add_author"  value="Přidat autora">' . "\n";
        }
        echo '<input id="profil" type="submit" name="profile"  value="Můj profil">' . "\n";
        echo '<input id="logout" type="submit" name="logout"  value="Odhlásit se">' . "\n";
    } else {
        echo '<input id="login" type="submit" name="login"  value="Přihrásit se">' . "\n";
    }
    echo '</form>' . "\n";
    echo '</div>';

    echo '</div>';
    echo '</div>';

    if (isset($_POST["logout"])) {
        unset($_SESSION["username"]);
        unset($_SESSION["password"]);
        header("Location: /index.php");
    }

    if (isset($_POST["login"])) {
        header("Location: /login.php");
    }

    if (isset($_POST["add_book"])) {
        header("Location: /add_book.php");
    }

    if (isset($_POST["add_author"])) {
        header("Location: /add_author.php");
    }

    if (isset($_POST["profile"])) {
        header("Location: /profile.php");
    }

    if (isset($_POST["delete_book"])) {
        hide_book($conn, $_GET["id"]);
        header("Location: /index.php");
    }

    if (isset($_POST["users"])) {
        header("Location: /users.php");
    }

    if (isset($_POST["save"])) {
        update_book($conn, $_POST["id"], $_POST["name"], $_POST["relase"], $_POST["language"], $_POST["ISBN"], $_POST["pages"], $_POST["img"], $_POST["room_name"]);
        update_book_has_author($conn, $_POST["id"], $_POST["author"]);
        echo "Uloženo";
    }

    if (isset($_POST["reservations"])) {
        header("Location: /reservations.php");
    }

    echo "<div class=\"book\">";
    echo '<form method="POST" action="">Knížka<br>';
    echo '<input class="none" type="text" name="id" value="' . $book["id"] . '">';
    echo '<input type="text" name="name" value="' . $book["name"] . '"><br>Rok vydání <br>';
    echo '<input type="number" name="relase" value="' . $book["relase"] . '"><br>Jazyk <br>';
    echo '<input type="text" name="language" value="' . $book["language"] . '"><br>ISBN <br>';
    echo '<input type="text" name="ISBN" value="' . $book["ISBN"] . '"><br>Stránek <br>';
    echo '<input type="text" name="pages" value="' . $book["pages"] . '"><br>Obrázek<br> ';
    echo '<input type="text" name="img" value="' . $book["img"] . '"><br>Místnost <br>';
    echo '<input type="text" name="room_name" value="' . $book["room_name"] . '"><br>';

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

    echo '<div id="footer">';
    echo '<div id="footercon">';
    echo '<div id="social">';
    echo '<a href="https://www.facebook.com/skolavdf/?ref=bookmarks" target="_blank" class="fab fa-facebook-f"></a>';
    echo '<a href="https://www.instagram.com/skolavdf/" target="_blank" class="fab fa-instagram"></a>';
    echo "</div>";
    echo '<div id="splitter"></div>';
    echo '<div id="team">';
    echo 'Code: Jan Chlouba <br>';
    echo 'Designe: Ibrahim Daghstani';
    echo "</div>";
    echo "</div>";
    echo "</div>";
    ?>
</body>

</html>