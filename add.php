<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>Add book</title>
</head>
<body>
<?php
include "functions.php";
ini_set('max_execution_time', 0);
$configs = include('config.php');
$conn = connect_to_db($configs["servername"], $configs["dbname"], $configs["username"], $configs["password"]);

echo '<form method="POST" action="">' . "\nNázev knihy";
echo '<input type="text" maxlength="45" name="name"><br>' . "\nRok vydán";
echo '<input type="number"  name="relase"><br>' . "\nJazyk";
echo '<input type="text" maxlength="2" name="language"><br>' . "\nISBN";
echo '<input type="text" name="ISBN"><br>' . "\nmístnost";

echo '<select name="room">' . "\n";
echo '<option></option>' . "\n";
foreach(get_table($conn, "room") as $row){
    echo '<option>'. $row["name"] .'</option>' . "\n";
}
echo '</select>'. " nebo ";

echo '<input type="text" name="room_name"><br>' . "\nPočet stran";

echo '<input type="number" name="pages"><br>' . "\nŽánry";
echo '<input type="text" maxlength="45" name="genres">(mezera rozděluje žánry)<br>' . "\n";
echo '<input type="submit" name="submit" value="Přidat">' . "\n";
echo '</form>'. "\n";


if(isset($_POST["submit"])){
    $genres = explode(" ", $_POST["genres"]);
    add_genres($conn, $genres);
    $gendrs_get = NULL;
    foreach($genres as $item){
        $item = get_genres_id($conn, $item);
        if($gendrs_get != NULL){
            $gendrs_get = $gendrs_get . "," . $item;
        }else{
            $gendrs_get =  $item;
        }
    }
    if($_POST["room_name"] != ""){
        $room = $_POST["room_name"];
    }else{
        $room = $_POST["room"];
    }
    add_book($conn, $_POST["name"], $_POST["relase"], $_POST["language"], $_POST["ISBN"], $room, $_POST["pages"]);
    header("Location: /book_to author.php?name=" . $_POST["name"] . "&relase=" . $_POST["relase"] . "&language=" . $_POST["language"] . "&ISBN=" . $_POST["ISBN"] . "&room_name=" . $room . "&pages=" . $_POST["pages"] . "&genres=" . $gendrs_get);
}



?>
</body>
</html>