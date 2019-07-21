<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<?php
include "functions.php";
ini_set('max_execution_time', 0);
$configs = include('config.php');

echo '<form method="POST" action="">' . "\n";
echo '<input type="submit" name="import_to_db"  value="Import to DB">' . "\n";
echo '</form>';

$conn = connect_to_db($configs["servername"], $configs["dbname"], $configs["username"], $configs["password"]);

add_book($conn, "Stopařův průvodce po Galaxii", 1991, "CZ", "80-207-0229-6", "14", 304);



?>
</body>
</html>