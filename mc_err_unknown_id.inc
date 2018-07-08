<div class="title">
    <div class="row"><div class="col-12">
            <h2>Инентификатор пользователя не найден.</h2>
        </div></div></div>
<div class="row"><div class="col-8"><div class="main">
            <p>Если Вы зарегистрировались в Летний физико-математический лагерь "Дельта" в Мюнхене и попали на эту страницу из ссылки
                в письме с подтверждением регистрации, пожалуйста свяжитесь с организаторами"</p>
            <form method="post" action="feedback.php">
                <p>
                    <input name="id" type="hidden" id="id" value="<?php echo $row['UniqueId'] ?>">
                    <input name="name" type="hidden" id="name" value="<?php echo $row['Name'] . ' ' . $row['Surname'] ?>">
                    <input name="email" type="hidden" id="email" value="<?php echo $row['Email'] ?>">
                    <input type="submit" value="Связь с организаторами">
                </p>
            </form>
        </div> </div>
</div>
