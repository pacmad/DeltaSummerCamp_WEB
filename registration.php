<!doctype html>
<html>
<head>
<meta charset="windows-1251">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>�����������</title>
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
     * ����� �����������
     *
     */
?>
<div class="row"><div class="col-12">
<h1>����������� � ������ ������-�������������� ������ "������"</h1>
<p class="explanation">����, ���������� ��������� <span class="required">*</span> �����������.</p>
</div></div> <!-- col12, row -->

<div class="main">
<form id="form" name="form" method="post" onsubmit="return checkForm()">
  <div class="row">
  <div class="col-4">
  <p>E-Mail <span class="required">*</span><br>
    <input class="text-input" name="email" type="email" required id="email" placeholder="ivan@ivanov.earth" title="����� ����������� �����">
    <br>
  <span class="explanation">��� ����� ����������� �����. �� ���� ����� ������������� �����������.</span></p>
  </div> <!-- col- -->
  <div class="col-4">
  <p> ���������� ������� <span class="required">*</span><br>
    <input name="tel" type="tel" required class="text-input" id="tel" title="������� ��� �����">
  </p>
  </div> <!-- col- -->
  </div> <!-- row -->
  <div class="row">
  <div class="col-3">
  <p>������� <span class="required">*</span><br>
    <input class="text-input" name="surname" type="text" required id="surname" placeholder="������" title="������� ������">
  </p>
  </div> <!-- col-4 -->
  <div class="col-3">
  <p>��� <span class="required">*</span><br>
    <input class="text-input" name="name" type="text" required id="name" placeholder="����" title="��� ������">
  </p>
  </div> <!-- col-4 -->
  <div class="col-3">
  <p>��������<br>
    <input class="text-input" name="middlename" type="text" id="middlename" placeholder="��������" title="�������� ������">
  </p>
  </div> <!-- col-4 -->
  </div> <!-- row -->
  <div class="row">
  <div class="col-3">
  <p>���� �������� <span class="required">*</span><br>
    <input class="text-input" name="birthday" type="text" required id="birthday" placeholder="dd/mm/yyyy"
           title="���� ��������" onChange="checkDate(this);">
    <span class="hidden_error" id="date_error">�������� ������ ����!<br>����������� "����/�����/���"<br>��������: 27/03/1999</span>
    <span class="explanation"><span class="age" id="age"></span></span>
  </p>
  </div> <!-- col-3 -->
  <div class="col-3">
  <div id="gender">
  <p>��� <span class="required">*</span><br>
    <input name="gender" type="radio" required id="female" value="f"><label for="female"><span><span></span></span>�������</label>&nbsp;&nbsp;
    <input name="gender" type="radio" required id="male" value="m"><label for="male"><span><span></span></span>�������</label>
  </p>
  </div> <!-- gender -->
  </div> <!-- col-3 -->
  </div> <!-- raw -->
  <div class="row">
  <div class="col-3">
  <p>�����<br>
    <input class="text-input" name="class" type="number" id="class" title="�����">
    <br>
  <span class="explanation">�� ������� 11-������ ������� ��� ��� �������� ������ � �����.</span> </p>
  </div> <!-- col-3 -->
  <div class="col-3">
  <p>����� <span class="required">*</span><br>
    <input class="text-input" name="school" type="text" required id="school" title="�����">
  </p>
  </div> <!-- col-3 -->
  <div class="col-3">
  <p>����� <span class="required">*</span><br>
    <input class="text-input" name="city" type="text" required id="sity" title="����� ����������">
  </p>
  </div> <!-- col-3 -->
  <div class="col-3">
  <p>������ <span class="required">*</span><br>
    <input class="text-input" name="country" type="text" required id="country" title="������ ����������">
  </p>
  </div> <!-- col-3 -->
  </div> <!-- row -->
  <div class="row"> <div class="col-6">
  <p>����� <span class="required">*</span><br>
    <input class="text-input" name="langs" type="text" required id="langs" title="������ ������� ������� ������" value="�������" size="60">
    <br>
  <span class="explanation">�����, �������� ������� ������. ����������� ����� �������.</span></p>
  <p>�������������� ����������<br>
    <textarea class="text-input" name="notes" cols="60" rows="4" id="notes" title="���������"></textarea>
    <br>
  <span class="explanation">��, ��� �� ��� ������ �� ��� ��������. ����� �� ������ ������ ���� ������� ������� �� delta@mathbaby.ru</span></p>
  <div class="indented" id="agree"><p>�� ��������� ��.64 �.1 ��������� ������� �� ��� ���� �������� ��   ��������� ��������� ���� ������ ����� ������� ��� ������� � ��������   �����, ��������� ���������� � �����, �������, �����������. �������������   ������ ��� ������ ����� �� �������������.<br>
  <b>��������</b><span class="required" title="����������� ��� ����������.">*</span>: 
  <input name="agree" type="checkbox" id="agree" value="agree" title="��������">
  <label for="agree"><span><span></span></span></label>
  </p></div>
  <p>
    <input name="submit" type="submit" id="submit" value="����������������">
  </p>
  </div></div> <!-- col-6 row -->
  <p><input name="ALL_DONE" type="hidden" id="ALL_DONE" value="Ok"><br>
  </p>
