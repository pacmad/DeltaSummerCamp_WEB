<?php
/**
 * Страница для работы с курсами и проектами.
 * Date: 24.06.2018
 * Time: 16:39
 */
include "phplib/validate.inc";
include_once "phplib/dbConnect.php";
?>

<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Расписание</title>
    <link href="CSS/common.css" rel="stylesheet" type="text/css">
    <link href="CSS/admin.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.3.0.min.js"
            integrity="sha256-RTQy8VOmNlT6b2PIRur37p6JEBZUE7o8wPgMvu18MC4="
            crossorigin="anonymous"></script>
    <script src="JS/timetable.js"></script>
    <?php
    $name = $_SESSION['Name'];
    $surname = $_SESSION['Surname'];
    $db = new dbConnect();
    $CID = $_GET['CID'];
    $course = $db->getCourseDetails($CID);
    $timeSlot = $_GET['TS'];

    if(isset($_POST['Save'])) {
        unset($_POST['Save']);
        $db->cleanCourse($CID, $timeSlot);
        foreach ($_POST as $key => $UID) {
            $db->addStudentToCourse($UID, $CID, $timeSlot);
        }
    }
    ?>
</head>

<body>
<div class="title">
    <div class="row">
        <div class="col-6">
            <h1>Привет, <?php echo "$name $surname!"?></h1>
        </div>
        <div class="col-6">
            <div class="icons">
                <div class="tooltip">
                    <a href="admin.php">
                        <div class="iconbox"><span class="fa fa-child icon"></span></div>
                        <span class="tooltiptext">Дети</span>
                    </a>
                </div>
                <div class="tooltip">
                    <a href="teachers.php">
                        <div class="iconbox"><span class="fa fa-user icon"></span></div>
                        <span class="tooltiptext">Преподаватели</span>
                    </a>
                </div>
                <div class="tooltip">
                    <a href="courses.php">
                        <div class="iconbox"><span class="fa fa-graduation-cap icon"></span></div>
                        <span class="tooltiptext">Курсы и проекты</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="main">
    <h3>Расписание курса:</h3>
    <div class="courses-table">
    <?php
        $result = '
            <div class="row course-block">
                <div class="col-4 course-title">
                    <b>' . $course['NameRus'] . '</b>
                </div>
                <div class="col-4 course-authors">
        ';
        foreach($course["Teachers"] as $TID) {
            $teacher = $db->getTeacher($TID)->fetch();
            $result .= "<p>" . $teacher['Surname'] . " " . $teacher['Name'] . "</p>";
        };
        $result .= '
                </div>
                <div class="col-2">
                    <div class="course-icon">
                        <table>
                            <tr>
                                <td' . (($timeSlot == 11) ? ' class="course-icon-1"' : '')  .'></td>
                                <td' . (($timeSlot == 21) ? ' class="course-icon-1"' : '')  .'></td>
                                <td' . (($timeSlot == 31) ? ' class="course-icon-1"' : '')  .'></td>
                            </tr>
                            <tr>
                                <td' . (($timeSlot == 12) ? ' class="course-icon-1"' : '')  .'></td>
                                <td' . (($timeSlot == 22) ? ' class="course-icon-1"' : '')  .'></td>
                                <td' . (($timeSlot == 32) ? ' class="course-icon-1"' : '')  .'></td>
                            </tr>
                            <tr>
                                <td colspan="3"' . (($timeSlot == 0) ? ' class="course-icon-project"' : '')  .'></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div><!-- row -->
        ';
        echo $result;
        ?>
    </div><!-- courses-table -->
    <div class="row course-block">
        <?php
        try {
            $students = $db->getStudentsList("UniqueId, Surname, Name", "Surname", "AppStatus >= 9");
        } catch (PDOException $exception) {
            error("Error in getStudentsList: $exception");
        }
        foreach ($students as $student) {
            $UID = $student['UniqueId'];
            $name = $student['Name'];
            $surname = $student['Surname'];
            if ($db->isStudentInCourse($UID, $CID, $timeSlot)) {
                $targetDir = "./photos/";
                $pattern = $targetDir . $UID . '.*';
                $photos = glob($pattern);

                if (isset($photos) && $photos != false && $photos !== '') {
                    $photo = $photos[0];
                };

                echo "<div class='student'><a href='student.php?ID=$UID'><b>$surname $name</b><br><img src=$photo alt='Photo'></a></div>";
            }
        }
        ?>
    </div>

<?php
/*
 * Вывод списка детей для изменения состава на курсе (появляется по нажатию кнопки)
 *
 */
if(!$_SESSION['ReadOnly']) {
    $output = '
    <div class="row">
        <button id="change-btn" onclick="showChange()">Изменить</button>
    </div>
    <div class="row course-block add-form hidden" id="change-block">
    <form method="post" id="students">
        <h3>Список детей:</h3>
        <ul>
    ';
    try {
        $students = $db->getStudentsList("UniqueId, Surname, Name", "Surname", "AppStatus >= 9");
    } catch (PDOException $exception) {
        error("Error in getStudentsList: $exception");
    }
    foreach ($students as $student) {
        $UID = $student['UniqueId'];
        $name = $student['Name'];
        $surname = $student['Surname'];
        $checked = ($db->isStudentInCourse($UID, $CID, $timeSlot)) ? "checked" : "";

        $output .= "<li><label for='UID-$UID'><input type='checkbox' name='UID-$UID' id='UID-$UID' value='$UID' $checked>$surname&nbsp;$name</li>";
    };
    $output .= '
        </ul>
        <input type="submit" name="Save" value="Сохранить">
    </form>
    </div>
    ';
    echo $output;
}
?>
</div><!-- main -->
</body>
</html>