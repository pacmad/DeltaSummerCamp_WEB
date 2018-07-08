<!doctype html>
<html>
<head>
<meta charset="windows-1251">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<?php
include 'phplib/yandex.metrika.php';
include 'phplib/google.analytics.php';
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>������ �������</title>
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
<!-- ��� �������� ������ ���������� ������� ���������� jquery.uploadfile.js -->
<!-- � ���������� ���� ����������� ����������!!! -->
<script src="JS/lib/jquery.uploadfile.js"></script>
<script src="JS/cabinet_functions.js"></script>
</head>
<body>
<div class="logo"></div>

<?php
require_once 'phplib/dbConnect.php';
require_once 'phplib/mail.php';
require_once 'phplib/common.php';

if (!isset($_GET["id"])) {
    include 'mc_err_empty.inc';
    http_response_code(404);
    exit();
} else {
    $UID = $_GET["id"];
    /*
     * ��������� AJAX-������� �� ������� setDateWorkSent() �� ��������� ������� ������
     * ����� ����, ��� ���� ������� ���������. ������ ������ ����������� �� "2"
     * (���� �� � ��� �� "2") �, ���������� ���� ������� ���������.
     */
    if (isset($_GET["SetStatus"])) {
        $db = new dbConnect();
        if ($db->getAppStatus($UID) != 2) {
            $db->setWorkDaySent($UID);
            $db->dbLog("������� ������������� ���������", $UID);
        }
        exit();
    }

    /*
     * ��������� AJAX-������� �� ��������� � ������� "���������� 2" �� ������
     */
    if (isset($_GET["app"])) {
        include 'mc_app2gen.inc';
        exit();
    }

    /*
     * �������� ������ ������� ��������
     */
    $db = new dbConnect();
    if ($row = $db->getPerson($UID)) {
        $db->dbLog($row['Name'] . " " . $row['Surname'] . " ����� � ������ �������", $UID);
        if ($row['AppStatus'] == 0) {
            $db->setAppStatus($UID, 1); // ������������ ������ ��� ����� � ������ �������
        }
        /*
         * ����� �������� ������� ��������
         *
         */

        $appStatus = $db->getAppStatus($UID);

        // ������� ���������� subTitle
        $subTitle = "";
        if ($appStatus > 1) {
            $name = $row["Name"];
            $surname = $row["Surname"];
            $email = $row["Email"];
            $phone = $row["Tel"];
            $subTitle = "<strong>$name $surname</strong>";
        } elseif ($appStatus < 0) {
            $subTitle = "����������� �������.";
        } else {
            $subTitle = "����� �� ����� ����������� ��� ����������� ���������� �� ����������� �������.";
        }

        // ������� ���������
        echo "
            <div class=\"title\">
            <h1>������ ������� ��������� ������� ������-��������������� ������ \"������\"</h1>
            <h2 id=\"subTitle\">$subTitle</h2>
            </div>
        ";

        // ����� ��������� ����� � ����������� �� ������� ������������
        echo "<div class=\"main\">";
        if ($appStatus < 0) { // ����������� �������
            include "mc_-.inc";
        } elseif ($appStatus < 2) { // ������� ���������
            include "mc_01.inc";
        } elseif ($appStatus < 4) { // �������� ����������
            include "mc_23.inc";
        } elseif ($appStatus < 25) { // ��������� ������ (������ = 15) � ������������� ���������� � �������� (������ = 20)
            include "mc_5.inc";
        }
        include "mc_bottom.inc";
        echo "</div>";


        /*
         * ��������� ������� ������� ����-��������� �������
         */
        if (isset($_GET['sbm']) && $_GET['sbm']==='yes') {
            try {
                sendAssignmentsMail($row);
                $db->dbLog($row['Name'] . " " . $row['Surname'] . ": ������� ������������� ���������", $_GET["id"]);
                $db->setWorkDaySent($_GET["id"]);
                echo '
                    <script>
                    document.getElementById("sendStatus").innerHTML = "������ �� ������������� ���������� ������� ��� �� �����.";
                    document.getElementById("sendStatus").style.color = "#188";
                    document.getElementById("sendStatus").style.opacity = "1";
                    </script>
                ';
            } catch (\PHPMailer\PHPMailer\Exception $e) {
                echo '
                    <script>
                    document.getElementById("sendStatus").innerHTML = "�������� � ��������� ������. ���������, ����������, � ��������������!";
                    document.getElementById("sendStatus").style.color = "#A33";
                    document.getElementById("sendStatus").style.opacity = "1";
                    </script>
                ';
                error("Unable to send file assignments.pdf to " . $row['Email'] . " UID=" . $UID);
            }
        }

    } else { // ��������� ������� � �������� UID
        include "mc_err_unknown_id.inc";
    }
}
?>
</body>
</html>
