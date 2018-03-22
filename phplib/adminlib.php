<?php
/**
 * ���������� ������� �����������������
 * Created by PhpStorm.
 * User: Dima
 * Date: 22.01.2018
 * Time: 14:00
 */
require_once 'dbConnect.php';

/*
 *  ������ �� ��������� ������ ������
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
    <h1>������������, $name $sname!</h1>
    <form id="newpass">
    <p>������� ����� ������:<br>
    <input type="password" id="pass1" class="text-input"></p>
    <p>&nbsp;</p>
    <p>�������� ������� ����� ������:<br>
    <input type="password" id="pass2" class="text-input"></p>
    <p>&nbsp;</p>    
    <input type="button" value="��������� ������" onclick="saveNewPass('$UID');">
    </form>
    <p>&nbsp;</p>
</div>
FORM;
    admPrintFooter();
}

/*
 * ���� ������ � �����������
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
    <h1>������������, $name $sname!</h1>
    <form id="passform" action="" onsubmit="return chkPass('$UID')">
    <p>������� ������:<br>
    <input type="password" id="pass" autofocus></p>
    <p>&nbsp;</p>    
    <input type="submit" value="������ ������">
    </form>
    <p>&nbsp;</p>
</div>
FORM;
    admPrintFooter();
    return 0;
}

/*
 * ��������� �������� ��� ��� �� ������ UID
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
        echo '<h1 style="text-align: center">�������� �� ������������� ��� ��������� ��� ����������.</h1>';
    } else {
        echo '<h1 style="text-align: center">������������� �� ������.</h1>';
    }
    echo '<h1>&nbsp;</h1>';
    echo '<h1>&nbsp;</h1>';
    echo '<h1>&nbsp;</h1>';
    echo '<h1>&nbsp;</h1>';
    echo '</div>';
    admPrintFooter();
}

/*
 * ����� ��������� html-��������
 */
function admPrintHeader(){
    echo <<<HEADER
<!doctype html>
<html lang="ru">
<head>
    <meta charset="windows-1251">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>������ ��������������</title>
    <link href="CSS/common.css" rel="stylesheet" type="text/css">
    <link href="../CSS/mycabinet.css" rel="stylesheet" type="text/css">
    
    <script src="JS/adm_functions.js"></script>
</head>

<body>
HEADER;
}

/*
 * ����� ��������� html-��������
 */
function admPrintFooter(){
    echo <<<FOOTER
</body>
</html>
FOOTER;
}