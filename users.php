<!DOCTYPE html>
<html lang="cz">

<head>
    <meta charset="UTF-8">
    <link href="styles/frontend.scss" rel="stylesheet" type="text/css">
    <link rel="icon" href="images/logo.ico">
    <link rel="shortcut icon" href="/images/fav.png" type="image/x-icon" />
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
    header("Location: /");
}
$per_page = 30;
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
    $search = $_GET["q"];
} else {
    $search = "";
}
if (isset($_GET["page"])) {
    $page = $_GET["page"];
} else {
    $page = 1;
}

echo '<div id="header">';
echo '<div id="logo"><a href="index.php"><img src="/images/skola_logo_mono.png" alt="logo"></a></div>';
echo '<div id="searchsmol">';
echo '<form method="GET" action="">' . "\n";
echo '<input type="text" onfocusout=" " placeholder="Hledáte něco?" name="q" autocomplete="off" value="';
if (isset($_GET["q"])) {
    echo $_GET["q"] . '">' . "\n";
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
    echo '<input type="submit" name="profile"  value="Můj profil">' . "\n";
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

// echo '<div id="serch">';
// echo '<form method="GET" action="">' . "\n";
// echo '<input type="text" onfocusout=" " placeholder="Hledáte něco?" name="q" autocomplete="off" value="';
// if (isset($_GET["q"])) {
//     echo $_GET["q"] . '">' . "\n";
// } else {
//     echo '">' . "\n";
// }
// // echo '<button type="submit" name="search"><i class="fa fa-search"></i></button>' . "\n";
// // echo '<input type="submit" name="search"  value="Hledat">' . "\n";
// echo '</form>' . "\n";
// echo '</div>';
// echo '</div>';
// echo '</div>';

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

if (isset($_GET["set_role"])) {
    if (!($is_admin) and $_GET["role"] == "admin") {
        header("Location: /users.php");
    } else {
        set_role($conn, $_GET["username"], $_GET["role"]);
        header("Location: /users.php");
    }
}

if (isset($_GET["delete"])) {
    if ($is_admin) {
        delete_user($conn, $_GET["username"]);
        header("Location: /users.php");
    } else {
        header("Location: /users.php");
    }
}

if (isset($_POST["reservations"])) {
    header("Location: /reservations.php");
}

$users = users($conn, $search, $page, $per_page);
$count_users = count_users($conn, $search);

echo '<div id="usermain">';
echo '<table id="tab">';
echo "<tr>";
echo '<th>Jméno</th><th>Přezdívka</th><th>Role</th>';
if ($is_admin) {
    echo "<th>Smazat</th>";
}
echo "</tr>";
foreach ($users as $value) {
    echo "<tr>";

    echo "<th> " . $value["f_name"] . " " . $value["l_name"] . "</th><th>" . $value["username"] . "</th>";
    echo "<th>";

    echo '<form method="GET" action="">';
    echo '<input type="text" name="username" value="' . $value["username"] . '" class="none">';
    echo '<select name="role" id="sel">' . "\n";
    foreach ($roles as $item) {
        if ($item == $value["role"]) {
            echo '<option selected>';
        } else {
            echo '<option>';
        }
        echo $item . '</option>' . "\n";
    }
    echo '</select>' . "\n";

    echo '<input type="submit" name="set_role" value="nastavit">';
    echo '</form></th>';

    if ($is_admin) {
        echo "<th>";
        echo '<form method="GET" action="">';
        echo '<input type="text" name="username" value="' . $value["username"] . '" class="none">';
        echo '<input type="submit" name="delete" value="smazat">';
        echo '</form>';
        echo '</th>';
    }

    echo "</tr>";
}
echo "</table>";
echo "</div>";

echo '<div id="aqua">';
$maxpage = (int) ($count_users / $per_page) + 1;
$i = 1;
if ($maxpage > 1) {
    if ($page > 1) {
        echo "<a href=\"/users.php?q=" . $search . "&page=" . ($page - 1) . "\">< </a>";
    }
    while (1) {
        if ($maxpage != 0) {
            do {
                if ($i == 1) {
                    echo "stránky: ";
                    echo "<a href=\"/users.php?q=" . $search . "&page=" . $i . "\">" . $i . "</a>";
                }
                echo ", ";
                echo "<a href=\"/users.php?q=" . $search . "&page=" . ($i + 1) . "\">" . ($i + 1) . "</a>";
                $i++;
            } while ($i > $maxpage);
            break;
        } else {
            break;
        }
    }

    if ($page < $maxpage) {
        echo "<a href=\"/users.php?q=" . $search . "&page=" . ($page + 1) . "\"> ></a>";
    }
}
echo "</div>";

echo '<div id="footer">
<div id="footercon">
<div id="social">
<a href="http://www.skolavdf.cz" target="_blank"><img src="/images/skola_logo_color.png" alt="logo"></a>
<a href="https://www.facebook.com/skolavdf/?ref=bookmarks" target="_blank" class="fab fa-facebook-f"></a>
<a href="https://www.instagram.com/skolavdf/" target="_blank" class="fab fa-instagram"></a>
</div>
<div id="splitter"></div>
    <div id="team">
    <a href="https://github.com/Boubik" target="_blank">Code: Jan Chlouba</a><br>
    <a href="https://github.com/JINXisHERE" target="_blank">Designe: Ibrahim Daghstani</a>

</div>
</div>
</div>';

?>
</body>

</html>