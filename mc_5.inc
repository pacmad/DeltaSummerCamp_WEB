<?php
/*
 * Анкета участника
 * $UID - уникальный номер пользователя, передаётся из mycabinet.php
 */
require_once 'phplib/dbConnect.php';
require_once 'phplib/mail.php';
require_once 'phplib/common.php';

if (!isset($UID)) {
    exit('Данный файл не предназначен для самостоятельного запуска');
}

/*
 *  Обработка загрузки фото
 */

$targetDir = 'photos/';

if (isset($_FILES['file'])) {

    define('MIN_FREE_SPACE', 20*1024*1024);

    $ret = array();

    if (disk_free_space($targetDir) < MIN_FREE_SPACE) {
        error("Загрузка фото: недостаточно места на диске! UID: " . $UID);
    }

    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir)) {
            error("Загрузка фотографии: не удалось создать директорию $targetDir. UID: $UID");
            exit;
        }
    }

    if (!is_array($_FILES['file']['name'])) { // single file
        $fileName = $_FILES['file']['name'];
        $tmpFileName = $_FILES['file']['tmp_name'];
        $error = $_FILES['file']['error'];
    } else { //Multiple files, file[], выбираем первый файл
        $fileCount = count($_FILES['file']['name']);
        if ($fileCount >= 1) {
            $fileName = $_FILES['file']['name'][0];
            $tmpFileName = $_FILES['file']['tmp_name'][0];
            $error = $_FILES['file']['error'][0];
        } else {
            $tmpFileName = null;
        }
    }

    if ($error) {
        $error("Загрузка фотографии, ошибка " . $error);
        exit();
    }

    $fileExt = strrchr($fileName, '.');

    list($widht, $height, $type) = getimagesize($tmpFileName);

    if ($type == IMG_JPG || $type == IMG_GIF || $type == IMG_PNG) {
        require_once "phplib/ImageManipulator.php";
        define("AVATAR_SIZE", 400);
        try {
            $manipulator = new ImageManipulator($tmpFileName);
            $cropSize = min($widht, $height);
            $x1 = round(($widht - $cropSize) / 2.0 );
            $x2 = $x1 + $widht;
            $y1 = round(($height - $cropSize) / 2.0);
            $y2 = $y1 + $cropSize;
            $manipulator->crop($x1, $y1, $x2, $y2);
            $manipulator->resample(AVATAR_SIZE, AVATAR_SIZE);
            $manipulator->save($targetDir . $UID . $fileExt);
        }
        catch (InvalidArgumentException $e){
            error("ImageManipulator: invalid argument " . $e);
        }
        catch (RuntimeException $e) {
            $error("ImageManipulator: runtime error " . $e);
        }

    }

    $ret[] = $fileName;
    echo json_encode($ret);

    $db->dbLog("Загружена фотография", $UID);
    if ($db->getAppStatus($UID) < 6) {
        $db->setAppStatus($UID, 6); // Меняем статус на "загружена фотография"
    }

    exit();
}

/*
 * Обработка AJAX-форм (параметры прилетают в $_POST)
 */

if (isset($_POST) && is_array($_POST) && count($_POST) > 0) {
    if ($_POST['form'] === "CertName") {
        try {
            $db->setCertName($UID, $_POST['certLang'], $_POST['certName']);
        } catch (PDOException $exception) {
            error("Error in setCertName(): $exception");
        }
        header("Content-type: text/html; charset=UTF-8");
        echo $_POST['certName'];

    }

    exit();
}

/*
 * Установка фото, если есть, иначе - заглушки.
 */

if (!empty($photos = glob($targetDir . $UID . '.*'))) {
    $photo = $photos[0];
} else {
    if ($row['Gender'] === 'f') {
        copy($targetDir . 'f.jpg', $photo = $targetDir . $UID . ".JPG");
    } else {
        copy($targetDir . 'm.jpg', $photo = $targetDir . $UID . ".JPG");
    }
}

/*
 * Инициализация полей форм
 */
try {
    $result = $db->getCertName($UID);
} catch (PDOException $exception) {
    error("Error in getCertName(): $exception");
}

foreach ($result as $row) {
    $certLang = $row['CertLang'];
    $certName = $row['CertName'];
//    $certName = urldecode($certName);
//    if ($certLang === "ru") {
//        $certName = iconv("UTF-8", "WINDOWS-1251", $certName);
//    }
//    elseif ($certLang === "de") {
//        $certName = iconv("UTF-8", "ASCII", $certName);
//    }
//    if (mb_check_encoding($certName, 'UTF-8')) $certName = utf8_decode($certName);
    break;
}

