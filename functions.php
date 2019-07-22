<?php

/**
 * connect to db
 * @param   String  $servername     name of the book
 * @param   String  $dbname         dbname
 * @param   String  $username       username
 * @param   String  $password       password
 * @return  mixed   $conn
 */
function connect_to_db(string $servername, string $dbname, string $username, string $password){

        //connect
        try {
            $conn = new PDO("mysql:host=".$servername.";dbname=".$dbname.";charset=utf8", $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
        catch(PDOException $e)
            {
            echo "Something goes worn give us time to fix it";
            }

    $character = $conn->prepare("SET character SET UTF8");
    $character->execute();

    return $conn;
}

/**
 * insert book
 * @param   mixed   $conn           db connection
 * @param   String  $name           name of the book
 * @param   String  $relase         relase year
 * @param   String  $language       two characters "CZ"
 * @param   String  $ISBN           ISBN
 * @param   String  $room_name      room_name
 * @param   String  $pages          pages
 */
function add_book($conn, string $name, int $relase, string $language, string $ISBN, string $room_name, int $pages){

    $db_room = true;
    $select_search = "SELECT * FROM `room`";
    $select_search = $conn->prepare($select_search); 
    $numrows = $select_search->execute();
    if($numrows > 0){
        while($row = $select_search->fetch()){
            if($room_name == $row["name"]){
                $db_room = false;
                break;
            }
        }
    }

    if($db_room){
        $sql = "INSERT INTO `room`(`name`) VALUES ('". $room_name ."')";
        $sql = $conn->prepare($sql); 
        $sql->execute();
    }

    $sql = "INSERT INTO `book` (`name`, `relase`, `language`, `ISBN`, `room_name`, `pages`) VALUES ('". $name ."', '". $relase ."-01-01', '". $language ."', '". $ISBN ."', '". $room_name ."', ". $pages .")";
    $sql = $conn->prepare($sql); 
    $sql->execute();
    
}

/**
 * insert author
 * @param   mixed   $conn           db connection
 * @param   String  $f_name         first name
 * @param   String  $l_name         last name
 * @param   String  $bday           b. day
 * @param   String  $country        two characters "CZ"
 */
function add_author($conn, string $f_name, string $l_name, int $bday, string $country){

    $sql = "INSERT INTO `author`(`f_name`, `l_name`, `bday`, `country`) VALUES ('". $f_name ."', '". $l_name ."', '". $bday ."-01-01', '". $country ."')";
    $sql = $conn->prepare($sql); 
    $sql->execute();
    
}

/**
 * get table rows
 * @param   mixed   $conn           db connection
 * @param   String  $name           name of table
 * @return  array   return array of rows from table
 */
function get_table($conn, string $name){

    $rooms = array();
    $select_search = "SELECT * FROM `$name`";
    $select_search = $conn->prepare($select_search); 
    $numrows = $select_search->execute();
    if($numrows > 0){
        $rows = array();
        while($row = $select_search->fetch()){
            $rows[] = $row;
        }
        return $rows;
    }else {
        return NULL;
    }

}

/**
 * insert author
 * @param   mixed   $conn           db connection
 * @param   String  $id_book        id of book
 * @param   String  $id_author      id of author
 */
function add_book_has_author($conn, int $id_book, int $id_author){

    $sql = "INSERT INTO `book_has_author`(`book_id`, `author_id`) VALUES (". $id_book .", ". $id_author .")";
    $sql = $conn->prepare($sql); 
    $sql->execute();
}

/**
 * insert author
 * @param   mixed   $conn           db connection
 * @param   String  $id_book        id of book
 * @param   String  $id_author      id of author
 */
function add_genres($conn, array $names){
    $genres = get_table($conn, "genres");
    if($genres != NULL){
        $i = $genres;
        $genres = array();
        foreach($i as $item){
            $genres[] = $item["name"];
        }

        foreach($names as $item){
            if(!(in_array($item, $genres))){
                insert_genres($conn, $item);
            }
        }
    }else{
        foreach($names as $item){
            insert_genres($conn, $item);
        }
    }

}

/**
 * insert author
 * @param   mixed   $conn           db connection
 * @param   String  $name           name to insert
 */
function insert_genres($conn, string $name){
    $sql = "INSERT INTO `genres`(`name`) VALUES ('". $name ."')";
    $sql = $conn->prepare($sql);
    $sql->execute();
}

/**
 * insert author
 * @param   mixed   $conn           db connection
 * @param   String  $name           name to search in db
 * @return  int     id
 */
function get_genres_id($conn, string $name){
    $sql = "SELECT * FROM `genres` WHERE `name` = '". $name ."'";
    $sql = $conn->prepare($sql);
    $numrows = $sql->execute();
    if($numrows > 0){
        $row = $sql->fetch();
        return $row["id"];
    }else {
        return NULL;
    }
}

/**
 * insert author
 * @param   mixed   $conn           db connection
 * @param   int     $id_book        id of book
 * @param   int     $id_genres      id of genres
 */
function add_book_has_genres($conn, int $id_book, int $id_genres){

    $sql = "INSERT INTO `book_has_genres`(`book_id`, `genres_id`) VALUES (". $id_book .", ". $id_genres .")";
    $sql = $conn->prepare($sql); 
    $sql->execute();
}

/**
 * insert author
 * @param   mixed   $conn           db connection
 * @param   String  $username       username to look for
 * @return  Bool    if username exist in db return true
 */
function username_exist($conn, String $username){

    $sql = "SELECT * FROM `user` WHERE `username` = '". $username ."'";
    $sql = $conn->prepare($sql);
    $numrows = $sql->execute();
    $row = $sql->fetch();
    if($row["username"] == $username){
        return true;
    }else {
        return false;
    }
}

/**
 * insert author
 * @param   mixed   $conn           db connection
 * @param   String  $f_name         first name
 * @param   String  $l_name         last name
 * @param   String  $username       username
 * @param   String  $password       password
 */
function add_user($conn, string $f_name, string $l_name, string $username, string $password){
    date_default_timezone_set('Europe/Prague');
    $datetime = date("Y-m-d H:i:s");
    $password = hash_password($password);
    $sql = "INSERT INTO `user`(`f_name`, `l_name`, `username`, `password`, `last_login`, `ceated`) VALUES ('".$f_name."', '".$l_name."', '".$username."', '".$password."', '".$datetime."', '".$datetime."')";
    $sql = $conn->prepare($sql); 
    $sql->execute();
    
}

/**
 * check if login is ok
 * @param   mixed   $conn           db connection
 * @param   String  $username       username
 * @param   String  $password       password
 * @return  Bool    if true you can login
 */
function login($conn, string $username, string $password){
    date_default_timezone_set('Europe/Prague');
    $datetime = date("Y-m-d H:i:s");
    $password = hash_password($password);

    $sql = "SELECT * FROM `user` WHERE `username` = '". $username ."' AND`password` = '". $password ."'";
    $sql = $conn->prepare($sql);
    $numrows = $sql->execute();
    $row = $sql->fetch();
    if($row["username"] == $username and $row["password"] == $password ){
        return true;
    }else {
        return false;
    }
    
}

/**
 * make hash from pasword
 * @param   String  $password       password
 * @return  String    if true you can login
 */
function hash_password(string $password){
    return hash("sha3-512", $password);
}