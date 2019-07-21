<?php

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
        $sql = "INSERT INTO `room`(`name`) VALUES (". $room_name .")";
        $sql = $conn->prepare($sql); 
        $sql->execute();
    }

    $sql = "INSERT INTO `book` (`name`, `relase`, `language`, `ISBN`, `room_name`, `pages`) VALUES ('". $name ."', '". $relase ."-01-01', '". $language ."', '". $ISBN ."', '". $room_name ."', ". $pages .")";
    $sql = $conn->prepare($sql); 
    $sql->execute();
    
}