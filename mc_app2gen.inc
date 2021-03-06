<?php
/*
 * �������� �������� ������, ������������ � ������� �� ����� "���������� 2" � �������� �� ������������� ������.
 *
 * ��������! ������ ���� ������ ���� � ��������� WINDOWS-1251
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
        $this->Cell(0, 10, '- �������� ' . $this->PageNo() . ' �� {nb} -', 0, 0, 'C');
    }
}

$db = new dbConnect();
$UID = $_GET['id'];
$row = $db->getPerson($UID);

// �������� ������: �����, ������, ���������, �������� � ����-����� (���� ������ �������)
$readyToCreate = true;

// �������� ���������������� ����� �������
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
 * ��������� PDF-�����
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
    $pdf->Cell(82.5, 5, '���������� 2');
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Cell(82.5);
    $pdf->Cell(82.5, 5, '� �������� ��������� �_________________');
    $pdf->Ln();
    $pdf->Cell(82.5);
    $pdf->Cell(82.5, 6, '�� ___/___/____, ������������ ����� ');
    $pdf->Ln();
    $pdf->SetFont('', 'BU');
    $pdf->Cell(82.5);
    $pdf->Cell(82.5, 8, '________________________________________');
    $pdf->Ln();
    $pdf->SetFont('', '', 10);
    $pdf->Cell(82.5);
    $pdf->Cell(82.5, 3, '            (����������)                     ');
    $pdf->Ln();
    $pdf->SetFont('', 'B', 12);
    $pdf->Cell(82.5);
    $pdf->Cell(82.5, 7, '�');
    $pdf->Ln();
    $pdf->SetFont('', 'BU', 12);
    $pdf->Cell(82.5);
    $pdf->Cell(82.5, 7, '_____��������� ����� ����������________');
    $pdf->Ln();
    $pdf->SetFont('', '', 10);
    $pdf->Cell(82.5);
    $pdf->Cell(82.5, 3, '            (����������)                     ');
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();

    $text = "��� ������: " . iconv('UTF-8', 'Windows-1251', $p['Surname'])
        . " " . iconv('UTF-8', 'Windows-1251', $p['Name']);
    if ($p['MiddleName'] !== "") $text .= " " . iconv('UTF-8', 'Windows-1251', $p['MiddleName']);
    $text .= ".";
    $pdf->SetFont('Times', 'BI', 12);
    $pdf->Cell(0, 7, $text);

    $pdf->Ln();

    $text = "��������� ���������� � ���������� ����������� ��������� ������, ����������� ���������� ��� ���������� ���������:\n\n";
    $text .= "1.   ���� � ����� �������� ������ �� ����� ������� � ����������: " . $p['ComingDate'] . " � " . $p['ComingTime'] . ".\n";

    $Pl = $p['ComingPlace'];
    if($Pl === 'DME') $Pl = '�������� ����������';
    elseif ($Pl === 'VKO') $Pl = '�������� �������';
    elseif ($Pl === 'SVO') $Pl = '�������� �����������';
    elseif ($Pl === 'gorod') $Pl = '���������� ����� GOROD (KULTURZENTRUM GOROD) �� ������ Arnulfstrasse 197, 80634 Muenchen';
    elseif ($Pl === 'hbf') $Pl = '����������� ��������������� ������ �.������� (Muenchen Hauptbahnhof), �����: ' . $p['ComingFlight'];
    elseif ($Pl === 'MUC') $Pl = '������������� �������� �.������� ����� ������-������ ������� (Flughafen Muenchen �Franz Josef Strauss�), ����: ' . $p['ComingFlight'];
    elseif ($Pl === 'other') $Pl = "\n����: _____________________________________________________________________________________________________________";
    $text .= "2.   ����� ������� � ����������: " . $Pl . ".\n";

    $Pl = $p['ComingPlace'];
    if ($Pl === 'DME') $Pl = '�������� ����������, ����� ����� ' . $p['ComingFlight'] . ', ' . $p['ComingDate'] . ' � ' . $p['ComingTime'] . ' (������� ����� ������� � ���������� � ���������, �� ��� ���� �� ����������� �����)';
    elseif ($Pl === 'VKO') $Pl = '�������� �������, ����� ����� ' . $p['ComingFlight'] . ', ' . $p['ComingDate'] . ' � ' . $p['ComingTime'] . ' (������� ����� ������� � ���������� � ���������, �� ��� ���� �� ����������� �����)';
    elseif ($Pl === 'SVO') $Pl = '�������� �����������, ����� ����� ' . $p['ComingFlight'] . ', ' . $p['ComingDate'] . ' � ' . $p['ComingTime'] . ' (������� ����� ������� � ���������� � ���������, �� ��� ���� �� ����������� �����)';
    else $Pl = '��� ������������� ��� �������';
    $text .= "3.   ������������� (���� ����) ��� ������� ������ � ������ (�������� ������, ����� �����, ���� � ����� ������): " . $Pl . ".\n";

    $text .= "4.   ������ ���������� ���������� ������ � ���������� ������ GOROD � ������� � ��������� ������������: � " . $p['ComingDate'] . " �� " . $p['LeavingDate'] .".\n";

    $Pl = $p['LeavingPlace'];
    if ($Pl === 'DME') $Pl = '�������� ����������, ����� ����� ' . $p['LeavingFlight'] . ', ' . $p['LeavingDate'] . ' � ' . $p['LeavingTime'];
    elseif ($Pl === 'VKO')  $Pl = '�������� �������, ����� ����� ' . $p['LeavingFlight'] . ', ' . $p['LeavingDate'] . ' � ' . $p['LeavingTime'];
    elseif ($Pl === 'SVO')  $Pl = '�������� �����������, ����� ����� ' . $p['LeavingFlight'] . ', ' . $p['LeavingDate'] . ' � ' . $p['LeavingTime'];
    else $Pl = '��� ������������� ��� �������';
    $text .= "5.   ������������� (���� ����) ��� ������� ������ � ������ (�������� ��������, ����� �����, ���� � ����� ��������): " . $Pl . ".\n";

    $text .= "6.   ���� � ����� �������� ������ �� ����� �������� ������ ��� ��������������� ����������: " . $p['LeavingDate'] . " � " . $p['LeavingTime'] . ".\n";

    $Pl = $p['LeavingPlace'];
    if($Pl === 'DME') $Pl = '�������� ����������';
    elseif ($Pl === 'VKO') $Pl = '�������� �������';
    elseif ($Pl === 'SVO') $Pl = '�������� �����������';
    elseif ($Pl === 'gorod') $Pl = '���������� ����� GOROD (KULTURZENTRUM GOROD) �� ������ Arnulfstrasse 197, 80634 Muenchen';
    elseif ($Pl === 'hbf') $Pl = '����������� ��������������� ������ �.������� (Muenchen Hauptbahnhof), �����: ' . $p['LeavingFlight'];
    elseif ($Pl === 'MUC') $Pl = '������������� �������� �. ������� ����� ������-������ ������� (Flughafen Muenchen �Franz Josef Strauss�), ����: ' . $p['LeavingFlight'];
    elseif ($Pl === 'other') $Pl = "\n����: _____________________________________________________________________________________________________________";
    $text .= "7.   ����� �������� ������ ��� ��������������� ����������: " . $Pl . ".\n";

    $Pl = $p['LeavingWith'];
    if ($Pl !== 'Singly') $Pl = '�������� ������ ��������������� ���������� ��� ���������� �� �������� ����';
    else {
        if ($p['LeavingPlace'] === 'MUC') $Pl = '��������� ������ �� ���� �������� �� ���� ' . $p['LeavingFlight'] . ' � ������������� ��������� �. ������� ����� ������-������ ������� (Flughafen Muenchen �Franz Josef Strauss�)';
        elseif ($p['LeavingPlace'] === 'hbf') $Pl = '�������� ������ �� ����� ' . $p['LeavingFlight'] . ' �� ����������� ��������������� ������� �.������� (Muenchen Hauptbahnhof)';
        else $Pl = "\n����: _____________________________________________________________________________________________________________";
    }
    $text .= "8.   ������ �������� ������ ��� ��������������� ����������: " . $Pl . ".\n";

    $text .= "9.   ���� � �����, ����� �����/������/����� ������������� �������� �� �. 8 ���������� 2: " . $p['LeavingDate'] . " � " . $p['LeavingTime'] . ", ���� " . $p['LeavingFlight'] . ".\n";

    $text .= "10.  ���, ���������� � ���������� ������ �������� ���� �� �. 8 ���������� 2: _______________________________________________________________________________________________________.\n";

    $pdf->SetFont('', '', 12);
    $pdf->MultiCell(0, 7, $text, 0, 'L');

    $text = "11.  ��� � ���������� ������ ��� �� �. 1.3 ��������, � ����� �������������� �������� �� �. 1.2 ��������: ";
    $pdf->MultiCell(0, 7, $text, 0, 'L');
    $pdf->SetFont('', 'I', 12);
    $text = iconv('UTF-8', 'Windows-1251', $p['NotesText']);
    $pdf->MultiCell(0, 7, $text, 0, 'L');
    $pdf->SetFont('', '', 12);

    $text = "12.  �������� � ��������� ������ ������: ";
    $pdf->MultiCell(0, 7, $text, 0, 'L');
    $pdf->SetFont('', 'I', 12);
    $text = iconv('UTF-8', 'Windows-1251', $p['Insurance']);
    $pdf->MultiCell(0, 7, $text, 0, 'L');
    $pdf->SetFont('', '', 12);

    $text = "13.  ������ � �������� �������� �. 3.2 ��������: ";
    $pdf->MultiCell(0, 7, $text, 0, 'L');
    $pdf->SetFont('', 'I', 12);
    $text = iconv('UTF-8', 'Windows-1251', $p['Health']);
    $pdf->MultiCell(0, 6, $text, 0, 'L');

    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();

    $pdf->SetFont('', 'B');
    $pdf->Cell(0, 5, '������� ������:', 0, 0, 'C');
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Cell(($pdf->GetPageWidth()-($leftMargin+$rightMargin)) / 2, 10, '����������:', 'LTR');
    $pdf->Cell(($pdf->GetPageWidth()-($leftMargin+$rightMargin)) / 2, 10, '����������:', 'LTR');
    $pdf->Ln();
    $pdf->Cell(($pdf->GetPageWidth()-($leftMargin+$rightMargin)) / 2, 10, '', 'LBR');
    $pdf->Cell(($pdf->GetPageWidth()-($leftMargin+$rightMargin)) / 2, 10, '', 'LBR');
    $pdf->Ln();



    return $pdf->Output("S");
}
?>
