<?php
/*
 * Анкета участника
 * $UID - уникальный номер пользователя, передаётся из mycabinet.php
 */
require_once 'phplib/dbConnect.php';
require_once 'phplib/mail.php';
require_once 'phplib/common.inc';

if (!isset($UID)) {
    exit('Данный файл не предназначен для самостоятельного запуска');
}

/*
 *
 *  Обработка загрузки фото AJAX
 *
 */

$targetDir = 'photos/';

if (isset($_FILES['file'])) {

    console_log($_FILES);

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

    $error = false;
    if (!is_array($_FILES['file']['name'])) { // single file
        $fileName = $_FILES['file']['name'];
        $fileSize = $_FILES['file']['size'];
        $tmpFileName = $_FILES['file']['tmp_name'];
        $error = $_FILES['file']['error'];
    } else { //Multiple files, file[], выбираем первый файл
        $fileCount = count($_FILES['file']['name']);
        if ($fileCount >= 1) {
            $fileName = $_FILES['file']['name'][0];
            $fileSize = $_FILES['file']['size'][0];
            $tmpFileName = $_FILES['file']['tmp_name'][0];
            $error = $_FILES['file']['error'][0];
        } else {
            $tmpFileName = null;
            error("Загрузка фотографии,  UID: $UID, ошибка: '$tmpFileName' = null. " . var_dump($_FILES));
            exit();
        }
    }

    if ($error) {
        error("Загрузка фотографии, ошибка " . $error);
        exit();
    }

    $fileExt = strrchr($fileName, '.');
    if (!strcasecmp($fileExt, ".jpeg")) $fileExt = ".jpg";
    $rawFileName = $targetDir . "rawPhoto" . $fileExt;
    $targetFile = $targetDir . $UID . $fileExt;
    move_uploaded_file($tmpFileName, $rawFileName);

    list($width, $height, $type) = getimagesize($rawFileName);

    if ($type != IMG_JPG && $type != IMG_GIF && $type != IMG_PNG) {
        rename($rawFileName, $targetFile . 'unknown');
        copy('photos/toEdit.jpg', $targetFile);
        error("Загружена не фотография (неверный тип файла $fileExt): $fileName,  UID: $UID");
    }

    if ($fileSize < 1024*1024) { // На очень больших файлах функция не работает
        $cropSize = min($width, $height);
        $x1 = round(($width - $cropSize) / 2.0);
        $x2 = $x1 + $width;
        $y1 = round(($height - $cropSize) / 2.0);
        $y2 = $y1 + $cropSize;
        require_once "phplib/ImageManipulator.php";
        define("AVATAR_SIZE", 400);
        try {
            $manipulator = new ImageManipulator($rawFileName);
            $manipulator->setImageFile($rawFileName);
            $manipulator->crop($x1, $y1, $x2, $y2);
            $manipulator->resample(AVATAR_SIZE, AVATAR_SIZE);
            $manipulator->save($targetFile);
        } catch (InvalidArgumentException $e) {
            error("ImageManipulator: invalid argument " . $e);
            exit();
        } catch (RuntimeException $e) {
            error("ImageManipulator: runtime error " . $e);
            exit();
        }
    } else {
        rename($rawFileName, $targetFile . '.toEdit.jpg');
        copy('photos/toEdit.jpg', $targetFile);
        error("ToDo: отредактировать фото $targetFile");
    }
    $ret[] = $fileName;
    echo json_encode($ret);

    $db->dbLog("Загружена фотография", $UID);

    exit();
}

/*
 * Обработка AJAX-форм (параметры прилетают в $_POST)
 */

