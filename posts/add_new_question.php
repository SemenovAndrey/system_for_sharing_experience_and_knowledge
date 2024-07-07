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

        if (isset($_POST["add_q"])) {
            $title = htmlspecialchars($_POST["title"]);
            $post_text = htmlspecialchars($_POST["post_text"]);
            $tags = isset($_POST["tags"]) ? $_POST["tags"] : array();
            $username = $_SESSION['username'];

            $date = date('Y-m-d');
//            $sql = "INSERT INTO posts (user_id, title, date, post_text, relevance)
//                VALUES ((SELECT id FROM users WHERE username = '$username'), '$title', '$date', '$post_text', '1');";
//            $result = $conn->query($sql);

            $stmt = $conn->prepare("INSERT INTO posts (user_id, title, date, post_text, relevance)
                                        VALUES ((SELECT id FROM users WHERE username = '$username'), ?, ?, ?, 1)");
            $stmt->bind_param("sss", $title, $date, $post_text);
            $stmt->execute();

            // Получение id добавленного поста
            $post_id = $stmt->insert_id;
            $stmt->close();

//            $stmt = $conn->prepare("user_id, title, date, post_text, relevance)
//                VALUES (?, ?, ?, ?, '1')");
//            $user_id_sql = "(SELECT id FROM users WHERE username = '$username')";
//            $stmt->bind_param("isss", $user_id_sql, $title, $date, $post_text);
//            $stmt->execute();

//            $post_id = $stmt->insert_id;

//            $sql_post = "SELECT id FROM posts
//                        WHERE title = '$title'";
//            $result_post = $conn->query($sql_post);
//            $post_id = $result_post->fetch_assoc()['id'];

            foreach ($tags as $tag) {
                if ($tag !== '') {
                    $stmt = $conn->prepare("INSERT INTO tags_and_posts (post_id, tag) VALUES (?, ?)");
                    $stmt->bind_param("is", $post_id, $tag);
                    $stmt->execute();
                    $stmt->close();
                }
            }

            $conn->commit();
            $message = "Пост успешно добавлен";
        } else {
            $message = "Произошла ошибка при добавлении поста";
        }
    } catch (mysqli_sql_exception $ex) {
        $conn->rollback();
        $message = "Ошибка сервера: " . $ex->getMessage();
    } finally {
        $conn->close();
    }

//    if ($message == "Пост успешно добавлен") {
//        echo $message;
//        echo "<form class='confirm_form' action='/../index.php' method='get'>";
//        echo "<input type='submit' class='confirm_button' value='На главную'>";
//        echo "</form>";
//    } else {
//        echo $message;
//        echo "<form class='confirm_form' action='/add_new_question.php' method='get'>";
//        echo "<input type='submit' class='confirm_button' value='Вернуться'>";
//        echo "</form>";
//    }

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Подтверждение</title>
    <style>
        .confirm_message {
            text-align: center;
        }

        .confirm_container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            margin-top: 20px;
        }

        .confirm_form {
            text-align: center;
        }

        .confirm_button {
            background-color: white;
            color: black;
            padding: 10px 20px;
            border: 1px solid black;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<?php
echo "<div class='confirm_message'>";
echo "<p>" . htmlspecialchars($message) . "</p>";
echo "<div class='confirm_container'>";
echo "<form class='confirm_form' action='/index.php' method='get'>";
echo "<input type='submit' class='confirm_button' value='На главную'>";
echo "</form>";
echo "</div>";
echo "</div>";
?>
</body>
</html>
