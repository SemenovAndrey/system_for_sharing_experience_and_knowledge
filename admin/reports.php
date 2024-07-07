<?php
session_start();
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Система обмена опытом и знаниями: Репорты</title>
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
                    $sql = "SELECT r.reporter_id, r.author_id, r.reason, r.report_date, u1.username AS reporter_username, u2.username AS author_username
                            FROM user_reports AS r
                            LEFT JOIN users AS u1
                                ON r.reporter_id = u1.id
                            LEFT JOIN users AS u2
                                ON r.author_id = u2.id
                            WHERE r.is_current = 1
                            ORDER BY r.report_date DESC;";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        echo "<div class='reports_container'>";
                        echo "<div class='list_title'>Текущие репорты</div>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<div class='report'>";
                            $username1 = $row['reporter_username'];
                            $username2 = $row['author_username'];
                            echo "<p><a href='/../user/profile.php?username=$username1'><strong>От</strong>: $username1</a></p>";
                            echo "<a href='/../user/profile.php?username=$username2'><strong>На</strong>: $username2</a>";
                            echo "<p><strong>Причина:</strong> " . htmlspecialchars($row['reason']) . "</p>";
                            $report_date = new DateTime($row["report_date"]);
                            $report_date = $report_date->format('d.m.Y');
                            echo "<p><strong>Дата репорта:</strong> " . $report_date . "</p>";
                            echo "<form action='/admin/resolve_report.php' method='post'>";
                            echo "<input type='hidden' name='reporter_id' value='" . htmlspecialchars($row['reporter_id']) . "'>";
                            echo "<input type='hidden' name='author_id' value='" . htmlspecialchars($row['author_id']) . "'>";
                            echo "<input class='action_button' type='submit' value='Закрыть репорт'>";
                            echo "</form>";
                            echo "</div>";
                        }
                        echo "</div>";
                    } else {
                        echo "<div class='reports_container'>";
                        echo "<p>Текущие репорты отсутствуют.</p>";
                        echo "</div>";
                    }

                    $conn->close();
                } catch (mysqli_sql_exception $ex) {
                    echo "Ошибка: " . $ex->getMessage();
                }
            } else {
                echo "<div class='reports_container'>";
                echo "<p>У вас нет прав для просмотра этой страницы.</p>";
                echo "</div>";
            }
        ?>
    </body>
</html>
