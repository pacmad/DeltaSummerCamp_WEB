<?php
/**
 * Генерация страницы - сертификата
 * Date: 21/07/2018
 * Time: 19:31
 */
if (isset($_GET['UID']))
    $UID = $_GET['UID'];
else
    return;

include_once "phplib/dbConnect.php";
require_once 'vendor/phpoffice/phpword/bootstrap.php'; // Библиотека PhpWord

function single_array($arr){
    foreach($arr as $key){
        if(is_array($key)){
            $arr1=single_array($key);
            foreach($arr1 as $k){
                $new_arr[]=$k;
            }
        }
        else{
            $new_arr[]=$key;
        }
    }
    return $new_arr;
}

$db = new dbConnect();
$row = $db->getPerson($UID);
$gender = $row['Gender'];
$lang = $row['CertLang'];
$fullName = $row['CertName'];
list($name, $surname) = explode(" ", $fullName);

$courseList = $db->getCoursesForCert($UID);
$projectName = $db->getProjectForCert($UID);
$projectLeaders = [];

if ($lang == 'ru') $certificateText = "Сертификат";
elseif ($lang == 'de') $certificateText = "Urkunde";
else $certificateText = "Certificate of participation";

// Подготовка текста
if ($lang == 'ru') {
    $paragraph_1 = $name;
    if ($gender == 'm')
        $paragraph_1 .= " принимал ";
    else
        $paragraph_1 .= " принимала ";
    $paragraph_1 .= "участие в работе физико-математического лагеря «Дельта» с 22 июля по 5 августа 2019 г., проводимого совместно творческим объединением «Дважды-Два» (Москва) и культурным центром GOROD (Мюнхен).";
    $paragraph_2 = $name;
    if ($gender == 'm')
        $paragraph_2 .= " прослушал следующие курсы:";
    else
        $paragraph_2 .= " прослушала следующие курсы:";

    foreach ($courseList as $course) {
        $courses[] = $course['courseName'] . " (академических часов: " . $course['length'] . ")";
        $teachers[] = $course['teachers'];
    }

    if ($gender == 'm') {
        $paragraph_3 = "и участвовал в проекте";
        $paragraph_4 = $name . " показал заинтересованность в учёбе, активно участвовал в жизни лагеря.";
    }
    else {
        $paragraph_3 = "и участвовала в проекте";
        $paragraph_4 = $name . " показала заинтересованность в учёбе, активно участвовала в жизни лагеря.";
    }

    $directorText = "Анна Семовская, руководитель лагеря:";

    foreach($db->getProjectLeaders($UID) as $leader) {
        $projectLeaders[] = $leader['Name'] . " " . $leader['Surname'] . ", руководитель проекта:";
    }

    $teachersText = "Ведущие курсов:";

} elseif ($lang == 'de') {
    $paragraph_1 = 'hat am Mathematik- und Physik-Sommercamp  "Delta"  vom 22. Juli bis 5. August 2019 in München teilgenommen.';
    $paragraph_1 .= ' Das Camp wurde von der Kreativen Vereinigung "Dvazhdy dva" (Moskau) in Kooperation mit dem Kulturzentrum "GOROD" (München) organisiert und durchgeführt.';
    $paragraph_2 = "$name hat folgende Kurse belegt:";

    foreach ($courseList as $course) {
        $courses[] = $course['courseName'] . " (" . $course['length'] . " akademische Stunden)";
        $teachers[] = $course['teachers'];
    }

    $paragraph_3 = "Projektarbeit zum Thema:";
    $paragraph_4 = $name . " hat sich mit Elan am Campgeschehen teilgenommen und durch seine offene und intelligente Art das Camp bereichert.";

    $directorText = "Anna Semovskaya, Sommercamp-Leiterin:";

    foreach($db->getProjectLeaders($UID) as $leader) {
        $projectLeaders[] = $leader['Name_en'] . " " . $leader['Surname_en'] . ", Projektleiter/Projektleiterin:";
    }

    $teachersText = "Kursleiter/Kursleiterinnen:";

} else {
    $paragraph_1 = "$name took part in the international MINT summer camp „Delta“ from 22th of July till 5th of August,"
        ." 2019, co-organized by the society “Dvazhdy-Dva” (Moscow) and the Cultural Centre GOROD (Munich).";

    if ($gender == 'm')
        $paragraph_2 = "He ";
    else
        $paragraph_2 = "She ";

    $paragraph_2 .= "attended the following courses:";

    foreach ($courseList as $course) {
        $courses[] = $course['courseName'] . " (" . $course['length'] . " academic hours)";
        $teachers[] = $course['teachers'];
    }

    if ($gender == 'm')
        $paragraph_3 = "He participated in the following project:";
    else
        $paragraph_3 = "She participated in the following project:";

    $paragraph_4 = $name . " has shown a keen interest in his chosen courses, contributing to lessons and outside of lessons as well, making friends and being a pleasure to teach.";

    $directorText = "Anna Semovskaya, Director of the Summer Camp:";

    foreach($db->getProjectLeaders($UID) as $leader) {
        $projectLeaders[] = $leader['Name_en'] . " " . $leader['Surname_en'] . ", Project Lead:";
    }

    $teachersText = "Teachers:";
}

