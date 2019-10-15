<!DOCTYPE html>
<html lang="cz">

<head>
    <meta charset="UTF-8">
    <link href="styles/frontend.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="images/logo.ico">
    <link rel="shortcut icon" href="images/skola_logo_mono.png" type="image/x-icon" />
    <script src="js/350205fd30.js"></script>
    <title>Uživatelé</title>
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

        .taken {
            color: red;
        }
    </style>
</head>

<body>
    <?php
    include "functions.php";
    ini_set('max_execution_time', 0);
    $configs = include 'config.php';
    date_default_timezone_set('Europe/Prague');
    session_start();
    $conn = connect_to_db($configs["servername"], $configs["dbname"], $configs["username"], $configs["password"]);
    if (!(isset($_SESSION["username"]) and isset($_SESSION["password"]) and login($conn, $_SESSION["username"], $_SESSION["password"]) and login($conn, $_SESSION["username"], $_SESSION["password"], true))) {
        header("Location: index.php");
    }
    $roles = array();
    $roles[] = "user";
    $roles[] = "mod";
    if (is_admin($conn, $_SESSION["username"], $_SESSION["password"])) {
        $is_admin = true;
        $roles[] = "admin";
    } else {
        $is_admin = false;
    }
    if (isset($_GET["q"])) {
        $search = filter_input(INPUT_GET, 'q');
    } else {
        $search = "";
    }

    echo '<div class="container">';
    echo '<div id="header">';
    echo '<div id="logo"><a href="index.php"><img src="images/skola_logo_color.png" alt="logo"></a></div>';
    echo '<div id="searchfull">';
    echo '<form method="GET" action="">' . "\n";
    echo '<input type="text" onfocusout=" " placeholder="Hledáte něco?" name="q" autocomplete="off" value="';
    if (isset($_GET["q"])) {
        echo filter_input(INPUT_GET, 'q') . '">' . "\n";
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

    if (isset($_POST["return"])) {
        change_reservation_status($conn, filter_input(INPUT_POST, "id"), filter_input(INPUT_POST, "taken"));
    }

    if (isset($_POST["take"])) {
        change_reservation_status($conn, filter_input(INPUT_POST, "id"), filter_input(INPUT_POST, "taken"));
    }

    if (isset($_GET["set_role"])) {
        if (!($is_admin) and filter_input(INPUT_GET, 'role') == "admin") {
            header("Location: users.php");
        } else {
            set_role($conn, filter_input(INPUT_GET, 'username'), filter_input(INPUT_GET, 'role'));
            header("Location: users.php");
        }
    }

    if (isset($_POST["reservations"])) {
        header("Location: reservations.php");
    }

    if (isset($_POST["delete"])) {
        delete_reservation($conn, filter_input(INPUT_POST, 'id'));
    }

    if (isset($_POST["delete"])) {
        delete_reservation($conn, filter_input(INPUT_POST, "id"));
    }

    $actual_reservations = get_actual_reservations($conn, $search);

    echo '<div id="main" style="margin-top:100px;margin-bottom:100px;">';
    if (isset($actual_reservations[0])) {
        echo '<table id="tab">';
        echo "<tr>";
        echo '<th>Knížka</th><th>Jméno</th><th>od</th><th>do</th><th>status</th><th>Smazat</th>';
        echo "</tr>";
        foreach ($actual_reservations as $value) {
            echo "<tr>";

            if ($value["taken"]) {
                echo "<th>" . $value["book_name"] . "</th><th> " . $value["f_name"] . " " . $value["l_name"] . "</th><th>" . to_cz_date(substr($value["s-reservation"], 0, 10)) . "</th>";
                if (strtotime(date("Y-m-d")) >= strtotime(substr($value["e-reservation"], 0, 10))) {
                    echo "<th class='taken'>";
                } else {
                    echo "<th>";
                }
                echo to_cz_date(substr($value["e-reservation"], 0, 10)) . "</th>";

                echo "<th>";
                echo "Je zapůjčenat";
                echo '<form method="POST" action="">';
                echo '<input class="none" type="text" name="id" value="' . $value["reservation_id"] . '">';
                echo '<input class="none" type="number" name="taken" value="0">';
                echo '<input type="submit" name="return" value="Vrátil">';
                echo '</form>';
                echo "</th>";

                echo "<th>";
                echo '<form method="POST" action="">';
                echo '<input class="none" type="text" name="id" value="' . $value["reservation_id"] . '">';
                echo '<input type="submit" id="del" name="delete" value="smazat">';
                echo '</form>';
                echo "</th>";
            } else {
                echo "<th>" . $value["book_name"] . "</th><th> " . $value["f_name"] . " " . $value["l_name"] . "</th><th>" . to_cz_date(substr($value["s-reservation"], 0, 10)) . "</th><th>" . to_cz_date(substr($value["e-reservation"], 0, 10)) . "</th>";

                echo "<th>";
                echo "Je v místnosti: " . $value["room_name"];
                echo '<form method="POST" action="">';
                echo '<input class="none" type="text" name="id" value="' . $value["reservation_id"] . '">';
                echo '<input class="none" type="number" name="taken" value="1">';
                echo '<input type="submit" name="take" value="Vyzvednul si">';
                echo '</form>';
                echo "</th>";

                echo "<th>";
                echo '<form method="POST" action="">';
                echo '<input class="none" type="text" name="id" value="' . $value["reservation_id"] . '">';
                echo '<input type="submit" id="del" name="delete" value="smazat">';
                echo '</form>';
                echo "</th>";
                echo "</tr>";
            }
        }
        echo "</table>";
    } else {
        echo "Žádné rezervace";
    }
    echo "</div>";

    echo '<div id="footer" style="margin-top:100px;">
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
</div>
</div>';
    echo '</div>';
    ?>

</body>

</html>