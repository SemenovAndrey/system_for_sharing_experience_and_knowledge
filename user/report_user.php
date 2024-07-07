<?php
    session_start();
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Система обмена опытом и знаниями</title>
        <link rel="stylesheet" href="../style.css">
        <script>
            function goBack() {
                window.history.back();
            }
        </script>
    </head>
    <body>
        <?php
            require_once __DIR__ . '/../includes/header.php';

            $reported_user = $_GET['username'];
            if (!isset($_SESSION['username'])) {
                header("Location: ../index.php");
            }
            $reporter = $_SESSION['username'];

            echo "<button class='back_button' onclick='goBack()'>Назад</button>";

            echo "<div class='report_form'>";
            echo "<form action='/user/do_report_user.php' method='post' style='padding: 5px'>";
            echo "<input type='hidden' name='author_username' value='" . $reported_user . "'>";
            echo "<label for='reason'>Причина:</label><br>";
            echo "<textarea name='reason' id='reason' required style='width: 400px; height: 200px'></textarea><br>";
            echo "<input class='action_profile_button' type='submit' value='Пожаловаться'>";
            echo "</form>";
            echo "</div>";
        ?>
    </body>
</html>