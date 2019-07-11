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
        $_SESSION['ID'] = $db->getNextUID($_SESSION['ID'], $_SESSION['SortBy'], "AppStatus >= 0");
    }
    elseif ($_POST['ID'] === '-1') {
        $_SESSION['ID'] = $db->getPrevUID($_SESSION['ID'], $_SESSION['SortBy'], "AppStatus >= 0");
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
    header("Content-type: text/html; charset=UTF-8");
    echo $output;
}
/*
 * Вывод детальной информации
 */
elseif ($_SESSION['View']==="Details") {
    include "details.inc";
}