foreach (array_unique(single_array($teachers)) as $teacher) {
    $teacherNames[] = "$teacher";
}
// конец подготовки текста


// Создаём новый документ
$phpWord = new \PhpOffice\PhpWord\PhpWord();

// Set default settings
$phpWord->setDefaultFontName('Times New Roman');
$phpWord->setDefaultFontSize(12);
if ($lang == 'ru') {
    $phpWord->getSettings()->setThemeFontLang(new PhpOffice\PhpWord\Style\Language('ru-RU'));
} elseif ($lang == 'de') {
    $phpWord->getSettings()->setThemeFontLang(new PhpOffice\PhpWord\Style\Language('de-DE'));
} else {
    $phpWord->getSettings()->setThemeFontLang(new PhpOffice\PhpWord\Style\Language('en-GB'));
}
$phpWord->getDocInfo()->setCreator('Delta Summer Camp');
$phpWord->getDocInfo()->setTitle('Certificate');

// Defining styles
$phpWord->addParagraphStyle('CertPStyle',
    array(
        'spaceBefore' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(3.5),
        'spaceAfter' => 0,
        'keepNext' => true,
        'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER,
        'spacingLineRule' => 'auto'
    ));
$phpWord->addFontStyle('CertFStyle',
    array(
        'size' => 20
    ));
$phpWord->addParagraphStyle('NamePStyle',
    array(
        'spaceBefore' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(0),
        'keepNext' => true,
        'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER
    ));
$phpWord->addFontStyle('NameFStyle',
    array(
        'size' => 26,
        'bold' => true
    ));
$phpWord->addParagraphStyle('NormalPStyle',
    array(
        'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(10),
        'keepNext' => false,
        'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START,
        'spacingLineRule' => 'auto',
        'contextualSpacing' => false
    ));
$phpWord->addFontStyle('NormalFStyle',
    array(
        'size' => 12,
        'bold' => false
    ));
$phpWord->addParagraphStyle('ListPStyle',
    array(
        'spaceBefore' => 0,
        'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(10),
        'spacingLineRule' => 'auto',
        'contextualSpacing' => true
    ));
$phpWord->addParagraphStyle('ProjectPStyle',
    array(
        'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(10),
        'keepNext' => false,
        'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START,
        'indent' => 0.525,
        'spacingLineRule' => 'auto',
        'contextualSpacing' => false
    ));
$phpWord->addFontStyle('ProjectFStyle',
    array(
        'size' => 14,
        'bold' => true
    ));
$phpWord->addParagraphStyle('LeadersPStyle',
    array(
        'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(10),
        'keepNext' => false,
        'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START,
        'indent' => 0,
        'spacingLineRule' => 'auto',
        'contextualSpacing' => false
    ));
$phpWord->addFontStyle('LeadersFStyle',
    array(
        'size' => 12,
        'bold' => true
    ));
$phpWord->addFontStyle('TeachersFStyle',
    array(
        'size' => 12,
        'bold' => false
    ));
$phpWord->addParagraphStyle('TeachersPStyle',
    array(
        'spaceAfter' => 0,
        'keepNext' => false,
        'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START,
        'indent' => 0,
        'spacingLineRule' => 'auto',
        'contextualSpacing' => false
    ));
$phpWord->addParagraphStyle('TeachersNamesPStyle',
    array(
        'spaceAfter' => 0,
        'keepNext' => false,
        'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START,
        'indent' => 0.525,
        'spacingLineRule' => 'auto',
        'contextualSpacing' => false
    ));
// End of Styles Definitions

// Adding an empty Section to the document and fill it
$section = $phpWord->addSection();

$section->addText($certificateText, 'CertFStyle', 'CertPStyle');
$section->addText($fullName, 'NameFStyle', 'NamePStyle');
$section->addText($paragraph_1, 'NormalFStyle', 'NormalPStyle');
$section->addText($paragraph_2, 'NormalFStyle', 'NormalPStyle');
foreach ($courses as $courseText)
    $section->addListItem($courseText, 0, null, null, 'ListPStyle');
$section->addText($paragraph_3, 'NormalFStyle', 'NormalPStyle');
$section->addText($projectName, 'ProjectFStyle', 'ProjectPStyle');
$section->addText($paragraph_4, 'NormalFStyle', 'NormalFStyle');
$section->addText($directorText, 'LeadersFStyle', 'LeadersPStyle');
foreach ($projectLeaders as $projectLeader) {
    $section->addText($projectLeader, 'LeadersFStyle', 'LeadersPStyle');
}
$section->addText($teachersText, 'TeachersFStyle', 'TeachersPStyle');
foreach ($teacherNames as $teacher) {
    $section->addText($teacher, 'TeachersFStyle', 'TeachersNamesPStyle');
}
/**/
// Saving the document as OOXML file...
try {
    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
} catch (\PhpOffice\PhpWord\Exception\Exception $e) {
    error("Ошибка создания файла phpWord: " . $e->getMessage());
}
$objWriter->save("certificates/$fullName.docx");
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сертификат: <?php echo $fullName ?></title>
    <link href="CSS/certificate.css" rel="stylesheet" type="text/css">
</head>

<body>
    <h1>Создан файл сертификата:</h1>
    <h1><a href="certificates/<?php echo $fullName;?>.docx"><?php echo $fullName;?>.docx</a> </h1>
</body>
</html>
