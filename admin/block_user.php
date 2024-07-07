<?php
    session_start();

    // проверка авторизован ли пользователь
    if (isset($_SESSION['username'])) {
        header("Location: ../index.php");
    }

    $current_username = $_SESSION['username'];
    $role_id = $_SESSION['role_id'];
    $get_username = trim($_GET['username']);

    if ($_SESSION['role_id'] != 1) {
        header("Location: ../index.php");
    }

    if (!isset($conn)) {
        $conn = require_once __DIR__ . '/../db/dbconfig.php';
    }

    if ($conn->connect_error) {
        die("Ошибка сервера: " . $conn->connect_error);
    }

    try {
        $conn->begin_transaction();

        $sql_check = "SELECT relevance_id FROM users WHERE username = '$get_username'";
        $result_check = $conn->query($sql_check);
        $relevance_id = $result_check->fetch_assoc()['relevance_id'];

        if ($relevance_id == 1) {
            // Обновляем relevance_id пользователя на 1 (разблокировка)
            $sql = "UPDATE users SET relevance_id = 2 WHERE username = '$get_username'";
            $result = $conn->query($sql);
        }

        $conn->commit();
        $conn->close();
    } catch (mysqli_sql_exception $ex) {
        $conn->rollback();
        echo "Ошибка: " . $ex->getMessage();
    }

    echo "<script>window.history.back()</script>";
