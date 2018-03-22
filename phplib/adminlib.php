<?php
/**
 * Библиотека функций администрирования
 * Created by PhpStorm.
 * User: Dima
 * Date: 22.01.2018
 * Time: 14:00
 */
require_once 'dbConnect.php';

/*
 *  Диалог по установке нового пароля
 * @param $UID
 */
function admNewPass($UID) {
    try {
        $db = new dbConnect();
        $admin = $db->admGet($UID);
        $name = $admin["Name"];
        $sname = $admin["Surname"];
    } catch (PDOException $exception) {
        error("newAdmPass error: " . $exception);
        exit();
    }
    admPrintHeader();
    echo <<<FORM
<p>&nbsp;</p>    
<div class="main" id="main">
    <h1>Здравствуйте, $name $sname!</h1>
    <form id="newpass">
    <p>Введите новый пароль:<br>
    <input type="password" id="pass1" class="text-input"></p>
    <p>&nbsp;</p>
    <p>Повторно введите новый пароль:<br>
    <input type="password" id="pass2" class="text-input"></p>
    <p>&nbsp;</p>    
    <input type="button" value="Сохранить пароль" onclick="saveNewPass('$UID');">
    </form>
    <p>&nbsp;</p>
</div>
FORM;
    admPrintFooter();
}

/*
 * Ввод пароля и авторизация
 * @param $UID
 * @return: sessionID (success) or 0 (fault)
 */
function admLogin($UID) {
    try {
        $db = new dbConnect();
        $admin = $db->admGet($UID);
        $name = $admin["Name"];
        $sname = $admin["Surname"];
    } catch (PDOException $exception) {
        error("newLogin error: " . $exception);
        exit();
    }
    admPrintHeader();
    echo <<<FORM
<p>&nbsp;</p>    
<div class="main" id="main">
    <h1>Здравствуйте, $name $sname!</h1>
    <form id="passform" action="" onsubmit="return chkPass('$UID')">
    <p>Введите пароль:<br>
    <input type="password" id="pass" autofocus></p>
    <p>&nbsp;</p>    
    <input type="submit" value="Ввести пароль">
    </form>
    <p>&nbsp;</p>
</div>
FORM;
    admPrintFooter();
    return 0;
}

/*
 * Обработка ситуации нет или не верный UID
 */
function admWrongUID($UID = 0) {
    admPrintHeader();
    echo '<p>&nbsp;</p>';
    echo '<div class="main">';
    echo '<h1>&nbsp;</h1>';
    echo '<h1>&nbsp;</h1>';
    echo '<h1>&nbsp;</h1>';
    echo '<h1>&nbsp;</h1>';
    if ($UID == 0) {
        echo '<h1 style="text-align: center">Страница не предназначена для просмотра без параметров.</h1>';
    } else {
        echo '<h1 style="text-align: center">Идентификатор не найден.</h1>';
    }
    echo '<h1>&nbsp;</h1>';
    echo '<h1>&nbsp;</h1>';
    echo '<h1>&nbsp;</h1>';
    echo '<h1>&nbsp;</h1>';
    echo '</div>';
    admPrintFooter();
}

/*
 * Вывод заголовка html-страницы
 */
function admPrintHeader(){
    echo <<<HEADER
<!doctype html>
<html lang="ru">
<head>
    <meta charset="windows-1251">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Уголок администратора</title>
    <link href="CSS/common.css" rel="stylesheet" type="text/css">
    <link href="../CSS/mycabinet.css" rel="stylesheet" type="text/css">
    
    <script src="JS/adm_functions.js"></script>
</head>

<body>
HEADER;
}

/*
 * Вывод окончания html-страницы
 */
function admPrintFooter(){
    echo <<<FOOTER
</body>
</html>
FOOTER;
}