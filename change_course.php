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
    <meta charset="windows-1251">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Курсы и проекты</title>
    <link href="CSS/common.css" rel="stylesheet" type="text/css">
    <link href="CSS/admin.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.3.0.min.js"
            integrity="sha256-RTQy8VOmNlT6b2PIRur37p6JEBZUE7o8wPgMvu18MC4="
            crossorigin="anonymous"></script>
    <script src="JS/change_course.js"></script>
    <?php
    $name = $_SESSION['Name'];
    $surname = $_SESSION['Surname'];
    $db = new dbConnect();
    $course = $db->getCourseDetails($_GET['CID']);
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
    <div class="row course-block add-form">
        <h2>Изменить курс / проект</h2>
        <form method="post" action="courses.php" id="add_course_form">
            <p class="course-title">Название курса (рус): <input type="text" name="NameRus" id="course-rus" value="<?php echo $course["NameRus"] ?>"></p>
            <p class="course-title">Kursname (ger): <input type="text" name="NameGer" id="course-ger" value="<?php echo $course["NameGer"] ?>"></p>
            <p class="course-title">Course name (eng): <input type="text" name="NameEng" id="course-eng" value="<?php echo $course["NameEng"] ?>"></p>
            <div class="row">
                <div class="col-8">
                    <p class="course-title">Преподаватели:</p>
                    <ul>
                        <?php
                            foreach ($db->getTeachers() as $teacher) {
                                $checked = (in_array($teacher['UID'], $course['Teachers'])) ? 'checked' : '';
                                echo '<li><label for="TID-' . $teacher['UID'] . '"><input type="checkbox" name="TID-' .
                                    $teacher['UID'] . '" id="TID-' . $teacher['UID'] . '" value="' . $teacher['UID'] .
                                    '" ' . $checked . '>' . $teacher['Name'] . ' ' . $teacher['Surname'] . '</li>';
                            }
                        ?>
                    </ul>
                </div>
                <div class="col-1"></div>
                <?php
                $output = '                
                <div class="col-2">
                    <p class="course-title">Расписание:</p>
                    <div class="course-icon">
                        <table>
                            <tr>
                                <td id="t11" ' . (in_array('11', ($course['TimeSlots'])) ? 'class="course-icon-1"' : '') . '></td>
                                <td id="t21" ' . (in_array('21', ($course['TimeSlots'])) ? 'class="course-icon-1"' : '') . '></td>
                                <td id="t31" ' . (in_array('31', ($course['TimeSlots'])) ? 'class="course-icon-1"' : '') . '></td>
                            </tr>
                            <tr>
                                <td id="t12" ' . (in_array('12', ($course['TimeSlots'])) ? 'class="course-icon-1"' : '') . '></td>
                                <td id="t22" ' . (in_array('22', ($course['TimeSlots'])) ? 'class="course-icon-1"' : '') . '></td>
                                <td id="t32" ' . (in_array('32', ($course['TimeSlots'])) ? 'class="course-icon-1"' : '') . '></td>
                            </tr>
                            <tr>
                                <td colspan="3" id="t0" ' . (in_array('0', ($course['TimeSlots'])) ? 'class="course-icon-project"' : '') . '></td>
                            </tr>
                        </table>
                    </div>
                </div>
                ';
                echo $output;
                ?>
            </div>
            <?php
            $output = '
            <input type="hidden" id="tt0" name="t0" value="' . (in_array('0', ($course['TimeSlots'])) ? '1' : '0') . '">
            <input type="hidden" id="tt11" name="t11" value="' . (in_array('11', ($course['TimeSlots'])) ? '1' : '0') . '">
            <input type="hidden" id="tt21" name="t21" value="' . (in_array('21', ($course['TimeSlots'])) ? '1' : '0') . '">
            <input type="hidden" id="tt31" name="t31" value="' . (in_array('31', ($course['TimeSlots'])) ? '1' : '0') . '">
            <input type="hidden" id="tt12" name="t12" value="' . (in_array('12', ($course['TimeSlots'])) ? '1' : '0') . '">
            <input type="hidden" id="tt22" name="t22" value="' . (in_array('22', ($course['TimeSlots'])) ? '1' : '0') . '">
            <input type="hidden" id="tt32" name="t32" value="' . (in_array('32', ($course['TimeSlots'])) ? '1' : '0') . '">
            ';
            echo $output;
            ?>
            <input type="hidden" name="ChangeCourse" value="<?php echo$_GET['CID'] ?>">
            <input type="submit" value="Изменить">
        </form>
    </div>
</div>
</body>
</html>