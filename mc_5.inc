<?php
/*
 * ������ ���������
 * $UID - ���������� ����� ������������, ��������� �� mycabinet.php
 */
require_once 'phplib/dbConnect.php';
require_once 'phplib/mail.php';
require_once 'phplib/common.php';

if (!isset($UID)) {
    exit('������ ���� �� ������������ ��� ���������������� �������');
}

/*
 *
 *  ��������� �������� ���� AJAX
 *
 */

$targetDir = 'photos/';

if (isset($_FILES['file'])) {

    console_log($_FILES);

    define('MIN_FREE_SPACE', 20*1024*1024);

    $ret = array();

    if (disk_free_space($targetDir) < MIN_FREE_SPACE) {
        error("�������� ����: ������������ ����� �� �����! UID: " . $UID);
    }

    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir)) {
            error("�������� ����������: �� ������� ������� ���������� $targetDir. UID: $UID");
            exit;
        }
    }

    $error = false;
    if (!is_array($_FILES['file']['name'])) { // single file
        $fileName = $_FILES['file']['name'];
        $fileSize = $_FILES['file']['size'];
        $tmpFileName = $_FILES['file']['tmp_name'];
        $error = $_FILES['file']['error'];
    } else { //Multiple files, file[], �������� ������ ����
        $fileCount = count($_FILES['file']['name']);
        if ($fileCount >= 1) {
            $fileName = $_FILES['file']['name'][0];
            $fileSize = $_FILES['file']['size'][0];
            $tmpFileName = $_FILES['file']['tmp_name'][0];
            $error = $_FILES['file']['error'][0];
        } else {
            $tmpFileName = null;
            error("�������� ����������, ������: '$tmpFileName' = null. " . var_dump($_FILES));
            exit();
        }
    }

    if ($error) {
        error("�������� ����������, ������ " . $error);
        exit();
    }

    $fileExt = strrchr($fileName, '.');
    if ($fileExt === "jpeg") $fileExt = "jpg";
    $rawFileName = $targetDir . "rawPhoto" . $fileExt;
    $targetFile = $targetDir . $UID . $fileExt;
    move_uploaded_file($tmpFileName, $rawFileName);

    list($width, $height, $type) = getimagesize($rawFileName);

    if ($type != IMG_JPG && $type != IMG_GIF && $type != IMG_PNG) {
        error("��������� �� ���������� (�������� ��� �����)");
        exit();
    }

    if ($fileSize < 1024*1024) { // �� ����� ������� ������ ������� �� ��������
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
        error("ToDo: ��������������� ���� $targetFile");
    }
    $ret[] = $fileName;
    echo json_encode($ret);

    $db->dbLog("��������� ����������", $UID);

    exit();
}

/*
 * ��������� AJAX-���� (��������� ��������� � $_POST)
 */

