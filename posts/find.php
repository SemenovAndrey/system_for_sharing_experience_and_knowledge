<?php
    session_start();
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Система обмена опытом и знаниями</title>
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

            try {
                $conn->begin_transaction();

                $search = isset($_GET['search']) ? trim($_GET['search']) : '';
                $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

                // посты
                if ($filter == 'all' || $filter == 'posts') {
                    $sql_posts = "SELECT p.id AS id, p.title AS title, p.date, p.post_text, p.relevance, u.username AS username,
                                u.role_id, u.relevance_id  FROM posts AS p
                            LEFT JOIN users AS u
                                ON p.user_id = u.id
                            WHERE p.title LIKE '%$search%'
                                AND u.relevance_id = 1
                                AND p.relevance = 1
                            ORDER BY id DESC;";
                    $result_posts = $conn->query($sql_posts);

                    if ($result_posts->num_rows > 0) {
                        echo "<div class='list_title'>Найденные посты</div>";
                        while ($row = $result_posts->fetch_assoc()) {
                            $post_id = $row['id'];
                            $username = $row['username'];
                            echo "<div class=post_preview>";
                            echo "<div class=post_text_preview id=title_post>";
                            echo "<a href='/posts/post.php?id=$post_id'>" . $row['title'] . "</a>";
                            echo "</div>";
                            echo "<div class=post_text_preview id=username_post>";
                            echo "<a href='../user/profile.php?username=$username'>" . $row['username'] . "</a>";
                            echo "</div>";
                            $date = new DateTime($row["date"]);
                            $date = $date->format('d.m.Y');
                            echo "<div class=post_text_preview id=date_post>" . $date . "</div>";
                            echo "</div>";
                        }
                    }
                }

                // тэги
                if ($filter == 'all' || $filter == 'tags') {
                    $sql_tags = "SELECT
                                tag
                            FROM tags_and_posts AS tap
                            LEFT JOIN posts AS p
                                ON tap.post_id = p.id
                            LEFT JOIN users AS u
                                ON p.user_id = u.id
                            WHERE tag LIKE '%$search'
                              AND u.relevance_id = 1
                              AND p.relevance = 1;";
                    $result_tags = $conn->query($sql_tags);

                    if ($result_tags->num_rows > 0) {
                        echo "<div class='list_title' id='tags_preview'>Найденные тэги</div>";
                        while ($row = $result_tags->fetch_assoc()) {
                            $tag = $row['tag'];
                            echo "<div class='tags_preview'>";
                            echo "<a href='/posts/tags.php?tag=$tag'>" . $tag . "</a>";
                            echo "</div>";
                        }
                    }
                }

                // пользователи
                if ($filter == 'all' || $filter == 'users') {
                    $sql_tags = "SELECT
                                id, username
                            FROM users
                            WHERE username LIKE '%$search%'
                                AND relevance_id = 1
                            ORDER BY id DESC;";
                    $result_tags = $conn->query($sql_tags);

                    if ($result_tags->num_rows > 0) {
                        echo "<div class='list_title'>Найденные пользователи</div>";
                        while ($row = $result_tags->fetch_assoc()) {
                            $username = $row['username'];
                            echo "<div class='users_preview'>";
                            echo "<a href='/user/profile.php?username=$username'>" . $username . "</a>";
                            echo "</div>";
                        }
                    }
                }

                $conn->commit();
                $conn->close();
            } catch (mysqli_sql_exception $ex) {
                $conn->rollback();
            }
        ?>
    </body>
</html>