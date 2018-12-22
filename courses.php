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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Курсы и проекты</title>
    <link href="CSS/common.css" rel="stylesheet" type="text/css">
    <link href="CSS/admin.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.3.0.min.js"
            integrity="sha256-RTQy8VOmNlT6b2PIRur37p6JEBZUE7o8wPgMvu18MC4="
            crossorigin="anonymous"></script>
    <script src="JS/common.js"></script>
    <script src="JS/courses.js"></script>
    <?php
    $db = new dbConnect();
    $row = $db->admGet($_SESSION['UID']);
    $name = $_SESSION['Name'] = $row['Name'];
    $surname = $_SESSION['Surname'] = $row['Surname'];

    // Обработка добавления курса
    if (isset($_POST['AddCourse'])) {
        $CID = $db->addCourse($_POST['NameRus'], $_POST['NameGer'], $_POST['NameEng']);
        $db->addCourseToTimetable($CID, 99); // Заглушка, просто маркер того, что курс зарегистрирован
        foreach ($_POST as $key => $TID) {
            if (strpos($key, 'TID-') === 0) {
                $db->addTeacherToCourse($CID, $TID);
            }
        }
        if($_POST['t0'] == 1) $db->addCourseToTimetable($CID, 0);
        if($_POST['t11'] == 1) $db->addCourseToTimetable($CID, 11);
        if($_POST['t21'] == 1) $db->addCourseToTimetable($CID, 21);
        if($_POST['t31'] == 1) $db->addCourseToTimetable($CID, 31);
        if($_POST['t12'] == 1) $db->addCourseToTimetable($CID, 12);
        if($_POST['t22'] == 1) $db->addCourseToTimetable($CID, 22);
        if($_POST['t32'] == 1) $db->addCourseToTimetable($CID, 32);
    }

    // Обработка изменения курса
    elseif (isset($_POST['ChangeCourse'])) {
        $CID = $_POST['ChangeCourse'];
        $db->updateCourse($CID, $_POST['NameRus'], $_POST['NameGer'], $_POST['NameEng']);

        if (strlen($_POST['NameRus'] . $_POST['NameGer'] . $_POST['NameEng']) != 0) {
            $db->cleanTeachersFromCourse($CID);
            foreach ($_POST as $key => $TID) {
                if (strpos($key, 'TID-') === 0) {
                    $db->addTeacherToCourse($CID, $TID);
                }
            }
            $db->updateCourseToTimetable($CID,
                $_POST['t0'],
                $_POST['t11'],
                $_POST['t21'],
                $_POST['t31'],
                $_POST['t12'],
                $_POST['t22'],
                $_POST['t32']
            );
        }
    }

    // Формируем массив курсов вида: [имя_курса, [массив_преподавателей "Teachers[]"], [массив_пар "TimeSlots[]']]
    $courses = $db->getCoursesTable();
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
            </div>
        </div>
    </div>
</div>
<div class="main">
    <h3>Проекты и курсы:</h3>
    <div class="courses-table">
