<?php
/**
 * Вывод карточки студента
 */
include "phplib/validate.inc";
include_once "phplib/dbConnect.php";
header("Content-type: text/html; charset=UTF-8");

// Обработка AJAX запроса на изменение статуса
if (isset($_POST['new_status'])) {
    if (!isset($_SESSION['ID'])) {
        error("student.php AJAX error: SESSION[ID] not defined");
        http_response_code(400);
        exit();
    }
    try {
        $db = new dbConnect();
        $db->setAppStatus($_SESSION['ID'], $_POST['new_status']);
        print "Статус: " . $db->AppStatus[$_POST['new_status']];
        $headers =
            "MIME-Version: 1.0\r\n" .
            "Content-type: text/html; charset=UTF-8\r\n" .
            "From: Delta <delta_mail_robot@cintra.ru>\r\n" .
            "To: summer.camp.delta@gmail.com\r\n" .
            "X-Mailer: PHP/" . phpversion();
        $mailtext = "Статус для записи " . $_POST['name'] . " изменён на " . $_POST['new_status'];
        mail("summer.camp.delta@gmail.com", "Delta: status has been changed", $mailtext, $headers);
    } catch (PDOException $PDOException) {
        error("student.php: Cannot change status: $PDOException");
    }
    exit();
}

if (isset($_GET['ID'])) {
    $_SESSION['ID'] = $_GET['ID'];
}

if (!isset($_SESSION['ID'])) {
    print "
    <!doctype html>
    <html lang='ru'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Карточка участника</title>
        <link href='CSS/common.css' rel='stylesheet' type='text/css'>
        <link href='CSS/admin.css' rel='stylesheet' type='text/css'>
    </head>
    <body>
        <div class='main'>
            <h1>Данные не найдены</h1>
        </div>
    </body>
    </html>
    ";
    exit();
}

$db = new dbConnect();
try {
    $result = $db->getStudentsList("*", $_SESSION['SortBy'], 'UniqueId="'.$_SESSION["ID"].'" AND ' . $_SESSION['WHERE']);
    if ($result->rowCount() != 1) {
        header("Content-type: text/html; charset=UTF-8");
        echo '<h1>Данные не найдены</h1>';
        exit();
    }
    $row = $result->fetch();
    $next_UID = $db->getNextUID($_SESSION['ID'], $_SESSION['SortBy'], $_SESSION['WHERE']);
    $prev_UID = $db->getPrevUID($_SESSION['ID'], $_SESSION['SortBy'], $_SESSION['WHERE']);
} catch (PDOException $exception) {
    error("admFetch error: $exception");
    exit();
}

$UID = $row['UniqueId'];
$email = $row['Email'];
$surname = $row['Surname'];
$name = $row['Name'];
$bDay = $row['Birthday'];
$city = $row['City'];
$country = $row['Country'];
$tel = $row['Tel'];
$oTel = $row['OwnTel'];
$status = $row['AppStatus'];
$certLang = $row['CertLang'];
$certName = $row['CertName'];
$cWith = $row['ComingWith'];
$cDate = $row['ComingDate'];
$cTime = $row['ComingTime'];
$cFlight = $row['ComingFlight'];
$cPlace = $row['ComingPlace'];
$lWith = $row['LeavingWith'];
$lDate = $row['LeavingDate'];
$lTime = $row['LeavingTime'];
$lFlight = $row['LeavingFlight'];
$lPlace = $row['LeavingPlace'];
$health = $row['Health'];
$insurance = $row['Insurance'];
$specialNote = $row['NotesText'];
$visa = $row['Visa'];
$notebook = $row['Notebook'];
$shirt = $row['Shirt'];

$status = $db->getAppStatus($UID);

$targetDir = 'photos/';
$pattern = $targetDir . $UID . '.*';
$photos = glob($pattern);
if (isset($photos) && $photos != false && $photos !== '') {
    $photo = $photos[0];
} else {
    $photo = $targetDir . $row['Gender'] . ".jpg";
}

?>

<!doctype html>
<html lang='ru'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Карточка участника</title>
    <link href='CSS/common.css' rel='stylesheet' type='text/css'>
    <link href='CSS/admin.css' rel='stylesheet' type='text/css'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
    <!-- jQuery  -->
    <script src="https://code.jquery.com/jquery-3.3.0.min.js"
            integrity="sha256-RTQy8VOmNlT6b2PIRur37p6JEBZUE7o8wPgMvu18MC4="
            crossorigin="anonymous"></script>
    <!-- jQuery Form -->
    <script src="JS/lib/jquery.form.min.js"></script>
    <script src="JS/student_functions.js"></script>
