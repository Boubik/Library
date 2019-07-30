<?php
use function PHPSTORM_META\elementType;

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

    $sql = $conn->prepare("SET character SET UTF8");
    $sql->execute();

    return $conn;
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
function add_book($conn, string $name, int $relase, string $language, string $ISBN, string $room_name, int $pages, string $img){

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

    $sql = "INSERT INTO `book` (`name`, `relase`, `language`, `ISBN`, `room_name`, `pages`, `img`) VALUES ('". $name ."', '". $relase ."-01-01', '". $language ."', '". $ISBN ."', '". $room_name ."', ". $pages .", '" . $img . "')";
    echo $sql;
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
 * add ids to table book_has_author
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
 * add genres
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
 * insert genres
 * @param   mixed   $conn           db connection
 * @param   String  $name           name to insert
 */
function insert_genres($conn, string $name){
    $sql = "INSERT INTO `genres`(`name`) VALUES ('". $name ."')";
    $sql = $conn->prepare($sql);
    $sql->execute();
}

/**
 * get genres id from db
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
 * add ids to table book_has_genres
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
 * check if user exist
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
 * add user to db
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
 * @param   Bool    check if is moderator or admin
 * @return  Bool    if true you can login
 */
function login($conn, string $username, string $password, $mod_plus = false){
    date_default_timezone_set('Europe/Prague');
    $datetime = date("Y-m-d H:i:s");
    $password = hash_password($password);

    $sql = "SELECT * FROM `user` WHERE `username` = '". $username ."' AND`password` = '". $password ."'";
    $sql = $conn->prepare($sql);
    $numrows = $sql->execute();
    $row = $sql->fetch();
    if($row["username"] == $username and $row["password"] == $password ){
        if($mod_plus and !($row["role"] == "mod" or $row["role"] == "admin")){
            return false;
        }
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

/**
 * get ids from mn table
 * @param   Mixed   $conn           db
 * @param   String  $mn_table       mn table name
 * @param   Int     $id             id you want with
 * @param   String  $id_name        id name to check
 * @param   String  $id_get         id name what you want
 * @return  Array   return ids
 */
function mn($conn, string $mn_table, int $id, string $id_name, string $id_get){
    $ids = array();
    $mn_table = get_table($conn, $mn_table);

    foreach($mn_table as $item){
        if($item[$id_name] == $id){
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
function get_genre($conn, int $id){

    $sql = "SELECT `name` FROM `genres` WHERE `id` = ". $id;
    $sql = $conn->prepare($sql);
    $numrows = $sql->execute();
    $row = $sql->fetch();
    if($numrows > 0){
        return $row["name"];
    }else {
        return NULL;
    }
}

/**
 * get author
 * @param   Mixed   $conn       db connection
 * @param   Int     $id         id
 * @return  Array   return author
 */
function get_author($conn, int $id){

    $sql = "SELECT * FROM `author` WHERE `id` = ". $id;
    $sql = $conn->prepare($sql);
    $numrows = $sql->execute();
    $row = $sql->fetch();
    if($numrows > 0){
        return $row;
    }else {
        return NULL;
    }
}

/**
 * get book by id from db
 * @param   Mixed   $conn           db connection
 * @param   Int     $id             name to search in db
 * @return  Arry    info about book
 */
function get_book_by_id($conn, int $id){
    $sql = "SELECT * FROM `book` WHERE `id` = '". $id ."'";
    $sql = $conn->prepare($sql);
    $numrows = $sql->execute();
    if($numrows > 0){
        $row = $sql->fetch();
        return $row;
    }else {
        return NULL;
    }
}

/**
 * get reservation from db
 * @param   Mixed   $conn           db connection
 * @param   Int     $id             id of book
 * @return  Arry    info about reservation
 */
function get_reservations($conn, $id){
    $sql = "SELECT * FROM `reservation` WHERE `e-reservation` > CURTIME() ORDER BY `e-reservation`";
    $sql = $conn->prepare($sql);
    $numrows = $sql->execute();

    if($numrows > 0){
        $rows = array();
        while($row = $sql->fetch()){
            if($row["id"] == $id){
                $rows[] = $row;
            }
        }
        return $rows;
    }else {
        return NULL;
    }
}

/**
 * add reservation to db
 * @param   Mixed   $conn               db connection
 * @param   String  $s_reservation      start reservation date
 * @param   String  $e_reservation      end reservation date
 * @return  Bool    if reservation can be done
 */
function reservations($conn, $s_reservation, $e_reservation){
    $sql = "SELECT * FROM `reservation` WHERE `e-reservation` > CURTIME() ORDER BY `e-reservation`";
    $sql = $conn->prepare($sql);
    $numrows = $sql->execute();
    if($numrows > 0){
        while($row = $sql->fetch()){
            if( ( (strtotime($row["e-reservation"]) < strtotime($s_reservation) and strtotime($row["e-reservation"]) < strtotime($e_reservation) ) or strtotime($row["s-reservation"]) > strtotime($e_reservation) ) ){
            }else{
                return false;
            }
        }
    }
    add_reservations($conn, $s_reservation, $e_reservation);
    return true;
}

/**
 * add reservation to db
 * @param   Mixed   $conn               db connection
 * @param   String  $s_reservation      start reservation date
 * @param   String  $e_reservation      end reservation date
 * @return  Bool    if reservation can be done
 */
function add_reservations($conn, $s_reservation, $e_reservation){
    $id = get_user_id($conn, $_SESSION["username"]);
    $sql = "INSERT INTO `reservation`(`s-reservation`, `e-reservation`, `user_id`) VALUES ('".$s_reservation."', '".$e_reservation."' , $id)";
    $sql = $conn->prepare($sql); 
    $sql->execute();
}

/**
 * get usr id
 * @param   Mixed   $conn               db connection
 * @param   String  $username           username
 * @return  Int     id from user
 */
function get_user_id($conn, $username){
    $sql = "SELECT * FROM `user` WHERE `username` = '" . $username . "'";
    $sql = $conn->prepare($sql);
    $numrows = $sql->execute();
    if($numrows > 0){
        $row = $sql->fetch();
        return $row["id"];
    }
}

/**
 * get reservation id
 * @param   Mixed   $conn               db connection
 * @param   String  $s_reservation      start reservation date
 * @param   String  $e_reservation      end reservation date
 * @return  Int     id from reservation
 */
function get_reservation_id($conn, $s_reservation, $e_reservation){
    $sql = "SELECT * FROM `reservation` WHERE `s-reservation` = '". $s_reservation ."' AND `e-reservation` = '". $e_reservation ."'";
    $sql = $conn->prepare($sql);
    $numrows = $sql->execute();
    if($numrows > 0){
        $row = $sql->fetch();
        return $row["id"];
    }
}

/**
 * add ids to table book_has_reservation
 * @param   mixed   $conn           db connection
 * @param   int     $id_book        id of book
 * @param   int     $id_reservation id of reservation
 */
function add_book_has_reservation($conn, int $id_book, int $id_reservation){
    $sql = "INSERT INTO `book_has_reservation`(`book_id`, `reservation_id`) VALUES (". $id_book .", ". $id_reservation .")";
    $sql = $conn->prepare($sql); 
    $sql->execute();
}

/**
 * search
 * @param   mixed   $conn           db connection
 * @param   String  $search         search in db
 * @return  Array   array
*/
function book($conn, String $search = ""){
    if($search == ""){
        $sql = "SELECT book.id AS 'book_id', book.name AS 'book_name', book.relase, book.language, book.ISBN, book.pages, book.img, book.room_name, genres.id AS 'genres_id', genres.name AS 'genres_name', author.id AS 'author_id', author.f_name, author.l_name, room.name AS 'room_name' FROM book INNER JOIN book_has_genres ON book_has_genres.book_id = book.id INNER JOIN genres ON genres.id = book_has_genres.genres_id INNER JOIN book_has_author ON book_has_author.book_id = book.id INNER JOIN room ON room.name = room_name INNER JOIN author ON author.id = book_has_author.author_id ORDER BY book.name";
    }else{
        $sql = "SELECT book.id AS 'book_id', book.name AS 'book_name', book.relase, book.language, book.ISBN, book.pages, book.img, book.room_name, genres.id AS 'genres_id', genres.name AS 'genres_name', author.id AS 'author_id', author.f_name, author.l_name, room.name AS 'room_name' FROM book INNER JOIN book_has_genres ON book_has_genres.book_id = book.id INNER JOIN genres ON genres.id = book_has_genres.genres_id INNER JOIN book_has_author ON book_has_author.book_id = book.id INNER JOIN room ON room.name = room_name INNER JOIN author ON author.id = book_has_author.author_id WHERE book.room_name = room.name AND book.id = book_has_genres.book_id AND book_has_genres.genres_id = genres.id AND book_has_author.author_id = author.id AND book.id = book_has_author.book_id AND (book.room_name LIKE '%". $search ."%' OR book.name LIKE '%". $search ."%' OR book.relase LIKE '%". $search ."%' OR book.language LIKE '%". $search ."%'OR book.ISBN LIKE '%". $search ."%'OR book.pages LIKE '%". $search ."%'OR author.f_name LIKE '%". $search ."%' OR author.l_name LIKE '%". $search ."%' OR author.bday LIKE '%". $search ."%' OR author.country LIKE '%". $search ."%' OR genres.name LIKE '%". $search ."%' OR room.name LIKE '%". $search ."%' OR author.bday LIKE '%Jana Hollanová%' OR author.country LIKE '%Jana Hollanová%' OR genres.name LIKE '%Jana Hollanová%' OR room.name LIKE '%Jana Hollanová%' OR CONCAT(author.f_name, ' ' , author.l_name) LIKE '%". $search ."%' OR CONCAT(author.l_name, ' ', author.f_name) LIKE '%". $search ."%')";
    }
    //echo $sql;
    $sql = $conn->prepare($sql);
    $numrows = $sql->execute();
    $search = false;
    if($numrows > 0){
        $rows = array();
        $rows2 = array();
        while($row = $sql->fetch()){
            $rows[] = $row;
            $search = true;
        }

        if($search){
            $ids = array();
            foreach($rows as $value){
                if(!in_array($value["book_id"], $ids)){
                    $ids[] = $value["book_id"];
                }
            }
            foreach($ids as $id){
                $k[$id] = array();
                foreach($rows as $value){
                    if($value["book_id"] == $id){
                        if(!isset($k[$id]["genres_name"]) or !in_array($value["genres_name"], $k[$id]["genres_name"])){
                            $k[$id]["genres_name"][] = $value["genres_name"];
                        }
                        if(!isset($k[$id]["author"]) or !in_array(($value["f_name"] . " " . $value["l_name"]), $k[$id]["author"])){
                            $k[$id]["author"][] = $value["f_name"] . " " . $value["l_name"];
                        }
                    }
                }
            }
            foreach($k as $key => $value){
                foreach($rows as $item){
                    if($item["book_id"] == $key){
                        $item["author"] = $value["author"];
                        $item["genres_name"] = $value["genres_name"];
                        break;
                    }
                }
                $rows2[] = $item;
            }
            /*foreach($rows2 as $key => $value){
                echo $key. " ";
                print_r($value);
                echo "<br><br>";
            }*/
            return $rows2;
        }else{
            return NULL;
        }


    }
    return NULL;
}

/**
 * check if db is up to date or if exist
 */
function generate_db(){
    ini_set('max_execution_time', 0);
    $configs = include('config.php');
    $servername = $configs["servername"];
    $dbname = $configs["dbname"];
    $username = $configs["username"];
    $password = $configs["password"];
    $version = $configs["version"];
    $db = false;
    $update = false;

        //connect
        try {
            $conn = new PDO("mysql:host=".$servername.";dbname=".$dbname.";charset=utf8", $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
        catch(PDOException $e)
            {
                //connect
                try {
                    $conn = new PDO("mysql:host=".$servername.";charset=utf8", $username, $password);
                    // set the PDO error mode to exception
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    }
                catch(PDOException $e)
                    {
                    echo "Something goes worn give us time to fix it";
                    }
        
            $sql = $conn->prepare("SET character SET UTF8");
            $sql->execute();
            $db = true;
            }
        
            $sql = $conn->prepare("SET character SET UTF8");
            $sql->execute();

            if($db){
                $sql = $conn->prepare("CREATE SCHEMA IF NOT EXISTS `Library` DEFAULT CHARACTER SET utf8 ;USE `Library` ;");
                $sql->execute();
                $update = true;
            }else {
                try {
                    $sql = $conn->prepare("SELECT version.version FROM version ORDER BY version.version DESC");
                    $numrows = $sql->execute();
                    if($numrows > 0){
                        $row = $sql->fetch();
                        if($row["version"] < $version){
                            $update = true;
                        }
                    }else {
                        $update = true;
                    }
                    }
                catch(PDOException $e)
                    {
                        $update = true;
                    }
            }
            
            if($update){
                $fileList = glob('db/*.sql');
                $sql = load_file($fileList[0]);
                $sql = explode("USE `Library` ;", $sql);
                $sql = $sql[1];
                $sql = explode(";", $sql);
                foreach($sql as $item){
                    try {
                        $sql = $conn->prepare($item);
                        $sql->execute();
                        }
                    catch(PDOException $e)
                        {
                        }
                }
                $sql = "INSERT version VALUES ('".$version."')";
                $sql = $conn->prepare($sql);
                $sql->execute();
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
        $text = $text.$line;
    }
    return $text;
}