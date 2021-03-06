<!DOCTYPE html <head>
<meta charset="UTF-8">
<title>Přidání autora</title>
<link rel="stylesheet" type="text/css" href="styles/frontend.css">
<link rel="shortcut icon" href="images/skola_logo_mono.png" type="image/x-icon" />
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

    if (isset($_SESSION["username"]) and isset($_SESSION["password"]) and login($conn, $_SESSION["username"], $_SESSION["password"], true)) { } else {
        header("Location: login.php");
    }
    echo '<div class="container">';
    echo '<div id="logincon">';
    echo '<a href="index.php"><img src="images/skola_logo_mono.png" alt="logo"></a>';
    if (isset($_GET["name"]) and isset($_GET["relase"]) and isset($_GET["language"]) and isset($_GET["ISBN"]) and isset($_GET["room_name"]) and isset($_GET["pages"]) and isset($_GET["genres"]) and isset($_GET["img"])) {

        echo "<p>Přiřadte autora k \"" . filter_input(INPUT_GET, "name") . "\"</p><br>\n";
        echo '<form method="POST" action="">';
        echo '<input type="text" maxlength="45" name="f_name" placeholder="Jméno"><br>';
        echo '<input type="text" maxlength="45" name="l_name" placeholder="Přímen"><br>';
        echo '<input type="number" name="bday" placeholder="Rok narozen"><br>' . "\n";
        echo '<input type="text" maxlength="2" name="country" placeholder="Země narození"><br>';
        echo '<input type="submit" name="submit1"  value="Spojit">';
        echo '</form>' . "\n";

        echo '<br><br><form method="POST" action="">' . "\n";
        echo '<select id="sel" name="authors">' . "\n";
        foreach (get_table($conn, "author") as $author) {
            $bday = explode("-", $author["bday"]);
            $bday = $bday[0];
            echo '<option>' . $author["f_name"] . ", " . $author["l_name"] . ", " . $bday . ", " . $author["country"] . '</option>' . "\n";
        }
        echo '</select><br>' . "\n";
        echo '<input type="submit" name="submit2"  value="Spojit">' . "\n";
        echo '</form>' . "\n";
        echo '<div>';

        if (isset($_POST['submit1'])) {
            add_author($conn, $_POST["f_name"], $_POST["l_name"], $_POST["bday"], $_POST["country"]);
            foreach (get_table($conn, "author") as $items) {
                $bday = explode("-", $items["bday"]);
                $bday = $bday[0];
                if (filter_input(INPUT_POST, "f_name") == $items["f_name"] and filter_input(INPUT_POST, "l_name") == $items["l_name"] and filter_input(INPUT_POST, "bday") == $bday and filter_input(INPUT_POST, "country") == $items["country"]) {
                    $id_author = $items["id"];
                    break;
                }
            }
        }

        if (isset($_POST['submit2'])) {
            $author = explode(", ", filter_input(INPUT_POST, 'authors'));

            echo "<br><br><br>";
            foreach (get_table($conn, "author") as $items) {
                $bday = explode("-", $items["bday"]);
                $bday = $bday[0];
                if ($author[0] == $items["f_name"] and $author[1] == $items["l_name"] and $author[2] == $bday and $author[3] == $items["country"]) {
                    $id_author = $items["id"];
                    break;
                }
            }
        }

        if (isset($_POST['submit1']) or isset($_POST['submit2'])) {
            echo "<br><br><br>";
            foreach (get_table($conn, "book") as $items) {
                if (filter_input(INPUT_GET, "name") == $items["name"] and filter_input(INPUT_GET, "language") == $items["language"] and filter_input(INPUT_GET, "ISBN") == $items["ISBN"] and filter_input(INPUT_GET, "room_name") == $items["room_name"] and filter_input(INPUT_GET, "pages") == $items["pages"]) {
                    $id_book = $items["id"];
                    break;
                }
            }

            $genres = explode(",", filter_input(INPUT_GET, "genres"));
            foreach ($genres as $id_genres) {
                add_book_has_genre($conn, (int) $id_book, (int) $id_genres);
            }
            add_book_has_author($conn, (int) $id_book, (int) $id_author);
            header("Location: index.php");
        }
    } else {
        echo "<p>Přiřadte autora</p> <br>\n";
        echo '<form method="POST" action="">';
        echo '<input type="text" maxlength="45" name="f_name" placeholder="Jméno"><br>';
        echo '<input type="text" maxlength="45" name="l_name" placeholder="Přímen"><br>';
        echo '<input type="number" name="bday" placeholder="Rok narozen"><br>' . "\n";
        echo '<input type="text" maxlength="2" name="country" placeholder="Země narození"><br>';
        echo '<select id="sel" name="books">' . "\n";
        echo '<option>ke knížce</option>' . "\n";
        $books = book($conn);
        foreach ($books as $author) {
            echo '<option>' . $author["book_name"] . '</option>' . "\n";
        }
        echo '</select><br>' . "\n";
        echo '<input type="submit" name="add_author"  value="Přidat">';
        echo '</form>' . "\n";

        if (isset($_POST["add_author"])) {
            add_author($conn, filter_input(INPUT_POST, "f_name"), filter_input(INPUT_POST, "l_name"), filter_input(INPUT_POST, "bday"), filter_input(INPUT_POST, "country"));
            header("Location: index.php");
            if (isset($_POST["books"]) and $_POST["books"] != "k nikomu") {
                foreach (get_table($conn, "author") as $items) {
                    $bday = explode("-", $items["bday"]);
                    $bday = $bday[0];
                    if (filter_input(INPUT_POST, "f_name") == $items["f_name"] and filter_input(INPUT_POST, "l_name") == $items["l_name"] and filter_input(INPUT_POST, "bday") == $bday and filter_input(INPUT_POST, "country") == $items["country"]) {
                        $id_author = $items["id"];
                        break;
                    }
                }

                $slecect = explode(", ", filter_input(INPUT_POST, "books"));
                $id_book = $slecect[0];
                add_book_has_author($conn, (int) $id_book, (int) $id_author);
                header("Location: index.php");
            }
        }
    }
    echo '</div>';
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