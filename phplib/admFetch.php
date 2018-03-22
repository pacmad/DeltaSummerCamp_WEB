<?php
/**
 * Created by PhpStorm.
 * User: Dima
 * Date: 21.02.2018
 * Time: 18:57
 */
include "validate.inc";
include_once "dbConnect.php";

$db = new dbConnect();
if ($_SESSION['View'] === "List") {
    $output = '
    <div class="table">
        <div class="table-row">
            <div class="table-cell table-header l_chk">&nbsp;</div>
            <div class="table-cell table-header l_photo">&nbsp;</div>
            <div class="table-cell table-header l_name" onclick="changeSort(\'Name\')">Как звать</div>
            <div class="table-cell table-header l_owntel" onclick="changeSort(\'OwnTel\')">Телефон</div>
            <div class="table-cell table-header l_tel" onclick="changeSort(\'Tel\')">Телефон родителей</div>
            <div class="table-cell table-header l_mail" onclick="changeSort(\'Email\')">E-Mail</div>
        </div>
    ';

    if (isset($_POST['SortBy'])) {
        if ($_SESSION['SortBy'] === "Name" && $_POST['SortBy'] === "Name") {
            $_SESSION['SortBy'] = "Surname";
        } else {
            $_SESSION['SortBy'] = $_POST["SortBy"];
        }
    }
    try {
        $result = $db->getStudentsList("UniqueId, Surname, Name, Gender, Tel, OwnTel, Email", $_SESSION['SortBy']);
        if ($result->rowCount() > 0) {
            foreach ($result as $row) {
                $photo = "/photos/" . $row['UniqueId'] . ".jpg";
                if (!file_exists(".." . $photo)) {
                    $photo = "/photos/" . $row['Gender'] . ".jpg";
                }

                $output .= '
                <div class="table-row" id="tr-'.$row["UniqueId"].'">
                    <div class="table-cell l_chk"><input type="checkbox" id="cb-'.$row["UniqueId"].'"></div>
                    <div class="table-cell l_photo" onclick="checkIt(\''.$row["UniqueId"].'\')"><img src="' . $photo . '" alt="Photo"></div>';
                if ($_SESSION['SortBy'] === 'Name') {
                    $output .= '<div class="table-cell l_name"><b>' . $row["Name"] . '</b><br>' . $row["Surname"] . '</div>';
                } else {
                    $output .= '<div class="table-cell l_name"><b>' . $row["Surname"] . '</b><br> ' . $row["Name"] . '</div>';
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
    $output .= '</div>';
    header("Content-type: text/html; charset=windows-1251");
    echo $output;
}
