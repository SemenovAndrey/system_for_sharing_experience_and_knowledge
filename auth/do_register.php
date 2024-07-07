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

        if (isset($_POST["register"])) {
            if (!empty($_POST["username"]) && !empty($_POST["email"]) && !empty($_POST["password"])) {
                $username = htmlspecialchars(trim($_POST["username"]));
                $email = htmlspecialchars(trim($_POST["email"]));
                $password = htmlspecialchars($_POST["password"]);

                // Проверка на формат email (@)
                if (strpos($email, '@') === false) {
                    $message = "Необходим символ @";
                } else if (strpos($email, ' ') !== false) {
                    $message = "В почте не должно быть пробелов";
                } else {
                    $sql_users = "SELECT * FROM users WHERE username='$username'";
                    $result_users = $conn->query($sql_users);
                    $num_rows_users = $result_users->num_rows;

                    $sql_email = "SELECT * FROM users WHERE email='$email'";
                    $result_email = $conn->query($sql_email);
                    $num_rows_email = $result_email->num_rows;

                    if ($num_rows_users == 0 && $num_rows_email == 0) {
                        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                        $date = new DateTime();
                        $date = $date->format('Y-m-d');

                        $insert_query = "INSERT INTO users(username, email, password, role_id, relevance_id, registration_date)
                            VALUES(?, ?, ?, 2, 1, ?)";
                        $stmt = $conn->prepare($insert_query);
                        $stmt->bind_param("ssss", $username, $email, $hashed_password, $date);

                        if ($stmt->execute()) {
                            $_SESSION['username'] = $username;
                            $_SESSION['role_id'] = 2;

                            $message = "Аккаунт успешно создан";
                        }

                        $conn->commit();
                        $stmt->close();
                    } else {
                        if ($num_rows_users > 0) {
                            $message = "Имя пользователя уже занято";
                        } else {
                            $message = "Почта уже занята";
                        }
                    }
                }
            } else {
                $message = "Все поля должны быть заполнены";
            }
        }
    } catch (mysqli_sql_exception $ex) {
        $conn->rollback();
        echo "<p class='error_message'>Ошибка сервера</p>";
        echo "<form action='/../index.php' method='get'>";
        echo "<input type='submit' value='На главную'>";
        echo "</form>";
    }

    if (!empty($message)) {
        if ($message == "Аккаунт успешно создан") {
            echo "<!doctype html>";
            echo "<html lang='en'>";
            echo "<head>";
            echo "<meta charset='utf-8'>";
            echo "<title>Система обмена опытом и знаниями: Регистрация</title>";
            echo "<link rel='stylesheet' href='/../style.css'>";
            echo "</head>";
            echo "<body>";
            echo "<h2 class='successfull_message'> $message </h2>";
            echo "<form class='confirm_form' action='/../index.php' method='get'>";
            echo "<input type='submit' class='confirm_button' value='На главную'>";
            echo "</form>";
            echo "</body>";
            echo "</html>";
        } else {
            require_once __DIR__ . "/register.php";
            echo "<h3 class='error_message_reg'>$message</h3>";
        }
    }

    $conn->close();
?>
