<?php

/**
 * connect to db
 * @param   String  $servername     name of the book
 * @param   String  $dbname         dbname
 * @param   String  $username       username
 * @param   String  $password       password
 * @return  mixed   $conn
 */
function connect_to_db(string $servername, string $dbname, string $username, string $password)
{
    //connect
    try {
        $conn = new PDO("mysql:host=" . $servername . ";dbname=" . $dbname . ";charset=utf8", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = $conn->prepare("SET character SET UTF8");
        $sql->execute();

        return $conn;
    } catch (PDOException $e) {
        echo "Something goes worn give us time to fix it";
    }
}

/**
 * add book
 * @param   mixed   $conn           db connection
 * @param   String  $name           name of the book
 * @param   String  $relase         relase year
 * @param   String  $language       two characters "CZ"
 * @param   String  $ISBN           ISBN
 * @param   String  $room_name      room_name
 * @param   String  $pages          pages
 */
function add_book($conn, string $name, int $relase, string $language, string $ISBN, string $room_name, int $pages, string $img)
{

    save_to_log("Add book: \"" . $name . "\" by: \"" . $_SESSION["username"] . "\"");
    $db_room = true;
    $select_search = "SELECT * FROM `room`";
    $select_search = $conn->prepare($select_search);
    $numrows = $select_search->execute();
    if ($numrows > 0) {
        while ($row = $select_search->fetch()) {
            if ($room_name == $row["name"]) {
                $db_room = false;
                break;
            }
        }
    }

    if ($db_room) {
        $sql = "INSERT INTO `room`(`name`) VALUES ('" . $room_name . "')";
        $sql = $conn->prepare($sql);
        $sql->execute();
    }

    $sql = "INSERT INTO `book` (`name`, `relase`, `language`, `ISBN`, `room_name`, `pages`, `img`) VALUES ('" . $name . "', '" . $relase . "', '" . $language . "', '" . $ISBN . "', '" . $room_name . "', " . $pages . ", '" . $img . "')";
    $sql = $conn->prepare($sql);
    $sql->execute();
}

/**
 * add author
 * @param   mixed   $conn           db connection
 * @param   String  $f_name         first name
 * @param   String  $l_name         last name
 * @param   String  $bday           b. day
 * @param   String  $country        two characters "CZ"
 */
function add_author($conn, string $f_name, string $l_name, int $bday, string $country)
{

    save_to_log("Add author: \"" . $f_name . " " . $l_name . "\"  by: \"" . $_SESSION["username"] . "\"");
    $sql = "INSERT INTO `author`(`f_name`, `l_name`, `bday`, `country`) VALUES ('" . $f_name . "', '" . $l_name . "', '" . $bday . "-01-01', '" . $country . "')";
    $sql = $conn->prepare($sql);
    $sql->execute();
}

/**
 * get table rows
 * @param   mixed   $conn           db connection
 * @param   String  $name           name of table
 * @return  array   return array of rows from table
 */
function get_table($conn, string $name)
{

    $rooms = array();
    $select_search = "SELECT * FROM `$name`";
    $select_search = $conn->prepare($select_search);
    $numrows = $select_search->execute();
    if ($numrows > 0) {
        $rows = array();
        while ($row = $select_search->fetch()) {
            $rows[] = $row;
        }
        return $rows;
    } else {
        return NULL;
    }
}

/**
 * add ids to table book_has_author
 * @param   mixed   $conn           db connection
 * @param   String  $id_book        id of book
 * @param   String  $id_author      id of author
 */
function add_book_has_author($conn, int $id_book, int $id_author)
{

    $sql = "INSERT INTO `book_has_author`(`book_id`, `author_id`) VALUES (" . $id_book . ", " . $id_author . ")";
    $sql = $conn->prepare($sql);
    $sql->execute();
}

/**
 * add genres
 * @param   mixed   $conn           db connection
 * @param   String  $id_book        id of book
 * @param   String  $id_author      id of author
 */
function add_genres($conn, array $names)
{
    $genres = get_table($conn, "genres");
    if ($genres != NULL) {
        $i = $genres;
        $genres = array();
        foreach ($i as $item) {
            $genres[] = $item["name"];
        }

        foreach ($names as $item) {
            if (!(in_array($item, $genres))) {
                insert_genres($conn, $item);
            }
        }
    } else {
        foreach ($names as $item) {
            insert_genres($conn, $item);
        }
    }
}

/**
 * insert genres
 * @param   mixed   $conn           db connection
 * @param   String  $name           name to insert
 */
function insert_genres($conn, string $name)
{
    $sql = "INSERT INTO `genres`(`name`) VALUES ('" . $name . "')";
    $sql = $conn->prepare($sql);
    $sql->execute();
}

/**
 * get genres id from db
 * @param   mixed   $conn           db connection
 * @param   String  $name           name to search in db
 * @return  int     id
 */
function get_genres_id($conn, string $name)
{
    $sql = "SELECT * FROM `genres` WHERE `name` = '" . $name . "'";
    $sql = $conn->prepare($sql);
    $numrows = $sql->execute();
    if ($numrows > 0) {
        $row = $sql->fetch();
        return $row["id"];
    } else {
        return NULL;
    }
}

/**
 * add ids to table book_has_genres
 * @param   mixed   $conn           db connection
 * @param   int     $id_book        id of book
 * @param   int     $id_genres      id of genres
 */
function add_book_has_genres($conn, int $id_book, int $id_genres)
{

    $sql = "INSERT INTO `book_has_genres`(`book_id`, `genres_id`) VALUES (" . $id_book . ", " . $id_genres . ")";
    $sql = $conn->prepare($sql);
    $sql->execute();
}

/**
 * check if user exist
 * @param   mixed   $conn           db connection
 * @param   String  $username       username to look for
 * @return  Bool    if username exist in db return true
 */
function username_exist($conn, String $username)
{

    $sql = "SELECT * FROM `user` WHERE `username` = '" . $username . "'";
    $sql = $conn->prepare($sql);
    $numrows = $sql->execute();
    $row = $sql->fetch();
    if ($row["username"] == $username) {
        return true;
    } else {
        return false;
    }
}

/**
 * add user to db
 * @param   mixed   $conn           db connection
 * @param   String  $f_name         first name
 * @param   String  $l_name         last name
 * @param   String  $username       username
 * @param   String  $password       password
 */
function add_user($conn, string $f_name, string $l_name, string $username, string $password)
{
    save_to_log("Add user: \"" . $username . "\"");
    date_default_timezone_set('Europe/Prague');
    $datetime = date("Y-m-d H:i:s");
    $password = hash_password($password);
    $sql = "INSERT INTO `user`(`f_name`, `l_name`, `username`, `password`, `last_login`, `ceated`) VALUES ('" . $f_name . "', '" . $l_name . "', '" . $username . "', '" . $password . "', '" . $datetime . "', '" . $datetime . "')";
    $sql = $conn->prepare($sql);
    $sql->execute();
}

/**
 * set new password
 * @param   mixed   $conn           db connection
 * @param   String  $username       username
 * @param   String  $password       new password
 */
function update_password($conn, string $username, string $password)
{
    date_default_timezone_set('Europe/Prague');
    $datetime = date("Y-m-d H:i:s");
    $password = hash_password($password);
    $sql = "UPDATE `user` SET `password` = '" . $password . "' WHERE `username` = '" . $username . "'";
    $sql = $conn->prepare($sql);
    $sql->execute();
}

/**
 * check if login is ok
 * @param   mixed   $conn           db connection
 * @param   String  $username       username
 * @param   String  $password       password
 * @param   Bool    check if is moderator or admin
 * @return  Bool    if true you can login
 */
function login($conn, string $username, string $password, $mod_plus = false)
{
    $password = hash_password($password);

    $sql = "SELECT * FROM `user` WHERE `username` = '" . $username . "' AND`password` = '" . $password . "'";
    $sql = $conn->prepare($sql);
    $numrows = $sql->execute();
    $row = $sql->fetch();
    if ($row["username"] == $username and $row["password"] == $password) {
        last_login($conn, $username);
        if ($mod_plus != false and !($row["role"] == "mod" or $row["role"] == "admin")) {
            return false;
        }
        return true;
    } else {
        return false;
    }
}

/**
 * check if is admin
 * @param   mixed   $conn           db connection
 * @param   String  $username       username
 * @param   String  $password       password
 * @return  Bool    if true you can login
 */
function is_admin($conn, string $username, string $password)
{
    $password = hash_password($password);

    $sql = "SELECT * FROM `user` WHERE `username` = '" . $username . "' AND`password` = '" . $password . "'";
    $sql = $conn->prepare($sql);
    $numrows = $sql->execute();
    $row = $sql->fetch();
    if ($row["username"] == $username and $row["password"] == $password) {
        if ($row["role"] == "admin") {
            return true;
        }
    } else {
        return false;
    }
}

/**
 * set role
 * @param   mixed   $conn           db connection
 * @param   String  $username       username
 * @param   String  $role           role
 */
function set_role($conn, string $username, string $role)
{

    save_to_log("Set role: " . $role . " to: \"" . $username . "\" by: \"" . $_SESSION["username"] . "\"");
    $sql = "UPDATE `user` SET `role` = '" . $role . "' WHERE `username` = '" . $username . "'";
    $sql = $conn->prepare($sql);
    $sql->execute();
}

/**
 * set role
 * @param   mixed   $conn           db connection
 * @param   String  $username       username
 */
function delete_user($conn, string $username)
{
    delete_reservations($conn, $username);
    save_to_log("Delete user: \"" . $username . "\" by: \"" . $_SESSION["username"] . "\"");
    $sql = "DELETE FROM `user` WHERE `username` = '" . $username . "'";
    $sql = $conn->prepare($sql);
    $sql->execute();
}

/**
 * set role
 * @param   mixed   $conn           db connection
 * @param   String  $username       username
 */
function delete_reservations($conn, string $username)
{
    save_to_log("Delete reservations for user: \"" . $username . "\" by: \"" . $_SESSION["username"] . "\"");
    $user_id = get_user_id($conn, $username);
    $sql = "SELECT * FROM `reservation` WHERE `user_id` = '" . $user_id . "'";
    $sql = $conn->prepare($sql);
    $sql->execute();
    while ($row = $sql->fetch()) {
        $reservation_id = $row["id"];
        $sql2 = "DELETE FROM `book_has_reservation` WHERE `reservation_id` = '" . $reservation_id . "'";
        $sql2 = $conn->prepare($sql2);
        $sql2->execute();
    }
    $sql = "DELETE FROM `reservation` WHERE `user_id` = '" . $user_id . "'";
    $sql = $conn->prepare($sql);
    $sql->execute();
}

/**
 * add user to db
 * @param   mixed   $conn           db connection
 * @param   String  $username       username
 */
function last_login($conn, string $username)
{
    date_default_timezone_set('Europe/Prague');
    $datetime = date("Y-m-d H:i:s");
    $sql = "UPDATE `user` SET `last_login` = '" . $datetime . "' WHERE `username` = '" . $username . "'";
    $sql = $conn->prepare($sql);
    $sql->execute();
}

/**
 * make hash from pasword
 * @param   String  $password       password
 * @return  String    if true you can login
 */
function hash_password(string $password)
{
    //$password = hash("sha3-512", $password);
    return $password;
}

/**
 * get ids from mn table
 * @param   Mixed   $conn           db
 * @param   String  $mn_table       mn table name
 * @param   Int     $id             id you want with
 * @param   String  $id_name        id name to check
 * @param   String  $id_get         id name what you want
 * @return  Array   return ids
 */
function mn($conn, string $mn_table, int $id, string $id_name, string $id_get)
{
    $ids = array();
    $mn_table = get_table($conn, $mn_table);

    foreach ($mn_table as $item) {
        if ($item[$id_name] == $id) {
            $ids[] = $item[$id_get];
        }
    }

    return $ids;
}

/**
 * get genres
 * @param   Mixed   $conn       db connection
 * @param   Int     $id         id
 * @return  String  return genres
 */
function get_genre($conn, int $id)
{

    $sql = "SELECT `name` FROM `genres` WHERE `id` = " . $id;
    $sql = $conn->prepare($sql);
    $numrows = $sql->execute();
    $row = $sql->fetch();
    if ($numrows > 0) {
        return $row["name"];
    } else {
        return NULL;
    }
}

/**
 * get book
 * @param   Mixed   $conn       db connection
 * @param   Int     $id         id
 * @return  String  return book
 */
function get_book($conn, int $id)
{

    $sql = "SELECT `name` FROM `book` WHERE `id` = " . $id;
    $sql = $conn->prepare($sql);
    $numrows = $sql->execute();
    $row = $sql->fetch();
    if ($numrows > 0) {
        return $row["name"];
    } else {
        return NULL;
    }
}

/**
 * get author
 * @param   Mixed   $conn       db connection
 * @param   Int     $id         id
 * @return  Array   return author
 */
function get_author($conn, int $id)
{

    $sql = "SELECT * FROM `author` WHERE `id` = " . $id;
    $sql = $conn->prepare($sql);
    $numrows = $sql->execute();
    $row = $sql->fetch();
    if ($numrows > 0) {
        return $row;
    } else {
        return NULL;
    }
}

/**
 * get book by id from db
 * @param   Mixed   $conn           db connection
 * @param   Int     $id             name to search in db
 * @return  Arry    info about book
 */
function get_book_by_id($conn, int $id)
{
    $sql = "SELECT * FROM `book` WHERE `id` = '" . $id . "'";
    $sql = $conn->prepare($sql);
    $numrows = $sql->execute();
    if ($numrows > 0) {
        $row = $sql->fetch();
        return $row;
    } else {
        return NULL;
    }
}

/**
 * get reservation from db
 * @param   Mixed   $conn           db connection
 * @return  Arry    info about reservation
 */
function get_reservations($conn)
{
    $sql = "SELECT book.id as 'book_id', reservation.id AS 'reservation_id', `s-reservation`, `e-reservation` FROM `reservation` INNER JOIN book_has_reservation ON book_has_reservation.reservation_id = reservation.id INNER JOIN book ON book.id = book_has_reservation.book_id WHERE `e-reservation` >= CURRENT_DATE() ORDER BY `e-reservation`";
    $sql = $conn->prepare($sql);
    $numrows = $sql->execute();

    if ($numrows > 0) {
        $rows = array();
        while ($row = $sql->fetch()) {
            $rows[] = $row;
        }
        return $rows;
    } else {
        return NULL;
    }
}

/**
 * add reservation to db
 * @param   Mixed   $conn               db connection
 * @param   String  $s_reservation      start reservation date
 * @param   String  $e_reservation      end reservation date
 * @param   String  $user               username
 * @return  Bool    if reservation can be done
 */
function reservations($conn, $s_reservation, $e_reservation, $book_id, $user)
{
    $sql = "SELECT * FROM `reservation` INNER JOIN book_has_reservation ON book_has_reservation.reservation_id = reservation.id INNER JOIN book ON book.id = book_has_reservation.book_id WHERE `e-reservation` > CURTIME() AND book.id = " . $book_id . " ORDER BY `e-reservation`";
    $sql = $conn->prepare($sql);
    $numrows = $sql->execute();
    if ($numrows > 0) {
        while ($row = $sql->fetch()) {
            if (((strtotime($row["e-reservation"]) < strtotime($s_reservation) and strtotime($row["e-reservation"]) < strtotime($e_reservation)) or strtotime($row["s-reservation"]) > strtotime($e_reservation))) { } else {
                return false;
            }
        }
    }
    add_reservations($conn, $s_reservation, $e_reservation, $user);
    return true;
}

/**
 * add reservation to db
 * @param   Mixed   $conn               db connection
 * @param   String  $s_reservation      start reservation date
 * @param   String  $e_reservation      end reservation date
 * @param   String  $user               username
 * @return  Bool    if reservation can be done
 */
function add_reservations($conn, $s_reservation, $e_reservation, $user)
{
    if ($user == $_SESSION["username"]) {
        save_to_log("Add reservation from: \"" . $s_reservation . "\" to: \"" . $e_reservation . "\" by: \"" . $user . "\"");
    } else {
        save_to_log("Add reservation from: \"" . $s_reservation . "\" to: \"" . $e_reservation . "\" reservation for: \"" . $user . "\" by: \"" . $_SESSION["username"] . "\"");
    }

    $id = get_user_id($conn, $user);
    $sql = "INSERT INTO `reservation`(`s-reservation`, `e-reservation`, `user_id`) VALUES ('" . $s_reservation . "', '" . $e_reservation . "' , $id)";
    $sql = $conn->prepare($sql);
    $sql->execute();
}

/**
 * update book
 * @param   Mixed   $conn               db connection
 * @param   String  $id                 id
 * @param   String  $name               name
 * @param   String  $relase             relase date
 * @param   String  $language           language
 * @param   String  $ISBN               ISBN
 * @param   String  $pages              pages
 * @param   String  $img                image
 * @param   String  $room_name          room_name
 */
function update_book($conn, String $id, String $name, Int $relase, String $language, String $ISBN, Int $pages, String $img, String $room_name)
{
    save_to_log("Update book: \"" . $name . "\" by: \"" . $_SESSION["username"] . "\"");
    $sql = "UPDATE `book` SET `name` = '" . $name . "',`relase` = '" . $relase . "',`language` = '" . $language . "',`ISBN` = '" . $ISBN . "',`pages` = '" . $pages . "',`img` = '" . $img . "',`room_name` = '" . $room_name . "' WHERE `id` = '" . $id . "'";
    //echo $sql;
    $sql = $conn->prepare($sql);
    $sql->execute();
}

/**
 * update book_has_author
 * @param   Mixed   $conn               db connection
 * @param   Int     $id                 book id
 * @param   String  $author             author
 */
function update_book_has_author($conn, Int $id, String $author)
{
    save_to_log("Update book_has_author by: \"" . $_SESSION["username"] . "\"");
    $author = explode(" ", $author);
    $sql = "SELECT * FROM `author` WHERE `f_name` = '" . $author[0] . "' AND `l_name` = '" . $author[1] . "'";
    //echo $sql;
    $sql = $conn->prepare($sql);
    $sql->execute();
    $row = $sql->fetch();
    $author_id = $row["id"];
    $sql = "UPDATE `book_has_author` SET `author_id` = '" . $author_id . "' WHERE `book_id` = '" . $id . "'";
    //echo $sql;
    $sql = $conn->prepare($sql);
    $sql->execute();
}

/**
 * get usr id
 * @param   Mixed   $conn               db connection
 * @param   String  $username           username
 * @return  Int     id from user
 */
function get_user_id($conn, $username)
{
    $sql = "SELECT * FROM `user` WHERE `username` = '" . $username . "'";
    $sql = $conn->prepare($sql);
    $numrows = $sql->execute();
    if ($numrows > 0) {
        $row = $sql->fetch();
        return $row["id"];
    }
}

/**
 * get usr id
 * @param   Mixed   $conn               db connection
 * @return  Array   users
 */
function get_users($conn)
{
    $sql = "SELECT * FROM `user`";
    $sql = $conn->prepare($sql);
    $rows = array();
    $sql->execute();
    while ($row = $sql->fetch()) {
        $rows[] = $row;
    }
    return $rows;
}

/**
 * get reservation id
 * @param   Mixed   $conn               db connection
 * @param   String  $s_reservation      start reservation date
 * @param   String  $e_reservation      end reservation date
 * @param   Int     $book_id            id of reservation
 * @return  Int     id from reservation
 */
function get_reservation_id($conn, $s_reservation, $e_reservation, $book_id)
{
    $sql = "SELECT * FROM `reservation` WHERE `s-reservation` = '" . $s_reservation . "' AND `e-reservation` = '" . $e_reservation . "' ORDER BY `reservation`.`id` DESC";
    $sql = $conn->prepare($sql);
    $numrows = $sql->execute();
    if ($numrows > 0) {
        $row = $sql->fetch();
        return $row["id"];
    }
}

/**
 * get reservation id
 * @param   Mixed   $conn               db connection
 * @return  Array   table with reservation and book
 */
function get_reservation_with_book($conn)
{
    $sql = "SELECT book.id AS 'book_id', reservation.id AS 'reservation_id', reservation.`s-reservation`, reservation.`e-reservation` FROM `book` INNER JOIN book_has_reservation ON book_has_reservation.book_id = book.id INNER JOIN reservation on reservation.id = book_has_reservation.reservation_id";
    $sql = $conn->prepare($sql);
    $numrows = $sql->execute();
    if ($numrows > 0) {
        $rows = array();
        while ($row = $sql->fetch()) {
            $rows[] = $row;
        }
        return $rows;
    }
}

/**
 * add ids to table book_has_reservation
 * @param   mixed   $conn           db connection
 * @param   int     $id_book        id of book
 * @param   int     $id_reservation id of reservation
 */
function add_book_has_reservation($conn, int $id_book, int $id_reservation)
{
    $sql = "INSERT INTO `book_has_reservation`(`book_id`, `reservation_id`) VALUES (" . $id_book . ", " . $id_reservation . ")";
    $sql = $conn->prepare($sql);
    $sql->execute();
}

/**
 * search
 * @param   mixed   $conn           db connection
 * @param   String  $search         search in db
 * @return  Array   array
 */
function book($conn, String $search = "", $count_books = 1, $page = 1, $per_page = 30)
{
    $page -= 1;
    if ($page == 0) {
        $items = 0;
    } else {
        $items = $page * $per_page;
    }
    $book_id = array();
    $books = array();
    if ($search == "") {
        $sql = "SELECT book.id AS 'book_id' FROM book INNER JOIN book_has_genres ON book_has_genres.book_id = book.id INNER JOIN genres ON genres.id = book_has_genres.genres_id INNER JOIN book_has_author ON book_has_author.book_id = book.id INNER JOIN room ON room.name = room_name INNER JOIN author ON author.id = book_has_author.author_id WHERE `show` != '0' OR `show` IS NULL ORDER BY book.name";
    } else {
        $sql = "SELECT book.id AS 'book_id' FROM book INNER JOIN book_has_genres ON book_has_genres.book_id = book.id INNER JOIN genres ON genres.id = book_has_genres.genres_id INNER JOIN book_has_author ON book_has_author.book_id = book.id INNER JOIN room ON room.name = room_name INNER JOIN author ON author.id = book_has_author.author_id WHERE book.room_name = room.name AND book.id = book_has_genres.book_id AND book_has_genres.genres_id = genres.id AND book_has_author.author_id = author.id AND book.id = book_has_author.book_id AND (book.room_name LIKE '%" . $search . "%' OR book.name LIKE '%" . $search . "%' OR book.relase LIKE '%" . $search . "%' OR book.language LIKE '%" . $search . "%'OR book.ISBN LIKE '%" . $search . "%'OR book.pages LIKE '%" . $search . "%'OR author.f_name LIKE '%" . $search . "%' OR author.l_name LIKE '%" . $search . "%' OR author.bday LIKE '%" . $search . "%' OR genres.name LIKE '%" . $search . "%' OR room.name LIKE '%" . $search . "%' OR CONCAT(author.f_name, ' ' , author.l_name) LIKE '%" . $search . "%' OR CONCAT(author.l_name, ' ', author.f_name) LIKE '%" . $search . "%') and (`show` != '0' OR `show` IS NULL) ORDER BY book.name";
    }
    //echo $sql;
    $sql = $conn->prepare($sql);
    $sql->execute();
    $i = 0;
    $k = 0;
    $b = array();
    while ($row = $sql->fetch()) {
        if (!in_array($row["book_id"], $b)) {
            if ($k == $items) {
                $book_id[] = $row["book_id"];
                $i++;
                if ($i == $per_page) {
                    break;
                }
            } else {
                $k++;
            }
            $b[] = $row["book_id"];
        }
    }
    unset($b);

    if (isset($book_id[0]) and $items <= $count_books) {
        $sql = "SELECT `id`, `name` as 'book_name', `relase`, `language`, `ISBN`, `pages`, `img`, `room_name` FROM `book` WHERE `id` IN(";
        $i = 0;
        foreach ($book_id as $id) {
            if ($i > 0) {
                $sql .= ", ";
            }
            $sql .= $id;
            $i++;
        }
        $sql .= ")";
        $sql = $conn->prepare($sql);
        $number = $sql->execute();
        $db_book = array();
        while ($row = $sql->fetch()) {
            $db_book[$row["id"]] = $row;
        }
        foreach ($book_id as $id) {
            $books[$id] = array();
            $genres_ids = mn($conn, "book_has_genres", $id, "book_id", "genres_id");
            $sql = "SELECT `name` FROM `genres` WHERE `id` IN(";
            $i = 0;
            foreach ($genres_ids as $value) {
                if ($i > 0) {
                    $sql .= ", ";
                }
                $sql .= $value;
                $i++;
            }
            $sql .= ")";
            $sql = $conn->prepare($sql);
            $sql->execute();
            $books[$id]["genres_name"] = array();
            while ($row = $sql->fetch()) {
                $books[$id]["genres_name"][] = $row["name"];
            }

            $author_ids = mn($conn, "book_has_author", $id, "book_id", "author_id");
            $sql = "SELECT `f_name`, `l_name` FROM `author` WHERE `id` IN(";
            $i = 0;
            foreach ($author_ids as $value) {
                if ($i > 0) {
                    $sql .= ", ";
                }
                $sql .= $value;
                $i++;
            }
            $sql .= ")";
            $sql = $conn->prepare($sql);
            $sql->execute();
            $books[$id]["author"] = array();
            while ($row = $sql->fetch()) {
                $books[$id]["author"][] = $row["f_name"] . " " . $row["l_name"];
            }

            $books[$id]["book_name"] = $db_book[$id]["book_name"];
            $books[$id]["language"] = $db_book[$id]["language"];
            $books[$id]["img"] = $db_book[$id]["img"];
            $books[$id]["room_name"] = $db_book[$id]["room_name"];
        }
    }
    return $books;
}

/**
 * search
 * @param   mixed   $conn           db connection
 * @param   String  $search         search in db
 * @return  Int     number of users
 */
function count_users($conn, String $search = "")
{
    if ($search == "") {
        $sql = "SELECT COUNT(id) AS 'count' FROM `user`";
        $sql = $conn->prepare($sql);
        $sql->execute();
        $row = $sql->fetch();
        return $row["count"];
    } else {
        $sql = "SELECT COUNT(id) AS 'count' FROM `user` WHERE `f_name` LIKE '%" . $search . "%' OR `l_name` LIKE '%" . $search . "%' OR `username` LIKE '%" . $search . "%' OR CONCAT(f_name, ' ' , l_name) LIKE '%" . $search . "%' OR `role` LIKE '%" . $search . "%'";
        $sql = $conn->prepare($sql);
        $sql->execute();
        $row = $sql->fetch();
        return $row["count"];
    }
}


/**
 * search
 * @param   mixed   $conn           db connection
 * @param   String  $search         search in db
 * @param   String  $page           page
 * @param   String  $per_page       users per page
 * @return  Array   array
 */
function users($conn, String $search = "", $page = 1, $per_page = 30)
{
    $page -= 1;
    if ($page == 0) {
        $skipp = 0;
    } else {
        $skipp = $page * $per_page;
    }
    if ($search == "") {
        $sql = "SELECT * FROM `user`";
    } else {
        $sql = "SELECT * FROM `user` WHERE `f_name` LIKE '%" . $search . "%' OR `l_name` LIKE '%" . $search . "%' OR `username` LIKE '%" . $search . "%' OR CONCAT(f_name, ' ' , l_name) LIKE '%" . $search . "%' OR `role` LIKE '%" . $search . "%'";
    }
    //echo $sql;
    $sql = $conn->prepare($sql);
    $sql->execute();
    $i = 0;
    while ($row = $sql->fetch()) {
        if ($skipp != 0) {
            $skipp--;
        } else {
            if ($i != $per_page) {
                $users[] = $row;
                $i++;
            } else {
                return $users;
            }
        }
    }
    return $users;
}

/**
 * count books
 * @param   mixed   $conn           db connection
 */
function count_books($conn)
{
    $sql = "SELECT COUNT(id) as \"books\" FROM book";
    $sql = $conn->prepare($sql);
    $sql->execute();
    while ($row = $sql->fetch()) {
        if (isset($row["books"])) {
            return $row["books"];
        } else {
            return 0;
        }
    }
}

/**
 * check if db is up to date or if exist
 */
function generate_db()
{
    ini_set('max_execution_time', 0);
    $configs = include('config.php');
    $servername = $configs["servername"];
    $dbname = $configs["dbname"];
    $username = $configs["username"];
    $password = $configs["password"];

    $dsn = "mysql:host=$servername;dbname=$dbname;";
    //connect
    try {
        $conn = new PDO($dsn, $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $execute = false;
    } catch (PDOException $e) {
        $execute = true;
        $dsn = "mysql:host=$servername;";
        //connect
        try {
            $conn = new PDO($dsn, $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $execute = false;
            echo "Something goes worn give us time to fix it";
        }

        $sql = $conn->prepare("SET character SET UTF8");
        $sql->execute();
    }


    $sql = $conn->prepare("SET character SET UTF8");
    $sql->execute();
    if ($execute) {
        save_to_log("Generating DB");
        $fileList = glob('db/*.sql');
        $sql = load_file($fileList[0]);
        $sql = explode(";", $sql);
        foreach ($sql as $item) {
            try {
                $sql = $conn->prepare($item . ";");
                $sql->execute();
            } catch (PDOException $e) { }
        }
    }
}

/**
 * will load file in root folder of program
 * @param   String  $filename   filen name with end (.txt, etc)
 * @param   String  $mode       mode (w, a, etc)
 * @return  String  text in file
 */
function load_file($filename, $mode = "r")
{

    $handle = fopen($filename, $mode);
    $text = "";
    while (($line = fgets($handle)) !== false) {
        $text = $text . $line;
    }
    return $text;
}

/**
 * will load file in root folder of program
 * @param   Mixed   $conn           db connection
 * @param   Int     $id             id of book
 */
function hide_book($conn, $id)
{
    $book = get_book($conn, $id);
    save_to_log("Hide book: \"" . $book . "\" by: \"" . $_SESSION["username"] . "\"");
    $sql = "UPDATE `book` SET `show` = '0' WHERE `id` = '" . $id . "'";
    $sql = $conn->prepare($sql);
    $sql->execute();
}

/**
 * will delete reservation 
 * @param   Mixed   $conn           db connection
 * @param   Int     $id             id of book
 */
function delete_reservation($conn, $id)
{
    $sql = "DELETE FROM `book_has_reservation` WHERE `reservation_id` = '" . $id . "'";
    $sql = $conn->prepare($sql);
    $sql->execute();

    $sql = "DELETE FROM `reservation` WHERE `id` = '" . $id . "'";
    $sql = $conn->prepare($sql);
    $sql->execute();
}

/**
 * will get user by reservation id 
 * @param   Mixed   $conn           db connection
 * @param   Int     $id             id of book
 * @return  Array   text in file
 */
function get_user_by_reservation_id($conn, $id)
{
    $sql = "SELECT * FROM `reservation` WHERE `id` = '" . $id . "'";
    $sql = $conn->prepare($sql);
    $sql->execute();
    $reservation = $sql->fetch();
    $user_id = $reservation["user_id"];
    $sql = "SELECT * FROM `user` WHERE `id` = '" . $user_id . "'";
    $sql = $conn->prepare($sql);
    $sql->execute();

    return $sql->fetch();
}

/**
 * save text to .log and delete old
 * @param   String  $log_text   text that will be putet to log
 */
function save_to_log(String $log_text)
{
    date_default_timezone_set('Europe/Prague');
    $configs = include('config.php');
    if (!file_exists('logs')) {
        mkdir('logs', 0777, true);
    }
    $date = date("Y-m-d");
    $fa = fopen("logs/" . $date . ".log", "a");
    fwrite($fa, (date("H:i") . ": " . $log_text) . "\n");
    fclose($fa);

    if ($configs["delete_log"] != null) {
        $fileList = glob('logs/*.log');
        foreach ($fileList as $filename) {
            $date = substr($filename, 5, 10);
            if (strtotime($date) < strtotime('-' . ($configs["delete_log"] + 1) . ' days')) {
                unlink($filename);
            }
        }
    }
}
