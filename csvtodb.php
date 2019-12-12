<?php
ini_set('max_execution_time', 0);
$configs = include('config.php');
$servername = $configs["servername"];
$dbname = $configs["dbname"];
$username = $configs["username"];
$password = $configs["password"];
//connect
try {
    $conn = new PDO("mysql:host=" . $servername . ";dbname=" . $dbname . ";charset=utf8", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = $conn->prepare("SET character SET UTF8");
    $sql->execute();
} catch (PDOException $e) {
    echo "Something goes worn give us time to fix it";
}

$lines = load_csv("knihovna.csv");
foreach ($lines as $key => $line) {
    if ($key == 0) {
        print_r($line);
        echo "<br>\n";
        echo "<br>\n";
        echo "<br>\n";
        echo "<br>\n";
        continue;
    }
    echo "NEW ITEM";
    echo "<br>\n";
    echo "<br>\n";
    echo "<br>\n";
    $value = explode(";", $line);
    $author = explode(" ", $value[1]);
    if ($value[4] == "British English") {
        $value[4] = "GB";
    }
    if ($value[4] == "American English") {
        $value[4] = "US";
    }
    $genres = explode("/", $value[10]);
    $genres2 = explode("/", $value[11]);

    $db_room = true;
    $sql = "INSERT INTO `room`(`name`) VALUES (\"nikde\")";
    echo $sql;
    $select_search = "SELECT * FROM `room`";
    $select_search = $conn->prepare($select_search);
    $numrows = $select_search->execute();
    if ($numrows > 0) {
        while ($row = $select_search->fetch()) {
            if ("nikde" == $row["name"]) {
                $db_room = false;
                break;
            }
        }
    }

    if ($db_room) {
        $sql = $conn->prepare($sql);
        $sql->execute();
    }
    echo "<br>\n";

    $insert = true;
    $select_search = "SELECT `name` FROM `book`";
    $select_search = $conn->prepare($select_search);
    $numrows = $select_search->execute();
    $in_genres = array();
    if ($numrows > 0) {
        while ($row = $select_search->fetch()) {
            if ($row["name"] == $value[0]) {
                $insert = false;
            }
        }
    }
    if ($insert) {
        $sql = "INSERT INTO `book`(`name`, `relase`, `language`, `pages`, `room_name`, `price`, `level`, `accesories`, `for`) VALUES (\"" . $value[0] . "\", \"" . $value[3] . "\", \"" . $value[4] . "\", \"" . $value[5] . "\",\"nikde\", \"" . $value[6] . "\", \"" . $value[9] . "\", \"" . $value[13] . "\", ' . $value[14] . ')";
        echo $sql;
        $sql = $conn->prepare($sql);
        $sql->execute();
        echo "<br>\n";
    }

    $insert = true;
    $select_search = "SELECT `f_name`, `l_name` FROM `author`";
    $select_search = $conn->prepare($select_search);
    $numrows = $select_search->execute();
    $in_genres = array();
    if ($numrows > 0) {
        while ($row = $select_search->fetch()) {
            if ($row["f_name"] == $author[0] and $row["l_name"] == $author[1]) {
                $insert = false;
            }
        }
    }
    if ($insert) {
        $sql = "INSERT INTO `author`(`f_name`, `l_name`) VALUES (\"" . $author[0] . "\", \"" . $author[1] . "\")";
        echo $sql;
        $sql = $conn->prepare($sql);
        $sql->execute();
        echo "<br>\n";
    }

    $insert = true;
    $select_search = "SELECT `name` FROM `genre`";
    $select_search = $conn->prepare($select_search);
    $numrows = $select_search->execute();
    $in_genres = array();
    if ($numrows > 0) {
        while ($row = $select_search->fetch()) {
            $in_genres[] = $row["name"];
        }
    }
    foreach ($genres as $key => $item) {
        if (!(in_array($item, $in_genres))) {
            if ($insert) {
                $sql = "INSERT INTO `genre`(`name`) VALUES (\"" . $item . "\"";
                $insert = false;
            }
            if (!($key == 0)) {
                $sql .= "), (\"" . $item . "\"";
            }
        }
    }
    foreach ($genres2 as $key => $item) {
        if (!(in_array($item, $in_genres))) {
            if ($insert) {
                $sql = "INSERT INTO `genre`(`name`) VALUES (\"" . $item . "\"";
                $insert = false;
            }
            $sql .= "), (\"" . $item . "\"";
        }
    }



    /*$sql = "INSERT INTO `genre`(`name`) VALUES (\"" . $genres[0] . "\"";
foreach ($genres as $key => $item) {
    if (!($key == 0)) {
        $sql .= "), (\"" . $item . "\"";
    }
}
foreach ($genres2 as $key => $item) {
    $sql .= "), (\"" . $item . "\"";
}*/

    if (!$insert) {
        $sql .= ")";
        echo $sql;
        $sql = $conn->prepare($sql);
        $sql->execute();
    }
    echo "<br>\n";
    echo "<br>\n";




    $sql = "SELECT `id` FROM `book` WHERE `name` = \"" . $value[0] . "\"";
    echo $sql;
    $sql = $conn->prepare($sql);
    $sql->execute();
    $book_id = $sql->fetch();
    $book_id = $book_id["id"];
    echo "<br>\n";

    $genres_id = array();
    foreach ($genres as $key => $item) {
        $sql = "SELECT `id` FROM `genre` WHERE `name` = \"" . $item . "\"";
        echo $sql;
        $sql = $conn->prepare($sql);
        $sql->execute();
        while ($row = $sql->fetch()) {
            $genres_id[] = $row["id"];
        }
        echo "<br>\n";
    }
    foreach ($genres2 as $key => $item) {
        $sql = "SELECT `id` FROM `genre` WHERE `name` = \"" . $item . "\"";
        echo $sql;
        $sql = $conn->prepare($sql);
        $sql->execute();
        while ($row = $sql->fetch()) {
            $genres_id[] = $row["id"];
        }
        echo "<br>\n";
    }

    $sql = "SELECT `id` FROM `author` WHERE `f_name` = \"" . $author[0] . "\" AND `l_name` = \"" . $author[1] . "\"";
    echo $sql;
    $sql = $conn->prepare($sql);
    $sql->execute();
    $author_id = $sql->fetch();
    $author_id = $author_id["id"];
    echo "<br>\n";
    echo "<br>\n";


    foreach ($genres_id as $item) {
        $sql = "INSERT INTO `book_has_genre`(`book_id`, `genre_id`) VALUES (\"" . $book_id . "\", \"" . $item . "\")";
        echo $sql;
        $sql = $conn->prepare($sql);
        $sql->execute();
        echo "<br>\n";
    }


    $sql = "INSERT INTO `book_has_author`(`book_id`, `author_id`) VALUES (\"" . $book_id . "\", \"" . $author_id . "\")";
    echo $sql;
    $sql = $conn->prepare($sql);
    $sql->execute();
    echo "<br>\n";
    echo "<br>\n";
    echo "<br>\n";
    echo "<br>\n";
}


function load_csv($path)
{

    $handle = fopen($path, "r");
    $lines = array();
    while (($line = fgets($handle)) !== false) {
        $lines[] = $line;
    }
    print_r($lines[0]);
    echo "<br>\n";
    unset($lines[0]);
    return $lines;
}
