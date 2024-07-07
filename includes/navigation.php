<?php
    if (isset($_SESSION['username'])) {
        echo "<nav class='navigation'>";
            echo "<ul class='menu'>";
                echo "<li class='menu_field'>";
                echo "<a href='/posts/posts.php'>Посты</a>";
                echo "</li>";
                echo "<li class='menu_field'>";
                echo "<a href='/user/subscribes.php'>Подписки</a>";
                echo "</li>";
                echo "<li class='menu_field'>";
                echo "<a href='/posts/new_question.php'>Задать вопрос</a>";
                echo "</li>";
            echo "</ul>";
        echo "</nav>";
    }
?>

<form class="search_form" action="/posts/find.php" method="get">
    <input type="text" class="search_form_field" name="search" placeholder="Поиск">
    <select name="filter" class="search_form_select">
        <option value="all">Все записи</option>
        <option value="posts">Посты</option>
        <option value="tags">Тэги</option>
        <option value="users">Пользователи</option>
    </select>
    <input type="submit" value="Найти" class="find_button">
</form>