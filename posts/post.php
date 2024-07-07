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

            if (!isset($conn)) {
                $conn = require_once __DIR__ . '/../db/dbconfig.php';
            }

            if ($conn->connect_error) {
                die("Ошибка сервера: " . $conn->connect_error);
            }

            try {
                $conn->begin_transaction();

                $post_id = $_GET["id"];
                $sql = "SELECT * FROM posts AS p
                        LEFT JOIN users AS u
                            ON p.user_id = u.id
                        LEFT JOIN tags_and_posts AS tap
                            ON p.id = tap.post_id
                        WHERE p.id=$post_id";
                $result = $conn->query($sql);

                $is_added = false;
                $counter = 0;
                $num_of_rows = $result->num_rows;
                $post_relevance = 1;
                while ($row = $result->fetch_assoc()) {
                    if (!$is_added) {
                        $username = $row['username'];
                        echo "<div class='post'>";
                        echo "<h1>" . htmlspecialchars($row['title']) . "</h1>";
                        echo "<div class='post-content'>" . htmlspecialchars($row['post_text']) . "</div>";
                        echo "<a href='/user/profile.php?username=$username'><strong>Автор:</strong> $username</a>";
                        echo "<div class='post-tags'><strong>Теги:</strong> ";
                        $post_relevance = $row['relevance'];
                        $is_added = true;
                    }

                    $counter++;
                    $tag = $row['tag'];
                    if ($counter == $num_of_rows) {
                        echo "<a href='/posts/tags.php?tag=$tag'> $tag </a>";
                    } else {
                        echo "<a href='/posts/tags.php?tag=$tag'> $tag / </a>";
                    }
                }
                echo "</div>";

                if (isset($_SESSION['username'])) {
                    $sql_reactions = "SELECT
                                        SUM(is_liked) AS likes, SUM(is_disliked) AS dislikes
                                    FROM reactions_on_posts
                                    WHERE post_id = $post_id";
                    $result_reactions = $conn->query($sql_reactions);
                    $reactions = $result_reactions->fetch_assoc();
                    $likes = $reactions['likes'];
                    $dislikes = $reactions['dislikes'];

                    // Лайки и дизлайки
                    echo "<div class='post-actions'>";
                    echo "<form action='/posts/like.php' method='post' class='like-form'>";
                    echo "<input type='hidden' name='post_id' value='$post_id'>";
                    if ($likes == 0) {
                        echo "<button type='submit' name='like'>👍 0</button>";
                    } else {
                        echo "<button type='submit' name='like'>👍 $likes</button>";
                    }
                    echo "</form>";
                    echo "<form action='/posts/dislike.php' method='post' class='dislike-form'>";
                    echo "<input type='hidden' name='post_id' value='$post_id'>";
                    if ($dislikes == 0) {
                        echo "<button type='submit' name='dislike'>👎 0</button>";
                    } else {
                        echo "<button type='submit' name='like'>👎 $dislikes</button>";
                    }
                    echo "</form>";
                    echo "</div>";

                    $role_id = $_SESSION['role_id'];
                    if ($role_id == 1) {
                        if ($post_relevance == 1) {
                            echo "<form action='/admin/block_post.php?id=$post_id' method='post'>";
                            echo "<input class='action_profile_button' type='submit' value='Заблокировать' style='margin-top: 20px;'>";
                            echo "</form>";
                        } else if ($post_relevance == 2) {
                            echo "<form action='/admin/unblock_post.php?id=$post_id' method='post'>";
                            echo "<input class='action_profile_button' type='submit' value='Разблокировать' style='margin-top: 20px;'>";
                            echo "</form>";
                        }
                    }

                    // Форма комментариев
                    echo "<div class='comment-section'>";
                    echo "<h2>Комментарии</h2>";
                    echo "<form action='/posts/add_comment.php' method='post' class='comment-form'>";
                    echo "<input type='hidden' name='post_id' value='$post_id'>";
                    echo "<textarea name='comment' rows='4' placeholder='Напишите комментарий' required></textarea><br>";
                    echo "<button type='submit'>Отправить</button>";
                    echo "</form>";
                    echo "<br>";
                }

                // Отображение комментариев
                $sql_comments = "SELECT u.username, c.comment, c.comment_date
                                FROM comments AS c
                                JOIN users AS u
                                    ON c.user_id = u.id
                                WHERE c.post_id = $post_id
                                ORDER BY c.comment_date DESC, c.id DESC;";
                $result_comments = $conn->query($sql_comments);

                if ($result_comments->num_rows > 0) {
                    while ($comment = $result_comments->fetch_assoc()) {
                        $username_comment = $comment['username'];
                        echo "<div class='comment'>";
                        echo "<p></p><a href='/user/profile.php?username=$username_comment'><strong>$username_comment: </strong></a>";
                        echo htmlspecialchars($comment['comment']) . "</p>";
                        echo "</div>";
                    }
                } else {
                    echo "<br><p>Пока нет комментариев</p>";
                }

                echo "</div>";

                echo "</div>";


                $conn->commit();
                $conn->close();
            } catch (mysqli_sql_exception $ex) {
                $conn->rollback();
                echo "Ошибка: " . $ex->getMessage();
            }
        ?>
    </body>
</html>
