<div class="header">
    <h1 class="site_title">
        <a href="../index.php">Система обмена опытом и знаниями</a>
    </h1>

    <?php
        if (isset($_SESSION['username'])) {
//            if (!isset($conn)) {
//                $conn = require_once __DIR__ . '/../db/dbconfig.php';
//            }
//
//            if ($conn->connect_error) {
//                die("Ошибка сервера: " . $conn->connect_error);
//            }
//
//            try {
//                $conn->begin_transaction();
//
//                $username = $_SESSION['username'];
//
//                // проверка на админа
//                $sql = "SELECT role_id FROM users WHERE username = '$username'";
//                $result = $conn->query($sql);
//                $role_id = $result->fetch_assoc()['role_id'];
//
//                if ($role_id == 1) {
//                    echo "<a href='/admin/admin.php?username=$username'>";
//                } else {
//                    echo "<a href='/user/profile.php?username=$username'>";
//                }
//            }  catch (mysqli_sql_exception $ex) {
//                $conn->rollback();
//                echo "Ошибка: " . $ex->getMessage();
//            }
            $username = $_SESSION['username'];
            echo "<a href='/user/profile.php?username=$username'>";
            echo "<button class='profile_button'>Профиль</button>";
            echo "</a>";
            echo "<a href='/auth/logout.php'>";
            echo "<button class='logout_button'>Выйти</button>";
            echo "</a>";
        } else {
            echo "<a href='/auth/login.php'>";
            echo "<button class='enter_button'>Войти</button>";
            echo "</a>";
        }
    ?>
</div>