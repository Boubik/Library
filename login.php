<!DOCTYPE html>

<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prihlášení</title>
    <link rel="stylesheet" type="text/css" href="styles/frontend.css">
    <link rel="shortcut icon" href="images/skola_logo_mono.png" type="image/x-icon" />
    <script src="js/350205fd30.js"></script>
    <script src="js/jquery-2.1.1.min.js" type="text/javascript"></script>
    <script src="js/sha3.js"></script>
</head>

<body style="overflow:hidden;">
    <?php
    include "functions.php";
    ini_set('max_execution_time', 0);
    $configs = include 'config.php';
    date_default_timezone_set('Europe/Prague');
    $conn = connect_to_db($configs["servername"], $configs["dbname"], $configs["username"], $configs["password"]);
    session_start();
    echo '<div class="logcon">';
    echo '<div id="logincon">';
    if (isset($_SESSION["username"]) and isset($_SESSION["password"]) and login($conn, $_SESSION["username"], $_SESSION["password"]) and !isset($_GET["reset"])) {
        header("Location: index.php");
    } else {

        if (!isset($_GET["reset"])) {

            if (!isset($_GET["register"])) {

                echo '<a href="index.php"><img src="images/skola_logo_mono.png" alt="logo"></a>';
                echo '<form class="form-signin" method="POST" role="form" action="">';
                echo '<input type="text" name="username" class="form-control" placeholder="Přezdívka" required autofocus><br>';
                echo '<input type="hidden" id="passwordHMAC" name="passwordHMAC" value="">';
                echo '<div id="form">';
                echo '<input type="password" id="password" placeholder="Heslo" name="password" class="form-control" required><br>';
                echo "<br>";
                echo '</div>';
                echo '<input type="submit" name="login"  value="Přihlásit se">' . "\n";
                echo '</form>' . "\n";

                echo '<div id="swap">';
                echo 'mate učet?<a href="login.php?register=yes"> Zaregistruj se!</a>';
                echo '</div>';
            } else {
                echo '<a href="index.php"><img src="images/skola_logo_mono.png" alt="logo"></a>';
                echo '<form class="form-signin" method="POST" role="form" action="">';
                echo '<input type="text" maxlength="45" name="f_name" placeholder="Jméno" required><br>';
                echo '<input type="text" maxlength="45" name="l_name" placeholder="Přímení" required><br>';
                echo '<input type="text" name="username" placeholder="Přezdívka" required><br>';
                echo '<input type="password" id="password" name="password" class="form-control" placeholder="Heslo" required><br>' . "\n";
                echo '<input type="hidden" id="passwordHMAC" name="passwordHMAC" value="">';
                echo "<br>";
                echo '<input type="submit" name="register"  value="Registrovat">' . "\n";
                echo '</form>' . "\n";
                echo '<div id="swap">';
                echo 'mate učet?<a href="login.php"> Přihlaš se!</a>';
                echo '</div>';
            }
        } else {

            echo "Reset hesla pro uživatele \"" . $_SESSION["username"] . "\"";
            echo '<form class="form-signin" method="POST" role="form" action="">';
            echo '<input id="reset" type="password" maxlength="45" name="old_pass" placeholder="staré heslo"><br>';
            echo '<input id="reset" id="password" type="password" maxlength="45" class="form-control" name="password" placeholder="nové heslo"><br>';
            echo '<input type="hidden" id="passwordHMAC" name="passwordHMAC" value="">';
            echo '<input id="reset" type="password" maxlength="45" name="new2_pass" placeholder="potvrzení nové heslo"><br>';
            echo "<br>";
            echo '<input id="konec" type="submit" name="reset"  value="Změnit">' . "\n";
            echo '</form>' . "\n";
        }
    }
    echo '</div>';

    if (isset($_POST["reset"])) {
        if (login($conn, $_SESSION["username"], filter_input(INPUT_POST, "old_pass"))) {
            if ($_POST["new_pass"] == $_POST["new2_pass"]) {
                update_password($conn, $_SESSION["username"], filter_input(INPUT_POST, "passwordHMAC"));
                $_SESSION["password"] = filter_input(INPUT_POST, "new_pass");
                header("Location: profile.php");
            } else {
                echo "Hesla se neschodují";
            }
        } else {
            echo "Špatné heslo";
        }
    }

    if (isset($_POST["register"])) {
        if (!(username_exist($conn, filter_input(INPUT_POST, "username")))) {
            add_user($conn, filter_input(INPUT_POST, "f_name"), filter_input(INPUT_POST, "l_name"), filter_input(INPUT_POST, "username"), filter_input(INPUT_POST, "passwordHMAC"));
            echo "jsi přihlášený";
            $_SESSION["username"] = filter_input(INPUT_POST, "username");
            $_SESSION["password"] = filter_input(INPUT_POST, "passwordHMAC");
            header("Location: index.php");
        } else {
            echo '<div class="nicktaken">';
            echo "Přezdívka již existuje";
            echo '</div>';
        }
    }

    if (isset($_POST["login"])) {
        if (login($conn, filter_input(INPUT_POST, "username"), filter_input(INPUT_POST, "passwordHMAC"))) {
            echo "jsi přihlášený";
            $_SESSION["username"] = filter_input(INPUT_POST, "username");
            $_SESSION["password"] = filter_input(INPUT_POST, "passwordHMAC");
            header("Location: index.php");
        } else {
            echo '<div class="warning">';
            echo "Špatné přihlašovací údaje";
            echo '</div>';
            unset($_SESSION["username"]);
            unset($_SESSION["password"]);
        }
    }
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

    ?>
</body>
<script>
    function myFunction() {
        var x = document.getElementById("Log");
        var y = document.getElementById("Reg");
        if (x.style.display === "none") {
            x.style.display = "block";
            y.style.display = "none";
        } else {
            x.style.display = "none";
            y.style.display = "block";
        }
    }
</script>
    <script type="text/javascript">
        $('.form-signin').submit(function() {
            if ($("#password").val().length !== 0) {
                var hash = CryptoJS.SHA3($("#password").val(), {
                    outputLength: 512
                });
                $("#passwordHMAC").val(hash);
            } else {
                $("#passwordHMAC").val("");
            }
            $("#password").val("");
        });
    </script>


</html>