<?php
    if (!isset($conn)) {
        $conn = require_once __DIR__ . '/../db/dbconfig.php';
    }

    try {
        $conn->begin_transaction();

        // вывод постов
        $sql_posts = "SELECT p.id AS id, p.title AS title, p.date, p.post_text, p.relevance, u.username AS username,
                           u.role_id, u.relevance_id  FROM posts AS p
                    LEFT JOIN users AS u
                        ON p.user_id = u.id
                    WHERE p.relevance = 1 AND u.relevance_id = 1
                    ORDER BY id DESC
                    LIMIT 15;";
        $result_posts = $conn->query($sql_posts);

        if ($result_posts->num_rows > 0) {
            echo "<div class='posts_list'>";
            while ($row = $result_posts->fetch_assoc()) {
                $post_id = $row['id'];
                $username = $row['username'];
                echo "<div class=post_preview>";
                echo "<div class=post_text_preview id=title_post>";
                echo "<a href='../posts/post.php?id=$post_id'>" . $row['title'] . "</a>";
                echo "</div>";
                echo "<div class=post_text_preview id=username_post>";
                echo "<a href='../user/profile.php?username=$username'>" . $row['username'] . "</a>";
                echo "</div>";
                $date = new DateTime($row["date"]);
                $date = $date->format('d.m.Y');
                echo "<div class=post_text_preview id=date_post>" . $date . "</div>";
                echo "</div>";
            }
            echo "</div>";
        }

        // вывод тэгов
        $sql_tags = "SELECT DISTINCT
                        tag, COUNT(*) AS quantity
                    FROM tags_and_posts as tap
                    GROUP BY tag
                    ORDER BY quantity DESC
                    LIMIT 15;";
        $result_tags = $conn->query($sql_tags);

        if ($result_tags->num_rows > 0) {
            echo "<div class='tags_list'>";
            echo "<div id='tags_title'>Популярные тэги</div>";
            while ($row = $result_tags->fetch_assoc()) {
                $tag = $row['tag'];
                echo "<div>";
                echo "<div class='tag_preview' id='tag_preview'>";
                echo "<a href='../posts/tags.php?tag=$tag'>" . $tag . "</a>";
                echo "</div>";
                echo "</div>";
            }
            echo "</div>";
        }

        $conn->commit();
        $conn->close();
    } catch (mysqli_sql_exception $ex) {
        $conn->rollback();
    }
?>