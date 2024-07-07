<?php
    session_start();
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Система обмена опытом и знаниями: Заблокированные пользователи</title>
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
            echo "<button class='back_button' onclick='goBack()'>Назад</button>";

            if (!isset($conn)) {
                $conn = require_once __DIR__ . '/../db/dbconfig.php';
            }

            if ($conn->connect_error) {
                die("Ошибка сервера: " . $conn->connect_error);
            }

            // Проверка роли пользователя
            if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) { // Если пользователь администратор
                try {
                    $sql = "SELECT id, username, email, registration_date
                                        FROM users
                                        WHERE relevance_id = 2
                                        ORDER BY registration_date DESC;";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        echo "<div class='list_title'>Заблокированные пользователи</div>";
                        echo "<ul class='blocked_users_list'>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<li class='blocked_user'>";
                            $username = $row['username'];
                            echo "<a href='/user/profile.php?username=$username'>Пользователь: $username</a>";
                            echo "<p>Email: " . htmlspecialchars($row['email']) . "</p>";
                            $registration_date = new DateTime($row["registration_date"]);
                            $registration_date = $registration_date->format('d.m.Y');
                            echo "<p>Дата регистрации: " . $registration_date . "</p>";
                            echo "<form action='/admin/unblock_user.php?username=$username' method='post'>";
                            echo "<input type='hidden' name='username' value='" . $username . "'>";
                            echo "<input class='action_button' type='submit' value='Разблокировать'>";
                            echo "</form>";
                            echo "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "<p>Заблокированные пользователи отсутствуют.</p>";
                    }

                    $conn->close();
                } catch (mysqli_sql_exception $ex) {
                    echo "Ошибка: " . $ex->getMessage();
                }
            } else {
                echo "<p>У вас нет прав для просмотра этой страницы.</p>";
            }
        ?>
    </body>
</html>
