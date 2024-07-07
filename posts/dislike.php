<?php
    session_start();

    if (!isset($_SESSION['username'])) {
        echo "<script>window.history.back();</script>";
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (!isset($conn)) {
            $conn = require_once __DIR__ . '/../db/dbconfig.php';
        }

        if ($conn->connect_error) {
            die("Ошибка сервера: " . $conn->connect_error);
        }

        $username = $_SESSION['username'];
        $post_id = $_POST['post_id'];

        $sql_check = "SELECT
                            post_id, username, is_liked, is_disliked
                        FROM reactions_on_posts AS r
                        JOIN users AS u
                            ON r.user_id = u.id
                        WHERE post_id = $post_id;";
        $result_check = $conn->query($sql_check);

        if ($result_check->num_rows > 0) {
            $row = $result_check->fetch_assoc();
            $is_liked = $row['is_liked'];
            $is_disliked = $row['is_disliked'];

            if ($is_disliked == 1) {
                $sql = "UPDATE reactions_on_posts
                        SET is_disliked = 0
                        WHERE post_id = '$post_id'
                          AND user_id = (SELECT id FROM users WHERE username = '$username')";
            } else {
                if ($is_liked == 0) {
                    $sql = "UPDATE reactions_on_posts
                        SET is_disliked = 1
                        WHERE post_id = '$post_id'
                          AND user_id = (SELECT id FROM users WHERE username = '$username')";
                } else if ($is_liked == 1) {
                    $sql = "UPDATE reactions_on_posts
                        SET is_liked = 0, is_disliked = 1
                        WHERE post_id = '$post_id'
                          AND user_id = (SELECT id FROM users WHERE username = '$username')";
                }
            }
        } else {
            $sql = "INSERT INTO reactions_on_posts (post_id, user_id, is_liked, is_disliked)
                    VALUES ($post_id, (SELECT id FROM users WHERE username = '$username'), 0, 1)";
        }

        if ($conn->query($sql) === TRUE) {
            header("Location: post.php?id=$post_id");
        } else {
            echo "Ошибка: " . $sql . "<br>" . $conn->error;
        }

        $conn->close();
    }
?>
