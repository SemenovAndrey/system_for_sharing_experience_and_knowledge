<?php
    session_start();

    if (!isset($conn)) {
        $conn = require_once __DIR__ . '/../db/dbconfig.php';
    }

    if ($conn->connect_error) {
        die("Ошибка сервера: " . $conn->connect_error);
    }

    try {
        $conn->begin_transaction();

        if (isset($_POST["login"])) {
            if (!empty($_POST["username"]) && !empty($_POST["password"])) {
                $username = htmlspecialchars(trim($_POST["username"]));
                $password = htmlspecialchars($_POST["password"]);

                // Подготовка SQL-запроса для получения пользователя по имени пользователя
                $sql = "SELECT * FROM users WHERE username=?";
                $stmt = $conn->prepare($sql);
                if ($stmt === false) {
                    throw new Exception("Ошибка подготовки запроса: " . $conn->error);
                }

                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();

                    // Проверка хэшированного пароля
                    if (password_verify($password, $user['password'])) {
                        // Установка сессии и перенаправление на главную страницу
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['role_id'] = $user['role_id'];
                        $message = "Вход выполнен успешно";
                    } else {
                        $message = "Неверный пароль";
                    }
                } else {
                    $message = "Неверное имя пользователя или пароль";
                }

                $stmt->close();
            } else {
                $message = "Все поля должны быть заполнены";
            }
        }

        $conn->commit();
    } catch (mysqli_sql_exception $ex) {
        $conn->rollback();
        $message = "Ошибка сервера: " . $ex->getMessage();
    } catch (Exception $ex) {
        $message = "Ошибка: " . $ex->getMessage();
    }

    if (!empty($message)) {
        if ($message == "Вход выполнен успешно") {
            header("Location: /../index.php");
            exit();
        } else {
            require_once __DIR__ . "/login.php";
            echo "<h3 class='error_message_login'>$message</h3>";
        }
    }

    $conn->close();
