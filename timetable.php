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
    <title>����������</title>
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

    if(isset($_POST) && is_array($_POST) && count($_POST) > 0) {
        $db->cleanCourse($CID, $timeSlot);
        foreach ($_POST as $UID) {
            $db->addStudentToCourse($UID, $CID, $timeSlot);
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
                <div class="tooltip">
                    <a href="courses.php">
                        <div class="iconbox"><span class="fa fa-graduation-cap icon"></span></div>
                        <span class="tooltiptext">����� � �������</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="main">
    <h3>���������� �����:</h3>
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
            $students = $db->getStudentsList("UniqueId, Surname, Name", "Surname", "AppStatus >= 15");
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

                echo "<div class='student'><b>$surname $name</b><br><img src=$photo alt='Photo'></div>";
            }
        }
        ?>
    </div>

<?php
/*
 * ����� ������ ����� ��� ��������� ������� �� ����� (���������� �� ������� ������)
 *
 */
?>
    <div class="row">
        <button id="change-btn" onclick="showChange()">��������</button>
    </div>
    <div class="row course-block add-form hidden" id="change-block">
    <form method="post" id="students">
        <h3>������ �����:</h3>
        <ul>
            <?php
            try {
                $students = $db->getStudentsList("UniqueId, Surname, Name", "Surname", "AppStatus >= 15");
            } catch (PDOException $exception) {
                error("Error in getStudentsList: $exception");
            }
            foreach ($students as $student) {
                $UID = $student['UniqueId'];
                $name = $student['Name'];
                $surname = $student['Surname'];
                $checked = ($db->isStudentInCourse($UID, $CID, $timeSlot)) ? "checked" : "";

                echo "<li><label for='UID-$UID'><input type='checkbox' name='UID-$UID' id='UID-$UID' value='$UID' $checked>$name $surname</li>";
            }
            ?>
        </ul>
        <input type="submit" value="���������">
    </form>
    </div>
</div><!-- main -->
</body>
</html>