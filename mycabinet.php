<?php

// Обработка AJAX-запросов

require_once 'phplib/dbConnect.php';
require_once 'phplib/mail.php';
require_once 'phplib/common.inc';

if (!isset($_GET["id"])) {
    include 'mc_err_empty.inc';
    http_response_code(404);
    exit();
} else {
$UID = $_GET["id"];
/*
 * Обработка AJAX-запроса из функции setDateWorkSent() на изменение статуса записи
 * после того, как была открыта Олимпиада. Меняем статус абитуриента на "2"
 * (если он и так не "2") и, записываем дату отсылки олимпиады.
 */
if (isset($_GET["SetStatus"])) {
    $db = new dbConnect();
    if ($db->getAppStatus($UID) != 2) {
        $db->setWorkDaySent($UID);
        $db->dbLog("Открыта вступительная олимпиада", $UID);
    }
    exit();
}

/*
 * Отработка AJAX-запроса на генерацию и отсылку "Приложения 2" по анкете
 */
if (isset($_GET["app"])) {
    include 'mc_app2gen.inc';
    exit();
}
?>


<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php
include 'phplib/yandex.metrika.php';
include 'phplib/google.analytics.php';
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Личный кабинет</title>
<link href="CSS/common.css" rel="stylesheet" type="text/css">
<link href="CSS/mycabinet.css" rel="stylesheet" type="text/css">
<link href="CSS/uploader.css" rel="stylesheet" type="text/css">
<link href="CSS/uploadfile.css" rel="stylesheet" type="text/css">
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="//oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="JS/lib/jquery.form.min.js"></script>
<!-- Для загрузки файлов используем ужасную библиотеку jquery.uploadfile.js -->
<!-- В дальнейшем надо обязательно переписать!!! -->
<script src="JS/lib/jquery.uploadfile.js"></script>
<script src="JS/cabinet_functions.js"></script>
</head>
<body>
<div class="logo"></div>

<?php

    /*
     * Основной модуль личного кабинета
     */
    $db = new dbConnect();
    if ($row = $db->getPerson($UID)) {
        $db->dbLog($row['Name'] . " " . $row['Surname'] . " зашёл в Личный кабинет", $UID);
        if ($row['AppStatus'] == 0) {
            $db->setAppStatus($UID, 1); // Пользователь первый раз зашёл в Личный кабинет
        }
        /*
         * Вывод страницы личного кабинета
         *
         */

        $appStatus = $db->getAppStatus($UID);

        // Сначала выставляем subTitle
        $subTitle = "";
        if ($appStatus > 1) {
            $name = $row["Name"];
            $surname = $row["Surname"];
            $email = $row["Email"];
            $phone = $row["Tel"];
            $subTitle = "<strong>$name $surname</strong>";
        } elseif ($appStatus < 0) {
            $subTitle = "Регистрация закрыта.";
        } else {
            $subTitle = "Здесь мы будем выкладывать всю необходимую информацию по организации поездки.";
        }

        // Выводим заголовок
        echo "
            <div class=\"title\">
            <h1>Личный кабинет участника летнего физико-математического лагеря \"Дельта\"</h1>
            <h2 id=\"subTitle\">$subTitle</h2>
            </div>
        ";

        // Вывод основного куска в зависимости от статуса пользователя
        echo "<div class=\"main\">";
        if ($appStatus < 0) { // Регистрация закрыта
            include "mc_-.inc";
        } elseif ($appStatus < 2) { // Скачать олимпиаду
            include "mc_01.inc";
        } elseif ($appStatus < 4) { // Прислать результаты
            include "mc_23.inc";
        } elseif ($appStatus < 25) { // Заполнить анкету (статус = 15) и сгенерировать приложение к договору (статус = 20)
            include "mc_5.inc";
        }
        include "mc_bottom.inc";
        echo "</div>";


        /*
         * Обработка запроса выслать файл-олимпиады письмом
         */
        if (isset($_GET['sbm']) && $_GET['sbm']==='yes') {
            try {
                sendAssignmentsMail($row);
                $db->dbLog($row['Name'] . " " . $row['Surname'] . ": выслана вступительная олимпиада", $_GET["id"]);
                $db->setWorkDaySent($_GET["id"]);
                echo '
                    <script>
                    document.getElementById("sendStatus").innerHTML = "Письмо со вступительной олимпиадой выслано Вам на почту.";
                    document.getElementById("sendStatus").style.color = "#188";
                    document.getElementById("sendStatus").style.opacity = "1";
                    </script>
                ';
            } catch (\PHPMailer\PHPMailer\Exception $e) {
                echo '
                    <script>
                    document.getElementById("sendStatus").innerHTML = "Проблема с отправкой письма. Свяжитесь, пожалуйста, с организаторами!";
                    document.getElementById("sendStatus").style.color = "#A33";
                    document.getElementById("sendStatus").style.opacity = "1";
                    </script>
                ';
                error("Unable to send file assignments.pdf to " . $row['Email'] . " UID=" . $UID);
            }
        }

    } else { // Обработка запроса с неверным UID
        include "mc_err_unknown_id.inc";
    }
}
?>
</body>
</html>
