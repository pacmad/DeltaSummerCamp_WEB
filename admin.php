<?php
require_once 'phplib/dbConnect.php';
require_once 'phplib/common.php';
require_once 'phplib/adminlib.inc';

session_start();
if (isset($_SESSION['signed_in']) && $_SESSION['signed_in']) {
    if (isset($_SESSION['UID'])) {
        include 'phplib/admMain.inc';
        exit();
    } else {
        admWrongUID();
        exit();
    }
} elseif (isset($_GET["id"])) { // ��������� ���� �� ������ GET
    $UID = $_GET["id"];
    try {
        $db = new dbConnect();
        $res = $db->admCheck($UID);
        $db = null;
    } catch (PDOException $exception) {
        error("PDO Exception in admin.php: $exception");
    }

    switch ($res) {
        case -1: // �������� UID
            admWrongUID($UID);
            break;
        case 1: // ������ ���� (������ ������), ������� ���������� ������
            admNewPass($UID);
            break;
        case 0: // ������ ����������, ��������� �����
            admLogin($UID);
            break;
    }
} else {
    admWrongUID();
    exit();
}
?>