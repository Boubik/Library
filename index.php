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
$books = book($conn, $search, $count_books, $page, $per_page);
echo '<div id="container">';
echo '<div id="header">';
echo '<button id="showhide" onclick="myFunction()">MENU</button>';
echo '<div id="logo"><a href="index.php"><img src="images/skola_logo_color.png" alt="logo"></a></div>';
echo '<div id="searchfull">';
echo '<form method="GET" action="">' . "\n";
echo '<input type="text" placeholder="  Hledáte něco?" name="q" autocomplete="off"';
if (isset($_GET["q"])) {
    echo filter_input(INPUT_GET, "q") . '">' . "\n";
} else {
    echo '">' . "\n";
}
echo '</form>' . "\n";
echo '</div>';
echo '<form method="POST" action="">' . "\n";
if (isset($_SESSION["username"]) and isset($_SESSION["password"]) and login($conn, $_SESSION["username"], $_SESSION["password"])) {
    if (login($conn, $_SESSION["username"], $_SESSION["password"], true)) {
        echo '<div id="admin">';
        echo '<a name="admin"  value="Administrace"><i class="fas fa-cog"></i></a>' . "\n";
        echo '<div id="adminhid">';
        echo '<input id="reservations" type="submit" name="reservations"  value="Rezervace">' . "\n";
        echo '<input id="addbook" type="submit" name="users"  value="Uživatelé"><br>';
        echo '<input type="submit" name="add_book"  value="Přidat knížku"><br>';
        echo '<input type="submit" name="add_author"  value="Přidat autora"><br>';
        echo '</div>';
        echo '</div>';
    }
    echo '<div id="klient">';
    echo '<input type="submit" name="profile" id="profile" value="Můj profil">' . "\n";
    echo '<input type="submit" name="logout"  id="out" value="Odhlásit se">' . "\n";
} 
else {
    echo '<div id="fullmenue">';
    echo '<input type="submit" name="login"  value="Přihlásit se"></input>' . "\n";
    echo '</div>';
}
echo '</div>';
echo '</form>' . "\n";
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
echo '<div id="filter" style="display: none;">';
echo '<div id="filtercon">';
echo '<div class="dropdown">';
echo '<a id="category" id="zanr">Žánr</a>';
echo '<div class="dropdown-content" class="zanr">';
$genres = get_table($conn, "genre");
foreach ($genres as $item) {
    echo "<a href=\"index.php?q=" . $item["name"] . "\">" . $item["name"] . "</a><br>\n";
}
echo '</div>';
echo '</div>';
echo '<div class="dropdown">';
echo '<br><a id="category" id="autor">Autor</a><br>';
echo '<div class="dropdown-content" class="autor">';
$author = get_table($conn, "author");
foreach ($author as $item) {
    echo "<a href=\"index.php?q=" . $item["f_name"] . " " . $item["l_name"] . "\">" . $item["f_name"] . " " . $item["l_name"] . "</a><br>\n";
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
        echo "<a href=\"index.php?q=" . $item["language"] . "\">" . $item["language"] . "</a><br>\n";
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
        index.php?q=" . $item["name"] . "\">" . $item["name"] . "</a><br>\n";
}
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';
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
        echo "<img src=\"" . $book["img"] . "\">";
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
            <div id="splitter"></div>
            <div id="team">
                <a href="https://github.com/Boubik" target="_blank">Coder: Jan Chlouba</a><br>
                <a href="https://github.com/JINXisHERE" target="_blank">Designer: Ibrahim Daghstani</a>
            </div>
            <div id="kontakt">
            kontakty:<br><br>
            <a href="mailto:kristina.petrackova@skolavdf.cz">Kristina Petráčková</a>: 412 315 049<br>
            <a href="mailto:andrea.skodova@skolavdf.cz">Andrea Škodová</a>: 412 315 049<br>
            </div>
        </div>
    </div>
</footer>
<script>
    AOS.init();
</script>
<script>
function myFunction() {
  var x = document.getElementById("filter");
//   var y = document.getElementById("logo");
  var z = document.getElementById("profile");
  var l = document.getElementById("out");
  if (x.style.display === "none") {
    x.style.display = "block";
    // y.style.display = "none";
    // z.style.marginTop = "25px";
    // l.style.marginTop = "25px";

  } else {
    x.style.display = "none";
    z.style.marginTop = "";
    l.style.marginTop = "";
  }
}
</script>


</html>