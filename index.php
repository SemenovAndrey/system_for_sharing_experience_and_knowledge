<?php
    session_start();
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Система обмена опытом и знаниями</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <?php
            require_once __DIR__ . '/includes/header.php';
            require_once __DIR__ . '/includes/navigation.php';
            require_once __DIR__ . '/includes/main.php';
        ?>
    </body>
</html>