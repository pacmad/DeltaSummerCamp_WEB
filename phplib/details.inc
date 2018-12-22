<?php
try {
    $result = $db->getStudentsList("*", $_SESSION['SortBy'], 'UniqueId="'.$_SESSION["ID"].'"');
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

$status = intval($status);
if ($status < 2) $status = "Новая регистрация";
elseif ($status < 9) $status = "Из прошлой Дельты";
elseif ($status < 15) $status = "Будут точно";
else $status = "Не определён";


$targetDir = 'photos/';
$pattern = $targetDir . $UID . '.*';
$photos = glob($pattern);
if (isset($photos) && $photos != false && $photos !== '') {
    $photo = $photos[0];
} else {
    $photo = $targetDir . $row['Gender'] . ".jpg";
}

$output = <<<OUTPUT
        <div class="details" id="details">
            <div class="row navigator">
                <div class="nav-button" onclick='showDetails($prev_UID, 1)'>
                    <span class="fa fa-backward"></span>
                </div>
                <div class="nav-button" onclick='history.back()'>
                    <span class="fa fa-eject"></span>
                </div>
                <div class="nav-button" onclick='showDetails($next_UID, 1)'>
                    <span class="fa fa-forward"></span>                    
                </div>
            </div>
            <div class="full_name">$name $surname (<span id="age"></span>)</div>
            <div class="row">
                <div class="col-2 photo"><img src="$photo" alt="Фотография"></div>
                <div class="col-7">
                    <div class="row infoblock">
                        <div class="o_tel"><a href="tel:$oTel">Личный телефон:&nbsp;$oTel</a></div>
                        <div class="email"><a href="mailto:$email">E-mail:&nbsp;$email</a></div>
                        <div class="tel">
                            <a href="tel:$tel">Телефон родителей:&nbsp;$tel</a>&nbsp;(<a href="whatsapp:$tel">WhatsApp</a>)
                        </div>
                        <div class="status">Статус: $status</div> 
                    </div>
                </div>
                <div class="col-3">
                    <div class="col-6 sp_note">Действия в форс-мажорном случае:<br>$specialNote</div>                
                </div>
            </div>
            <div class="row infoblock">
                <div class="col-6"><strong>Сведения о здоровье:</strong><br>$health</div>
                <div class="col-6"><strong>Страховка:</strong><br>$insurance</div>
            </div>
            <div class="row infoblock">
                <div class="col-6">
                    <strong>Детали приезда:</strong><br>
                    С кем: <strong>$cWith</strong><br>
                    Когда: <strong>$cDate в $cTime</strong><br>
                    Рейс: <strong>$cFlight</strong>, куда: <strong>$cPlace</strong>
                </div>
                <div class="col-6">
                    <strong>Детали отъезда:</strong><br>
                    С кем: <strong>$lWith</strong><br>
                    Когда: <strong>$lDate в $lTime</strong><br>
                    Рейс: <strong>$lFlight</strong>, куда: <strong>$lPlace</strong>
                </div>
            </div>
OUTPUT;

if(!$_SESSION['ReadOnly']) {
    $output .= "
            <div class='row'>
                <div class='col-3 cablink'><a href='mycabinet.php?id=$UID' >Личный кабинет</a></div>
                <div class='col-6'></div>
                <div class='col-3 set_status'>
                    <form>
                        <select name='new_status'>
                            <option value='' disabled selected>Изменить статус...</option>
                            <option value='3'>Получено решение олимпиады</option>
                            <option value='5'>Свой</option>
                            <option value='6'>Послано подтверждение о приёме</option>
                            <option value='9'>Обещали</option>
                            <option value='10'>Внесли предоплату</option>
                        </select>
                        <input type='submit'>
                    
                    </form>
                </div>
            </div>
            ";
}

$output .= <<<OUTPUT
            <hr>
            <p><strong>Дополнительная информация...</strong></p>
            <div class="row infoblock">
                <div class="col-4">
                    День рождения: <strong>$bDay</strong><br>
                    Место жительства: <strong>$city, $country</strong>
                </div>
                <div class="col-4">
                    Ноутбук: <strong>$notebook</strong><br>
                    Футболка: <strong>$shirt</strong>
                </div>
                <div class="col-4">
                    Виза: <strong>$visa</strong><br>
                    Сертификат ($certLang): <strong>$certName</strong>                    
                </div>                    
            </div>
            <div class="row infoblock">
                <div class="col-11" 
OUTPUT;

$output .= "<ul>";
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

$output .= <<<OUTPUT
                </div>
                <div class="col-1">
                    <a href="certificate.php?UID=$UID" target="_blank">
                        <div class="iconbox"><span class="fa  fa-sign-in icon"></span></div>
                    </a>
                </div>
            </div>
            <div class="row navigator">
                <div class="nav-button" onclick='showPrev()'>
                    <span class="fa fa-backward"></span>
                </div>
                <div class="nav-button" onclick='history.back()'>
                    <span class="fa fa-eject"></span>
                </div>
                <div class="nav-button" onclick='showNext()'>
                    <span class="fa fa-forward"></span>                    
                </div>
            </div>
        </div>
        <script src="JS/common.js"></script>
        <script>
            $(function() { // Вычисляем возраст и заполняем соответствующее поле
                const bd = new Date("$bDay");
                let happy = happyBirthday(bd);
                if(happy !== '')
                    happy = ", День рождения: " + happy;
                $("#age").html(age(bd) + ' лет' + happy);
            });
        </script>
OUTPUT;

header("Content-type: text/html; charset=UTF-8");
echo $output;
?>