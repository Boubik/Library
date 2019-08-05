<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <link href="styles/index.css" rel="stylesheet" type="text/css">
    <link href="styles/header.css" rel="stylesheet" type="text/css">
    <link href="styles/footer.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="images/logo.ico">
    <script src="https://kit.fontawesome.com/350205fd30.js"></script>
    <title>Knihovna</title>
</head>
<body>
<?php
include "functions.php";
ini_set('max_execution_time', 0);
generate_db();
$configs = include('config.php');
$conn = connect_to_db($configs["servername"], $configs["dbname"], $configs["username"], $configs["password"]);
session_start();
if(isset($_GET["q"])){
    $books = book($conn, $_GET["q"]);
}else{
    $books = book($conn);
}

echo '<div id="header">';
    echo "<a href=\"/\"><image src=\"/images/logo_1.png\" style=\"height: 100px\"></a>";
        echo '<div id="inheader">';
        echo '<div id="monkaS">';
                echo '<form method="POST" action="">' . "\n";
                if(isset($_SESSION["username"]) and isset($_SESSION["password"]) and login($conn, $_SESSION["username"], $_SESSION["password"])){
                    if(login($conn, $_SESSION["username"], $_SESSION["password"], true)){
                        echo '<input type="submit" name="add_book"  value="Přidat knížku">' . "\n";
                        echo '<input type="submit" name="add_author"  value="Přidat autora">' . "\n";
                    }
                    echo '<input type="submit" name="profile"  value="Moje rezervace">' . "\n";
                    echo '<input type="submit" name="logout"  value="Odhlásit se">' . "\n";
                }else{
                    echo '<input type="submit" name="login"  value="Přihrásit se">' . "\n";
                }
                echo '</form>'. "\n";
            echo '</div>';

            echo '<div id="serch">';
                echo '<form method="POST" action="">' . "\n";
                echo '<input type="text" placeholder="Hledate neco?" name="q" value="';
                if(isset($_GET["q"])){
                    echo $_GET["q"].'">' . "\n";
                }else{
                    echo '">' . "\n";
                }
                echo '<input type="submit" name="search"  value="Hledat">' . "\n";
                echo '</form>'. "\n";
            echo '</div>';
        echo '</div>';
    echo '</div>';

if(isset($_POST["logout"])){
    unset($_SESSION["username"]);
    unset($_SESSION["password"]);
    header("Location: /index.php");
}

if(isset($_POST["login"])){
    header("Location: /login.php");
}

if(isset($_POST["add_book"])){
    header("Location: /add_book.php");
}

if(isset($_POST["add_author"])){
    header("Location: /add_author.php");
}

if(isset($_POST["profile"])){
    header("Location: /profile.php");
}

if(isset($_POST["search"]) and isset($_POST["q"]) and $_POST["q"] != ""){
    header("Location: /index.php?q=".$_POST["q"]);
}



echo "<div class=\"filtr\">";

    echo '<div id="side">';

        echo '<div id="no_clue">';
        echo '<div class="dropdown">';
            echo '<a id="category">Žánr</a>';
            echo '<div class="dropdown-content">';  
        $genres = get_table($conn, "genres");
        foreach($genres as $item){
            echo "<a href=\"/index.php?q=". $item["name"] ."\">".$item["name"]."</a><br>\n";
        }
        echo "</div>";
        echo "</div>";
        echo "</div>";

        echo '<div id="no_clue">';
        echo '<div class="dropdown">';
            echo '<a id="category">Autor</a>';
            echo '<div class="dropdown-content">';  
        $author = get_table($conn, "author");
        foreach($author as $item){
            echo "<a href=\"/index.php?q=". $item["f_name"]. " ". $item["l_name"] ."\">".$item["f_name"]. " ". $item["l_name"]."</a><br>\n";
        }
        echo "</div>";
        echo "</div>";
        echo "</div>";

        echo '<div id="no_clue">';
        echo '<div class="dropdown">';
            echo '<a id="category">Jazyk</a>';
            echo '<div class="dropdown-content">';   
        $language = get_table($conn, "book");
        $k = array();
        foreach($language as $item){
            if(!in_array($item["language"], $k)){
                echo "<a href=\"/index.php?q=". $item["language"] ."\">".$item["language"]."</a><br>\n";
                $k[] = $item["language"];
            }
        }
        echo "</div>";
        echo "</div>";
        echo "</div>";

        echo '<div id="no_clue">';
        echo '<div class="dropdown">';
            echo '<a id="category">Mistnost</a>';
            echo '<div class="dropdown-content">';     
        $room = get_table($conn, "room");
        foreach($room as $item){
            echo "<a href=\"/index.php?q=". $item["name"] ."\">".$item["name"]."</a><br>\n";
        }
        echo "</div>";
        echo "</div>";
        echo '</div>';
    echo '</div>';

    echo '<div id="bookcon">';
        
    if(!isset($books[0])){
        echo '<div id="warning>';
            echo "Žádná taková knížka tu není<br>\n";
        echo "</div>";
    }else{
        foreach($books as $book){
            echo "<a href=\"/book.php?id=". $book["book_id"] ."&name=". $book["book_name"] ."\"><div class=\"book\">";

            echo "<div class=\"name\">";
                echo $book["book_name"];
            echo "</div>";


            $status = "free";
            $k = get_table($conn, "reservation");
            foreach($k as $reservation){
                if($reservation["user_id"] == $book["book_id"]){
                    if(strtotime($reservation["e-reservation"]) > strtotime('-' . 1 . ' days') and strtotime($reservation["s-reservation"]) < strtotime('-'. 0 . ' days')){
                        $status = "booked";
                        break;
                    }
                }
            }
            echo "<div class=\"status\" id=\"" . $status . "\"></div>";


            echo '<div id="img">';
                echo "<img src=\"". $book["img"] ."\">";
            echo "</div>";

            echo '<div id="info">';
                echo "<div class=\"class\">";
                echo "Místnost: ".$book["room_name"];
                echo "</div>";

                echo "<div class=\"language\">";
                echo "Jazyk: ".$book["language"];
                echo "</div>";

                echo "<div class=\"genres\">";
                $genres = NULL;
                foreach($book["genres_name"] as $value){
                    if($genres == NULL){
                        $genres = $value;
                    }else{
                        $genres = $genres. ", ". $value;
                    }
                }
                    echo "Žánr: ".$genres;
                echo "</div>";

                echo "<div class=\"author\">";
                $author = NULL;
                foreach($book["author"] as $value){
                    if($author == NULL){
                        $author = $value;
                    }else{
                        $author = $author. ", ". $value;
                    }
                }
                    echo "Napsal: ".$author;
                echo "</div>";
            echo "</div>";

    echo "</div></a>";
    }
}
echo "</div>";
echo "</div>";


echo '<div id="footer">';
    echo '<div id="footercon">';
        echo '<div id="social">';
            echo '<a href="https://www.facebook.com/skolavdf/?ref=bookmarks" target="_blank" class="fab fa-facebook-f"></a>';
            echo '<a href="https://www.instagram.com/skolavdf/" target="_blank" class="fab fa-instagram"></a>';
        echo "</div>";
        echo '<div id="splitter"></div>';
        echo '<div id="team">';
        echo'Code: Jan Chlouba <br>';
        echo'Designe: Ibrahim Daghstani';
        echo "</div>";
    echo "</div>";
echo "</div>";
    
?>
</body>
</html>