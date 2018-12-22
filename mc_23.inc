<?php
/*
 * Обработка загрузки файлов решений вступительной олимпиады
 * $UID - уникальный номер пользователя, передаётся из mycabinet.php
 */
require_once 'phplib/dbConnect.php';
require_once 'phplib/mail.php';
require_once 'phplib/common.inc';

if (!isset($UID)) {
    exit('Данный файл не предназначен для самостоятельного запуска');
}

define('MIN_FREE_SPACE', 50*1024*1024);
if (isset($_FILES['file'])) {
    $ret = array();
    $error = $_FILES['file']['error'];
    $targetDir = "uploads/$UID/";
    if (disk_free_space('uploads/') < MIN_FREE_SPACE) {
        $error("Загрузка олимпиады: недостаточно места на диске! UID: " . $UID);
    }
    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir)) {
            $error("Загрузка олимпиады: не удалось создать директорию $targetDir. UID: $UID");
            exit;
        }
    }
    if (!is_array($_FILES['file']['name'])) { // single file
        $fileName = $_FILES['file']['name'];
        move_uploaded_file($_FILES['file']['tmp_name'], $targetDir . $fileName);
        $ret[] = $fileName;
    } else { //Multiple files, file[]
        $fileCount = count($_FILES['file']['name']);
        for ($i=0; $i < $fileCount; $i++) {
            $fileName = $_FILES['file']['name'][$i];
            move_uploaded_file($_FILES['file']['tmp_name'][$i], $targetDir . $fileName);
            $ret[] = $fileName;
        }
    }
    echo json_encode($ret);
    $db->dbLog("Получено решение олимпиады", $UID);
    if ($db->getAppStatus($UID) < 3) {
        $db->setAppStatus($UID, 3); // Меняем статус на "получено"
        try {
            sendComfirmMail($db->getPerson($UID));
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            error("Не удалось выслать подтверждение о получении олимпиады");
        }
    }
}
?>
<div class="row">
    <div class="col-12">
        <h3>Здравствуйте! </h3>
    </div>
</div>
<div class="row">
    <div class="col-6">
        <p>Загрузите, пожалуйста, решение олимпиады ниже или вышлите на адрес электронной почты:
            <a href="mailto:summer.camp.delta@gmail.com">summer.camp.delta@gmail.com</a>
        </p>
        <!-- D&D Zone -->
        <div id="fileuploader">
            <button id="uploadLabel">Выберите из списка</button>
            <p>или переместите сюда файлы для загрузки</p>
        </div>
        <!-- /D&D Zone -->
    </div>
    <!-- col-6 -->
    <div class="col-6">
        <p>Вступительную олимпиаду можно открыть по этой ссылке:
            <a href="documents/assignments.pdf" title="Вступительная олимпиада." target="_blank"
            onclick='setDateWorkSent("<?php echo $row['UniqueId'] ?>");'>вступительную олимпиаду</a> (.pdf).</p>
        <p>Также Вы можете отправить файл с задачами себе на почту (<?php echo $row["Email"] ?>):</p>
        <form id="SentWorkByMail" name="SentWorkByMail" method="get">
            <input name="sbm" type="hidden" id="SendByMail">
            <input name="id" type="hidden" id="UniqueId" value="<?php echo $row['UniqueId'] ?>">
            <input type="submit" value="Выслать на почту!" onClick='document.getElementById("SendByMail").value = "yes";'>
            <div id="sendStatus"></div>
        </form>
    </div> <!-- col-6 -->
</div> <!-- row -->
