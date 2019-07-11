<?php
/*
 * Проверка анкетных данных, формирование и отсылка по почте "Приложения 2" к Договору на сопровождение ребёнка.
 *
 * ВНИМАНИЕ! ДАННЫЙ ФАЙЛ ДОЛЖЕН БЫТЬ В КОДИРОВКЕ WINDOWS-1251
 */
require_once 'phplib/dbConnect.php';
require_once 'phplib/fpdf/fpdf.php';
require_once 'phplib/mail.php';
require_once 'phplib/common.inc';

class PDF extends FPDF {
    // Page footer
    function Footer() {
        // Position at 2 cm from bottom
        $this->SetY(-20);

        // Print page number
        $this->SetFont('Times', 'BI', 6);
        $this->Cell(0, 10, '- Страница ' . $this->PageNo() . ' из {nb} -', 0, 0, 'C');
    }
}

$db = new dbConnect();
$UID = $_GET['id'];
$row = $db->getPerson($UID);

// Проверка анкеты: заезд, отъезд, страховка, медицина и форс-мажор (пока делаем вручную)
$readyToCreate = true;

// Отправка сгенерированного файла письмом
if ($readyToCreate) {
    try {
        sendApp2Mail($row, createPDF($row));
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        error("sendApp2Mail error; " . $e . ", UID:" . $UID);
        header("Content-type: text/html; charset=Windows-1251");
        http_response_code(406);
        echo "Error in sendApp2Mail";
    }
    exit();
} else {
    header("Content-type: text/html; charset=Windows-1251");
    http_response_code(400);
    echo "Wrong Data";
    exit();
}

/*
 * Генерация PDF-файла
 */
