<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link href="styles/frontend.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="images/logo.ico">
    <link rel="shortcut icon" href="images/fav.png" type="image/x-icon" />
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
    $configs = include 'config.php';
    date_default_timezone_set('Europe/Prague');
    $conn = connect_to_db($configs["servername"], $configs["dbname"], $configs["username"], $configs["password"]);
    session_start();

    $book = get_book_by_id($conn, filter_input(INPUT_GET, "id"));

    echo '<div class="container">';
    echo '<div id="header">';
    echo '<div id="logo"><a href="index.php"><img src="images/skola_logo_color.png" alt="logo"></a></div>';
    echo '<div id="searchfull">';
    echo '<form method="GET" action="">' . "\n";
    echo '<input type="text" onfocusout=" " placeholder="Hledáte něco?" name="q" autocomplete="off" value="';
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
        echo '<input type="submit" name="profile" id="profil" value="Můj profil">' . "\n";
        echo '<input type="submit" name="logout"  value="Odhlásit se">' . "\n";
    } else {
        echo '<div id="fullmenue">';
        echo '<input type="submit" name="login"  value="Přihrásit se"></input>' . "\n";
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
    } else {
        echo '<input type="submit" name="login"  value="Přihrásit se"></input>';
    }
    echo '</form>' . "\n";
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

    if (isset($_POST["edit"])) {
        header("Location: edit_book.php?id=" . $book["id"] . "&name=" . $_GET["name"]);
    }

    if (isset($_POST["delete_reservation"])) {
        delete_reservation($conn, $_POST["reservation_id"]);
    }

    if (isset($_POST["reservations"])) {
        header("Location: reservations.php");
    }

    if (isset($_POST["delete_book"])) {
        hide_book($conn, $book["id"]);
        header("Location: index.php");
    }


    echo '<div id="bookmain">';
    echo "<div id=\"book\">";
    echo "<div class=\"name\">";

    echo $book["name"];
    echo "</div>";
    echo '<div id="image">';
    echo "<img src=\"" . $book["img"] . "\" onError='this.src=\"/images/no_cover.png\"' >";
    echo "</div>";

    echo '<div id="info";>';
    $status = get_status_by_book($conn, $book["id"]);
    if ($status) {
        echo '<div class="status" id="free">';
    } else {
        echo '<div class="status" id="booked">';
    }
    echo "</div>";

    echo "<div class=\"language\">";
    echo "Jazyk: " . $book["language"];
    echo "</div>";

    echo "<div class=\"class\">";
    echo "Místnost: " . $book["room_name"];
    echo "</div>";

    echo "<div class=\"genres\">";
    $k = mn($conn, "book_has_genre", $book["id"], "book_id", "genre_id");
    $genres = null;
    foreach ($k as $id) {
        $genre = get_genre($conn, $id);
        if ($genres != null) {
            $genres = $genre . ", " . $genres;
        } else {
            $genres = $genre;
        }
    }
    echo "Žánr: " . $genres;
    echo "</div>";

    echo "<div class=\"author\">";
    $k = mn($conn, "book_has_author", $book["id"], "book_id", "author_id");
    $authors = null;
    foreach ($k as $id) {
        $author = get_author($conn, $id);
        if ($authors != null) {
            $authors = $author["f_name"] . " " . $author["l_name"] . ", " . $authors;
        } else {
            $authors = $author["f_name"] . " " . $author["l_name"];
        }
    }
    echo "Napsal: " . $authors;
    echo "</div>";
    echo "</div>";

    echo "<div class=\"reservation\">";
    echo '<form method="POST" action="">';
    echo 'Začátek:<input type="date" name="s_date"><br>';
    echo 'Konec:   <input type="date" name="e_date"><br>';

    if (isset($_SESSION["username"]) and isset($_SESSION["password"]) and login($conn, $_SESSION["username"], $_SESSION["password"])) {
        if (login($conn, $_SESSION["username"], $_SESSION["password"], true)) {
            $users = get_users($conn);
            echo '<select name="user" id="sel">' . "\n";
            foreach ($users as $user) {
                if ($user["username"] == $_SESSION["username"]) {
                    echo '<option selected>';
                } else {
                    echo '<option>';
                }
                echo $user["username"] . '</option>' . "\n";
            }
            echo '</select>' . "<br>\n";
            echo '<input type="checkbox" name="taken"> Vyzvednuta<br>' . "\n";
        } else {
            echo '<select class="none" name="user" id="sel">' . "\n";
            echo '<option selected>';
            echo $_SESSION["username"] . '</option>' . "\n";
            echo '</select>' . "<br>\n";
        }
    }

    echo '<input type="submit" name="add_reservation"  value="zarezervovat">' . "\n";
    echo "</form>";
    echo '<br><br>';
    echo "<br>\nnadcházející rezervace:<br>\n";
    echo "</div>";

    if (isset($_SESSION["username"]) and isset($_SESSION["password"]) and login($conn, $_SESSION["username"], $_SESSION["password"])) {
        if (login($conn, $_SESSION["username"], $_SESSION["password"], true)) {
            echo '<div id="administrace">';
            echo 'Administrace';
            echo '<form method="POST" action="">';
            echo '<input type="submit"  name="edit"  value="Upravit knkížku"><br>';
            echo '<input type="submit" id="del" name="delete_book" value="Smazat knkížku"><br>';
            echo '</form>' . "\n";
        }
    }

    echo "</div>";

    $i = 0;
    $reservations = get_reservations($conn, $id);
    foreach ($reservations as $reservation) {
        if ($book["id"] == $reservation["book_id"]) {
            if ($i == 0) {
                echo '<div id="booktab">';
                echo '<div id="tab">';
                echo '<table>';
                echo "<tr>";
                echo "<th>Od kdy</th><th>Do kdy</th>";

                if (isset($_SESSION["username"]) and isset($_SESSION["password"]) and login($conn, $_SESSION["username"], $_SESSION["password"])) {
                    if (login($conn, $_SESSION["username"], $_SESSION["password"], true)) {
                        echo "<th>kdo</th><th>Smazat</th>";
                    }
                }
                echo "</tr>";
            }
            echo "<tr>";
            $start = substr($reservation["s-reservation"], 0, 10);
            $start = explode("-", $start);
            $stop = substr($reservation["e-reservation"], 0, 10);
            $stop = explode("-", $stop);

            echo "<th>" . $start[2] . ". " . $start[1] . ". " . $start[0] . "</th><th>" . $stop[2] . ". " . $stop[1] . ". " . $stop[0] . "</th>";

            if (isset($_SESSION["username"]) and isset($_SESSION["password"]) and login($conn, $_SESSION["username"], $_SESSION["password"])) {
                if (login($conn, $_SESSION["username"], $_SESSION["password"], true)) {
                    $user = get_user_by_reservation_id($conn, $reservation["reservation_id"]);
                    echo "<th>";
                    echo $user["username"];
                    echo "</th>";

                    echo "<th>";
                    echo '<form method="POST" action="">';
                    echo '<input type="text" class="none" name="reservation_id" value="' . $reservation["reservation_id"] . '">';
                    echo '<input type="submit" name="delete_reservation" value="Smazat">';
                    echo '</form>' . "\n";
                }
            }
            echo "</th>";
            echo "</tr>";
            $i++;

            if ($i == 10) {
                echo "</div>";
                break;
            }
        }
    }
    echo '</table>';
    echo "</div>";
    echo "</div>";

    echo '<div id="rez">';
    if (isset($_POST["add_reservation"])) {
        if (isset($_SESSION["username"]) and isset($_SESSION["password"]) and login($conn, $_SESSION["username"], $_SESSION["password"])) {
            if (isset($_POST["taken"])) {
                $taken = 1;
            } else {
                $taken = 0;
            }
            if (strtotime(filter_input(INPUT_POST, "s_date")) >= strtotime(date("Y-m-d")) and isset($_SESSION["username"]) and isset($_SESSION["password"]) and strtotime(filter_input(INPUT_POST, "s_date")) < strtotime(filter_input(INPUT_POST, "e_date"))) {
                if (reservations($conn, filter_input(INPUT_POST, "s_date"), filter_input(INPUT_POST, "e_date"), filter_input(INPUT_GET, "id"), filter_input(INPUT_POST, "user"), $taken)) {
                    $reservation_id = get_reservation_id($conn, filter_input(INPUT_POST, "s_date"), filter_input(INPUT_POST, "e_date"), filter_input(INPUT_GET, "id"));
                    add_book_has_reservation($conn, (int) $_GET["id"], (int) $reservation_id);
                    header("Location: book.php?id=" . filter_input(INPUT_GET, "id") . "&name=" . filter_input(INPUT_GET, "name"));
                } else {
                    echo '<div id="rezz">';
                    echo "Vaše rezervace nenímožná kryje se s jinou";
                    echo "</div>";
                }
            } else {
                echo '<div id="rezz">';
                echo "špatně zadaný datum";
                echo "</div>";
            }
        } else {
            header("Location: login.php");
        }
    }
    echo "</div>";

    echo "</div>";
    echo "</div>";
    echo '</div>';
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