?>

<div class="row">
    <div class="col-12">
        <h3>Здравствуйте! </h3>
        <p>Заполните, пожалуйста, анкету участника:</p>
    </div>
</div>
<div class="row">
    <div class="col-4">
        <div class="photo">
            <div id="imguploader">
                <img id="photo" src="<?php echo $photo;?>">
            </div>
        </div>
        <br>
        <form id="certNameForm" method="post">
            <input name="form" value="CertName" hidden>
            <p>Выберите язык сертификата:<br>
                <select name="certLang" id="certLang" required>
                    <option value="" hidden disabled
                        <?php
                        if ($certName === "") {
                            echo " selected";
                        }
                        ?>
                    >Выбрать...</option>
                    <option value="ru"
                        <?php
                        if ($certLang === "ru") {
                            echo " selected";
                        }
                        ?>
                    >Русский</option>
                    <option value="de"
                        <?php
                        if ($certLang === "de") {
                            echo " selected";
                        }
                        ?>
                    >Deutsch</option>
                    <option value="en"
                        <?php
                        if ($certLang === "en") {
                            echo " selected";
                        }
                        ?>
                    >English</option>
                </select>
            </p>
            <p>Фамилия и имя участника на языке сертификата:<br>
                <input id="certName" name="certName" type="text" class="text-input"
                       <?php if ($certName !== "") {
                           echo " value='$certName'";
                       }
                       ?>
                       required>
                <input id="btnCertName" type="submit" value="&#x2714" class="smallbtn"></p>
        </form>
    </div>
    <p><b>Введите, пожалуйста, детали поездки:</b></p>
    <div class="col-4">
        <p>В Мюнхен</p>
        <form>
            <select name="coming_with" id="coming_with" required>
                <option value="" hidden disabled selected>С кем приезжает...</option>
                <option value="DimaAnya">С организаторами, рейс Lufthansa</option>
                <option value="Dubeniuk">С организаторами, рейс Utair</option>
                <option value="Singly">Самостоятельно</option>
                <option value="WithParents">Привозят родители</option>
            </select>
            <br>
            Дата:<br> <input type="text" name="coming_date" id="coming_date" value="16/07/2018" required><br>
            Время:<br> <input type="text" name="coming_time" id="coming_time" placeholder="hh:mm" required><br>
            Номер рейса:<br> <input type="text" name="coming_flightno" id="coming_flightno" required><br>
            Куда:<br> <select name="coming_place" id="coming_place" required>
                <option value="" hidden disabled selected>...</option>
                <option value="MUC">В аэропорт (Мюнхен)</option>
                <option value="hbf">На вокзал Hauptbahnhof</option>
                <option value="gorod">В культурный центр GOROD</option>
                <option value="other">Иное... (свяжитесь с организаторами!)</option>
            </select>
            <br><br>
            <input type="submit" title="Сохранить" name="coming_submit" id="coming_submit">
        </form>
    </div>
    <div class="col-4">
        <p>Из мюнхена</p>
        <form>
            <select name="leaving_with" id="leaving_with" required>
                <option value="" hidden disabled selected>С кем уезжает...</option>
                <option value="DimaAnya">С организаторами, рейс Lufthansa</option>
                <option value="Dubeniuk">С организаторами, рейс Utair</option>
                <option value="Singly">Самостоятельно</option>
                <option value="WithParents">Забирают родители</option>
            </select>
            <br>
            Дата:<br> <input type="text" name="leaving_date" id="leaving_date" value="30/07/2018" required><br>
            Время:<br> <input type="text" name="leaving_time" id="leaving_time" placeholder="hh:mm" required><br>
            Номер рейса:<br> <input type="text" name="leaving_flightno" id="leaving_flightno" required><br>
            Откуда:<br> <select name="leaving_place" id="leaving_place" required>
                <option value="" hidden disabled selected>...</option>
                <option value="MUC">Из аэропорта (Мюнхен)</option>
                <option value="hbf">От вокзала Hauptbahnhof</option>
                <option value="gorod">Из культурный центр GOROD</option>
                <option value="other">Иное... (свяжитесь с организаторами!)</option>
            </select>
            <br><br>
            <input type="submit" title="Сохранить" name="leaving_submit" id="leaving_submit">
        </form>
    </div>
</div>

