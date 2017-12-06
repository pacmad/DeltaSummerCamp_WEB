<!doctype html>
<html>
<head>
    <meta charset="windows-1251">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>�������� �����</title>
    <link href="CSS/common.css" rel="stylesheet" type="text/css">
    <link href="CSS/form.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="logo"></div>
<?php
require_once 'phplib/mail.php';
require_once 'phplib/common.php';
require_once 'phplib/dbConnect.php';

if (!(isset($_POST['email'])) && isset($_POST['name']) && isset($_POST['message'])) {
    error("Bad data from feedback form received.");
}

try {
    sendFeedbackMail($_POST['email'], $_POST['name'], $_POST['message']);
    $db = new dbConnect();
    $db->dbLog("���������� ��������� �� ����� �������� �����: " . $_POST['email'] . " " . $_POST['name'] . " " . $_POST['message']);
    /*
     * ����� �������������
     *
     */ ?>
        <div class="row">
            <div class="col-12">
                <h1>����� �������� ����� � �������������� ������� ������-��������������� ������ &quot;������&quot; �
                    �������</h1>
            </div>
        </div>
        <div class="main">
            <form id="feedback" name="feedback" method="post" action="index.php">
                <div class="row">
                    <div class="col-8">
                        <h3>���������� ���!</h3>
                        <p>��������� ����������. �� ����� �������� � ����!</p>
                    </div>
                </div> <!-- col-8, row -->
                <div class="row">
                    <div class="col-8">
                        <p><input type="submit" value="��������� �� ������� ��������."></p>
                    </div>
                </div> <!-- row --><!-- col-8-->
            </form>
        </div><!-- main -->
    <?php
    } catch (\PHPMailer\PHPMailer\Exception $e) {
    /*
     * �������� �� ������
     */ ?>
    <div class="row">
        <div class="col-12">
            <h1>��������! ��������� ������ ��� �������� ������!</h1>
            <h1>����������, ��������� � ���� "�������":</h1>
        </div>
    </div>
    <div class="main">
        <form id="feedback" name="feedback" method="post" action="index.php">
            <div class="row"><div class="col-6">
                    <p><b>���� ���������</b><br>
                        +7(903)749-4851 (�������, Telegram, WhatsApp)<br>
                        anna.sem@gmail.com<br>
                        Skype: aselect1976</p></div> <!-- col-6 -->
                <div class="col-6">
                    <p><b>������� �����</b><br>
                        +7(903)795-4223 (�������, Telegram, Viber, WhatsApp)<br>
                        d.ablov@gmail.com<br>
                        Skype: d.ablov</p></div></div> <!-- col-6, row -->
            <div class="row">
                <div class="col-8">
                    <p><input type="submit" value="��������� �� ������� ��������."></p>
                </div>
            </div> <!-- row --><!-- col-8-->
        </form>
    </div><!-- main -->
<?php
}
?>
</body>
</html>
