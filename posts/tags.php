<?php
    session_start();
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Система обмена опытом и знаниями: Тэги</title>
        <link rel="stylesheet" href="../style.css">
    </head>
    <body>
        <?php
            require_once __DIR__ . '/../includes/header.php';
            require_once __DIR__ . '/../includes/navigation.php';

            if (!isset($conn)) {
                $conn = require_once __DIR__ . '/../db/dbconfig.php';
            }

            try {
                $conn->begin_transaction();

                if (isset($_GET['tag'])) {
                    $tag = htmlspecialchars($_GET['tag']);
                    echo "<div class='selected_tag'>Выбранный тэг: " . $tag . "</div>";
                    $sql_posts = "SELECT
                                    p.id AS id, p.title AS title, u.username AS username, p.date AS date, tap.tag AS tag
                                FROM posts AS p
                                LEFT JOIN tags_and_posts AS tap
                                    ON p.id = tap.post_id
                                LEFT JOIN users AS u
                                    ON p.user_id = u.id
                                WHERE tap.tag = '$tag'
                                    AND p.relevance = 1 AND u.relevance_id = 1
                                ORDER BY p.date DESC, p.id DESC;";
                    $result_posts = $conn->query($sql_posts);

                    if ($result_posts->num_rows > 0) {
                        echo "<div class='posts_list'>";
                        while ($row = $result_posts->fetch_assoc()) {
                            $post_id = $row['id'];
                            $username = $row['username'];
                            echo "<div class=post_preview>";
                            echo "<div class=post_text_preview id=title_post>";
                            echo "<a href='/posts/post.php?id=$post_id'>" . $row['title'] . "</a>";
                            echo "</div>";
                            echo "<div class=post_text_preview id=username_post>";
                            echo "<a href='/../user/profile.php?username=$username'>" . $row['username'] . "</a>";
                            echo "</div>";
                            $date = new DateTime($row["date"]);
                            $date = $date->format('d.m.Y');
                            echo "<div class=post_text_preview id=date_post>" . $date . "</div>";
                            echo "</div>";
                        }
                        echo "</div>";
                    }
                } else {
                    // присоединить таблицу с лайками и выводить самые залайканные
                    $sql_tags = "SELECT DISTINCT
                                tag, COUNT(*) AS quantity
                            FROM tags_and_posts as tap
                            GROUP BY tag
                            ORDER BY quantity DESC;";
                    $result = $conn->query($sql_tags);

                    if ($result->num_rows > 0) {
                        echo "<div class='tags_list'>";
                        echo "<div id='tags_title' style='left: 50% !important;'>Популярные тэги</div>";
                        while ($row = $result->fetch_assoc()) {
                            $tag_preview = $row['tag'];
                            echo "<div>";
                            echo "<div class='tags' id='tag_preview'>";
                            echo "<a href='../tags.php?tag=$tag_preview'>" . $tag_preview . "</a>";
                            echo "</div>";
                            echo "</div>";
                        }
                        echo "</div>";
                    }

                    $sql_all_tags = "SELECT DISTINCT
                                        id, tag
                                    FROM tags_and_posts as tap
                                    ORDER BY id DESC;";
                    $result_all_tags = $conn->query($sql_all_tags);

                    if ($result_all_tags->num_rows > 0) {
                        echo "<div class='all_tags_list'>";
//                        while ($row = $result_all_tags->fetch_assoc()) {
//                            echo "<div class=post_preview>";
//                            echo "<div class=post_text_preview id=title_post>";
//                            echo "<a href='../post.php?id=$post_id'>" . $row['title'] . "</a>";
//                            echo "</div>";
//                            echo "<div class=post_text_preview id=username_post>";
//                            echo "<a href='../user/profile.php?username=$username'>" . $row['username'] . "</a>";
//                            echo "</div>";
//                            $date = new DateTime($row["date"]);
//                            $date = $date->format('d.m.Y');
//                            echo "<div class=post_text_preview id=date_post>" . $date . "</div>";
//                            echo "</div>";
//                        }
                        echo "</div>";
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