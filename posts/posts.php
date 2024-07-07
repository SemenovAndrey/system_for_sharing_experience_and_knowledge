<?php
    session_start();
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Система обмена опытом и знаниями: Посты</title>
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

                $sql = "SELECT
                            p.id, p.title, p.date, p.relevance, u.username, COUNT(r.is_liked) AS likes
                        FROM posts AS p
                        LEFT JOIN users AS u
                            ON p.user_id = u.id
                        LEFT JOIN reactions_on_posts AS r
                            ON p.id = r.post_id
                        WHERE relevance = 1 AND u.relevance_id = 1
                        GROUP BY p.id, p.title, p.date, p.relevance, u.username
                        ORDER BY likes DESC, p.id DESC
                        LIMIT 20;";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    echo "<div class='posts_list'>";
                    while ($row = $result->fetch_assoc()) {
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
                    echo "</div>";
                }

                $conn->commit();
                $conn->close();
            } catch (mysqli_sql_exception $ex) {
                $conn->rollback();
            }
        ?>
    </body>
</html>