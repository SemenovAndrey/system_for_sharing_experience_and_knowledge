<?php
    session_start();
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Система обмена опытом и знаниями: Создание поста</title>
        <link rel="stylesheet" href="../style.css">
    </head>
    <body>
        <?php
            require_once __DIR__ . '/../includes/header.php';
            require_once __DIR__ . '/../includes/new_question_form.php';
        ?>
    </body>
</html>
