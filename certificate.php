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
$fullName = html_entity_decode($row['CertName']);
list($surName, $Name) = explode(" ", $fullName);

$courseList = $db->getCoursesForCert($UID);

$projectName = $db->getProjectForCert($UID);

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
<div class="title" id="title">
    <?php
    if ($lang == 'ru') echo "Сертификат";
    elseif ($lang == 'de') echo "Urkunde";
    else echo "Certificate of participation";
    ?>
</div>
<div class="name">
    <?php
    echo $fullName;
    ?>
</div>
<div class="plain">
    <?php
    if ($lang == 'ru') {
        $output = "<p>" . $Name;
        if ($gender == 'm')
            $output .= " принимал ";
        else
            $output .= " принимала ";
        $output .= "участие в работе физико-математического лагеря «Дельта» с 16 по 30 июля 2018 г., проводимого совместно творческим объединением «Дважды-Два» (Москва) и культурным центром GOROD (Мюнхен). </>";
        $output .= "<p>" . $Name;
        if ($gender == 'm')
            $output .= " прослушал следующие курсы:";
        else
            $output .= " прослушала следующие курсы:";
        $output .= "</p>";
        $output .= "<ul>";
        foreach ($courseList as $course) {
            $output .= "<li>" . $course['courseName'] . " (академических часов: " . $course['length'] . ")" . "</li>";
            $teachers[] = $course['teachers'];
        }
        $output .= "</ul>";

        if ($gender == 'm')
            $output .= "<p>и участвовал в проекте</p>";
        else
            $output .= "<p>и участвовала в проекте</p>";

    } elseif ($lang == 'de') {
        $output = "<p>hat am Mathematik- und Physik-Sommercamp  \"Delta\"  vom 16. Juli bis 30. Juli 2018 in München teilgenommen.";
        $output .= "<p>Das Camp wurde von der Kreativen Vereinigung \"Dvazhdy dva\" (Moskau) in Kooperation mit dem Kulturzentrum \"GOROD\" (München) organisiert und durchgeführt.</p>";
        $output .= "<p>$Name hat folgende Kurse belegt:";
        $output .= "<ul>";
        foreach ($courseList as $course) {
            $output .= "<li>" . $course['courseName'] . " (" . $course['length'] . " akademische Stunden)" . "</li>";
            $teachers[] = $course['teachers'];
        }
        $output .= "</ul>";

        $output .= "<p>Projektarbeit zum Thema:</p>";

    } else {
        $output = "<p>$surName took part in the international MINT summer camp „Delta“ from 16th till 30st of July, 2018, co-organized 
            by the society “Dvazhdy-Dva” (Moscow) and the Cultural Centre GOROD (Munich).</p>";

        if ($gender == 'm')
            $output .= "<p>He ";
        else
            $output .= "<p>She ";

        $output .= "attended the following courses:</p>";
        $output .= "<ul>";
        foreach ($courseList as $course) {
            $output .= "<li>" . $course['courseName'] . " (" . $course['length'] . " academic hours)" . "</li>";
            $teachers[] = $course['teachers'];
        }
        $output .= "</ul>";
        if ($gender == 'm')
            $output .= "<p>He participated in the following project:</p>";
        else
            $output .= "<p>She participated in the following project:</p>";
    }
    $output .= "<p class='project'>" . $projectName . "</p>";
    $output .= "<p class='greetings'>Тут приятные слова...</p>";

    if ($lang == 'ru') {
        $output .= "
            <p class='director'>Анна Семовская, руководитель лагеря:</p>
        ";
        foreach($db->getProjectLeader($UID) as $leader) {
            $output .= "<p class='director'>" . iconv("Windows-1251", "UTF-8", $leader['Name']) . " " . iconv("Windows-1251", "UTF-8", $leader['Surname']) . ", руководитель проекта:</p>";
        }
        $output .= "
            <p class='director'></p>
            <p>Ведущие курсов:</p>
            ";
    } elseif ($lang == 'de') {
        $output .= "
            <p class='director'>Anna Semovskaya, Sommercamp-Leiterin:</p>
        ";
        foreach($db->getProjectLeader($UID) as $leader) {
            $output .= "<p class='director'>" . $leader['Name_en'] . " " . $leader['Surname_en'] . ", Projektleiter/Projektleiterin:</p>";
        }
        $output .= "        
            <p>Kursleiter/Kursleiterinnen:</p>
            ";
    } else {
        $output .= "
            <p class='director'>Anna Semovskaya, Director of the Summer Camp:</p>
        ";
        foreach($db->getProjectLeader($UID) as $leader) {
            $output .= "<p class='director'>" . $leader['Name_en'] . " " . $leader['Surname_en'] . ", Project Lead:</p>";
        }
        $output .= "
            <p>Teachers:</p>
            ";
    }

    $output .= "<ul class='teachers'>";
    foreach (array_unique(single_array($teachers)) as $teacher) {
        $teacher = iconv("Windows-1251", "UTF-8", $teacher);
        $output .= "<li>$teacher</li>";
    }
    $output .= "</ul>";

    echo $output;
    ?>
</div>
</body>
</html>
