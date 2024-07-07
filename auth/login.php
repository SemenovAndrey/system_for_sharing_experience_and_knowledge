<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Система обмена опытом и знаниями: Авторизация</title>
        <link rel="stylesheet" href="/../style.css">
    </head>
    <body>
        <h1 class="site_title">
            <a href="/../index.php">Система обмена опытом и знаниями</a>
        </h1>
        <form class="auth_form" method="post" action="/auth/do_login.php">
            <h2 class="auth_title">Авторизация</h2>
            <div id="username_auth">
                <div class="input_description">Имя пользователя</div>
                <br>
                <input class="auth_form_field" type="text" id="username" name="username" placeholder="username" required>
            </div>
            <div id="password_auth">
                <div class="input_description">Пароль</div>
                <br>
                <input class="auth_form_field" type="password" id="password" name="password" placeholder="password" required>
            </div>
            <div class="auth_button">
                <input class="auth_form_field" type="submit" value="Войти" id="login" name="login">
            </div>
            <div class="auth_button">
                <a href="register.php">Зарегистрироваться</a>
            </div>
        </form>
    </body>
</html>