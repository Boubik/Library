<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <link href="styles/index.css" rel="stylesheet" type="text/css">
    <link href="styles/header.css" rel="stylesheet" type="text/css">
    <link href="styles/footer.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="images/logo.ico">
    <script src="js/350205fd30.js"></script>
    <link href="styles/aos.css" rel="stylesheet">
    <link rel="shortcut icon" href="/images/fav.png" type="image/x-icon" /> 
    <script src="js/aos.js"></script>
    <title>Knihovna</title>
</head>

<body>
    <?php
    include "functions.php";
    ini_set('max_execution_time', 0);
    generate_db();
    $configs = include('config.php');
    date_default_timezone_set('Europe/Prague');
    $conn = connect_to_db($configs["servername"], $configs["dbname"], $configs["username"], $configs["password"]);
    session_start();
    $search = "";
    $page = 1;
    $count_books = count_books($conn);
    if (isset($_SESSION["rows"])) {
        if (isset($_POST["rows"]) and $_POST["rows"] != $_SESSION["rows"]) {
            $_SESSION["rows"] = $_POST["rows"];
        }
        $per_page = $_SESSION["rows"] * 3;
        $books_rows = $_SESSION["rows"];
    } else {
        if (isset($_POST["rows"])) {
            $_SESSION["rows"] = $_POST["rows"];
            $per_page = $_SESSION["rows"] * 3;
            $books_rows = $_SESSION["rows"];
        } else {
            $per_page = 30;
            $books_rows = $per_page / 3;
            $_SESSION["rows"] = $per_page;
        }
    }
    unset($_POST["rows"]);
    unset($_POST["per_page"]);

    if (isset($_GET["q"])) {
        $search = $_GET["q"];
    }
    if (isset($_GET["page"])) {
        $page = $_GET["page"];
    }
    $books = book($conn, $search, $count_books, $page, $per_page);

    echo '<div id="header">';
    echo "<a href=\"/\"><image src=\"/images/logo_1.png\" style=\"height: 100px\"></a>";
    echo '<div id="inheader">';
    echo '<div id="monkaS">';
    echo '<form method="POST" action="">' . "\n";
    if (isset($_SESSION["username"]) and isset($_SESSION["password"]) and login($conn, $_SESSION["username"], $_SESSION["password"])) {
        if (login($conn, $_SESSION["username"], $_SESSION["password"], true)) {
            echo '<input id="addbook" type="submit" name="users"  value="Uživatelé">' . "\n";
            echo '<input type="submit" name="add_book"  value="Přidat knížku">' . "\n";
            echo '<input type="submit" name="add_author"  value="Přidat autora">' . "\n";
        }
        echo '<input type="submit" name="profile"  value="Můj profil">' . "\n";
        echo '<input type="submit" name="logout"  value="Odhlásit se">' . "\n";
    } else {
        echo '<input type="submit" name="login"  value="Přihrásit se">' . "\n";
    }
    echo '</form>' . "\n";
    echo '</div>';

    echo '<div id="serch">';
    echo '<form method="GET" action="">' . "\n";
    echo '<input type="text" onfocusout=" " placeholder="Hledáte něco?" name="q" autocomplete="off" value="';
    if (isset($_GET["q"])) {
        echo $_GET["q"] . '">' . "\n";
    } else {
        echo '">' . "\n";
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

    if (isset($_POST["users"])) {
        header("Location: /users.php");
    }

    if (isset($_POST["search"]) and isset($_POST["q"]) and $_POST["q"] != "") {
        $q = $_POST["q"];
        unset($_POST["search"]);
        header("Location: /?q=" . $q);
    }

    echo "<div class=\"filtr\">";

    echo '<div id="side">';

    echo '<div id="no_clue">';
    echo '<div class="dropdown">';
    echo '<a id="category" id="zanr">Žánr</a>';
    echo '<div class="dropdown-content" class="zanr">';
    $genres = get_table($conn, "genres");
    foreach ($genres as $item) {
        echo "<a href=\"/index.php?q=" . $item["name"] . "\">" . $item["name"] . "</a><br>\n";
    }
    echo "</div>";
    echo "</div>";
    echo "</div>";

    echo '<div id="no_clue">';
    echo '<div class="dropdown">';
    echo '<a id="category" id="autor">Autor</a>';
    echo '<div class="dropdown-content" class="autor">';
    $author = get_table($conn, "author");
    foreach ($author as $item) {
        echo "<a href=\"/index.php?q=" . $item["f_name"] . " " . $item["l_name"] . "\">" . $item["f_name"] . " " . $item["l_name"] . "</a><br>\n";
    }
    echo "</div>";
    echo "</div>";
    echo "</div>";

    echo '<div id="no_clue">';
    echo '<div class="dropdown">';
    echo '<a id="category" id="language">Jazyk</a>';
    echo '<div class="dropdown-content" class="language">';
    $language = get_table($conn, "book");
    $k = array();
    foreach ($language as $item) {
        if (!in_array($item["language"], $k)) {
            echo "<a href=\"/index.php?q=" . $item["language"] . "\">" . $item["language"] . "</a><br>\n";
            $k[] = $item["language"];
        }
    }
    echo "</div>";
    echo "</div>";
    echo "</div>";

    echo '<div id="no_clue">';
    echo '<div class="dropdown">';
    echo '<a id="category" id="room">Mistnost</a>';
    echo '<div class="dropdown-content" class="room">';
    $room = get_table($conn, "room");
    foreach ($room as $item) {
        echo "<a href=\"/index.php?q=" . $item["name"] . "\">" . $item["name"] . "</a><br>\n";
    }
    echo "</div>";
    echo "</div>";

    echo '<div class="sideset">';

    echo 'řádků na strácne:';

    echo '<form method="POST" action="/"><select id="sel" name="rows">' . "\n";
    $i = 1;
    while ($i != 21) {
        if ($i == ($per_page / 3)) {
            echo '<option selected>';
        } else {
            echo '<option>';
        }
        echo $i . '</option>' . "\n";
        $i++;
    }
    echo '</select><br>';
    echo '<input type="submit" name="per_page"  value="nastavit">' . "\n";
    echo '</form>' . "\n";
    echo '</div>';
    echo '</div>';
    echo "</div>";

    echo '<div id="bookcon">';
    // echo '<div data-aos="fade-in" data-aos-duration="2000">';

    if (!isset($books)) {
        echo '<div id="warning>';
        echo "Žádná taková knížka tu není<br>\n";
        echo "</div>";
    } else {
        $k = get_reservation_with_book($conn);
        foreach ($books as $key => $book) {
            echo "<a href=\"/book.php?id=" . $key . "&name=" . $book["book_name"] . "\"><div class=\"book\">";
            echo '<div data-aos="zoom-in" data-aos-once="true" data-aos-easing="linear" data-aos-duration="30">';
            echo "<div class=\"name\">";
            echo $book["book_name"];
            echo "</div>";


            $status = "free";
            foreach ($k as $reservation) {
                if ($reservation["book_id"] == $key) {
                    if (strtotime($reservation["e-reservation"]) > strtotime('-' . 1 . ' days') and strtotime($reservation["s-reservation"]) < strtotime('-' . 0 . ' days')) {
                        $status = "booked";
                        break;
                    }
                }
            }
            // echo '<div class="focus-in-expand ">';
            echo "<div class=\"status\" id=\"" . $status . "\"></div>";

            echo '<div id="img">';
            echo "<img src=\"" . $book["img"] . "\">";
            echo "</div>";

            echo '<div id="info">';
            echo "<div class=\"class\">";
            echo "Místnost: " . $book["room_name"];
            echo "</div>";

            echo "<div class=\"language\">";
            echo "Jazyk: " . $book["language"];
            echo "</div>";

            echo "<div class=\"genres\">";
            $genres = NULL;
            foreach ($book["genres_name"] as $value) {
                if ($genres == NULL) {
                    $genres = $value;
                } else {
                    $genres = $genres . ", " . $value;
                }
            }
            echo "Žánr: " . $genres;
            echo "</div>";

            echo "<div class=\"author\">";
            $author = NULL;
            foreach ($book["author"] as $value) {
                if ($author == NULL) {
                    $author = $value;
                } else {
                    $author = $author . ", " . $value;
                }
            }
            echo "Napsal: " . $author;
            echo "</div>";
            echo "</div>";

            echo "</div></a>";
            echo "</div>";
        }
    }
    echo "</div>";
    echo "</div>";


    echo '<div id="aqua">';
    $i = 1;
    if (($i * (3 * $books_rows)) <= $count_books) {
        if ($page  > 1) {
            echo "<a href=\"/?q=" . $search . "&page=" . ($page - 1) . "\">< </a>";
        }
    }
    while (1) {
        if (($i * (3 * $books_rows)) >= $count_books) {
            break;
        } else {
            if ($i == 1) {
                echo "stránky: ";
                echo "<a href=\"/?q=" . $search . "&page=" . $i . "\">" . $i . "</a>";
            }
            echo ", ";
            echo "<a href=\"/?q=" . $search . "&page=" . ($i + 1) . "\">" . ($i + 1) . "</a>";
        }
        $i++;
    }
    if ($page  < $i) {
        echo "<a href=\"/?q=" . $search . "&page=" . ($page + 1) . "\"> ></a>";
    }
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
<script>
    AOS.init();
</script>

</html>