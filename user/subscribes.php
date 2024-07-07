<?php
    session_start();
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Система обмена опытом и знаниями: Подписки</title>
        <link rel="stylesheet" href="../style.css">
    </head>
    <body>
        <?php
            require_once __DIR__ . '/../includes/header.php';
            require_once __DIR__ . '/../includes/navigation.php';

            if (!isset($conn)) {
                $conn = require_once __DIR__ . '/../db/dbconfig.php';
            }

            if ($conn->connect_error) {
                die("Ошибка сервера: " . $conn->connect_error);
            }

            // проверка авторизован ли пользователь
            if (isset($_SESSION['username'])) {
                $current_username = $_SESSION['username'];
            }

            $current_user = $_SESSION['username'];
            $sql = "SELECT
                        s.id AS id, u1.username AS subscriber, u2.username AS author
                    FROM subscribes_on_users AS s
                    INNER JOIN users AS u1
                        ON s.subscriber_id = u1.id
                    INNER JOIN users AS u2
                        ON s.author_id = u2.id
                    WHERE u1.username = '$current_user' AND u1.relevance_id = 1 AND u2.relevance_id = 1;";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<div class='subscribe_list'>";
                echo "<p style='text-align: center; font-size: 23px; border-bottom: 1px solid black; display: inline-block'>Подписки</p>";
                while ($row = $result->fetch_assoc()) {
                    $author = $row['author'];
                    echo "<div class='profile_info'>";
                    echo "<a href='/../user/profile.php?username=$author' class='subscribes_preview'> Имя пользователя: $author </a>";
                    echo "</div>";
                }
                echo "</div>";
            }

            $conn->commit();
            $conn->close();
        ?>
    </body>
</html>