if (isset($_POST) && is_array($_POST) && count($_POST) > 0) {
    if ($_POST['form'] === "OwnTel") {
        try {
            $db->setOwnTel($UID, $_POST['ownTel']);
        } catch (PDOException $exception) {
            error("Error in setOwnTel(): $exception, UID: $UID");
            exit();
        }
        $db->dbLog("�������� ������� ������", $UID);
    } elseif ($_POST['form'] === "ComingDetails") {
        try {
            $db->setComingDetails($UID, $_POST['comingWith'], $_POST['comingDate'], $_POST['comingTime'], $_POST['comingFlight'], $_POST['comingPlace']);
        } catch (PDOException $exception) {
            error("Error in setComingDetails(): $exception, UID: $UID");
            exit();
        }
        $db->dbLog("��������� ������ �������", $UID);
    } elseif ($_POST['form'] === "LeavingDetails") {
        try {
            $db->setLeavingDetails($UID, $_POST['leavingWith'], $_POST['leavingDate'], $_POST['leavingTime'], $_POST['leavingFlight'], $_POST['leavingPlace']);
        } catch (PDOException $exception) {
            error("Error in setLeavingDetails(): $exception, UID: $UID");
            exit();
        }
        $db->dbLog("��������� ������ �������", $UID);
    } elseif ($_POST['form'] === "HealthDetails") {
        try {
            $db->setHealthDetails($UID, $_POST['healthText']);
        } catch (PDOException $exception) {
            error("Error in setHealthDetails(): $exception, UID: $UID");
            exit();
        }
        $db->dbLog("��������� ������ ��������", $UID);
    } elseif ($_POST['form'] === "InsuranceDetails") {
        try {
            $db->setInshuranceDetails($UID, $_POST['insuranceText']);
        } catch (PDOException $exception) {
            error("Error in setInsuranceDetails(): $exception, UID: $UID");
            exit();
        }
        $db->dbLog("��������� ������ ���������", $UID);
    } elseif ($_POST['form'] === "NotesDetails") {
        try {
            $db->setNotesDetails($UID, $_POST['notesText']);
        } catch (PDOException $exception) {
            error("Error in setNotesDetails(): $exception, UID: $UID");
            exit();
        }
        $db->dbLog("��������� ������ ����-�����", $UID);
    } elseif ($_POST['form'] === "OtherDetails") {
        try {
            $db->setOtherDetails($UID, $_POST['certLang'], $_POST['certName'], $_POST['visa'], $_POST['notebook'], $_POST['shirt']);
        } catch (PDOException $exception) {
            error("Error in SetOtherDetails(): $exception, UID: $UID");
            exit();
        }
        $db->dbLog("��������� �������������� ����", $UID);
    }

    exit();
}

/*
 * ��������� ����, ���� ����, ����� - ��������.
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
 * ������������� ����� ����
 */
try {
    $result = $db->getOwnTel($UID);
} catch (PDOException $exception) {
    error("Error in getOwnTel(): $exception");
}
foreach ($result as $row) {
    if(!$ownTel = $row['OwnTel']) $ownTel = "";
    break;
}

try {
    $result = $db->getComingDetails($UID);
} catch (PDOException $exception) {
    error("Error in getComingDetails(): $exception");
}
foreach ($result as $row) {
    $comingWith = $row['ComingWith']; if($comingWith == null) $comingWith = "";
    $comingDate = $row['ComingDate']; if($comingDate == null) $comingDate = "";
    $comingTime = $row['ComingTime']; if($comingTime == null) $comingTime = "";
    $comingFlight = $row['ComingFlight']; if($comingFlight == null) $comingFlight = "";
    $comingPlace = $row['ComingPlace']; if($comingPlace == null) $comingPlace = "";
    break;
}

try {
    $result = $db->getLeavingDetails($UID);
} catch (PDOException $exception) {
    error("Error in getLeavingDetails(): $exception, UID: $UID");
}
foreach ($result as $row) {
    $leavingWith = $row['LeavingWith']; if($leavingWith == null) $leavingWith = "";
    $leavingDate = $row['LeavingDate']; if($leavingDate == null) $leavingDate = "";
    $leavingTime = $row['LeavingTime']; if($leavingTime == null) $leavingTime = "";
    $leavingFlight = $row['LeavingFlight']; if($leavingFlight == null) $leavingFlight = "";
    $leavingPlace = $row['LeavingPlace']; if($leavingPlace == null) $leavingPlace = "";
    break;
}

try {
    $result = $db->getHealthDetails($UID);
} catch (PDOException $exception) {
    error("Error in getHealthDetails(): $exception, UID: $UID");
}
foreach ($result as $row) {
    $healthDetails = $row['Health'];
    break;
}

try {
    $result = $db->getInsuranceDetails($UID);
} catch (PDOException $exception) {
    error("Error in getInsuranceDetails(): $exception, UID: $UID");
}
foreach ($result as $row) {
    $insuranceDetails = $row['Insurance'];
    break;
}

