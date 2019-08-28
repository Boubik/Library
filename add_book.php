<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <title>Přidání knížky</title>
    <link rel="stylesheet" type="text/css" href="styles/add.css">
    <link href="styles/header.css" rel="stylesheet" type="text/css">
    <link href="styles/footer.css" rel="stylesheet" type="text/css">
    <script src="js/350205fd30.js"></script>
</head>

<body>
    <?php
    include "functions.php";
    ini_set('max_execution_time', 0);
    $configs = include('config.php');
    date_default_timezone_set('Europe/Prague');
    $conn = connect_to_db($configs["servername"], $configs["dbname"], $configs["username"], $configs["password"]);
    session_start();

    echo '<div id="header">';
    echo "<a href=\"/\"><image src=\"/images/logo_1.png\" style=\"height: 100px\"></a>";
    echo "</div>";

    if (isset($_SESSION["username"]) and isset($_SESSION["password"]) and login($conn, $_SESSION["username"], $_SESSION["password"], true)) { } else {
        header("Location: /login.php");
    }
    echo '<div id="main">';
    echo '<div id="inner">';
    echo '<form method="POST" action="">';
    echo '<input type="text" maxlength="45" name="name" placeholder="Název knihy"><br>';
    echo '<input type="number"  name="relase" placeholder="Rok vydán"><br>';
    echo '<input type="text" maxlength="2" name="language" placeholder="Jazyk"><br>';
    echo '<input type="text" name="ISBN" placeholder="ISBN"><br>';

    echo '<select name="room" id="sel">' . "\n";
    echo '<option>Místnost</option>' . "\n";
    foreach (get_table($conn, "room") as $row) {
        echo '<option>' . $row["name"] . '</option>' . "\n";
    }
    echo '</select>' . "<br> nebo <br>";

    echo '<input type="text" name="room_name" placeholder="Místnost"><br>';

    echo '<input type="number" name="pages" placeholder="Počet stran"><br>';
    echo '<input type="text" maxlength="45" name="genres" placeholder="Žánry"><br>(mezera rozděluje žánry)<br>';
    echo '<input type="text" maxlength="200" name="img" placeholder="url obrázku"><br>' . "\n";
    echo '<input type="submit" name="submit" value="Přidat">'  . "\n";
    echo '</form>' . "\n";
    echo '</div>';
    echo '</div>';

    if (isset($_POST["submit"])) {
        $genres = explode(" ", $_POST["genres"]);
        add_genres($conn, $genres);
        $gendrs_get = NULL;
        foreach ($genres as $item) {
            $item = get_genres_id($conn, $item);
            if ($gendrs_get != NULL) {
                $gendrs_get = $gendrs_get . "," . $item;
            } else {
                $gendrs_get =  $item;
            }
        }
        if ($_POST["room_name"] != "") {
            $room = $_POST["room_name"];
        } else {
            $room = $_POST["room"];
        }
        add_book($conn, $_POST["name"], $_POST["relase"], $_POST["language"], $_POST["ISBN"], $room, $_POST["pages"], $_POST["img"]);
        header("Location: /add_author.php?name=" . $_POST["name"] . "&relase=" . $_POST["relase"] . "&language=" . $_POST["language"] . "&ISBN=" . $_POST["ISBN"] . "&room_name=" . $room . "&pages=" . $_POST["pages"] . "&genres=" . $gendrs_get . "&img=" . $_POST["img"]);
    }

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