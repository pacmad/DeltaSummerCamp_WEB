<?php
/**
 * Created by PhpStorm.
 * User: Dima
 * Date: 21.02.2018
 * Time: 18:57
 */
include "validate.inc";
include_once "dbConnect.php";

/*
 * Проверяем тип вывода (список, детали, редактирование и т.д.)
 * и сохраняем его в $_SESSION
 */
$db = new dbConnect();
if (isset($_POST['View'])) $_SESSION['View'] = $_POST['View'];
if (isset($_POST['ID'])) {
    if ($_POST['ID'] === '+1') {
        $_SESSION['ID'] = $db->getNextUID($_SESSION['ID'], $_SESSION['SortBy']);
    }
    elseif ($_POST['ID'] === '-1') {
        $_SESSION['ID'] = $db->getPrevUID($_SESSION['ID'], $_SESSION['SortBy']);
    }
    else {
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
        <div class="table-header" id="table-header">
            <div class="table-cell l_chk">&nbsp;</div>
            <div class="table-cell l_photo">&nbsp;</div>
            <div class="table-cell l_name" onclick="fetchData(\'Name\')">Как зовут</div>
            <div class="table-cell l_owntel" onclick="fetchData(\'OwnTel\')">Телефон</div>
            <div class="table-cell l_tel" onclick="fetchData(\'Tel\')">Телефон родителей</div>
            <div class="table-cell l_mail" onclick="fetchData(\'Email\')">E-Mail</div>
        </div>
    ';

    if (!isset($_SESSION['SortBy'])) $_SESSION['SortBy'] = "Name";
    if (isset($_POST['SortBy'])) {
        if ($_SESSION['SortBy'] === "Name" && $_POST['SortBy'] === "Name") {
            $_SESSION['SortBy'] = "Surname";
        } else {
            $_SESSION['SortBy'] = $_POST['SortBy'];
        }
    }
    try {
        $result = $db->getStudentsList("UniqueId, Surname, Name, Gender, Tel, OwnTel, Email", $_SESSION['SortBy']);
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
                <div class="table-row" id="tr-'.$row["UniqueId"].'">
                    <div class="table-cell l_chk"><input type="checkbox" id="cb-'.$row["UniqueId"].'"></div>
                    <div class="table-cell l_photo" onclick="checkIt(\''.$row["UniqueId"].'\')"><img src="' . $photo . '" alt="Photo"></div>';
                if ($_SESSION['SortBy'] === 'Name') {
                    $output .= '<div class="table-cell l_name" onclick="showDetails(\''.$row["UniqueId"].'\');"><b>' . $row["Name"] . '</b><br>' . $row["Surname"] . '</div>';
                } else {
                    $output .= '<div class="table-cell l_name" onclick="showDetails(\''.$row["UniqueId"].'\');"><b>' . $row["Surname"] . '</b><br> ' . $row["Name"] . '</div>';
                }
                $output .= '<div class="table-cell l_owntel"><div class="mobview">Личный тел.:<br></div><a href="tel:' . $row["OwnTel"] . '">'. $row["OwnTel"] .'</a></div>
                    <div class="table-cell l_tel"><div class="mobview">Родители:<br></div><a href="tel:'.$row["Tel"].'">' . $row["Tel"] . '</a></div>
                    <div class="table-cell l_mail"><a href="mailto:'.$row["Email"].'">' . $row["Email"] . '</a></div>
                </div>
            ';
            }
        } else {
            $output .= '
        <tr>
            <td colspan="4">Данные не найдены</td>
        </tr>
        ';
        }
    } catch (PDOException $exception) {
        error("admFetch error: $exception");
    }
    $output .= "
    </div>
    <script>
    $(function() {
        $('html, body').animate({
            scrollTop: $('#tr-" . $_SESSION["ID"] . "').offset().top - 60
        }, 1000);  
    });
    </script>";
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
        <div class="details">
            <div class="row navigator">
                <div class="nav-button" onclick='showPrev()'>
                    <span class="fa fa-backward"></span>
                </div>
                <div class="nav-button" onclick='showList("$UID")'>
                    <span class="fa fa-eject"></span>
                </div>
                <div class="nav-button" onclick='showNext()'>
                    <span class="fa fa-forward"></span>                    
                </div>
            </div>
            <div class="full_name">$name $surname</div>
            <div class="row">
                <div class="col-2 photo"><img src="$photo" alt="Фотография"></div>
                <div class="col-10">
                <div class="row">
                    <div class="col-2 o_tel"><a href="tel:$oTel">Личный телефон:<br>$oTel</a></div>
                    <div class="col-2 email"><a href="mailto:$email">E-mail:<br>$email</a></div>
                    <div class="col-2 tel">
                        <a href="tel:$tel">Телефон родителей:<br>$tel</a><br>
                        <a href="whatsapp:$tel">WhatsApp</a>
                    </div>    
                    <div class="col-6 sp_note">Действия в форс-мажорном случае:<br>$specialNote</div>                
                </div>
                <div class="row">
                    <div class="col-12">Статус: $status</div>
                </div>               
                </div>
            </div>
            <div class="row">
                <div class="col-6"><strong>Сведения о здоровье:</strong><br>$health</div>
                <div class="col-6"><strong>Страховка:</strong><br>$insurance</div>
            </div>
            <div class="row">
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
            <div class="row">
                <div class="col-3 cablink"><a href="mycabinet.php?id=$UID" >Личный кабинет</a></div>
                <div class="col-9"></div>
            </div>
            <hr>
            <p onclick="showMoreInfo()" class="clickable"><strong>Дополнительная информация...</strong></p>
            <div class="row" id="more_info">
                <div class="col-4">
                День рождения: <strong>$bDay</strong><br>
                Место жительства: <strong>$city, $country</strong><br>
                
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
            <div class="row navigator">
                <div class="nav-button" onclick='showPrev()'>
                    <span class="fa fa-backward"></span>
                </div>
                <div class="nav-button" onclick='showList("$UID")'>
                    <span class="fa fa-eject"></span>
                </div>
                <div class="nav-button" onclick='showNext()'>
                    <span class="fa fa-forward"></span>                    
                </div>
            </div>
        </div>
OUTPUT;

    header("Content-type: text/html; charset=windows-1251");
    echo $output;
}
