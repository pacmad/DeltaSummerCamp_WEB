<!doctype html>
<html>
<head>
<meta charset="windows-1251">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Регистрация</title>
<link href="CSS/common.css" rel="stylesheet" type="text/css">
<link href="CSS/form.css" rel="stylesheet" type="text/css">
<script src="JS/form_functions.js"></script>
</head>

<body>
<?php
require_once 'phplib/dbConnect.php';
require_once 'phplib/mail.php';
require_once 'phplib/common.php';
if (!isset($_POST["ALL_DONE"])) {
    /****
     *
     * Форма регистрации
     *
     */
?>
<div class="row"><div class="col-12">
<h1>Регистрация в летний физико-математический лагерь "Дельта"</h1>
<p class="explanation">Поля, помеченные звёздочкой <span class="required">*</span> обязательны.</p>
</div></div> <!-- col12, row -->

<div class="main">
<form id="form" name="form" method="post" onsubmit="return checkForm()">
  <div class="row">
  <div class="col-4">
  <p>E-Mail <span class="required">*</span><br>
    <input class="text-input" name="email" type="email" required id="email" placeholder="ivan@ivanov.earth" title="Адрес электронной почты">
    <br>
  <span class="explanation">Ваш адрес электронной почты. На него придёт подтверждение регистрации.</span></p>
  </div> <!-- col- -->
  <div class="col-4">
  <p> Контактный телефон <span class="required">*</span><br>
    <input name="tel" type="tel" required class="text-input" id="tel" title="Телефон для связи">
  </p>
  </div> <!-- col- -->
  </div> <!-- row -->
  <div class="row">
  <div class="col-3">
  <p>Фамилия <span class="required">*</span><br>
    <input class="text-input" name="surname" type="text" required id="surname" placeholder="Иванов" title="Фамилия ребёнка">
  </p>
  </div> <!-- col-4 -->
  <div class="col-3">
  <p>Имя <span class="required">*</span><br>
    <input class="text-input" name="name" type="text" required id="name" placeholder="Иван" title="Имя ребёнка">
  </p>
  </div> <!-- col-4 -->
  <div class="col-3">
  <p>Отчество<br>
    <input class="text-input" name="middlename" type="text" id="middlename" placeholder="Иванович" title="Отчество ребёнка">
  </p>
  </div> <!-- col-4 -->
  </div> <!-- row -->
  <div class="row">
  <div class="col-3">
  <p>Дата рождения <span class="required">*</span><br>
    <input class="text-input" name="birthday" type="text" required id="birthday" placeholder="dd/mm/yyyy"
           title="Дата рождения" onChange="checkDate(this);">
    <span class="hidden_error" id="date_error">Неверный формат даты!<br>Используйте "день/месяц/год"<br>Например: 27/03/1999</span>
    <span class="explanation"><span class="age" id="age"></span></span>
  </p>
  </div> <!-- col-3 -->
  <div class="col-3">
  <div id="gender">
  <p>Пол <span class="required">*</span><br>
    <input name="gender" type="radio" required id="female" value="f"><label for="female"><span><span></span></span>девочка</label>&nbsp;&nbsp;
    <input name="gender" type="radio" required id="male" value="m"><label for="male"><span><span></span></span>мальчик</label>
  </p>
  </div> <!-- gender -->
  </div> <!-- col-3 -->
  </div> <!-- raw -->
  <div class="row">
  <div class="col-3">
  <p>Класс<br>
    <input class="text-input" name="class" type="number" id="class" title="Класс">
    <br>
  <span class="explanation">Из расчёта 11-летней системы или год обучения ребёнка в школе.</span> </p>
  </div> <!-- col-3 -->
  <div class="col-3">
  <p>Школа <span class="required">*</span><br>
    <input class="text-input" name="school" type="text" required id="school" title="Школа">
  </p>
  </div> <!-- col-3 -->
  <div class="col-3">
  <p>Город <span class="required">*</span><br>
    <input class="text-input" name="city" type="text" required id="sity" title="Город проживания">
  </p>
  </div> <!-- col-3 -->
  <div class="col-3">
  <p>Страна <span class="required">*</span><br>
    <input class="text-input" name="country" type="text" required id="country" title="Страна проживания">
  </p>
  </div> <!-- col-3 -->
  </div> <!-- row -->
  <div class="row"> <div class="col-6">
  <p>Языки <span class="required">*</span><br>
    <input class="text-input" name="langs" type="text" required id="langs" title="Какими языками владеет ребёнок" value="русский" size="60">
    <br>
  <span class="explanation">Языки, которыми владеет ребёнок. Перечислите через запятую.</span></p>
  <p>Дополнительная информация<br>
    <textarea class="text-input" name="notes" cols="60" rows="4" id="notes" title="Замечания"></textarea>
    <br>
  <span class="explanation">Всё, что Вы ещё хотели бы нам сообщить. Также Вы можете задать свои вопросы письмом на delta@mathbaby.ru</span></p>
  <div class="indented" id="agree"><p>На основании ст.64 п.1 Семейного кодекса РФ даю свое согласие на   обработку указанных выше данных моего ребенка для участия в выездной   школе, получения информации о школе, отъезде, возвращении. Использование   данных для других целей не предусмотрено.<br>
  <b>Согласен</b><span class="required" title="Обязательно для заполнения.">*</span>: 
  <input name="agree" type="checkbox" id="agree" value="agree" title="Согласие">
  <label for="agree"><span><span></span></span></label>
  </p></div>
  <p>
    <input name="submit" type="submit" id="submit" value="Зарегистрировать">
  </p>
  </div></div> <!-- col-6 row -->
  <p><input name="ALL_DONE" type="hidden" id="ALL_DONE" value="Ok"><br>
  </p>
</form>
</div> <!-- class "main" -->
<?php
} elseif ($_POST["ALL_DONE"] !== 'Ok') {
    error('Что-то пошло не так...');
} else {
    /***
     *
     * Обработка данных после заполнения формы регистрации
     *
     */
    try {
        $db = new dbConnect();
        $uniqueID = $db->putRegData();
        if ($db->getStatus() == DB_ADD_OK) {
            $person = $db->getPerson($uniqueID);
            if (!sendRegMail($person)) {
                error("Проблема с отправкой письма подтверждения");
            } else {
                $db->dbLog("Отправлено письмо-подтверждение регистрации, UniqueId=" . $uniqueID);
            }
            /*****
             *
             * Сообщение об успешной регистрации
             *
             */
            ?>
            <div class="row">
                <div class="col-12">
                    <h1>Регистрация в летний физико-математический лагерь "Дельта"</h1>
                    <p class="explanation">&nbsp;</p>
                </div>
            </div> <!-- col12, row -->

            <div class="main">
                <form id="form" name="form" method="post" action="index.php">
                    <div class="row">
                        <div class="col-2">&nbsp;</div>
                        <div class="col-8">
                            <p>Спасибо за регистрацию!</p>
                            <p>На Ваш адрес будет выслано письмо.</p>
                        </div>
                        <!-- col-8 -->
                        <div class="col-2">&nbsp;</div>
                    </div> <!-- row -->
                    <div class="row">
                        <div class="col-6">
                            <p>
                                <input name="submit" type="submit" id="submit" value="Вернуться на сайт">
                            </p>
                        </div>
                    </div> <!-- col-6 row -->
                    <p><input name="ALL_DONE" type="hidden" id="ALL_DONE" value="Ok"><br>
                    </p>
                </form>
            </div> <!-- class "main" -->
            <?php
        } elseif ($db->getStatus() == DB_ADD_DUP) {
            /*
             * Обработка повторной регистрации
             */
            $person = $db->getPerson($uniqueID);
            if (!sendRegMail($person)) {
                error("Проблема с отправкой письма подтверждения");
            } else {
                $db->dbLog("Повторно отправлено письмо-подтверждение регистрации, UniqueId=" . $uniqueID);
            }
            ?>
            <div class="row">
                <div class="col-12">
                    <h1>Регистрация в летний физико-математический лагерь "Дельта"</h1>
                    <p class="explanation">&nbsp;</p>
                </div>
            </div> <!-- col12, row -->

            <div class="main">
                <form id="form" name="form" method="post" action="index.php">
                    <div class="row">
                        <div class="col-2">&nbsp;</div>
                        <div class="col-8">
                            <p>Внимание!</p>
                            <p><b><?php echo $person['Name'] . " " . $person['Surname']?></b> с датой рождения
                                <?php echo $person['Birthday']?> уже зарегистрирован!</p>
                            <p>На всякий случай мы высылаем повторное письмо на Ваш адрес (<?php echo $person['Email'] ?>).</p>
                        </div>
                        <!-- col-8 -->
                        <div class="col-2">&nbsp;</div>
                    </div> <!-- row -->
                    <div class="row">
                        <div class="col-6">
                            <p>
                                <input name="submit" type="submit" id="submit" value="Вернуться на сайт">
                            </p>
                        </div>
                    </div> <!-- col-6 row -->
                    <p><input name="ALL_DONE" type="hidden" id="ALL_DONE" value="Ok"><br>
                    </p>
                </form>
                <div class="row"><div class="col-12">
                        <hr>
                        <p>Если у Вас возникли вопросы или Вы заметили неточность в регистрационных данных, пожалуйста воспользуйтесь формой
                            обратной связи или свяжитесь с нами:</p>
                    </div></div>  <!-- col-12, row -->
                <div class="row"><div class="col-6">
                        <p><b>Анна Семовская</b><br>
                            +7(903)749-4851 (телефон, Telegram)<br>
                            anna.sem@gmail.com<br>
                            Skype: aselect1976</p></div> <!-- col-6 -->
                    <div class="col-6">
                        <p><b>Дмитрий Аблов</b><br>
                            +7(903)795-4223 (телефон, Telegram, Viber, WhatsUpp)<br>
                            d.ablov@gmail.com<br>
                            Skype: d.ablov</p></div></div> <!-- col-6, row -->
                <div class="row"><div class="col-8">
                        <form method="post" action="feedback.php">
                            <p>
                                <input name="id" type="hidden" id="id" value="<?php echo $person['UniqueId'] ?>">
                                <input name="name" type="hidden" id="name" value="<?php echo $person['Name'] . ' ' . $person['Surname'] ?>">
                                <input name="email" type="hidden" id="email" value="<?php echo $person['Email'] ?>">
                                <input type="submit" value="Связь с организаторами">
                            </p>
                            <p>&nbsp; </p>
                        </form>
                    </div></div> <!-- col-8, row -->

            </div> <!-- class "main" -->
            <?php

        }
        $db = null;
    }
    catch (PDOException $e) {
        error("PDO Exception: " . $e->getMessage());
    }
}
?>
</body>
</html>