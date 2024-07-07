<?php
    session_start();

    if (!isset($conn)) {
        $conn = require_once __DIR__ . '/../db/dbconfig.php';
    }

    if ($conn->connect_error) {
        die("Ошибка сервера: " . $conn->connect_error);
    }

    if (!isset($_SESSION['username'])) {
        header("Location: ../index.php");
    }

    $author_username = $_GET['username'];
    $subscriber_username = $_SESSION['username'];

    echo $author_username;
    echo $subscriber_username;

    if ($subscriber_username == $author_username) {
        header("Location: ../index.php");
    }

    $sql_check = "SELECT s.id, u1.username AS subscriber, u2.username AS author
                FROM subscribes_on_users AS s
                JOIN users AS u1
                    ON s.subscriber_id = u1.id
                JOIN users AS u2
                    ON s.author_id = u2.id
                WHERE u1.username = '$subscriber_username' AND u2.username = '$author_username';";
    $result = $conn->query($sql_check);

    $check_subscribe = true;
    if($result->num_rows > 0) {
        $check_subscribe = false;
    }

    if ($check_subscribe) {
        $sql_insert = "INSERT INTO subscribes_on_users(subscriber_id, author_id)
                    VALUES ((SELECT id FROM users WHERE username = '$subscriber_username'),
                            (SELECT id FROM users WHERE username = '$author_username'));";
        $result_insert = $conn->query($sql_insert);
    }

    $conn->commit();
    $conn->close();

    echo "<script>window.history.back();</script>";
