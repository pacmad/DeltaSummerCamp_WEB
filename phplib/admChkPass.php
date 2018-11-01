<?php
require_once 'dbConnect.php';
http_response_code(400); // Bad request
if (isset($_POST["UID"]) && $_POST["UID"] != null) {
    if (isset($_POST["PASS"]) && $_POST["PASS"] != null) {
        try {
            $db = new dbConnect();
            $result = $db->admCheck($_POST["UID"], $_POST["PASS"]);
        } catch (PDOException $exception) {
            error("admSetPass error: " . $exception);
        }
    }
}
if ($result == 1) {
    http_response_code(202); // Verified
    session_start();
    session_destroy();
    session_start();
    $_SESSION['signed_in'] = true;
    $_SESSION['UID'] = $_POST['UID'];
    $db->dbLog("Выполнен вход в админку", $_POST['UID']);
} else if ($result == 0) {
    http_response_code(401); //Wrong password
    $db->dbLog("Попытка входа в админку с неправильным паролем \"" . $_POST['PASS'] ."\"", $_POST['UID']);
}
