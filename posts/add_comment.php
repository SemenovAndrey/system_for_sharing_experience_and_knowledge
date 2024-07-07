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
        $comment = $conn->real_escape_string($_POST['comment']);
        $comment_date = date('Y-m-d');

        $sql = "INSERT INTO comments (post_id, user_id, comment, comment_date)
                VALUES ($post_id, (SELECT id FROM users WHERE username = '$username'), '$comment', '$comment_date')";
        $result = $conn->query($sql);

        if ($result) {
            header("Location: post.php?id=$post_id");
        } else {
            echo "Ошибка: " . $sql . "<br>" . $conn->error;
        }

        $conn->close();
    }
?>