if (isset($_POST) && is_array($_POST) && count($_POST) > 0) {
    if ($_POST['form'] === "OwnTel") {
        try {
            $db->setOwnTel($UID, $_POST['ownTel']);
        } catch (PDOException $exception) {
            error("Error in setOwnTel(): $exception, UID: $UID");
            exit();
        }
        $db->dbLog("Загружен телефон ребёнка", $UID);
    } elseif ($_POST['form'] === "ComingDetails") {
        try {
            $db->setComingDetails($UID, $_POST['comingWith'], $_POST['comingDate'], $_POST['comingTime'], $_POST['comingFlight'], $_POST['comingPlace']);
        } catch (PDOException $exception) {
            error("Error in setComingDetails(): $exception, UID: $UID");
            exit();
        }
        $db->dbLog("Загружены детали приезда", $UID);
    } elseif ($_POST['form'] === "LeavingDetails") {
        try {
            $db->setLeavingDetails($UID, $_POST['leavingWith'], $_POST['leavingDate'], $_POST['leavingTime'], $_POST['leavingFlight'], $_POST['leavingPlace']);
        } catch (PDOException $exception) {
            error("Error in setLeavingDetails(): $exception, UID: $UID");
            exit();
        }
        $db->dbLog("Загружены детали отъезда", $UID);
    } elseif ($_POST['form'] === "HealthDetails") {
        try {
            $db->setHealthDetails($UID, $_POST['healthText']);
        } catch (PDOException $exception) {
            error("Error in setHealthDetails(): $exception, UID: $UID");
            exit();
        }
        $db->dbLog("Загружены детали здоровья", $UID);
    } elseif ($_POST['form'] === "InsuranceDetails") {
        try {
            $db->setInshuranceDetails($UID, $_POST['insuranceText']);
        } catch (PDOException $exception) {
            error("Error in setInsuranceDetails(): $exception, UID: $UID");
            exit();
        }
        $db->dbLog("Загружены детали страховки", $UID);
    } elseif ($_POST['form'] === "NotesDetails") {
        try {
            $db->setNotesDetails($UID, $_POST['notesText']);
        } catch (PDOException $exception) {
            error("Error in setNotesDetails(): $exception, UID: $UID");
            exit();
        }
        $db->dbLog("Загружены детали форс-мажор", $UID);
    } elseif ($_POST['form'] === "OtherDetails") {
        try {
            $db->setOtherDetails($UID, $_POST['certLang'], $_POST['certName'], $_POST['visa'], $_POST['notebook'], $_POST['shirt']);
        } catch (PDOException $exception) {
            error("Error in SetOtherDetails(): $exception, UID: $UID");
            exit();
        }
        $db->dbLog("Загружены дополнительные поля", $UID);
    }

    exit();
}

/*
 * Установка фото, если есть, иначе - заглушки.
 */
$pattern = $targetDir . $UID . '.*';
$photos = glob($pattern);

if (isset($photos) && $photos != false && $photos !== '') {
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
    $result = $db->getOwnTel($UID);
} catch (PDOException $exception) {
    error("Error in getOwnTel(): $exception");
}
$row = $result->fetch();
if(!$ownTel = $row['OwnTel']) $ownTel = "";

try {
    $result = $db->getComingDetails($UID);
} catch (PDOException $exception) {
    error("Error in getComingDetails(): $exception");
}
$row = $result->fetch();
$comingWith = $row['ComingWith']; if($comingWith == null) $comingWith = "";
$comingDate = $row['ComingDate']; if($comingDate == null) $comingDate = "";
$comingTime = $row['ComingTime']; if($comingTime == null) $comingTime = "";
$comingFlight = $row['ComingFlight']; if($comingFlight == null) $comingFlight = "";
$comingPlace = $row['ComingPlace']; if($comingPlace == null) $comingPlace = "";

try {
    $result = $db->getLeavingDetails($UID);
} catch (PDOException $exception) {
    error("Error in getLeavingDetails(): $exception, UID: $UID");
}
$row = $result->fetch();
$leavingWith = $row['LeavingWith']; if($leavingWith == null) $leavingWith = "";
$leavingDate = $row['LeavingDate']; if($leavingDate == null) $leavingDate = "";
$leavingTime = $row['LeavingTime']; if($leavingTime == null) $leavingTime = "";
$leavingFlight = $row['LeavingFlight']; if($leavingFlight == null) $leavingFlight = "";
$leavingPlace = $row['LeavingPlace']; if($leavingPlace == null) $leavingPlace = "";

try {
    $result = $db->getHealthDetails($UID);
} catch (PDOException $exception) {
    error("Error in getHealthDetails(): $exception, UID: $UID");
}
$row = $result->fetch();
$healthDetails = $row['Health'];

try {
    $result = $db->getInsuranceDetails($UID);
} catch (PDOException $exception) {
    error("Error in getInsuranceDetails(): $exception, UID: $UID");
}
$row = $result->fetch();
$insuranceDetails = $row['Insurance'];

try {
    $result = $db->getNotesDetails($UID);
} catch (PDOException $exception) {
    error("Error in getNotesDetails: $exception, UID: $UID");
}
$row = $result->fetch();
$notesDetails = $row['NotesText'];

