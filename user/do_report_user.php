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
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
//        $reporter_username = $_SESSION['username'];
//        $author_username = $_POST['author_username'];
//        $reason = htmlspecialchars($_POST['reason']);
//
//        // Проверка, что пользователь не пытается пожаловаться на самого себя
//        if ($reporter_username == $author_username) {
//            header("Location: ../index.php");
//        }
//
//        $sql_check = "SELECT
//                        u1.username AS reporter, u2.username AS author
//                    FROM user_reports AS ur
//                    JOIN users AS u1
//                        ON u1.id = ur.reporter_id
//                    JOIN users AS u2
//                        ON u2.id = ur.author_id
//                    WHERE u1.username = '$reporter_username' AND u2.username = '$author_username'";
//        $result_check = $conn->query($sql_check);
//
//        if ($result_check->num_rows == 0) {
//            // Добавление нового репорта
//            $date = new DateTime();
//            $date = $date->format('Y-m-d');
//            $sql_insert_report = "INSERT INTO user_reports(reporter_id, author_id, reason, report_date, is_current)
//                                  VALUES ((SELECT id FROM users WHERE username = '$reporter_username'),
//                                          (SELECT id FROM users WHERE username = '$author_username'),
//                                          '$reason', $date, 1);";
//            $result_insert = $conn->query($sql_insert_report);
//
//            if ($result_insert) {
//                echo "Ваша жалоба успешно отправлена";
//            } else {
//                echo "Ошибка при отправке жалобы";
//            }
//        } else {
//            echo "Вы уже пожаловались на этого пользователя";
//        }
//
//        $conn->commit();
//        $conn->close();

        $reporter_username = $_SESSION['username'];
        $author_username = $_POST['author_username'];
        $reason = $conn->real_escape_string($_POST['reason']);
        $report_date = date('Y-m-d H:i:s');
        $is_current = 1;

        if ($reporter_username == $author_username) {
            header("Location: ../index.php");
            exit();
        }

        $reporter_username = $_SESSION['username'];
        $author_username = $_POST['author_username'];
        $reason = htmlspecialchars($_POST['reason']);

        // Проверка, что пользователь не пытается пожаловаться на самого себя
        if ($reporter_username == $author_username) {
            header("Location: ../index.php");
        }

        $sql_check = "SELECT
                        u1.username AS reporter, u2.username AS author
                    FROM user_reports AS ur
                    JOIN users AS u1
                        ON u1.id = ur.reporter_id
                    JOIN users AS u2
                        ON u2.id = ur.author_id
                    WHERE u1.username = '$reporter_username' AND u2.username = '$author_username'";
        $result_check = $conn->query($sql_check);

        if ($result_check->num_rows == 0) {
//            $sql_reporter_id = "SELECT id FROM users WHERE username = '$reporter_username'";
//            $result_reporter_id = $conn->query($sql_reporter_id);
//            $reporter_id = $result_reporter_id->fetch_assoc()['id'];
//
//            $sql_author_id = "SELECT id FROM users WHERE username = '$author_username'";
//            $result_author_id = $conn->query($sql_author_id);
//            $author_id = $result_author_id->fetch_assoc()['id'];

            $sql_insert = "INSERT INTO user_reports (reporter_id, author_id, reason, report_date, is_current)
                        VALUES ((SELECT id FROM users WHERE username = '$reporter_username'),
                                (SELECT id FROM users WHERE username = '$author_username'),
                                '$reason', '$report_date', '$is_current')";

            if ($conn->query($sql_insert) === TRUE) {
                $message = "Жалоба успешно отправлена";
            } else {
                $message = "Ошибка: " . $sql_insert . "<br>" . $conn->error;
            }

            $conn->commit();
            $conn->close();
        } else {
            $message = "Вы уже пожаловались на этого пользователя";
        }

        echo $message;
        echo "<form class='confirm_form' action='../index.php' method='get'>";
        echo "<input type='submit' class='confirm_button' value='На главную'>";
        echo "</form>";
    }
?>