</form>
</div> <!-- class "main" -->
<?php
} elseif ($_POST["ALL_DONE"] !== 'Ok') {
    error('���-�� ����� �� ���...');
} else {
    /***
     *
     * ��������� ������ ����� ���������� ����� �����������
     *
     */
    try {
        $db = new dbConnect();
        $uniqueID = $db->putRegData();
        if ($db->getStatus() == DB_ADD_OK) {
            $person = $db->getPerson($uniqueID);
            if (!sendRegMail($person)) {
                error("�������� � ��������� ������ �������������");
            } else {
                $db->dbLog("���������� ������-������������� �����������, UniqueId=" . $uniqueID);
            }
            /*****
             *
             * ��������� �� �������� �����������
             *
             */
            ?>
            <div class="row">
                <div class="col-12">
                    <h1>����������� � ������ ������-�������������� ������ "������"</h1>
                    <p class="explanation">&nbsp;</p>
                </div>
            </div> <!-- col12, row -->

            <div class="main">
                <form id="form" name="form" method="post" action="index.php">
                    <div class="row">
                        <div class="col-2">&nbsp;</div>
                        <div class="col-8">
                            <p>������� �� �����������!</p>
                            <p>�� ��� ����� ����� ������� ������.</p>
                        </div>
                        <!-- col-8 -->
                        <div class="col-2">&nbsp;</div>
                    </div> <!-- row -->
                    <div class="row">
                        <div class="col-6">
                            <p>
                                <input name="submit" type="submit" id="submit" value="��������� �� ����">
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
             * ��������� ��������� �����������
             */
            $person = $db->getPerson($uniqueID);
            if (!sendRegMail($person)) {
                error("�������� � ��������� ������ �������������");
            } else {
                $db->dbLog("�������� ���������� ������-������������� �����������, UniqueId=" . $uniqueID);
            }
            ?>
            <div class="row">
                <div class="col-12">
                    <h1>����������� � ������ ������-�������������� ������ "������"</h1>
                    <p class="explanation">&nbsp;</p>
                </div>
            </div> <!-- col12, row -->

            <div class="main">
                <form id="form" name="form" method="post" action="index.php">
                    <div class="row">
                        <div class="col-2">&nbsp;</div>
                        <div class="col-8">
                            <p>��������!</p>
                            <p><b><?php echo $person['Name'] . " " . $person['Surname']?></b> � ����� ��������
                                <?php echo $person['Birthday']?> ��� ���������������!</p>
                            <p>�� ������ ������ �� �������� ��������� ������ �� ��� ����� (<?php echo $person['Email'] ?>).</p>
                        </div>
                        <!-- col-8 -->
                        <div class="col-2">&nbsp;</div>
                    </div> <!-- row -->
                    <div class="row">
                        <div class="col-6">
                            <p>
                                <input name="submit" type="submit" id="submit" value="��������� �� ����">
                            </p>
                        </div>
                    </div> <!-- col-6 row -->
                    <p><input name="ALL_DONE" type="hidden" id="ALL_DONE" value="Ok"><br>
                    </p>
                </form>
                <div class="row"><div class="col-12">
                        <hr>
                        <p>���� � ��� �������� ������� ��� �� �������� ���������� � ��������������� ������, ���������� �������������� ������
                            �������� ����� ��� ��������� � ����:</p>
                    </div></div>  <!-- col-12, row -->
                <div class="row"><div class="col-6">
                        <p><b>���� ���������</b><br>
                            +7(903)749-4851 (�������, Telegram)<br>
                            anna.sem@gmail.com<br>
                            Skype: aselect1976</p></div> <!-- col-6 -->
                    <div class="col-6">
                        <p><b>������� �����</b><br>
                            +7(903)795-4223 (�������, Telegram, Viber, WhatsUpp)<br>
                            d.ablov@gmail.com<br>
                            Skype: d.ablov</p></div></div> <!-- col-6, row -->
                <div class="row"><div class="col-8">
                        <form method="post" action="feedback.php">
                            <p>
                                <input name="id" type="hidden" id="id" value="<?php echo $person['UniqueId'] ?>">
                                <input name="name" type="hidden" id="name" value="<?php echo $person['Name'] . ' ' . $person['Surname'] ?>">
                                <input name="email" type="hidden" id="email" value="<?php echo $person['Email'] ?>">
                                <input type="submit" value="����� � ��������������">
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