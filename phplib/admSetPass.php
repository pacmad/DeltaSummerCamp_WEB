<?php
require_once 'dbConnect.php';
http_response_code(400); // Bad request
if (isset($_POST["UID"]) && $_POST["UID"] != null) {
    if (isset($_POST["PASS"]) && $_POST["PASS"] != null) {
        try {
            $db = new dbConnect();
            $result = $db->admSetPass($_POST["UID"], $_POST["PASS"]);
        } catch (PDOException $exception) {
            error("admSetPass error: " . $exception);
        }
    }
}
if ($result == 1) {
    http_response_code(201); // Created
    $db->dbLog("Пароль администратора установлен", $_POST['UID']);
}
?>