<?php
require_once "phplib/common.inc";

// Обработка AJAX-запросов
doAjax();

// Формируем заголовок
include "phplib/header_index.inc";

function doAjax() {
    if(isset($_POST['width']) && isset($_POST['height'])) {
        $_SESSION['screen_width'] = $_POST['width'];
        $_SESSION['screen_height'] = $_POST['height'];
        if ($_SESSION['screen_width'] < $_SESSION['screen_height']) {

        }
//        echo json_encode(array('outcome'=>'success'));

        exit();
    } /*else {
        echo json_encode(array('outcome'=>'error','error'=>"Couldn't save dimension info"));
    }*/
}
?>
