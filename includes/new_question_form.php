<!--<div class="new_question">-->
<!--    <form class="new_question_form" method="post" action="add_new_question.php">-->
<!--        <div id="new_question_title">-->
<!--            <div class="new_question_form_text">Заголовок</div>-->
<!--            <input class="new_question_form_field" type="text" id="title" name="title" placeholder="Заголовок" required>-->
<!--        </div>-->
<!--        <div id="new_question_text">-->
<!--            <input class="new_question_form_field" type="text" id="post_text" name="post_text" placeholder="Текст" required>-->
<!--        </div>-->
<!--        <div id="new_question_tags">-->
<!--            <div class="new_question_form_text">Тэги</div>-->
<!--            <input class="new_question_form_field" type="text" id="tags" name="tags[]" placeholder="Тэг"><br>-->
<!--            <input class="new_question_form_field" type="text" id="tags" name="tags[]" placeholder="Тэг"><br>-->
<!--            <input class="new_question_form_field" type="text" id="tags" name="tags[]" placeholder="Тэг"><br>-->
<!--            --><?php
////                сделать кнопку "Добавить тэг" и чтобы появлялось новое поле для ввода тэга
//            ?>
<!--        </div>-->
<!--        <br>-->
<!--        <div class="add_question">-->
<!--            <input class="new_question_form_field" type="submit" value="Сохранить" id="add_q" name="add_q">-->
<!--        </div>-->
<!--    </form>-->
<!--</div>-->


<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Новый вопрос</title>
        <link rel="stylesheet" href="../style.css">
        <style>
            .new_question {
                width: 50%;
                margin: auto;
                padding: 20px;
                border: 1px solid #ccc;
                border-radius: 10px;
                background-color: #f9f9f9;
            }

            .new_question_form {
                display: flex;
                flex-direction: column;
            }

            .new_question_form_text {
                font-size: 18px;
                margin-bottom: 10px;
            }

            .new_question_form_field {
                padding: 10px;
                margin-bottom: 15px;
                border: 1px solid #ccc;
                border-radius: 5px;
                font-size: 16px;
                width: 100%;
                font-family: inherit;
            }

            #new_question_tags {
                display: flex;
                flex-direction: column;
            }

            .add_tag_button {
                background-color: white;
                border: 1px solid black;
                padding: 10px;
                cursor: pointer;
                border-radius: 5px;
            }

            .add_question {
                display: flex;
                justify-content: center;
            }
        </style>
        <script>
            let tagCount = 1;
            function addTag() {
                if (tagCount < 10) {
                    tagCount++;
                    const newTagField = document.createElement('input');
                    newTagField.setAttribute('type', 'text');
                    newTagField.setAttribute('name', 'tags[]');
                    newTagField.setAttribute('placeholder', 'Тэг');
                    newTagField.setAttribute('class', 'new_question_form_field');
                    document.getElementById('new_question_tags').appendChild(newTagField);
                }
            }

            function goBack() {
                window.history.back();
            }
        </script>
    </head>
    <body>
        <div class="new_question">
            <form class="new_question_form" method="post" action="add_new_question.php">
                <div id="new_question_title">
                    <div class="new_question_form_text">Заголовок</div>
                    <input class="new_question_form_field" type="text" id="title" name="title" placeholder="Заголовок" required>
                </div>
                <div id="new_question_text">
                    <div class="new_question_form_text">Текст</div>
                    <textarea class="new_question_form_field" id="post_text" name="post_text" placeholder="Текст" required></textarea>
                </div>
                <div id="new_question_tags">
                    <div class="new_question_form_text">Тэги</div>
                    <input class="new_question_form_field" type="text" name="tags[]" placeholder="Тэг">
                </div>
                <br>
                <button type="button" class="add_tag_button" onclick="addTag()">Добавить тэг</button>
                <br>
                <div class="add_question">
                    <input class="new_question_form_field" type="submit" value="Сохранить" id="add_q" name="add_q">
                </div>
            </form>
            <button class="back_button" onclick="goBack()">Назад</button>
        </div>
    </body>
</html>
