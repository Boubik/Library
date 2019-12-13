<!DOCTYPE html>
<html lang="cz">

<head>
    <meta charset="UTF-8">
    <link href="styles/frontend.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="images/logo.ico">
    <link rel="shortcut icon" href="images/fav.png" type="image/x-icon" />
    <script src="js/350205fd30.js"></script>
    <?php
    session_start();
    if (isset($_SESSION["username"])) {
        echo "<title>Učet: " . $_SESSION["username"] . "</title>";
    } else {
        header("Location: index.php");
    }
    ?>
    <!-- <style>
        table,
        th,
        td {
            border: 1px solid black;
            border-collapse: collapse;
        }
    </style>
</head> -->

<body>
    <?php
    include "functions.php";
    ini_set('max_execution_time', 0);
    $configs = include 'config.php';
    date_default_timezone_set('Europe/Prague');
    $conn = connect_to_db($configs["servername"], $configs["dbname"], $configs["username"], $configs["password"]);

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
    if (isset($_GET["q"])) {
        $search = $_GET["q"];
    } else {
        $search = "";
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
    echo '</div>';

    echo '<div id="main">';
    echo '<div id="res">';
    echo "<a href=\"login.php?reset=" . $_SESSION["username"] . "\">Change password</a><br>";
    echo '</div>';
    $new = array();
    $old = array();
    $user_id = get_user_id($conn, $_SESSION["username"]);
    $k = get_table($conn, "reservation");
    /*foreach ($k as $reservation) {
        if ($reservation["user_id"] == $user_id) {
            $book_has_reservation = mn($conn, "book_has_reservation", $reservation["id"], "reservation_id", "book_id");
            if (strtotime($reservation["e-reservation"]) > strtotime('-' . 1 . ' days')) {
                $new[] = "<th>" . get_book($conn, $book_has_reservation[0]) . "</th><th>" . to_cz_date(substr($reservation["s-reservation"], 0, 10)) . "</th><th>" . to_cz_date(substr($reservation["e-reservation"], 0, 10)) . "</th>";
            } else {
                $old[] = "<th>" . get_book($conn, $book_has_reservation[0]) . "</th><th>" . to_cz_date(substr($reservation["s-reservation"], 0, 10)) . "</th><th>" . to_cz_date(substr($reservation["e-reservation"], 0, 10)) . "</th>";
            }
        }
    }*/
    $k = my_reservation($conn, $user_id, $search);
    foreach ($k as $reservation) {
        if (strtotime($reservation["e-reservation"]) > strtotime('-' . 1 . ' days')) {
            $new[] = "<th>" . $reservation["name"] . "</th><th>" . to_cz_date(substr($reservation["s-reservation"], 0, 10)) . "</th><th>" . to_cz_date(substr($reservation["e-reservation"], 0, 10)) . "</th>";
        } else {
            $old[] = "<th>" . $reservation["name"] . "</th><th>" . to_cz_date(substr($reservation["s-reservation"], 0, 10)) . "</th><th>" . to_cz_date(substr($reservation["e-reservation"], 0, 10)) . "</th>";
        }
    }

    if (isset($new[0])) {
        echo "<p>Aktivní rezervace:</p><br>\n";
        echo '<table id="tab">';
        echo "<tr><th>Jménéno knihy</th><th>od kdy</th><th>do kdy</th></tr>";
        foreach ($new as $item) {
            echo "<tr>";
            echo $item;
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Nemáte žádné aktivní rezervace</p><br>\n";
    }

    echo '<hr>';

    if (isset($old[0])) {
        echo "<p>Staré rezervace:</p><br>\n";
        echo '<table id="tab">';
        echo "<tr><th>Jménéno knihy</th><th>od kdy</th><th>do kdy</th></tr>";
        foreach ($old as $item) {
            echo "<tr>";
            echo $item;
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<br><p>Nemáte žádné staré rezervace</p><br>\n";
    }

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


</html>