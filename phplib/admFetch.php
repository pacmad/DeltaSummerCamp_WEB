<?php

include "validate.inc";
include_once "dbConnect.php";

/*
 * Проверяем тип вывода (список, детали, редактирование и т.д.)
 * и сохраняем его в $_SESSION
 */
$db = new dbConnect();
if (isset($_POST['View'])) $_SESSION['View'] = $_POST['View'];
if (!isset($_SESSION['SortBy'])) $_SESSION['SortBy'] = "Surname";
if (isset($_POST['SortBy'])) $_SESSION['SortBy'] = $_POST['SortBy'];
if (isset($_POST['ID'])) {
    if ($_POST['ID'] === '+1') {
        $_SESSION['ID'] = $db->getNextUID($_SESSION['ID'], $_SESSION['SortBy']);
    }
    elseif ($_POST['ID'] === '-1') {
        $_SESSION['ID'] = $db->getPrevUID($_SESSION['ID'], $_SESSION['SortBy']);
    }
    elseif ($_POST['ID'] !== '0') {
        $_SESSION['ID'] = $_POST['ID'];
    }
}
if (!isset($_SESSION['WHERE'])) $_SESSION['WHERE'] = 'AppStatus >= 0';
if (isset($_POST['WHERE'])) $_SESSION['WHERE'] = $_POST['WHERE'];

/*
 * Вывод списка
 */
if ($_SESSION['View'] === "List") {
    $output = '
    <div class="table">
    ';

    try {
        $result = $db->getStudentsList("UniqueId, Surname, Name, Gender, Tel, OwnTel, Email", $_SESSION['SortBy']);
    } catch (PDOException $exception) {
        error("admFetch error: $exception");
    }
    if ($result->rowCount() > 0) {
        foreach ($result as $row) {
            $UID = $row['UniqueId'];
            $targetDir = '../photos/';
            $pattern = $targetDir . $UID . '.*';
            $photos = glob($pattern);

            if (isset($photos) && $photos != false && $photos !== '') {
                $photo = $photos[0];
            } else {
                $photo = $targetDir . $row['Gender'] . ".jpg";
            }

            $output .= '
            <div class="row table-row" id="tr-'.$row["UniqueId"].'">
                <div class="table-cell l_photo" onclick="showDetails(\''.$row["UniqueId"].'\')"><img src="' . $photo . '" alt="Photo"></div>
                <div class="table-info">
                    <div class="table-cell l_name" onclick="showDetails(\''.$row["UniqueId"].'\');"><b>' . $row["Surname"] . ' ' . $row["Name"] . '</b></div>
                    <div class="table-cell l_owntel"><a href="tel:' . $row["OwnTel"] . '">'. $row["OwnTel"] .'</a></div>
                    <div class="table-cell l_mail"><a href="mailto:'.$row["Email"].'">' . $row["Email"] . '</a></div>
                    <div class="table-cell l_tel"><p>Родители:</p><a href="tel:'.$row["Tel"].'">' . $row["Tel"] . '</a></div>
                    <div class="hidden">' . $UID . '</div>
                </div>
            </div>
            ';
        }
    } else {
        $output .= 'Данные не найдены';
    }
    $output .= '
    </div>
    <div class="search-block">
        <div class="search"><input type="text" name="search_string" id="search_string" autofocus><span class="fa fa-search" onclick="doSearch()"></span></div>
    </div>
    <script>
    $(function() {  // Прокрутка до активной записи при выходе из карточки участника
        $("html, body").animate({
            scrollTop: $("#tr-' . $_SESSION["ID"] . '").offset().top - 60
        }, 500);
    });
    $(function() {  // Обработка ввода в поле быстрого поиска по списку
        if("' . $_POST["Init"] . '" === "Init") {
            $("#search_string").focus().on("input", search);
        }      
    })
    </script>';
    header("Content-type: text/html; charset=windows-1251");
    echo $output;
}
/*
 * Вывод детальной информации
 */
elseif ($_SESSION['View']==="Details") {
    try {
        $result = $db->getStudentsList("*", $_SESSION['SortBy'], 'UniqueId="'.$_SESSION["ID"].'"');
        if ($result->rowCount() != 1) {
            header("Content-type: text/html; charset=windows-1251");
            echo '<h1>Данные не найдены</h1>';
            exit();
        }
        $row = $result->fetch();
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

    $targetDir = '../photos/';
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
            <div class="full_name">$name $surname (<span id="age"></span>)</div>
            <div class="row">
                <div class="col-2 photo"><img src="$photo" alt="Фотография"></div>
                <div class="col-10">
                    <div class="row infoblock">
                        <div class="col-2 o_tel"><a href="tel:$oTel">Личный телефон:<br>$oTel</a></div>
                        <div class="col-2 email"><a href="mailto:$email">E-mail:<br>$email</a></div>
                        <div class="col-2 tel">
                            <a href="tel:$tel">Телефон родителей:<br>$tel</a><br>
                            <a href="whatsapp:$tel">WhatsApp</a>
                        </div>    
                        <div class="col-6 sp_note">Действия в форс-мажорном случае:<br>$specialNote</div>                
                    </div>
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
                <div class='col-9'></div>
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

    header("Content-type: text/html; charset=windows-1251");
    echo $output;
}
