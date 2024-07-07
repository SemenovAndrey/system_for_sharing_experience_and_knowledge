<?php
    session_start();
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Система обмена опытом и знаниями: Заблокированные посты</title>
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
            echo "<button class='back_button' onclick='goBack()'>Назад</button>";

            if (!isset($conn)) {
                $conn = require_once __DIR__ . '/../db/dbconfig.php';
            }

            if ($conn->connect_error) {
                die("Ошибка сервера: " . $conn->connect_error);
            }

            // Проверка роли пользователя
            if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) { // Если пользователь администратор
                try {
                    $sql = "SELECT p.id, p.title, p.post_text, p.date, u.username
                                        FROM posts AS p
                                        LEFT JOIN users AS u ON p.user_id = u.id
                                        WHERE p.relevance = 2
                                        ORDER BY p.date DESC;";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        echo "<div class='blocked_posts_container'>";
                        echo "<div class='list_title'>Заблокированные посты</div>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<div class='blocked_post'>";
                            $post_id = $row['id'];
                            $title = $row['title'];
                            echo "<p>";
                            echo "<a href='/../posts/post.php?id=$post_id'><strong>Заголовок</strong>: $title</a>";
                            echo "</p>";
                            $username = $row['username'];
                            echo "<a href='/../user/profile.php?username=$username'><strong>Автор</strong>: $username</a>";
                            $date = new DateTime($row["date"]);
                            $date = $date->format('d.m.Y');
                            echo "<p><strong>Дата создания:</strong> " . $date . "</p>";
                            echo "<p><strong>Текст поста:</strong> " . htmlspecialchars($row['post_text']) . "</p>";
                            echo "<form action='/admin/unblock_post.php?id=$post_id' method='post'>";
                            echo "<input type='hidden' name='post_id' value='" . htmlspecialchars($row['id']) . "'>";
                            echo "<input class='action_button' type='submit' value='Разблокировать'>";
                            echo "</form>";
                            echo "</div>";
                        }
                        echo "</div>";
                    } else {
                        echo "<div class='blocked_posts_container'>";
                        echo "<p>Заблокированные посты отсутствуют.</p>";
                        echo "</div>";
                    }

                    $conn->close();
                } catch (mysqli_sql_exception $ex) {
                    echo "Ошибка: " . $ex->getMessage();
                }
            } else {
                echo "<div class='blocked_posts_container'>";
                echo "<p>У вас нет прав для просмотра этой страницы.</p>";
                echo "</div>";
            }
        ?>
    </body>
</html>
