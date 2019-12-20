<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <link href="styles/frontend.css" rel="stylesheet">
    <link rel="icon" href="images/logo.ico">
    <script src="js/350205fd30.js"></script>
    <link href="styles/aos.css" rel="stylesheet">
    <link rel="shortcut icon" href="images/skola_logo_mono.png" type="image/x-icon" />
    <script src="js/aos.js"></script>
    <title>Knihovna|VOŠ, SPŠ A SOŠ VDF</title>
    <style>
        .none {
            display: none;
        }
    </style>
</head>

<body>
    <?php
    include "functions.php";
    ini_set('max_execution_time', 0);
    generate_db();
    $configs = include 'config.php';
    date_default_timezone_set('Europe/Prague');
    $conn = connect_to_db($configs["servername"], $configs["dbname"], $configs["username"], $configs["password"]);
    session_start();
    $search = "";
    $page = 1;
    $count_books = count_books($conn);
    if (isset($_SESSION["rows"])) {
        if (isset($_POST["rows"]) and filter_input(INPUT_POST, "rows") != $_SESSION["rows"]) {
            $_SESSION["rows"] = filter_input(INPUT_POST, "rows");
        }
        $per_page = $_SESSION["rows"] * 3;
        $books_rows = $_SESSION["rows"];
    } else {
        if (isset($_POST["rows"])) {
            $_SESSION["rows"] = filter_input(INPUT_POST, "rows");
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
        $search = filter_input(INPUT_GET, "q");
    }
    if (isset($_GET["page"])) {
        $page = filter_input(INPUT_GET, "page");
    }
    if (isset($_GET["reset"]) and $_GET["reset"] == "true") {
        $_SESSION["search_genre"] = "";
        $_SESSION["search_author"] = "";
        $_SESSION["search_language"] = "";
        $_SESSION["search_room"] = "";
        if ($search != "") {
            header("Location: index.php?q=" . $search);
        } else {
            header("Location: index.php");
        }
    }


    if (isset($_GET["genre"])) {
        $search_genre = filter_input(INPUT_GET, "genre");
        $_SESSION["search_genre"] = $search_genre;
    } else {
        if (isset($_SESSION["search_genre"])) {
            $search_genre = $_SESSION["search_genre"];
        } else {
            $search_genre = "";
        }
    }

    if (isset($_GET["author"])) {
        $search_author = filter_input(INPUT_GET, "author");
        $_SESSION["search_author"] = $search_author;
    } else {
        if (isset($_SESSION["search_author"])) {
            $search_author = $_SESSION["search_author"];
        } else {
            $search_author = "";
        }
    }

    if (isset($_GET["language"])) {
        $search_language = filter_input(INPUT_GET, "language");
        $_SESSION["search_language"] = $search_language;
    } else {
        if (isset($_SESSION["search_language"])) {
            $search_language = $_SESSION["search_language"];
        } else {
            $search_language = "";
        }
    }

    if (isset($_GET["room"])) {
        $search_room = filter_input(INPUT_GET, "room");
        $_SESSION["search_room"] = $search_room;
    } else {
        if (isset($_SESSION["search_room"])) {
            $search_room = $_SESSION["search_room"];
        } else {
            $search_room = "";
        }
    }
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
    if (isset($_POST["users"])) {
        header("Location: users.php");
    }
    if (isset($_POST["reservations"])) {
        header("Location: reservations.php");
    }
    $books = book($conn, $search, $search_genre, $search_author, $search_language, $search_room, $count_books, $page, $per_page);
    echo '<div id="container">';
    echo '<div id="header">';
    echo '<button id="showhide" onclick="myFunction()">MENU</button>';
    echo '<div id="logo"><a href="index.php"><img src="images/skola_logo_color.png" alt="logo"></a></div>';
    echo '<div id="searchfull">';
    echo '<form method="GET" action="">' . "\n";
    echo '<input type="text" placeholder="  Hledáte něco?" name="q" autocomplete="off" value="';
    if (isset($_GET["q"])) {
        echo filter_input(INPUT_GET, "q") . '">' . "\n";
    } else {
        echo '">' . "\n";
    }
    echo '</form>' . "\n";
    echo '</div>';


    echo '<div id="fullmenue">';
    echo '<form method="POST" action="">' . "\n";
    if (isset($_SESSION["username"]) and isset($_SESSION["password"]) and login($conn, $_SESSION["username"], $_SESSION["password"])) {
        if (login($conn, $_SESSION["username"], $_SESSION["password"], true)) {
            echo '<input id="reservations" type="submit" name="reservations"  value="Rezervace">';
            echo '<input id="addbook" type="submit" name="users"  value="Uživatelé">';
            echo '<input type="submit" name="add_book"  value="Přidat knížku">';
            echo '<input type="submit" name="add_author"  value="Přidat autora">';
        }
        echo '<input type="submit" name="profile"  value="Můj profil">';
        echo '<input type="submit" name="logout"  value="Odhlásit se">';
    }
    echo '</form>' . "\n";
    echo '</div>';
    echo '<div id="filterbackground">';
    echo '<div id="filter" style="opacity: 0;">';
    if ($search != "") {
        echo '<a href="index.php?q=' . $search . '&reset=true">vymazat filtr</a>';
    } else {
        echo '<a href="index.php?reset=true">vymazat filtr</a>';
    }
    echo '<div id="filtercon">';
    echo '<div class="dropdown">';
    echo '<a id="category" id="zanr">Žánr</a>';
    echo '<div class="dropdown-content" class="zanr">';
    $genres = get_table($conn, "genre");
    foreach ($genres as $item) {
        echo "<a href=\"index.php?genre=" . $item["id"] . "\">" . $item["name"] . "</a><br>\n";
    }
    echo '</div>';
    echo '</div>';
    echo '<div class="dropdown">';
    echo '<br><a id="category" id="autor">Autor</a><br>';
    echo '<div class="dropdown-content" class="autor">';
    $author = get_table($conn, "author");
    foreach ($author as $item) {
        echo "<a href=\"index.php?author=" . $item["id"] . "\">" . $item["f_name"] . " " . $item["l_name"] . "</a><br>\n";
    }
    echo '</div>';
    echo '</div>';
    echo '<div class="dropdown">';
    echo '<a id="category" id="language">Jazyk</a><br>';
    echo '<div class="dropdown-content" class="language"><br>';
    $language = get_table($conn, "book");
    $k = array();
    foreach ($language as $item) {
        if (!in_array($item["language"], $k)) {
            echo "<a href=\"index.php?language=" . $item["language"] . "\">" . $item["language"] . "</a><br>\n";
            $k[] = $item["language"];
        }
    }
    echo '</div>';
    echo '</div>';
    echo '<div class="dropdown">';
    echo '<a id="category" id="room">Mistnost</a><br>';
    echo '<div class="dropdown-content" class="room">';
    $room = get_table($conn, "room");
    foreach ($room as $item) {
        echo "<a href=\"
        index.php?room=" . $item["name"] . "\">" . $item["name"] . "</a><br>\n";
    }
    echo '</div>';
    echo '</div>';
    echo '</div>';


    if ($search_genre != "" or $search_author != "" or $search_language != "" or $search_room != "") {
        echo 'Filtr: <br>';
        require "connector/Select.php";
    }
    echo '<table id="tab">';
    if ($search_genre != "") {
        echo "<tr>";
        echo "<th>";
        echo "žánr: ";
        echo "</th>";

        echo "<th>";
        $s = new Select();
        $s->setselect("name");
        $s->setfrom("genre");
        $s->setwhere("`id` = " . $search_genre);
        $search_genre = exe_sql($conn, $s);
        if (mb_strlen($search_genre[0]["name"]) > 12) {
            echo mb_substr($search_genre[0]["name"], 0, 9) . "...";
        } else {
            echo $search_genre[0]["name"];
        }
        echo "</th>";
        echo "</tr>";
    }
    if ($search_author != "") {
        echo "<tr>";
        echo "<th>";
        echo "autor: ";
        echo "</th>";

        echo "<th>";
        $s = new Select();
        $s->setselect("f_name");
        $s->addselect("l_name");
        $s->setfrom("author");
        $s->setwhere("`id` = " . $search_author);
        $search_author = exe_sql($conn, $s);
        if (mb_strlen($search_author[0]["l_name"]) > 10) {
            $search_author = mb_substr($search_author[0]["f_name"], 0, 1) . ". " . mb_substr($search_author[0]["l_name"], 0, 7) . "...";
        } else {
            $search_author = mb_substr($search_author[0]["f_name"], 0, 1) . ". " . $search_author[0]["l_name"];
        }
        echo $search_author;
        echo "</th>";
        echo "</tr>";
    }
    if ($search_language != "") {
        echo "<tr>";
        echo "<th>";
        echo "jazyk: ";
        echo "</th>";

        echo "<th>";
        echo $search_language;
        echo "</th>";
        echo "</tr>";
    }
    if ($search_room != "") {
        echo "<tr>";
        echo "<th>";
        echo "místnost: ";
        echo "</th>";

        echo "<th>";
        if (mb_strlen($search_room) > 12) {
            echo mb_substr($search_room, 0, 9) . "...";
        } else {
            echo $search_room;
        }
        echo "</th>";
        echo "</tr>";
    }
    echo "</table>";


    echo '</div>';
    echo '</div>';
    echo '<form method="POST" action="">' . "\n";
    if (isset($_SESSION["username"]) and isset($_SESSION["password"]) and login($conn, $_SESSION["username"], $_SESSION["password"])) {
        if (login($conn, $_SESSION["username"], $_SESSION["password"], true)) {
            // echo '<div id="admin">';
            // echo '<a name="admin"  value="Administrace"><i class="fas fa-cog"></i></a>' . "\n";
            // echo '<div id="adminhid">';
            // echo '<input id="reservations" type="submit" name="reservations"  value="Rezervace">' . "\n";
            // echo '<input id="addbook" type="submit" name="users"  value="Uživatelé"><br>';
            // echo '<input type="submit" name="add_book"  value="Přidat knížku"><br>';
            // echo '<input type="submit" name="add_author"  value="Přidat autora"><br>';
            // echo '</div>';
            // echo '</div>';
        }
        echo '<div id="klient">';
        echo '<input type="submit" name="profile" id="profile" value="Můj profil">' . "\n";
        echo '<input type="submit" name="logout"  id="out" value="Odhlásit se">' . "\n";
        echo '</div>';
    } else {
        echo '<div id="login">';
        echo '<input type="submit" name="login"  value="Přihlásit se"></input>' . "\n";
        echo '</div>';
    }
    echo '</div>';
    echo '</form>' . "\n";
    echo '</div>';



    echo '<div id="maincon">';
    if (!isset($books)) {
        echo '<div id="warning>';
        echo "Žádná taková knížka tu není<br>\n";
        echo "</div>";
    } else {
        $k = get_reservation_with_book($conn);
        foreach ($books as $key => $book) {
            echo "<a href=\"book.php?id=" . $key . "&name=" . $book["book_name"] . "\"><div class=\"book\">";
            // echo '<div data-aos="zoom-in" data-aos-once="true" data-aos-easing="linear" data-aos-duration="30">';
            echo "<div class=\"name\">";
            echo $book["book_name"];
            echo "</div>";

            $status = "free";
            foreach ($k as $reservation) {
                if ($reservation["book_id"] == $key) {
                    if (strtotime($reservation["e-reservation"]) > strtotime('-' . 1 . ' days') and strtotime($reservation["s-reservation"]) < strtotime('-' . 0 . ' days') or $reservation["taken"] == 1) {
                        $status = "booked";
                        break;
                    }
                }
            }

            echo '<div id="book">';
            echo "<div class=\"status\" id=\"" . $status . "\"></div>";

            echo '<div id="img">';
            echo "<img src=\"" . $book["img"] . "\" onError='this.src=\"images/no_cover.png\"' >";
            // echo "<img src=\"" . $book["img"] . "\">";
            echo "</div>";

            echo '<div class="info">';
            echo "<div class=\"class\">";
            echo "Místnost: " . $book["room_name"];
            echo "</div>";

            echo "<div class=\"language\">";
            echo "Jazyk: " . $book["language"];
            echo "</div>";

            echo "<div class=\"genres\">";
            $genres = null;
            foreach ($book["genre_name"] as $value) {
                if ($genres == null) {
                    $genres = $value;
                } else {
                    $genres = $genres . ", " . $value;
                }
            }
            echo "Žánr: " . $genres;
            echo "</div>";

            echo "<div class=\"author\">";
            $author = null;
            foreach ($book["author"] as $value) {
                if ($author == null) {
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
    echo "</div>";

    $i = 1;
    if (($i * (3 * $books_rows)) <= $count_books) {
        if ($page > 1) {
            echo "<a href=\"?q=" . $search . "&page=" . ($page - 1) . "\">< </a>";
        }
    }
    while (1) {
        if (($i * (3 * $books_rows)) >= $count_books) {
            break;
        } else {
            if ($i == 1) {
                echo "stránky: ";
                echo "<a href=\"?q=" . $search . "&page=" . $i . "\">" . $i . "</a>";
            }
            echo ", ";
            echo "<a href=\"?q=" . $search . "&page=" . ($i + 1) . "\">" . ($i + 1) . "</a>";
        }
        $i++;
    }
    if ($page < $i) {
        echo "<a href=\"?q=" . $search . "&page=" . ($page + 1) . "\"> ></a>";
    }
    echo "</div>";

    echo '</div>';
    ?>
</body>
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
<script>
    AOS.init();
</script>
<script>
    // function myFunction() {
    //   var x = document.getElementById("filter");
    // //   var y = document.getElementById("logo");
    //   var z = document.getElementById("profile");
    //   var l = document.getElementById("out");
    //   if (x.style.display === "none") {
    //     x.style.display = "block";
    //     x.style.opacity = "100";

    //   } else {
    //     x.style.display = "none";
    //     x.style.opacity = "0";
    //     z.style.marginTop = "";
    //     l.style.marginTop = "";
    //   }
    // }
    function myFunction() {
        var x = document.getElementById("filter");
        var z = document.getElementById("klient");
        var y = document.getElementById("logo");
        var p = document.getElementById("profile");
        if (x.style.opacity === "0") {
            x.style.opacity = "100";
            y.style.opacity = "0";
            z.style.opacity = "0";
            p.style.display = "none";


        } else {
            x.style.opacity = "0";
            y.style.opacity = "100";
            z.style.opacity = "100";
            p.style.display = "block";

        }
    }
</script>


</html>