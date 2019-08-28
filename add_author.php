<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <title>Přidání autora</title>
    <link rel="stylesheet" type="text/css" href="styles/add_author.css">
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
    echo '<div id="main">';

    if (isset($_SESSION["username"]) and isset($_SESSION["password"]) and login($conn, $_SESSION["username"], $_SESSION["password"], true)) { } else {
        header("Location: /login.php");
    }

    if (isset($_GET["name"]) and isset($_GET["relase"]) and isset($_GET["language"]) and isset($_GET["ISBN"]) and isset($_GET["room_name"]) and isset($_GET["pages"]) and isset($_GET["genres"]) and isset($_GET["img"])) {


        echo "Přiřadte autora k \"" . $_GET["name"] . "\"<br>\n";
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
                if ($_POST["f_name"] == $items["f_name"] and $_POST["l_name"] == $items["l_name"] and $_POST["bday"] == $bday and $_POST["country"] == $items["country"]) {
                    $id_author = $items["id"];
                    break;
                }
            }
        }

        if (isset($_POST['submit2'])) {
            $author = explode(", ", $_POST['authors']);

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
                if ($_GET["name"] == $items["name"] and $_GET["language"] == $items["language"] and $_GET["ISBN"] == $items["ISBN"] and $_GET["room_name"] == $items["room_name"] and $_GET["pages"] == $items["pages"]) {
                    $id_book = $items["id"];
                    break;
                }
            }

            $genres = explode(",", $_GET["genres"]);
            foreach ($genres as $id_genres) {
                add_book_has_genres($conn, (int) $id_book, (int) $id_genres);
            }
            add_book_has_author($conn, (int) $id_book, (int) $id_author);
            header("Location: /");
        }
    } else {
        echo "Přiřadte autora <br>\n";
        echo '<form method="POST" action="">';
        echo '<input type="text" maxlength="45" name="f_name" placeholder="Jméno"><br>';
        echo '<input type="text" maxlength="45" name="l_name" placeholder="Přímen"><br>';
        echo '<input type="number" name="bday" placeholder="Rok narozen"><br>' . "\n";
        echo '<input type="text" maxlength="2" name="country" placeholder="Země narození"><br>';
        /*echo '<select id="sel" name="books">' . "\n";
    echo '<option>k nikomu</option>' . "\n";
    $books = book($conn);
    foreach($books as $author){
        echo '<option>'. $author["book_id"] . ", " . $author["book_name"].'</option>' . "\n";
    }
    echo '</select><br>'. "\n";*/
        echo '<input type="submit" name="add_author"  value="Přidat">';
        echo '</form>' . "\n";

        if (isset($_POST["add_author"])) {
            add_author($conn, $_POST["f_name"], $_POST["l_name"], $_POST["bday"], $_POST["country"]);
            header("Location: /");
            /*if(isset($_POST["books"]) and $_POST["books"] != "k nikomu"){
            foreach(get_table($conn, "author") as $items){
                $bday = explode("-", $items["bday"]);
                $bday = $bday[0];
                if($_POST["f_name"] == $items["f_name"] and $_POST["l_name"] == $items["l_name"] and $_POST["bday"] == $bday and $_POST["country"] == $items["country"]){
                    $id_author = $items["id"];
                    break;
                }
            }

            $slecect = explode(", ", $_POST["books"]);
            $id_book = $slecect[0];
            add_book_has_author($conn, (int)$id_book, (int)$id_author);
            header("Location: /");
        }*/
        }
    }
    echo '</div>';

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