</head>
<body>
    <div class='main'>
        <div id='list_data'>
            <div class="details" id="details">
                <div class="row navigator">
                    <div class="nav-button" onclick="showDetails(<?php print "'" . $prev_UID . "'"?>, 1)">
                        <span class="fa fa-backward"></span>
                    </div>
                    <div class="nav-button" onclick='history.back()'>
                        <span class="fa fa-eject"></span>
                    </div>
                    <div class="nav-button" onclick="showDetails(<?php print "'" . $next_UID . "'"?>, 1)">
                        <span class="fa fa-forward"></span>
                    </div>
                </div>
                <div class="full_name"><?php print $name . " " . $surname ?> (<span id="age"></span>)</div>
                <div class="row">
                    <div class="col-2 photo"><img src="<?php print $photo ?>" alt="Фотография"></div>
                    <div class="col-1"></div>
                    <div class="col-6">
                        <div class="row infoblock">
                            <div class="o_tel"><a href="tel:<?php print $oTel?>">Личный телефон:&nbsp;<?php print $oTel?></a></div>
                            <div class="email"><a href="mailto:<?php print $email?>">E-mail:&nbsp;<?php print $email?></a></div>
                            <div class="tel">
                                <a href="tel:<?php print $tel?>">Телефон родителей:&nbsp;<?php print $tel?></a> (<a href="whatsapp:$tel">WhatsApp</a>)
                            </div>
                            <div class="status" id="status">Статус: <?php print $db->AppStatus[$status]?></div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="col-6 sp_note">Действия в форс-мажорном случае:<br><?php print $specialNote?></div>
                    </div>
                </div>
                <div class="row infoblock">
                    <div class="col-6"><strong>Сведения о здоровье:</strong><br><?php print $health?></div>
                    <div class="col-6"><strong>Страховка:</strong><br><?php print $insurance?></div>
                </div>
                <div class="row infoblock">
                    <div class="col-6">
                        <strong>Детали приезда:</strong><br>
                        С кем: <strong><?php print $cWith?></strong><br>
                        Когда: <strong><?php print $cDate?> в <?php print $cTime?></strong><br>
                        Рейс: <strong><?php print $cFlight?></strong>, куда: <strong><?php print $cPlace?></strong>
                    </div>
                    <div class="col-6">
                        <strong>Детали отъезда:</strong><br>
                        С кем: <strong><?php print $lWith?></strong><br>
                        Когда: <strong><?php print $lDate?> в <?php print $lTime?></strong><br>
                        Рейс: <strong><?php print $lFlight?></strong>, куда: <strong><?php print $lPlace?></strong>
                    </div>
                </div>
<?php
if (!$_SESSION['ReadOnly']) {
    print "
                <div class='row'>
                    <div class='col-3 cablink'><a href='mycabinet.php?id=$UID' >Личный кабинет</a></div>
                    <div class='col-3'></div>
                    <div class='col-6 set_status'>
                        <form id='SetAppStatus' method='post'>
                            <input type='submit' value='Изменить'>
                            <select name='new_status'>
    ";
    foreach ($db->AppStatus as $st => $stStr) {
        $option = "
                                <option value=$st" . (($st == $status) ? " selected" : "") . ">$stStr</option>
        ";
        print $option;
    }
    print "
                            </select>
                            <input type='hidden' name='name' value='$name  $surname'>
                        </form>
                    </div>
                </div>
    ";
}
print "
                <hr>
                <p><strong>Дополнительная информация...</strong></p>
                <div class='row infoblock'>
                    <div class='col-4'>
                        День рождения: <strong>$bDay</strong><br>
                        Место жительства: <strong>$city, $country</strong>
                    </div>
                    <div class='col-4'>
                        Ноутбук: <strong>$notebook</strong><br>
                        Футболка: <strong>$shirt</strong>
                    </div>
                    <div class='col-4'>
                        Виза: <strong>$visa</strong><br>
                        Сертификат ($certLang): <strong>$certName</strong>                    
                    </div>                    
                </div>
                <div class='row infoblock'>
                    <div class='col-11'> 
";
$output = "<ul>";
if ($result = $db->getCoursesForStudent($UID)) {
    foreach ($result as $row) {
        switch ($row['Time']) {
            case 0:
                $course = "Проект";
                break;
            case 11:
                $course = "I тройка, 1 пара";
                break;
            case 12:
                $course = "I тройка, 2 пара";
                break;
            case 21:
                $course = "II тройка, 1 пара";
                break;
            case 22:
                $course = "II тройка, 2 пара";
                break;
            case 31:
                $course = "III тройка, 1 пара";
                break;
            case 32:
                $course = "III тройка, 2 пара";
                break;
        }
        $output .= "<p><b>" . $course . ":</b> " . $row['NameRus'] . "</p>";
    }
}
$output .= "</ul>";
print $output;
?>
                </div>
                <div class="col-1">
                    <a href="certificate.php?UID=<?php print $UID?>" target="_blank">
                        <div class="iconbox"><span class="fa  fa-sign-in icon"></span></div>
                    </a>
                </div>
            </div>
            <div class="row navigator">
                <div class="nav-button" onclick="showDetails(<?php print "'" . $prev_UID . "'"?>, 1)">
                    <span class="fa fa-backward"></span>
                </div>
                <div class="nav-button" onclick='history.back()'>
                    <span class="fa fa-eject"></span>
                </div>
                <div class="nav-button" onclick="showDetails(<?php print "'" . $next_UID . "'"?>, 1)">
                    <span class="fa fa-forward"></span>
                </div>
            </div>
            </div>
            <script src="JS/common.js"></script>
            <script>
                $(function() { // Вычисляем возраст и заполняем соответствующее поле
                    const bd = new Date("<?php print $bDay?>");
                    let happy = happyBirthday(bd);
                    if(happy !== '')
                        happy = ", День рождения: " + happy;
                    $("#age").html(age(bd) + ' лет' + happy);
                });
            </script>

        </div>
    </div>
</body>
