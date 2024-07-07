<?php
    session_start();
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Система обмена опытом и знаниями: Профиль</title>
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
            $get_username = trim($_GET['username']);

            try {
                $sql_user = "SELECT username, email, registration_date, relevance_id FROM users WHERE username = '$get_username'";
                $result_user = $conn->query($sql_user);

                if ($result_user->num_rows > 0) {
                    $user = $result_user->fetch_assoc();
                    echo "<div class='profile_info'>";
                    echo "<div class='info_about_user'>";
                    echo "<h2>Профиль пользователя: " . htmlspecialchars($user['username']) . "</h2>";
                    echo "<p>Email: " . htmlspecialchars($user['email']) . "</p>";
                    $registration_date = new DateTime($user["registration_date"]);
                    $registration_date = $registration_date->format('d.m.Y');
                    echo "<p>Дата регистрации: " . $registration_date . "</p>";
                    echo "</div>";
                    if (isset($current_username)) {
                        $role_id = $_SESSION['role_id'];
                        if ($get_username !== $current_username) {
                            $sql_check_sub = "SELECT s.id, u1.username AS subscriber, u2.username AS author
                                                FROM subscribes_on_users AS s
                                                JOIN users AS u1
                                                    ON s.subscriber_id = u1.id
                                                JOIN users AS u2
                                                    ON s.author_id = u2.id
                                                WHERE u1.username = '$current_username'
                                                  AND u2.username = '$get_username';";
                            $result_check = $conn->query($sql_check_sub);

                            echo "<div class='profile_actions' id='user_actions'>";
                            if ($result_check->num_rows > 0) {
                                echo "<form action='/user/unsubscribe.php?username=$get_username' method='post' style='padding: 5px'>";
                                echo "<input class='action_profile_button' type='submit' value='Отписаться'>";
                                echo "</form>";
                            } else {
                                echo "<form action='/user/subscribe.php?username=$get_username' method='post' style='padding: 5px'>";
                                echo "<input class='action_profile_button' type='submit' value='Подписаться'>";
                                echo "</form>";
                            }

                            if ($role_id == 2) {
                                echo "<form action='/user/report_user.php?username=$get_username' method='post'>";
                                echo "<input class='action_profile_button' type='submit' value='Пожаловаться'>";
                                echo "</form>";
                                echo "</div>";
                            } else {
                                $user_relevance = $user['relevance_id'];
                                if ($user_relevance == 1) {
                                    echo "<form action='/admin/block_user.php?username=$get_username' method='post'>";
                                    echo "<input class='action_profile_button' type='submit' value='Заблокировать'>";
                                    echo "</form>";
                                    echo "</div>";
                                } else if ($user_relevance == 2) {
                                    echo "<form action='/admin/unblock_user.php?username=$get_username' method='post'>";
                                    echo "<input class='action_profile_button' type='submit' value='Разблокировать'>";
                                    echo "</form>";
                                    echo "</div>";
                                }
                            }
                        } else {
                            if ($role_id == 1) {
                                echo "<div class='profile_actions' id='admin_actions'>";
                                echo "<a href='/admin/blocked_users.php' class='admin_profile_button'>Заблокированные пользователи</a>";
                                echo "<a href='/admin/blocked_posts.php' class='admin_profile_button'>Заблокированные посты</a>";
                                echo "<a href='/admin/reports.php' class='admin_profile_button'>Репорты</a>";
                                echo "</div>";
                            }
                        }
                    }
                    echo "</div>";

                    $sql_posts = "SELECT *
                                FROM users
                                LEFT JOIN posts
                                    ON users.id = posts.user_id
                                WHERE username = '$get_username'
                                    AND posts.relevance = 1;";
                    $result_posts = $conn->query($sql_posts);

                    if ($result_posts->num_rows > 0) {
                        while ($row = $result_posts->fetch_assoc()) {
                            // Отображение постов пользователя
                            if ($row['id']) {
                                $post_id = $row['id'];
                                $username = $row['username'];
                                echo "<div class=post_preview>";
                                echo "<div class=post_text_preview id=title_post>";
                                echo "<a href='/posts/post.php?id=$post_id'>" . $row['title'] . "</a>";
                                echo "</div>";
                                $date = new DateTime($row["date"]);
                                $date = $date->format('d.m.Y');
                                echo "<div class=post_text_preview id=date_post>" . $date . "</div>";
                                echo "</div>";
                            }
                        }
                    }
                }

                echo "<button class='back_button' onclick='goBack()'>Назад</button>";

                $conn->commit();
                $conn->close();
            } catch (mysqli_sql_exception $ex) {
                $conn->rollback();
                echo "Ошибка: " . $ex->getMessage();
            }
        ?>
    </body>
</html>