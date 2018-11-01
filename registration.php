<?php
require_once 'phplib/dbConnect.php';
require_once 'phplib/mail.php';
require_once 'phplib/common.php';
// Обработка AJAX-запроса
if (isset($_POST["ALL_DONE"]) && $_POST["ALL_DONE"] === 'Check'){
    $db = new dbConnect();
    $row = $db->checkOldRegistration($_POST["surname"], $_POST["name"], $_POST["middlename"], $_POST["birthday"]);
    if ($row) {
        exit(json_encode($row));
    } else {
        http_response_code(204); // No content
        exit();
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <?php
    include 'phplib/yandex.metrika.php';
    include 'phplib/google.analytics.php';
    ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="title" content="Регистрация в Дельту">
    <title>Регистрация в Дельту</title>
    <link href="CSS/common.css" rel="stylesheet" type="text/css">
    <link href="CSS/mycabinet.css" rel="stylesheet" type="text/css">
    <script src="JS/common.js"></script>
    <script src="JS/form_functions.js"></script>
    <script src="JS/lib/is.min.js"></script>
</head>

<body>
<div class="title">
    <h1><span class="highlighted">Предварительная</span> регистрация в летний физико-математический лагерь "Дельта" <span class="highlighted">на июль 2019 года</span> </h1>
</div>

<?php
if (!isset($_POST["ALL_DONE"]) || $_POST["ALL_DONE"] === "") {
/****
 *
 * Форма регистрации
 *
 */
?>
<div class="main">
    <form id="form" name="form" method="post" onsubmit="return checkForm()">
        <div class="row">
            <div class="col-8">
                <h3>Здравствуйте! Заполните, пожалуйста, регистрационную анкету.</h3>
                <p>Поля, помеченные звёздочкой <span class="required">*</span> обязательны.</p>
                <p id="known"><b>Если вы уже бывали в "Дельте"</b>, достаточно заполнить только поля<br>
                    'Фамилия', 'Имя', 'Отчество' (если есть) и 'Дата рождения'.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                E-Mail <span class="required">*</span><br>
                <input class="text-input" name="email" type="email" required id="email" placeholder="ivan@ivanov.earth"
                       title="Адрес электронной почты">
                <br>
                <span class="explanation">Ваш адрес электронной почты. На него придёт подтверждение регистрации.</span>
            </div> <!-- col- -->
            <div class="col-4">
                Контактный телефон <span class="required">*</span><br>
                <input name="tel" type="tel" required class="text-input" id="tel" title="Телефон для связи"
                       placeholder="+71234567890">
            </div> <!-- col- -->
        </div> <!-- row -->
        <div class="row">
            <div class="col-3">
                Фамилия <span class="required">*</span><br>
                <input class="text-input" name="surname" type="text" required id="surname" placeholder="Иванов"
                       title="Фамилия ребёнка">
            </div> <!-- col-4 -->
            <div class="col-3">
                Имя <span class="required">*</span><br>
                <input class="text-input" name="name" type="text" required id="name" placeholder="Иван"
                       title="Имя ребёнка">
            </div> <!-- col-4 -->
            <div class="col-3">
                Отчество<br>
                <input class="text-input" name="middlename" type="text" id="middlename" placeholder="Иванович"
                       title="Отчество ребёнка">
            </div> <!-- col-4 -->
        </div> <!-- row -->
        <div class="row">
            <div class="col-3">
                Дата рождения <span class="required">*</span><br>
                <input class="text-input" name="birthday" type="tel" required id="birthday" placeholder="dd/mm/yyyy"
                       title="Дата рождения" onChange="checkDate(this);">
                <span class="hidden_error" id="date_error">Неверный формат даты!<br>Используйте "день/месяц/год"<br>Например: 27/03/1999</span>
                <span class="explanation"><span class="age" id="age"><br>Возраст на начало лагеря: </span></span>
            </div> <!-- col-3 -->
            <div class="col-3">
                <div id="gender">
                    Пол <span class="required">*</span><br>
                    <input name="gender" type="radio" required id="female" value="f"><label
                            for="female"><span><span></span></span>девочка</label>&nbsp;&nbsp;
                    <input name="gender" type="radio" required id="male" value="m"><label for="male"><span><span></span></span>мальчик</label>
                </div> <!-- gender -->
            </div> <!-- col-3 -->
            <div class="col-3">
                Класс<br>
                <input class="text-input" name="class" type="tel" id="class" title="Класс">
                <br>
                <span class="explanation">Из расчёта 11-летней системы или год обучения ребёнка в школе.</span>
            </div> <!-- col-3 -->
        </div> <!-- raw -->
        <div class="row">
            <div class="col-3">
                Школа <span class="required">*</span><br>
                <input class="text-input" name="school" type="text" required id="school" title="Школа">
            </div> <!-- col-3 -->
            <div class="col-3">
                Город <span class="required">*</span><br>
                <input class="text-input" name="city" type="text" required id="city" title="Город проживания">
            </div> <!-- col-3 -->
            <div class="col-3">
                Страна <span class="required">*</span><br>
                <input class="text-input" name="country" type="text" required id="country" title="Страна проживания">
            </div> <!-- col-3 -->
        </div> <!-- row -->
        <div class="row">
            <div class="col-6">
                Языки <span class="required">*</span><br>
                <input class="text-input" name="langs" type="text" required id="langs"
                       title="Какими языками владеет ребёнок" value="русский" size="60">
                <br>
                <span class="explanation">Языки, которыми владеет ребёнок. Перечислите через запятую.</span>
                <p>Дополнительная информация<br>
                    <textarea class="text-input" name="notes" cols="60" rows="4" id="notes"
                              title="Замечания"></textarea>
                    <br>
                    <span class="explanation">Всё, что Вы ещё хотели бы нам сообщить. Также Вы можете задать свои вопросы письмом на summer.camp.delta@gmail.com</span>
                </p>
                <div class="indented" id="agree_box">На основании ст.64 п.1 Семейного кодекса РФ даю свое согласие на
                    обработку указанных выше данных моего ребенка для участия в выездной школе, получения информации о
                    школе, отъезде, возвращении. Использование данных для других целей не предусмотрено.<br>
                    <b>Согласен</b><span class="required" title="Обязательно для заполнения.">*</span>:
                    <input name="agree" id="agree" type="checkbox" value="agree" title="Согласие">
                    <label for="agree"><span><span></span></span></label>
                </div>
                <p>
                    <input name="submit" type="submit" id="submit" value="Зарегистрировать">
                </p>
            </div>
        </div> <!-- col-6 row -->
        <input name="ALL_DONE" type="hidden" id="ALL_DONE" value="Ok">
        <p>&nbsp;<br></p>
    </form>
</div> <!-- class "main" -->
<?php
} elseif ($_POST["ALL_DONE"] === "Error") {
    error('Что-то пошло не так: ALL_DONE = Error POST[]=' . print_r($_POST));
} elseif ($_POST["ALL_DONE"] === "Ok" || $_POST["ALL_DONE"] === "Old") {
    /***
     *
     * Обработка данных после заполнения формы регистрации
     *
     */
    $db = new dbConnect();
    $uniqueID = $db->putRegData();
    if ($db->getStatus() == DB_ADD_OK) {
        /*****
         *
         * Сообщение об успешной регистрации
         *
         */
        $person = $db->getPerson($uniqueID);
        try {
            sendRegMail($person);
            $db->dbLog("Выслано подтверждение регистрации, UID=" . $uniqueID, $uniqueID);
        } catch (PHPMailer\PHPMailer\Exception $e) {
            error("Error in sendRegMail function, with person UID=" . $uniqueID . ": " . $e->errorMessage());
        }

        // Если были в прошлом году - перекидываем фотографию из бекапа в новую папку
        if ($_POST["ALL_DONE"] === "Old" && $OldUID = $db->getOldUID($uniqueID)) {
            $source_pattern = "_2018/photos/" . $OldUID . ".*";
            $all_source_photos = glob($source_pattern);
            $source = $all_source_photos[0];
            $dest = "photos/" . $uniqueID . ".JPG";
            $result = copy($source, $dest);
            if (!$result) error("Unable to copy photo from old account for UID: $uniqueID, old_UID: $OldUID");
        }
        ?>
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
        try {
            sendRegMail($person);
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            error($e->errorMessage());
        }
        $db->dbLog("Повторно отправлено письмо-подтверждение регистрации, UniqueId=" . $uniqueID, $uniqueID);
        ?>
        <div class="main">
            <form id="form" name="form" method="post" action="index.php">
                <div class="row">
                    <div class="col-2">&nbsp;</div>
                    <div class="col-8">
                        <p>Внимание!</p>
                        <p><b><?php echo $person['Name'] . " " . $person['Surname']?></b> с датой рождения
                            <?php echo $person['Birthday']?> уже зарегистрирован<?php if ($person['Gender'] === 'f') {
                            echo "а";} ?>!</p>
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
            <div class="row">
                <div class="col-12">
                    <hr>
                    <p>Если у Вас возникли вопросы или Вы заметили неточность в регистрационных данных, пожалуйста воспользуйтесь формой
                        обратной связи или свяжитесь с нами:</p>
                    <div class="row">
                        <div class="col-6">
                            <p><b>Анна Семовская</b><br>
                                <?php printContact('sem'); ?>
                            </p></div> <!-- col-6 -->
                        <div class="col-6">
                            <p><b>Дмитрий Аблов</b><br>
                                <?php printContact('abl'); ?>
                            </p>
                        </div>
                    </div> <!-- col-6, row -->
                </div>
            </div>  <!-- col-12, row -->
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
?>
</body>
</html>