function createPDF($p) {
    $topMargin = 20;
    $leftMargin = 30;
    $rightMargin = 20;
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddFont('Times', 'B', 'timesbd.php');
    $pdf->AddFont('Times', 'I', 'timesi.php');
    $pdf->AddFont('Times', 'BI', 'timesbi.php');
    $pdf->SetMargins($leftMargin, $topMargin, $rightMargin);

    $pdf->AddPage();
    $pdf->SetFont('Times', 'B', 12);
    $pdf->Cell(82.5);
    $pdf->Cell(82.5, 5, 'ПРИЛОЖЕНИЕ 2');
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Cell(82.5);
    $pdf->Cell(82.5, 5, 'к Договору поручения №_________________');
    $pdf->Ln();
    $pdf->Cell(82.5);
    $pdf->Cell(82.5, 6, 'от ___/___/____, заключенному между ');
    $pdf->Ln();
    $pdf->SetFont('', 'BU');
    $pdf->Cell(82.5);
    $pdf->Cell(82.5, 8, '________________________________________');
    $pdf->Ln();
    $pdf->SetFont('', '', 10);
    $pdf->Cell(82.5);
    $pdf->Cell(82.5, 3, '            (Доверитель)                     ');
    $pdf->Ln();
    $pdf->SetFont('', 'B', 12);
    $pdf->Cell(82.5);
    $pdf->Cell(82.5, 7, 'и');
    $pdf->Ln();
    $pdf->SetFont('', 'BU', 12);
    $pdf->Cell(82.5);
    $pdf->Cell(82.5, 7, '_____Семовской Анной Сергеевной________');
    $pdf->Ln();
    $pdf->SetFont('', '', 10);
    $pdf->Cell(82.5);
    $pdf->Cell(82.5, 3, '            (Поверенный)                     ');
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();

    $text = "ФИО ребёнка: " . iconv('UTF-8', 'Windows-1251', $p['Surname'])
        . " " . iconv('UTF-8', 'Windows-1251', $p['Name']);
    if ($p['MiddleName'] !== "") $text .= " " . iconv('UTF-8', 'Windows-1251', $p['MiddleName']);
    $text .= ".";
    $pdf->SetFont('Times', 'BI', 12);
    $pdf->Cell(0, 7, $text);

    $pdf->Ln();

    $text = "Настоящим Доверитель и Поверенный согласовали следующие данные, необходимые Доверителю для исполнения поручения:\n\n";
    $text .= "1.   Дата и время прибытия ребёнка на место встречи с Поверенным: " . $p['ComingDate'] . " в " . $p['ComingTime'] . ".\n";

    $Pl = $p['ComingPlace'];
    if($Pl === 'DME') $Pl = 'аэропорт Домодедово';
    elseif ($Pl === 'VKO') $Pl = 'аэропорт Внуково';
    elseif ($Pl === 'SVO') $Pl = 'аэропорт Шереметьево';
    elseif ($Pl === 'gorod') $Pl = 'Культурный центр GOROD (KULTURZENTRUM GOROD) по адресу Arnulfstrasse 197, 80634 Muenchen';
    elseif ($Pl === 'hbf') $Pl = 'Центральный железнодорожный вокзал г.Мюнхена (Muenchen Hauptbahnhof), поезд: ' . $p['ComingFlight'];
    elseif ($Pl === 'MUC') $Pl = 'Международный аэропорт г.Мюнхена имени Франца-Йозефа Штрауса (Flughafen Muenchen «Franz Josef Strauss»), рейс: ' . $p['ComingFlight'];
    elseif ($Pl === 'other') $Pl = "\nиное: _____________________________________________________________________________________________________________";
    $text .= "2.   Место встречи с Поверенным: " . $Pl . ".\n";

    $Pl = $p['ComingPlace'];
    if ($Pl === 'DME') $Pl = 'аэропорт Домодедово, номер рейса ' . $p['ComingFlight'] . ', ' . $p['ComingDate'] . ' в ' . $p['ComingTime'] . ' (указано время встречи с Поверенным в аэропорту, за три часа до отправления рейса)';
    elseif ($Pl === 'VKO') $Pl = 'аэропорт Внуково, номер рейса ' . $p['ComingFlight'] . ', ' . $p['ComingDate'] . ' в ' . $p['ComingTime'] . ' (указано время встречи с Поверенным в аэропорту, за три часа до отправления рейса)';
    elseif ($Pl === 'SVO') $Pl = 'аэропорт Шереметьево, номер рейса ' . $p['ComingFlight'] . ', ' . $p['ComingDate'] . ' в ' . $p['ComingTime'] . ' (указано время встречи с Поверенным в аэропорту, за три часа до отправления рейса)';
    else $Pl = 'без сопровождения при перелёте';
    $text .= "3.   Сопровождение (если есть) при перелёте Москва – Мюнхен (аэропорт вылета, номер рейса, дата и время вылета): " . $Pl . ".\n";

    $text .= "4.   Период временного пребывания ребёнка в культурном центре GOROD и участия в досуговых мероприятиях: с " . $p['ComingDate'] . " по " . $p['LeavingDate'] .".\n";

    $Pl = $p['LeavingPlace'];
    if ($Pl === 'DME') $Pl = 'аэропорт Домодедово, номер рейса ' . $p['LeavingFlight'] . ', ' . $p['LeavingDate'] . ' в ' . $p['LeavingTime'];
    elseif ($Pl === 'VKO')  $Pl = 'аэропорт Внуково, номер рейса ' . $p['LeavingFlight'] . ', ' . $p['LeavingDate'] . ' в ' . $p['LeavingTime'];
    elseif ($Pl === 'SVO')  $Pl = 'аэропорт Шереметьево, номер рейса ' . $p['LeavingFlight'] . ', ' . $p['LeavingDate'] . ' в ' . $p['LeavingTime'];
    else $Pl = 'без сопровождения при перелёте';
    $text .= "5.   Сопровождение (если есть) при перелёте Мюнхен – Москва (аэропорт прибытия, номер рейса, дата и время прибытия): " . $Pl . ".\n";

    $text .= "6.   Дата и время прибытия ребёнка на место передачи ребёнка под ответственность Доверителя: " . $p['LeavingDate'] . " в " . $p['LeavingTime'] . ".\n";

    $Pl = $p['LeavingPlace'];
    if($Pl === 'DME') $Pl = 'аэропорт Домодедово';
    elseif ($Pl === 'VKO') $Pl = 'аэропорт Внуково';
    elseif ($Pl === 'SVO') $Pl = 'аэропорт Шереметьево';
    elseif ($Pl === 'gorod') $Pl = 'Культурный центр GOROD (KULTURZENTRUM GOROD) по адресу Arnulfstrasse 197, 80634 Muenchen';
    elseif ($Pl === 'hbf') $Pl = 'Центральный железнодорожный вокзал г.Мюнхена (Muenchen Hauptbahnhof), поезд: ' . $p['LeavingFlight'];
    elseif ($Pl === 'MUC') $Pl = 'Международный аэропорт г. Мюнхена имени Франца-Йозефа Штрауса (Flughafen Muenchen «Franz Josef Strauss»), рейс: ' . $p['LeavingFlight'];
    elseif ($Pl === 'other') $Pl = "\nиное: _____________________________________________________________________________________________________________";
    $text .= "7.   Место передачи ребёнка под ответственность Доверителя: " . $Pl . ".\n";

    $Pl = $p['LeavingWith'];
    if ($Pl !== 'Singly') $Pl = 'передать ребёнка непосредственно Доверителю или указанному им третьему лицу';
    else {
        if ($p['LeavingPlace'] === 'MUC') $Pl = 'проводить ребёнка до зоны контроля на рейс ' . $p['LeavingFlight'] . ' в Международном аэропорту г. Мюнхена имени Франца-Йозефа Штрауса (Flughafen Muenchen «Franz Josef Strauss»)';
        elseif ($p['LeavingPlace'] === 'hbf') $Pl = 'посадить ребёнка на поезд ' . $p['LeavingFlight'] . ' на центральном железнодорожном вокзале г.Мюнхена (Muenchen Hauptbahnhof)';
        else $Pl = "\nиное: _____________________________________________________________________________________________________________";
    }
    $text .= "8.   Способ передачи ребёнка под ответственность Доверителя: " . $Pl . ".\n";

    $text .= "9.   Дата и время, номер рейса/поезда/иного транспортного средства по п. 8 Приложения 2: " . $p['LeavingDate'] . " в " . $p['LeavingTime'] . ", рейс " . $p['LeavingFlight'] . ".\n";

    $text .= "10.  ФИО, паспортные и контактные данные третьего лица по п. 8 Приложения 2: _______________________________________________________________________________________________________.\n";

    $pdf->SetFont('', '', 12);
    $pdf->MultiCell(0, 7, $text, 0, 'L');

    $text = "11.  ФИО и контактные данные лиц по п. 1.3 Договора, а также дополнительные указания по п. 1.2 Договора: ";
    $pdf->MultiCell(0, 7, $text, 0, 'L');
    $pdf->SetFont('', 'I', 12);
    $text = iconv('UTF-8', 'Windows-1251', $p['NotesText']);
    $pdf->MultiCell(0, 7, $text, 0, 'L');
    $pdf->SetFont('', '', 12);

    $text = "12.  Сведения о страховом полисе ребёнка: ";
    $pdf->MultiCell(0, 7, $text, 0, 'L');
    $pdf->SetFont('', 'I', 12);
    $text = iconv('UTF-8', 'Windows-1251', $p['Insurance']);
    $pdf->MultiCell(0, 7, $text, 0, 'L');
    $pdf->SetFont('', '', 12);

    $text = "13.  Данные о здоровье согласно п. 3.2 Договора: ";
    $pdf->MultiCell(0, 7, $text, 0, 'L');
    $pdf->SetFont('', 'I', 12);
    $text = iconv('UTF-8', 'Windows-1251', $p['Health']);
    $pdf->MultiCell(0, 6, $text, 0, 'L');

    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();

    $pdf->SetFont('', 'B');
    $pdf->Cell(0, 5, 'Подписи сторон:', 0, 0, 'C');
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Cell(($pdf->GetPageWidth()-($leftMargin+$rightMargin)) / 2, 10, 'Поверенный:', 'LTR');
    $pdf->Cell(($pdf->GetPageWidth()-($leftMargin+$rightMargin)) / 2, 10, 'Доверитель:', 'LTR');
    $pdf->Ln();
    $pdf->Cell(($pdf->GetPageWidth()-($leftMargin+$rightMargin)) / 2, 10, '', 'LBR');
    $pdf->Cell(($pdf->GetPageWidth()-($leftMargin+$rightMargin)) / 2, 10, '', 'LBR');
    $pdf->Ln();



    return $pdf->Output("S");
}
?>
