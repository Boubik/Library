echo "<div class=\"filtr\">";

    echo '<div id="side">';

        echo '<div id="no_clue">';
        echo '<div class="dropdown">';
            echo '<a id="category" id="zanr">Žánr</a>';
            echo '<div class="dropdown-content" class="zanr">';  
        $genres = get_table($conn, "genres");
        foreach($genres as $item){
            echo "<a href=\"/index.php?q=". $item["name"] ."\">".$item["name"]."</a><br>\n";
        }
        echo "</div>";
        echo "</div>";
        echo "</div>";

        echo '<div id="no_clue">';
        echo '<div class="dropdown">';
            echo '<a id="category" id="autor">Autor</a>';
            echo '<div class="dropdown-content" class="autor">';  
        $author = get_table($conn, "author");
        foreach($author as $item){
            echo "<a href=\"/index.php?q=". $item["f_name"]. " ". $item["l_name"] ."\">".$item["f_name"]. " ". $item["l_name"]."</a><br>\n";
        }
        echo "</div>";
        echo "</div>";
        echo "</div>";

        echo '<div id="no_clue">';
        echo '<div class="dropdown">';
            echo '<a id="category" id="language">Jazyk</a>';
            echo '<div class="dropdown-content" class="language">';   
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
            echo '<a id="category" id="room">Mistnost</a>';
            echo '<div class="dropdown-content" class="room">';     
        $room = get_table($conn, "room");
        foreach($room as $item){
            echo "<a href=\"/index.php?q=". $item["name"] ."\">".$item["name"]."</a><br>\n";
        }
        echo "</div>";
        echo "</div>";

    echo '<div class="sideset">';

        echo 'řádků na strácne:';

        echo '<div class="sidesethidden">';
        echo '<form method="POST" action="/"><select id="sel" name="rows">' . "\n";
        $i = 1;
        while($i != 21){
            if($i == ($per_page/3)){
            echo '<option selected>';
            }else{
                echo '<option>';
            }
            echo $i .'</option>' . "\n";
            $i++;
        }
        echo '</select><br>' ;
        echo '<input type="submit" name="per_page"  value="nastavit">' . "\n";
        echo'</form>'. "\n";
        echo '</div>';
    echo '</div>';
    echo '</div>';
    echo "</div>";