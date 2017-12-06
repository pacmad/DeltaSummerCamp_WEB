<!doctype html>
<html>
<head>
<meta charset="windows-1251">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>������ �������</title>
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
<p>�������� �� ������������� ��� ��������� ��� ����������.</p>
<?php
} else {
    /*
     * ��������� AJAX-������� �� ��������� ������� ������
     */
    if (isset($_GET["SetStatus"])) {
        $db = new dbConnect();
        $db->setAppStatus($_GET["id"], $_GET["SetStatus"]);
        exit();
    }

    /*
     * �������� ������ ������� ��������
     */
    $db = new dbConnect();
    if ($row = $db->getPerson($_GET["id"])) {
        $db->dbLog($row['Name'] . " " . $row['Surname'] . " ����� � ������ �������");
        if ($row['AppStatus'] == 0) {
            $db->setAppStatus($_GET["id"], 1); // ������������ ������ ��� ����� � ������ �������
        }
        /*
         * ����� �������� ������� ��������
         *
         */ ?>
<div class="row"><div class="col-12"> 
<h1>������ ������� ��������� ������� ������-��������������� ������ "������"</h1>
<p>����� �� ����� ����������� ��� ����������� ���������� �� ����������� �������.</p>
</div></div>
<div class="main">
<div class="row">
<div class="col-6">
<h3>������������! </h3>
<p>��������, ����������, <a href="documents/assignments.pdf" title="������������� ���������." target="_blank"
                            onclick='setAppStatus("<?php echo $row['UniqueId'] ?>", 2);'>������������� ���������</a> (.pdf).</p>
<p>����� �� ������ ��������� ���� � �������� ���� �� ����� (<?php echo $row["Email"] ?>):</p>
<form id="form1" name="form1" method="get">
<input name="sbm" type="hidden" id="SendByMail">
<input name="id" type="hidden" id="UniqueId" value="<?php echo $row['UniqueId'] ?>">
<input type="submit" value="������� �� �����!" onClick='document.getElementById("SendByMail").value = "yes";'>
<div id="sendStatus"></div>
</form>
</div> 
<!-- col-6 -->
<div class="col-4">
<h3><?php echo $row["Name"] . " " . $row["MiddleName"] . " " . $row["Surname"]; ?></h3>
<p><b>Email: </b><?php echo $row["Email"]?></p>
<p><b>�������: </b><?php echo $row["Tel"]?></p>
<p><b>���� ��������: </b><?php echo date_format(date_create($row["Birthday"]),'d/m/Y');?>
</p>
</div> <!-- col-4 -->
</div> <!-- row -->
<div class="row"><div class="col-12">
<hr>
<p>���� � ��� �������� ������� ��� �� �������� ���������� � ��������������� ������, ����������, �������������� ������
    �������� ����� ��� ��������� � ����:</p>
</div></div>  <!-- col-12, row -->
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
<div class="row"><div class="col-8">
<form method="post" action="feedback.php">
  <p>
  <input name="id" type="hidden" id="id" value="<?php echo $row['UniqueId'] ?>">
  <input name="name" type="hidden" id="name" value="<?php echo $row['Name'] . ' ' . $row['Surname'] ?>">
  <input name="email" type="hidden" id="email" value="<?php echo $row['Email'] ?>">
  <input type="submit" value="����� � ��������������">
  </p>
  <p>&nbsp; </p>
</form>
</div></div> <!-- col-8, row -->
</div> 
<!-- main -->
  <?php
        /*
         * ��������� ������� ������� ����-��������� �������
         */
        if (isset($_GET['sbm']) && $_GET['sbm']==='yes') {
            try {
                sendAssignmentsMail($row);
                $db->dbLog($row['Name'] . " " . $row['Surname'] . ": ������� ������������� ���������");
                $db->setAppStatus($row['UniqueId'], 2); // ������� ���������
                echo <<<SUCCESS
<script>
document.getElementById("sendStatus").innerHTML = "������ �� ������������� ���������� ������� ��� �� �����.";
document.getElementById("sendStatus").style.color = "#188";
document.getElementById("sendStatus").style.opacity = "1";
</script>
SUCCESS;
            } catch (\PHPMailer\PHPMailer\Exception $e) {
                echo <<<ERROR
<script>
document.getElementById("sendStatus").innerHTML = "�������� � ��������� ������. ���������, ����������, � ��������������!";
document.getElementById("sendStatus").style.color = "#A33";
document.getElementById("sendStatus").style.opacity = "1";
</script>
ERROR;
                error("Unable to send file assignments.pdf to " . $row['Email']);
            }
        }

    } else {
        /*
        ��������� ������� � �������� UID
        */
?>
<div class="row"><div class="col-12"> 
<h2>������������� ������������ �� ������.</h2>
</div></div>
<div class="row"><div class="col-8"><div class="main">
<p>���� �� ������������������ � ������ ������-�������������� ������ "������" � ������� � ������ �� ��� �������� �� ������
    � ������ � �������������� �����������, ���������� ��������� � ��������������"</p>
<form method="post" action="feedback.php">
  <p>
  <input name="id" type="hidden" id="id" value="<?php echo $row['UniqueId'] ?>">
  <input name="name" type="hidden" id="name" value="<?php echo $row['Name'] . ' ' . $row['Surname'] ?>">
  <input name="email" type="hidden" id="email" value="<?php echo $row['Email'] ?>">
  <input type="submit" value="����� � ��������������">
  </p>
</form>
</div> </div> </div>
<?php
    }
}
?>
</body>
</html>
