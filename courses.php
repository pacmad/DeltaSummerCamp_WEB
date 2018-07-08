<?php
/**
 * �������� ��� ������ � ������� � ���������.
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
    <title>����� � �������</title>
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

    // ��������� ���������� �����
    if (isset($_POST['AddCourse'])) {
        $CID = $db->addCourse($_POST['NameRus'], $_POST['NameGer'], $_POST['NameEng']);
        $db->addCourseToTimetable($CID, 99); // ��������, ������ ������ ����, ��� ���� ���������������
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

    // ��������� ��������� �����
    elseif (isset($_POST['ChangeCourse'])) {
        $CID = $_POST['ChangeCourse'];
        $db->updateCourse($CID, $_POST['NameRus'], $_POST['NameGer'], $_POST['NameEng']);
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

    try {
        $result = $db->getCoursesTable();
    } catch (PDOException $exception) {
        error("getCoursesTable error: $exception");
    }

    // ��������� ������ ������ ����: [���_�����, [������_�������������� "Teachers[]"], [������_��� "TimeSlots[]']]
    $courses = array();
    if ($result->rowCount() > 0) {
        foreach ($result as $row) {
            $courseID = intval($row['Course_ID']);
            if (!isset($courses[$courseID])) {
                $courses[$courseID] = ["NameRus" => $row['NameRus'], "Teachers" => array($row['Surname'] . ' ' . $row['Name']), "TimeSlots" => array(intval($row['Time']))];
            } else {
                array_push($courses[$courseID]["Teachers"], $row['Surname'] . ' ' . $row['Name']);
                array_push($courses[$courseID]["TimeSlots"], intval($row['Time']));
            }
            $courses[$courseID]["Teachers"] = array_unique($courses[$courseID]["Teachers"]);
            $courses[$courseID]["TimeSlots"] = array_unique($courses[$courseID]["TimeSlots"]);
        }
    }
    ?>
</head>

<body>
<div class="title">
    <div class="row">
        <div class="col-6">
            <h1>������, <?php echo "$name $surname!"?></h1>
        </div>
        <div class="col-6">
            <div class="icons">
                <div class="tooltip">
                    <a href="admin.php">
                        <div class="iconbox"><span class="fa fa-child icon"></span></div>
                        <span class="tooltiptext">����</span>
                    </a>
                </div>
                <div class="tooltip">
                    <a href="teachers.php">
                        <div class="iconbox"><span class="fa fa-user icon"></span></div>
                        <span class="tooltiptext">�������������</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="main">
    <h3>������� � �����:</h3>
    <div class="courses-table">
<?php
    foreach ($courses as $CID=>$course) {
        // �������� ����� � �������������
        $result = "
            <div class='row course-block'>
                <div class='col-4 course-title'>
                    <b>" . $course['NameRus'] . "</b>
                </div>
                <div class='col-4 course-authors'>
                ";
            foreach($course["Teachers"] as $teacher) {
                $result .= "<p>" . $teacher . "</p>";
            };
            $result .= "
                        </div>
                <div class='col-2'>
            ";

            // ������� - �����������. ���������� ������� ��� ����� ����� ����� �� �������� ���������� �����
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
            // "���������" - ��������� �����
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
    <button onclick="showChange()" id="change_button">��������...</button>
    <div class="row course-block add-form hidden" id="add_course">
        <h2>�������� ���� / ������</h2>
        <form method="post" id="add_course_form">
            <p class="course-title">�������� ����� (���): <input type="text" name="NameRus" id="course-rus"></p>
            <p class="course-title">Kursname (ger): <input type="text" name="NameGer" id="course-ger"></p>
            <p class="course-title">Course name (eng): <input type="text" name="NameEng" id="course-eng"></p>
            <div class="row">
                <div class="col-8">
                    <p class="course-title">�������������:</p>
                    <ul>
                        <?php
                            foreach ($db->getTeachers() as $teacher) {
                                echo '<li><label for="TID-' . $teacher['UID'] . '"><input type="checkbox" name="TID-' . $teacher['UID'] . '" id="TID-' . $teacher['UID'] . '" value="' . $teacher['UID'] . '">' . $teacher['Name'] . ' ' . $teacher['Surname'] . '</li>';
                            }
                        ?>
                    </ul>
                </div>
                <div class="col-1"></div>
                <div class="col-2">
                    <p class="course-title">����������:</p>
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
            <input type="submit" value="��������">
        </form>
    </div>
</div>
</body>
</html>