<?php
    session_start();

    // Проверка роли пользователя
    if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
        // Если пользователь не администратор, перенаправляем на главную страницу
        header("Location: /");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['reporter_id']) && isset($_POST['author_id'])) {
            $reporter_id = intval($_POST['reporter_id']);
            $author_id = intval($_POST['author_id']);

            if (!isset($conn)) {
                $conn = require_once __DIR__ . '/../db/dbconfig.php';
            }

            if ($conn->connect_error) {
                die("Ошибка сервера: " . $conn->connect_error);
            }

            try {
                $conn->begin_transaction();

                $sql = "UPDATE user_reports
                        SET is_current = 0
                        WHERE reporter_id = $reporter_id
                            AND author_id = $author_id";
                $result = $conn->query($sql);

                $conn->commit();
                $conn->close();
            } catch (mysqli_sql_exception $ex) {
                $conn->rollback();
                echo "Ошибка: " . $ex->getMessage();
            }
        } else {
            echo "Ошибка: недостаточно данных для закрытия репорта.";
        }
    } else {
        echo "Неверный метод запроса.";
    }

    echo "<script>window.history.back()</script>";
?>