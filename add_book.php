<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <title>Přidání knížky</title>
    <link rel="stylesheet" type="text/css" href="styles/frontend.scss">
    <link rel="shortcut icon" href="/images/skola_logo_mono.png" type="image/x-icon" />
    <script src="js/350205fd30.js"></script>
</head>

<body>
    <?php
include "functions.php";
ini_set('max_execution_time', 0);
$configs = include 'config.php';
date_default_timezone_set('Europe/Prague');
$conn = connect_to_db($configs["servername"], $configs["dbname"], $configs["username"], $configs["password"]);
session_start();

if (isset($_SESSION["username"]) and isset($_SESSION["password"]) and login($conn, $_SESSION["username"], $_SESSION["password"], true)) {} else {
    header("Location: /login.php");
}
echo '<div id="logincon">';
echo '<a href="index.php"><img src="/images/skola_logo_mono.png" alt="logo"></a>';
echo '<form method="POST" action="">';
echo '<input type="text" maxlength="45" name="name" placeholder="Název knihy"><br>';
echo '<input type="number"  name="relase" placeholder="Rok vydán"><br>';
echo '<input type="text" maxlength="2" name="language" placeholder="Jazyk"><br>';
echo '<input type="text" name="ISBN" placeholder="ISBN"><br>';

// echo '<select name="room" id="sel">' . "\n";
// echo '<option>Místnost</option>' . "\n";
// foreach (get_table($conn, "room") as $row) {
//     echo '<option>' . $row["name"] . '</option>';
// }
// echo '</select> nebo
echo '<input type="text" name="room_name" placeholder="Místnost"><br>';

// echo '<input type="text" name="room_name" placeholder="Místnost"><br>';

echo '<input type="number" name="pages" placeholder="Počet stran"><br>';
echo '<input type="text" maxlength="45" name="genres" placeholder="Žánry"><br><p>    (mezera rozděluje žánry)</p><br>';
echo '<input type="text" maxlength="200" name="img" placeholder="url obrázku"><br>' . "\n";
echo '<input type="submit" name="submit" value="Přidat">' . "\n";
echo '</form>' . "\n";
echo '</div>';

if (isset($_POST["submit"])) {
    $genres = explode(" ", $_POST["genres"]);
    add_genres($conn, $genres);
    $gendrs_get = null;
    foreach ($genres as $item) {
        $item = get_genres_id($conn, $item);
        if ($gendrs_get != null) {
            $gendrs_get = $gendrs_get . "," . $item;
        } else {
            $gendrs_get = $item;
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

echo '<div id="footer">
    <div id="footercon">
    <div id="social">
    <a href="http://www.skolavdf.cz" target="_blank"><img src="/images/skola_logo_color.png" alt="logo"></a>
    <a href="https://www.facebook.com/skolavdf/?ref=bookmarks"><img src="/images/facebook.png" alt="logo"></a>
    <a href="https://www.instagram.com/skolavdf/" target="_blank"><img src="/images/instagram.png" alt="logo"></a>
    </div>
    <div id="splitter"></div>
        <div id="team">
        <a href="https://github.com/Boubik" target="_blank">Coder: Jan Chlouba</a><br>
        <a href="https://github.com/JINXisHERE" target="_blank">Designer: Ibrahim Daghstani</a>

    </div>
    </div>
    </div>';

?>
</body>

</html>