<?php
    session_start();

    $_SESSION['username'] = null;
    $_SESSION['role_id'] = null;

    header('Location: ../index.php');
