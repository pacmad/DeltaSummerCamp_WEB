<!doctype html>
<html>
<head>
<meta charset="windows-1251">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Личный кабинет</title>
<link href="CSS/common.css" rel="stylesheet" type="text/css">
<link href="CSS/form.css" rel="stylesheet" type="text/css">
<script src="JS/cabinet_functions.js"></script>
</head>
<body>
<div class="logo"></div>
<?php
require_once 'phplib/dbConnect.php';
require_once 'phplib/mail.php';
require_once 'phplib/common.php';

if (!isset($_GET["id"])) {
?>
<p>Страница не предназначена для просмотра без параметров.</p>
<?php
} else {
    /*
     * Обработка AJAX-запроса на изменение статуса записи
     */
    if (isset($_GET["SetStatus"])) {
        $db = new dbConnect();
        $db->setAppStatus($_GET["id"], $_GET["SetStatus"]);
        exit();
    }

    /*
     * Основной модуль личного кабинета
     */
    $db = new dbConnect();
    if ($row = $db->getPerson($_GET["id"])) {
        $db->dbLog($row['Name'] . " " . $row['Surname'] . " зашёл в Личный кабинет");
        if ($row['AppStatus'] == 0) {
            $db->setAppStatus($_GET["id"], 1); // Пользователь первый раз зашёл в Личный кабинет
        }
        /*
         * Вывод страницы личного кабинета
         *
         */ ?>
<div class="row"><div class="col-12"> 
<h1>Личный кабинет участника летнего физико-математического лагеря "Дельта"</h1>
<p>Здесь мы будем выкладывать всю необходимую информацию по организации поездки.</p>
</div></div>
<div class="main">
<div class="row">
<div class="col-6">
<h3>Здравствуйте! </h3>
<p>Скачайте, пожалуйста, <a href="documents/assignments.pdf" title="Вступительная олимпиада." target="_blank"
                            onclick='setAppStatus("<?php echo $row['UniqueId'] ?>", 2);'>вступительную олимпиаду</a> (.pdf).</p>
<p>Также Вы можете отправить файл с задачами себе на почту (<?php echo $row["Email"] ?>):</p>
<form id="form1" name="form1" method="get">
<input name="sbm" type="hidden" id="SendByMail">
<input name="id" type="hidden" id="UniqueId" value="<?php echo $row['UniqueId'] ?>">
<input type="submit" value="Выслать на почту!" onClick='document.getElementById("SendByMail").value = "yes";'>
<div id="sendStatus"></div>
</form>
</div> 
<!-- col-6 -->
<div class="col-4">
<h3><?php echo $row["Name"] . " " . $row["MiddleName"] . " " . $row["Surname"]; ?></h3>
<p><b>Email: </b><?php echo $row["Email"]?></p>
<p><b>Телефон: </b><?php echo $row["Tel"]?></p>
<p><b>Дата рождения: </b><?php echo date_format(date_create($row["Birthday"]),'d/m/Y');?>
</p>
</div> <!-- col-4 -->
</div> <!-- row -->
<div class="row"><div class="col-12">
<hr>
<p>Если у Вас возникли вопросы или Вы заметили неточность в регистрационных данных, пожалуйста, воспользуйтесь формой
    обратной связи или свяжитесь с нами:</p>
</div></div>  <!-- col-12, row -->
<div class="row"><div class="col-6">
    <p><b>Анна Семовская</b><br>
    +7(903)749-4851 (телефон, Telegram, WhatsApp)<br>
    anna.sem@gmail.com<br>
    Skype: aselect1976</p></div> <!-- col-6 -->
<div class="col-6">
    <p><b>Дмитрий Аблов</b><br>
    +7(903)795-4223 (телефон, Telegram, Viber, WhatsApp)<br>
    d.ablov@gmail.com<br>
    Skype: d.ablov</p></div></div> <!-- col-6, row -->
<div class="row"><div class="col-8">
<form method="post" action="feedback.php">
  <p>
  <input name="id" type="hidden" id="id" value="<?php echo $row['UniqueId'] ?>">
  <input name="name" type="hidden" id="name" value="<?php echo $row['Name'] . ' ' . $row['Surname'] ?>">
  <input name="email" type="hidden" id="email" value="<?php echo $row['Email'] ?>">
  <input type="submit" value="Связь с организаторами">
  </p>
  <p>&nbsp; </p>
</form>
</div></div> <!-- col-8, row -->
</div> 
<!-- main -->
  <?php
        /*
         * Обработка запроса выслать файл-олимпиады письмом
         */
        if (isset($_GET['sbm']) && $_GET['sbm']==='yes') {
            try {
                sendAssignmentsMail($row);
                $db->dbLog($row['Name'] . " " . $row['Surname'] . ": выслана вступительная олимпиада");
                $db->setAppStatus($row['UniqueId'], 2); // Выслана олимпиада
                echo <<<SUCCESS
<script>
document.getElementById("sendStatus").innerHTML = "Письмо со вступительной олимпиадой выслано Вам на почту.";
document.getElementById("sendStatus").style.color = "#188";
document.getElementById("sendStatus").style.opacity = "1";
</script>
SUCCESS;
            } catch (\PHPMailer\PHPMailer\Exception $e) {
                echo <<<ERROR
<script>
document.getElementById("sendStatus").innerHTML = "Проблема с отправкой письма. Свяжитесь, пожалуйста, с организаторами!";
document.getElementById("sendStatus").style.color = "#A33";
document.getElementById("sendStatus").style.opacity = "1";
</script>
ERROR;
                error("Unable to send file assignments.pdf to " . $row['Email']);
            }
        }

    } else {
        /*
        Обработка запроса с неверным UID
        */
?>
<div class="row"><div class="col-12"> 
<h2>Инентификатор пользователя не найден.</h2>
</div></div>
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
</div> </div> </div>
<?php
    }
}
?>
</body>
</html>