try {
    $result = $db->getNotesDetails($UID);
} catch (PDOException $exception) {
    error("Error in getNotesDetails: $exception, UID: $UID");
}
foreach ($result as $row) {
    $notesDetails = $row['NotesText'];
    break;
}

try {
    $result = $db->getOtherDetails($UID);
} catch (PDOException $exception) {
    error("Error in getOtherDetails: $exception, UID: $UID");
}
foreach ($result as $row) {
    if(!$certLang = $row['CertLang']) $certLang = "";
    if(!$certName = $row['CertName']) $certName = "";
    if(!$visa = $row['Visa']) $visa = "";
    if(!$notebook = $row['Notebook']) $notebook = "";
    if(!$shirt = $row['Shirt']) $shirt = "";
}
?>

<div class="row">
    <div class="col-12">
        <h3>������������! </h3>
        <p>���������, ����������, ������ ���������:</p>
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
            <div class="form-title">����� ���������� �������� ������&nbsp;</div><br>
            ������� ����� ��������, ������� ����� � ������ � ����� � ������:<br>
            <input type="tel" name="ownTel" id="ownTel" class="text-input" value="<?php echo $ownTel ?>" placeholder="+4912345678999" required>
            <input id="btnOwnTel" type="submit" value="&#x2714" class="smallbtn">
        </form>
    </div>
    <div class="col-8">
        <p><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;������� ������ �������:</b></p>

        <div class="col-6">
        <form id="comeInForm" method="post" class="questionnaire">
            <div class="form-title">&nbsp;� ������&nbsp;</div><br>
            <input name="form" value="ComingDetails" hidden>
            <select name="comingWith" id="comingWith" required>
                <option value="" hidden disabled <?php if($comingWith === "") {echo 'selected';}?>>� ��� ���������...</option>
                <option value="DimaAnya" <?php if($comingWith === "DimaAnya") {echo 'selected';}?>>� ��������������, ���� Lufthansa</option>
                <option value="Dubeniuk" <?php if($comingWith === "Dubeniuk") {echo 'selected';}?>>� ��������������, ���� Utair</option>
                <option value="Singly" <?php if($comingWith === "Singly") {echo 'selected';}?>>���� ���������...</option>
                <option value="WithParents" <?php if($comingWith === "WithParents") {echo 'selected';}?>>�������� ��������</option>
            </select>
            <br>
            ����:<br> <input type="text" name="comingDate" id="comingDate" placeholder="dd/mm/yyyy"
                             value='<?php if($comingDate === "") {echo "16/07/2018";} else {echo "$comingDate";}?>'
                             <?php if($comingWith === "DimaAnya" || $comingWith === "Dubeniuk") {echo 'disabled';}?> required><br>
            �����:<br> <input type="text" name="comingTime" id="comingTime" placeholder="hh:mm"
                            value='<?php echo "$comingTime" ?>'
                            <?php if($comingWith === "DimaAnya" || $comingWith === "Dubeniuk") {echo 'disabled';}?> required><br>
            ����� �����:<br> <input type="text" name="comingFlight" id="comingFlight" value='<?php echo "$comingFlight" ?>'
                            <?php if($comingWith === "DimaAnya" || $comingWith === "Dubeniuk") {echo 'disabled';}
                            elseif ($comingWith ==="WithParents") {echo 'value="-" disabled';}?> required><br>
            ����� �������:<br> <select name="comingPlace" id="comingPlace" required <?php if($comingWith === "DimaAnya" || $comingWith === "Dubeniuk") {echo 'disabled';} ?>>
                <option value="" hidden disabled <?php if($comingPlace === "") {echo 'selected';}?>>...</option>
                <option value="MUC" <?php if($comingPlace === "MUC") {echo 'selected';}?>>� �������� (������)</option>
                <option value="hbf" <?php if($comingPlace === "hbh") {echo 'selected';}?>>�� ������ Hauptbahnhof</option>
                <option value="gorod" <?php if($comingPlace === "gorod") {echo 'selected';}?>>� ���������� ����� GOROD</option>
                <option value="other" <?php if($comingPlace === "other") {echo 'selected';}?>>����... (��������� � ��������������!)</option>
                <?php
                if ($comingWith === "DimaAnya") {
                    echo "<option value='DME' selected>�������� \"����������\"</option>";
                } elseif ($comingWith === "Dubeniuk") {
                    echo "<option value='VKO' selected>�������� \"�������\"</option>";
                }
                ?>
            </select>
            <input id="btnComingDetails" type="submit" value="&#x2714" class="smallbtn">
        </form>
        </div>

        <div class="col-6">
        <form id="leaveOutForm" method="post" class="questionnaire">
            <div class="form-title">&nbsp;�� �������&nbsp;</div><br>
            <input name="form" value="LeavingDetails" hidden>
            <select name="leavingWith" id="leavingWith" required>
                <option value="" hidden disabled <?php if($leavingWith === "") {echo 'selected';}?>>� ��� �������...</option>
                <option value="DimaAnya" <?php if($leavingWith === "DimaAnya") {echo 'selected';}?>>� ��������������, ���� Lufthansa</option>
                <option value="Dubeniuk" <?php if($leavingWith === "Dubeniuk") {echo 'selected';}?>>� ��������������, ���� Utair</option>
                <option value="Singly" <?php if($leavingWith === "Singly") {echo 'selected';}?>>���� ���������...</option>
                <option value="WithParents" <?php if($leavingWith === "WithParents") {echo 'selected';}?>>�������� ��������</option>
            </select>
            <br>
            ����:<br> <input type="text" name="leavingDate" id="leavingDate" placeholder="dd/mm/yyyy"
                             value='<?php if($leavingDate === "") {echo "30/07/2018";} else {echo "$leavingDate";}?>'
                <?php if($leavingWith === "DimaAnya" || $leavingWith === "Dubeniuk") {echo 'disabled';}?> required><br>
            �����:<br> <input type="text" name="leavingTime" id="leavingTime" placeholder="hh:mm"
                              value='<?php echo "$leavingTime" ?>'
                <?php if($leavingWith === "DimaAnya" || $leavingWith === "Dubeniuk") {echo 'disabled';}?> required><br>
            ����� �����:<br> <input type="text" name="leavingFlight" id="leavingFlight" value='<?php echo "$leavingFlight" ?>'
                <?php if($leavingWith === "DimaAnya" || $leavingWith === "Dubeniuk") {echo 'disabled';}
                elseif ($leavingWith ==="WithParents") {echo 'value="-" disabled';}?> required><br>
            ����� �������:<br> <select name="leavingPlace" id="leavingPlace" required <?php if($leavingWith === "DimaAnya" || $leavingWith === "Dubeniuk") {echo 'disabled';} ?>>
                <option value="" hidden disabled <?php if($leavingPlace === "") {echo 'selected';}?>>...</option>
                <option value="MUC" <?php if($leavingPlace === "MUC") {echo 'selected';}?>>�� ��������� (������)</option>
                <option value="hbf" <?php if($leavingPlace === "hbh") {echo 'selected';}?>>�� ������� Hauptbahnhof</option>
                <option value="gorod" <?php if($leavingPlace === "gorod") {echo 'selected';}?>>�� ����������� ������ GOROD</option>
                <option value="other" <?php if($leavingPlace === "other") {echo 'selected';}?>>����... (��������� � ��������������!)</option>
                <?php
                if ($leavingWith === "DimaAnya") {
                    echo "<option value='DME' selected>�������� \"����������\"</option>";
                } elseif ($leavingWith === "Dubeniuk") {
                    echo "<option value='VKO' selected>�������� \"�������\"</option>";
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
        <p><b>��������� ������ ���������� � �������</b></p>
        <form id="healthForm" method="post" class="questionnaire">
            <div class="form-title">&nbsp;��������&nbsp;</div><br>
            ������������� �������, ������ ����������� ��������, ����������� �����������, ����������� � ���.<br>
            <input name="form" value="HealthDetails" hidden>
            <textarea name="healthText" id="healthText"><?php echo $healthDetails ?></textarea>
            <input id="btnHealthDetails" type="submit" value="&#x2714" class="smallbtn">
        </form>
        <br>
        <form id="insuranceForm" method="post" class="questionnaire">
            <div class="form-title">&nbsp;���������&nbsp;</div><br>
            ���������� � ��������� ������; �������(�) � ���� ��������� ��������. ����������� ������������� ����������� ���������� ������.<br>
            <input name="form" value="InsuranceDetails" hidden>
            <textarea name="insuranceText" id="insuranceText"><?php echo $insuranceDetails ?></textarea>
            <input id="btnInsuranceDetails" type="submit" value="&#x2714" class="smallbtn">
        </form>
        <br>
        <form id="notesForm" method="post" class="questionnaire">
            <div class="form-title">&nbsp;����-�����&nbsp;</div><br>
            �������������� ���������� �� ��������� ������������� � ������� ���������: ������ "������� ���������" �� ������� ����� �������,
            �������������� ���������� � �.�.<br>
            <input name="form" value="NotesDetails" hidden>
            <textarea name="notesText" id="notesText"><?php echo $notesDetails ?></textarea>
            <input id="btnNotesDetails" type="submit" value="&#x2714" class="smallbtn">
        </form>
    </div>
    <div class="col-4">
        <p><b>...� ��� ��������� ������ ��������</b></p>
        <form id="otherForm" method="post" class="questionnaire">
            <input name="form" value="OtherDetails" hidden>
            ����� �� ������ ����:<br>
            <input name="visa" type="radio" required id="visa_yes" value="1" <?php if($visa == 1) echo "checked"; ?>>
            <label for="visa_yes"><span><span></span></span>�����</label>&nbsp;&nbsp;
            <input name="visa" type="radio" required id="visa_no" value="0" <?php if($visa == 0) echo "checked"; ?>>
            <label for="visa_no"><span><span></span></span>�� �����</label>
            <br><br>
            � ����� ����� ��� ��������� �������� �����������, � ������� ������� ��� ����� � ���� ������� ���������.<br>
            �������� ���� �����������:<br>
            <select name="certLang" id="certLang" required>
                <option value="" hidden disabled <?php if ($certName === "") echo " selected"; ?>>�������...</option>
                <option value="ru" <?php if ($certLang === "ru") echo " selected"; ?>>�������</option>
                <option value="de" <?php if ($certLang === "de") echo " selected"; ?>>Deutsch</option>
                <option value="en" <?php if ($certLang === "en") echo " selected"; ?>>English</option>
            </select>
            <br>
            ������� � ��� ��������� �� ����� �����������:<br>
            <input id="certName" name="certName" type="text" class="text-input" <?php if ($certName !== "") echo " value='$certName'"; ?> required>
            <br><br>
            � ��������� ������ � �������� ��������� ���������.<br>
            ����� �� � ������ � ����� �������:<br>
            <select name="notebook" id="notebook" required>
                <option value="" hidden disabled <?php if($notebook === "") echo "selected"; ?>>...</option>
                <option value="win" <?php if($notebook === "win") echo "selected"; ?>>��, Windows</option>
                <option value="mac" <?php if($notebook === "mac") echo "selected"; ?>>��, Mac OS</option>
                <option value="nix" <?php if($notebook === "nix") echo "selected"; ?>>��, Linux</option>
                <option value="no" <?php if($notebook === "no") echo "selected"; ?>>���</option>
            </select>
            <br><br>
            � ����� ����� ��������� �������� ��������� ��������.<br>
            ������ ��������:<br>
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
</div>