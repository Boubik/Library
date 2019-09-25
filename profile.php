<!DOCTYPE html>
<html lang="cz">

<head>
    <meta charset="UTF-8">
    <link href="styles/profile.css" rel="stylesheet" type="text/css">
    <link href="styles/header.css" rel="stylesheet" type="text/css">
    <link href="styles/footer.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="images/logo.ico">
    <link rel="shortcut icon" href="/images/fav.png" type="image/x-icon" />
    <script src="js/350205fd30.js"></script>
    <?php
    session_start();
    if (isset($_SESSION["username"])) {
        echo "<title>Učet: " . $_SESSION["username"] . "</title>";
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
    </style>
</head>

<body>
    <?php
    include "functions.php";
    ini_set('max_execution_time', 0);
    $configs = include('config.php');
    date_default_timezone_set('Europe/Prague');
    $conn = connect_to_db($configs["servername"], $configs["dbname"], $configs["username"], $configs["password"]);

    echo '<div id="header">';
    echo "<a href=\"/\"><image src=\"/images/logo_1.png\" style=\"height: 100px\"></a>";
    echo '<div id="inheader">';
    echo '<div id="monkaS">';
    echo '<form method="POST" action="">' . "\n";
    if (isset($_SESSION["username"]) and isset($_SESSION["password"]) and login($conn, $_SESSION["username"], $_SESSION["password"])) {
        if (login($conn, $_SESSION["username"], $_SESSION["password"], true)) {
            echo '<input id="reservations" type="submit" name="reservations"  value="Rezervace">' . "\n";
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
    echo '<input type="text" onfocusout=" " placeholder="Hledate neco?" name="q" autocomplete="off" value="';
    if (isset($_GET["q"])) {
        echo $_GET["q"] . '">' . "\n";
    } else {
        echo '">' . "\n";
    }
    // echo '<button type="submit" name="search"><i class="fa fa-search"></i></button>' . "\n";
    // echo '<input type="submit" name="search"  value="Hledat">' . "\n";
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
        header("Location: /index.php?q=" . $_POST["q"]);
    }

    if (isset($_POST["reservations"])) {
        header("Location: /reservations.php");
    }


    echo '<div id="main">';
    echo '<div id="res">';
    echo "<a href=\"/login.php?reset=" . $_SESSION["username"] . "\">Change password</a><br>";
    echo '</div>';
    $new = array();
    $old = array();
    $user_id = get_user_id($conn, $_SESSION["username"]);
    $k = get_table($conn, "reservation");
    foreach ($k as $reservation) {
        if ($reservation["user_id"] == $user_id) {
            $book_has_reservation = mn($conn, "book_has_reservation", $reservation["id"], "reservation_id", "book_id");
            if (strtotime($reservation["e-reservation"]) > strtotime('-' . 1 . ' days')) {
                $new[] = "<th>" . get_book($conn, $book_has_reservation[0]) . "</th><th>" . to_cz_date(substr($reservation["s-reservation"], 0, 10)) . "</th><th>" . to_cz_date(substr($reservation["e-reservation"], 0, 10)) . "</th>";
            } else {
                $old[] = "<th>" . get_book($conn, $book_has_reservation[0]) . "</th><th>" . to_cz_date(substr($reservation["s-reservation"], 0, 10)) . "</th><th>" . to_cz_date(substr($reservation["e-reservation"], 0, 10)) . "</th>";
            }
        }
    }

    if (isset($new[0])) {
        echo "Aktivní rezervace:<br>\n";
        echo '<table id="tab">';
        echo "<tr><th>Jménéno knihy</th><th>od kdy</th><th>do kdy</th></tr>";
        foreach ($new as $item) {
            echo "<tr>";
            echo $item;
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Nemáte žádné aktivní rezervace<br>\n";
    }

    echo '<hr>';

    if (isset($old[0])) {
        echo "Staré rezervace:<br>\n";
        echo '<table id="tab">';
        echo "<tr><th>Jménéno knihy</th><th>od kdy</th><th>do kdy</th></tr>";
        foreach ($old as $item) {
            echo "<tr>";
            echo $item;
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Nemáte žádné staré rezervace<br>\n";
    }


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