try {
    $result = $db->getOtherDetails($UID);
} catch (PDOException $exception) {
    error("Error in getOtherDetails: $exception, UID: $UID");
}
$row = $result->fetch();
if(!$certLang = $row['CertLang']) $certLang = "";
if(!$certName = $row['CertName']) $certName = "";
if(!$visa = $row['Visa']) $visa = "";
if(!$notebook = $row['Notebook']) $notebook = "";
if(!$shirt = $row['Shirt']) $shirt = "";
?>
<input id='data' name='data' value='<?php echo json_encode($flights); ?>' hidden>
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
        <form id="ownTelForm" method="post" class="questionnaire">
            <input name="form" value="OwnTel" hidden>
            <div class="form-title">Номер мобильного телефона ребёнка&nbsp;</div><br>
            Введите номер телефона, который будет у ребёнка с собой в лагере:<br>
            <input type="tel" name="ownTel" id="ownTel" class="text-input" value="<?php echo $ownTel ?>" placeholder="+4912345678999" required>
            <input id="btnOwnTel" type="submit" value="&#x2714" class="smallbtn">
        </form>
    </div>
    <div class="col-8">
        <p><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Введите детали поездки:</b></p>

        <div class="col-6">
        <form id="comeInForm" method="post" class="questionnaire">
            <div class="form-title">&nbsp;В Мюнхен&nbsp;</div><br>
            <input name="form" value="ComingDetails" hidden>
            <select name="comingWith" id="comingWith" required>
                <option value="" hidden disabled <?php if($comingWith === "") {echo 'selected';}?>>С кем приезжает...</option>
                <option value="Singly" <?php if($comingWith === "Singly") {echo 'selected';}?>>Надо встретить...</option>
                <option value="WithParents" <?php if($comingWith === "WithParents") {echo 'selected';}?>>Привозят родители</option>
                <?php
                foreach ($flights as $flightOwner => $flightOptions) {
                    echo '<option value="' . $flightOwner . '"';
                    if ($comingWith === $flightOwner) echo ' selected';
                    echo '>С преподавателями, рейс ' . $flightOptions["FlightTo"] . '</option>';
                }
                ?>
            </select>
            <br>
            Дата:<br> <input type="text" name="comingDate" id="comingDate" placeholder="dd/mm/yyyy"
                             value='<?php if($comingDate === "") {echo START_DAY;} else {echo "$comingDate";}?>'
                <?php if($comingWith !== "" && $comingWith !== "Singly" && $comingWith !== "WithParents") {
                                 echo ' disabled';}?> required><br>
            Время:<br> <input type="text" name="comingTime" id="comingTime" placeholder="hh:mm"
                            value='<?php echo "$comingTime" ?>'
                <?php if($comingWith !== "" && $comingWith !== "Singly" && $comingWith !== "WithParents") {
                                echo ' disabled';}?> required><br>
            Номер рейса:<br> <input type="text" name="comingFlight" id="comingFlight" value='<?php echo "$comingFlight" ?>'
                <?php if($comingWith !== "" && $comingWith !== "Singly" && $comingWith !== "WithParents") {
                    echo ' disabled';
                } elseif ($comingWith ==="WithParents") {
                    echo 'value="-" disabled';}?> required><br>
            Место встречи:<br> <select name="comingPlace" id="comingPlace" required
                <?php if($comingWith !== "" && $comingWith !== "Singly" && $comingWith !== "WithParents") {
                    echo 'disabled';} ?>>
                <option value="" hidden disabled <?php if($comingPlace === "") {echo 'selected';}?>>...</option>
                <option value="MUC" <?php if($comingPlace === "MUC") {echo 'selected';}?>>В аэропорту (Мюнхен)</option>
                <option value="hbf" <?php if($comingPlace === "hbf") {echo 'selected';}?>>На вокзале Hauptbahnhof</option>
                <option value="gorod" <?php if($comingPlace === "gorod") {echo 'selected';}?>>В культурном центре GOROD</option>
                <option value="other" <?php if($comingPlace === "other") {echo 'selected';}?>>Иное... (свяжитесь с организаторами!)</option>
                <?php
                if($comingWith !== "" && $comingWith !== "Singly" && $comingWith !== "WithParents") {
                    foreach ($flights as $flightOwner => $flightOptions) {
                        if ($comingWith === $flightOwner) {
                            echo '<option value=' . $flightOptions['From'] . ' selected>Аэропорт "';
                            if ($flightOptions['From'] === 'DME') echo 'Домодедово';
                            elseif ($flightOptions['From'] === 'SVO') echo 'Шереметьево';
                            elseif ($flightOptions['From'] === 'VKO') echo 'Внуково';
                            echo '"</option>';
                        }
                    }
                }
                ?>
            </select>
            <input id="btnComingDetails" type="submit" value="&#x2714" class="smallbtn">
        </form>
        </div>

        <div class="col-6">
        <form id="leaveOutForm" method="post" class="questionnaire">
            <div class="form-title">&nbsp;Из Мюнхена&nbsp;</div><br>
            <input name="form" value="LeavingDetails" hidden>
            <select name="leavingWith" id="leavingWith" required>
                <option value="" hidden disabled <?php if($leavingWith === "") {echo 'selected';}?>>С кем уезжает...</option>
                <option value="Singly" <?php if($leavingWith === "Singly") {echo 'selected';}?>>Надо проводить...</option>
                <option value="WithParents" <?php if($leavingWith === "WithParents") {echo 'selected';}?>>Забирают родители</option>
                <?php
                foreach ($flights as $flightOwner => $flightOptions) {
                    echo '<option value="' . $flightOwner . '"';
                    if ($leavingWith === $flightOwner) echo ' selected';
                    echo '>С преподавателями, рейс ' . $flightOptions["FlightOut"] . '</option>';
                }
                ?>
            </select>
            <br>
            Дата:<br> <input type="text" name="leavingDate" id="leavingDate" placeholder="dd/mm/yyyy"
                             value='<?php if($leavingDate === "") {echo FINISH_DAY;} else {echo "$leavingDate";}?>'
                <?php if($leavingWith !== "" && $leavingWith !== "Singly" && $leavingWith !== "WithParents") {
                    echo 'disabled';}?> required><br>
            Время:<br> <input type="text" name="leavingTime" id="leavingTime" placeholder="hh:mm"
                              value='<?php echo "$leavingTime" ?>'
                <?php if($leavingWith !== "" && $leavingWith !== "Singly" && $leavingWith !== "WithParents") {
                    echo 'disabled';}?> required><br>
            Номер рейса:<br> <input type="text" name="leavingFlight" id="leavingFlight" value='<?php echo "$leavingFlight" ?>'
                <?php if($leavingWith !== "" && $leavingWith !== "Singly" && $leavingWith !== "WithParents") {
                    echo 'disabled';
                } elseif ($leavingWith ==="WithParents") {
                    echo 'value="-" disabled';}?> required><br>
            Место отправления:<br> <select name="leavingPlace" id="leavingPlace" required
                <?php if($leavingWith !== "" && $leavingWith !== "Singly" && $leavingWith !== "WithParents") {
                    echo 'disabled';} ?>>
                <option value="" hidden disabled <?php if($leavingPlace === "") {echo 'selected';}?>>...</option>
                <option value="MUC" <?php if($leavingPlace === "MUC") {echo 'selected';}?>>Из аэропорта (Мюнхен)</option>
                <option value="hbf" <?php if($leavingPlace === "hbh") {echo 'selected';}?>>От вокзала Hauptbahnhof</option>
                <option value="gorod" <?php if($leavingPlace === "gorod") {echo 'selected';}?>>Из культурного центра GOROD</option>
                <option value="other" <?php if($leavingPlace === "other") {echo 'selected';}?>>Иное... (свяжитесь с организаторами!)</option>
                <?php
                if($leavingWith !== "" && $leavingWith !== "Singly" && $leavingWith !== "WithParents") {
                    foreach ($flights as $flightOwner => $flightOptions) {
                        if ($leavingWith === $flightOwner) {
                            echo '<option value=' . $flightOptions['From'] . ' selected>Аэропорт "';
                            if ($flightOptions['From'] === 'DME') echo 'Домодедово';
                            elseif ($flightOptions['From'] === 'SVO') echo 'Шереметьево';
                            echo '"</option>';
                        }
                    }
                }
                ?>
            </select>
            <input id="btnLeavingDetails" type="submit" value="&#x2714" class="smallbtn">
        </form>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-8">
        <p><b>Заполните важную информацию о ребенке</b></p>
        <form id="healthForm" method="post" class="questionnaire">
            <div class="form-title">&nbsp;Здоровье&nbsp;</div><br>
            Аллергические реакции, другие особенности здоровья, принимаемые медикаменты, ограничения в еде.<br>
            <input name="form" value="HealthDetails" hidden>
            <textarea name="healthText" id="healthText"><?php echo $healthDetails ?></textarea>
            <input id="btnHealthDetails" type="submit" value="&#x2714" class="smallbtn">
        </form>
        <br>
        <form id="insuranceForm" method="post" class="questionnaire">
            <div class="form-title">&nbsp;Страховка&nbsp;</div><br>
            Все участники «Дельты» из России в обязательном порядке страхуются компанией Bernhard (страховка действительна для получения визы).
            Если у ребёнка есть постоянная страховка страны Евросоюза, пожалуйста, опишите это здесь, включая последовательность действий в страховом случае.<br>
            <input name="form" value="InsuranceDetails" hidden>
            <textarea name="insuranceText" id="insuranceText"><?php echo $insuranceDetails ?></textarea>
            <input id="btnInsuranceDetails" type="submit" value="&#x2714" class="smallbtn">
        </form>
        <br>
        <form id="notesForm" method="post" class="questionnaire">
            <div class="form-title">&nbsp;Форс-мажор&nbsp;</div><br>
            Дополнительная информация по действиям организаторов в сложных ситуациях: список "горячих телефонов" по которым можно звонить,
            дополнительные инструкции и т.д.<br>
            <input name="form" value="NotesDetails" hidden>
            <textarea name="notesText" id="notesText"><?php echo $notesDetails ?></textarea>
            <input id="btnNotesDetails" type="submit" value="&#x2714" class="smallbtn">
        </form>
    </div>
    <div class="col-4">
        <p><b>...и ещё несколько важных вопросов</b></p>
        <form id="otherForm" method="post" class="questionnaire">
            <input name="form" value="OtherDetails" hidden>
            Нужна ли ребёнку виза:<br>
            <input name="visa" type="radio" required id="visa_onprocess" value="2" <?php if($visa == 2) echo "checked"; ?> hidden>
            <input name="visa" type="radio" required id="visa_yes" value="1" <?php if($visa == 1) echo "checked"; ?>>
            <label for="visa_yes"><span><span></span></span>нужна</label>&nbsp;&nbsp;
            <input name="visa" type="radio" required id="visa_no" value="0" <?php if($visa == 0) echo "checked"; ?>>
            <label for="visa_no"><span><span></span></span>не нужна</label>
            <br><br>
            В конце смены все участники получают сертификаты, в которых указаны все курсы и тема проекта участника.<br>
            Выберите язык сертификата:<br>
            <select name="certLang" id="certLang" required>
                <option value="" hidden disabled <?php if ($certName === "") echo " selected"; ?>>Выбрать...</option>
                <option value="ru" <?php if ($certLang === "ru") echo " selected"; ?>>Русский</option>
                <option value="de" <?php if ($certLang === "de") echo " selected"; ?>>Deutsch</option>
                <option value="en" <?php if ($certLang === "en") echo " selected"; ?>>English</option>
            </select>
            <br>
            Имя и фамилия участника на языке сертификата (первое слово - имя, второе - фамилия):<br>
            <input id="certName" name="certName" type="text" class="text-input" <?php if ($certName !== "") echo " value='$certName'"; ?> required>
            <br><br>
            В некоторых курсах и проектах требуется компьютер.<br>
            Будет ли у ребёнка с собой ноутбук:<br>
            <select name="notebook" id="notebook" required>
                <option value="" hidden disabled <?php if($notebook === "") echo "selected"; ?>>...</option>
                <option value="win" <?php if($notebook === "win") echo "selected"; ?>>Да, Windows</option>
                <option value="mac" <?php if($notebook === "mac") echo "selected"; ?>>Да, Mac OS</option>
                <option value="nix" <?php if($notebook === "nix") echo "selected"; ?>>Да, Linux</option>
                <option value="no" <?php if($notebook === "no") echo "selected"; ?>>Нет</option>
            </select>
            <br><br>
            В конце смены участники получают фирменные футболки.<br>
            Размер футболки:<br>
            <select name="shirt" id="shirt" required>
                <option value="" hidden disabled <?php if($shirt === "") echo "selected"; ?>>...</option>
                <option value="xs" <?php if($shirt === "XS") echo "selected"; ?>>XS</option>
                <option value="s" <?php if($shirt === "S") echo "selected"; ?>>S</option>
                <option value="m" <?php if($shirt === "M") echo "selected"; ?>>M</option>
                <option value="l" <?php if($shirt === "L") echo "selected"; ?>>L</option>
                <option value="xl" <?php if($shirt === "XL") echo "selected"; ?>>XL</option>
                <option value="xxl" <?php if($shirt === "XXL") echo "selected"; ?>>XXL</option>
            </select>
            <input id="btnOtherDetails" type="submit" value="&#x2714" class="smallbtn">
        </form>
    </div>
    <?php
        if($appStatus === "20") { // Появляется кнопка формирования .pfd-файла
            echo ('<button onclick="createPDF(\'' . $UID . '\')">Сформировать "Приложение №2" к Договору</button>');
        }
    ?>
</div>
