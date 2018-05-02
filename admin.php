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
} elseif (isset($_GET["id"])) { // Начальный вход по методу GET
    $UID = $_GET["id"];
    try {
        $db = new dbConnect();
        $res = $db->admCheck($UID);
        $db = null;
    } catch (PDOException $exception) {
        error("PDO Exception in admin.php: $exception");
    }

    switch ($res) {
        case -1: // Неверный UID
            admWrongUID($UID);
            break;
        case 1: // Первый вход (пустой пароль), требуем установить пароль
            admNewPass($UID);
            break;
        case 0: // Пароль установлен, обработка входа
            admLogin($UID);
            break;
    }
} else {
    admWrongUID();
    exit();
}
?>