<?php
    foreach ($courses as $CID=>$course) {
        // Название курса и преподаватели
        $result = "
            <div class='row course-block'>
                <div class='col-4 course-title'>
                    <b>" . $course['NameRus'] . "</b>
                </div>
                <div class='col-4 course-authors'>
                ";
            foreach($course["Teachers"] as $TID) {
                $row = $db->getTeacher($TID)->fetch();
                $teacher = $row['Name'] . ' ' . $row['Surname'];
                $result .= "<p>" . $teacher . "</p>";
            };
            $result .= "
                        </div>
                <div class='col-2'>
            ";

            // Таблица - разсписание. Помеченные участки при клике мышки ведут на страницу расписания курса
            $result .= '
            <div class="course-icon">
                <table>
                    <tr>
                        <td';
                            if (in_array(11, $course["TimeSlots"])) {
                                $result .= " class='course-icon-1 clickable' onclick='window.location.href=\"timetable.php?CID=$CID&TS=11\"'";
                            };
                        $result .= '
                        >
                        </td>
                        <td';
                            if (in_array(21, $course["TimeSlots"])) {
                                $result .= " class='course-icon-1 clickable' onclick='window.location.href=\"timetable.php?CID=$CID&TS=21\"'";
                            };
                        $result .= '
                        >
                        </td>
                        <td';
                            if (in_array(31, $course["TimeSlots"])) {
                                $result .= " class='course-icon-1 clickable' onclick='window.location.href=\"timetable.php?CID=$CID&TS=31\"'";
                            };
                        $result .= '
                        >
                        </td>
                    </tr>
                    <tr>
                        <td';
                            if (in_array(12, $course["TimeSlots"])) {
                                $result .= " class='course-icon-1 clickable' onclick='window.location.href=\"timetable.php?CID=$CID&TS=12\"'";
                            };
                        $result .= '
                        >
                        </td>
                        <td';
                            if (in_array(22, $course["TimeSlots"])) {
                                $result .= " class='course-icon-1 clickable' onclick='window.location.href=\"timetable.php?CID=$CID&TS=22\"'";
                            };
                        $result .= '
                        >
                        </td>
                        <td';
                            if (in_array(32, $course["TimeSlots"])) {
                                $result .= " class='course-icon-1 clickable' onclick='window.location.href=\"timetable.php?CID=$CID&TS=32\"'";
                            };
                        $result .= '
                        >
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3"';
                            if (in_array(0, $course["TimeSlots"])) {
                                $result .= " class='course-icon-project clickable' onclick='window.location.href=\"timetable.php?CID=$CID&TS=0\"'";
                            };
                        $result .= '
                        >
                        </td>
                    </tr>
                </table>
            </div>
            ';
            // "Шестерёнка" - изменение курса
            $result .= "
                </div>
                <div class='col-2 hidden' id='CID-$CID'>
                    <a href='change_course.php?CID=$CID'>
                        <div class='iconbox'><span class='fa fa-cog icon'></span></div>
                    </a>
                </div>
            </div><!-- row -->
        ";
        echo $result;
    }
?>
    </div>
    <?php
    if(!$_SESSION['ReadOnly']) {
        $output = '        
        <button onclick="showChange()" id="change_button">Изменить...</button>
        <div class="row course-block add-form hidden" id="add_course">
            <h2>Добавить курс / проект</h2>
            <form method="post" id="add_course_form">
                <p class="course-title">Название курса (рус): <input type="text" name="NameRus" id="course-rus"></p>
                <p class="course-title">Kursname (ger): <input type="text" name="NameGer" id="course-ger"></p>
                <p class="course-title">Course name (eng): <input type="text" name="NameEng" id="course-eng"></p>
                <div class="row">
                    <div class="col-8">
                        <p class="course-title">Преподаватели:</p>
                        <ul>
        ';
        foreach ($db->getTeachers() as $teacher) {
            $output .= '<li><label for="TID-' . $teacher['UID'] . '"><input type="checkbox" name="TID-' . $teacher['UID'] .
                '" id="TID-' . $teacher['UID'] . '" value="' . $teacher['UID'] . '">' . $teacher['Name'] . ' ' .
                $teacher['Surname'] . '</li>';
        };
        $output .= '

                        </ul>
                    </div>
                    <div class="col-1"></div>
                    <div class="col-2">
                        <p class="course-title">Расписание:</p>
                        <div class="course-icon">
                            <table>
                                <tr>
                                    <td id="t11"></td>
                                    <td id="t21"></td>
                                    <td id="t31"></td>
                                </tr>
                                <tr>
                                    <td id="t12"></td>
                                    <td id="t22"></td>
                                    <td id="t32"></td>
                                </tr>
                                <tr>
                                    <td colspan="3" id="t0"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="tt0" name="t0" value="0">
                <input type="hidden" id="tt11" name="t11" value="0">
                <input type="hidden" id="tt21" name="t21" value="0">
                <input type="hidden" id="tt31" name="t31" value="0">
                <input type="hidden" id="tt12" name="t12" value="0">
                <input type="hidden" id="tt22" name="t22" value="0">
                <input type="hidden" id="tt32" name="t32" value="0">
                <input type="hidden" name="AddCourse">
                <input type="submit" value="Добавить">
            </form>
        </div>
        ';
        echo $output;
    }
    ?>
</div>
</body>
</html>