<?php
    // mysql
    $host = "localhost";
    $port = "3306";
    $dbname = "experience_change_system";
    $user = "user";
    $password = "password";

    $conn = new mysqli($host, $user, $password, $dbname, $port);
    if ($conn->connect_error) {
        die("Ошибка соединения: " . $conn->connect_error);
    }

    return $conn;
?>