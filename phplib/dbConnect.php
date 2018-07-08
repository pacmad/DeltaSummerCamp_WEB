<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Ablov
 * Date: 05.10.2017
 * Time: 14:12
 */

require_once 'common.php';

define("DB_NOT_FOUND", "-1"); // ������ � ����� UID �� ����������
define("DB_ADD_OK", "0"); // ���������� ������ � ��� ������� �������
define("DB_ADD_DUP", "1"); // ������ �� ��������� - ���� UniqueId ��� ���� � ����

class dbConnect
{
    public $status = DB_ADD_OK; // ��������� ������ � ����� ������
    private $conn; // �������� ����������

    /**
     * dbConnect constructor. Open 'delta' database.
     */
    function __construct()
    {
        $servername = "localhost";
        $username = "PHP";
        $password = "DeltaDB";
        $this->conn = new PDO("mysql:host=$servername;dbname=delta;charset=CP1251", $username, $password);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // set the PDO error mode to exception
        $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

/*
 *
 *  � � � � � � � � � � �
 *
 */


    /**
     * putRegData() - put registration data from _POST array into the 'registrations' table
     * and return Unique ID
     */
    public function putRegData()
    {
        // ����� ���������� �����
        if (!isset($_POST["ALL_DONE"]) || strcmp($_POST["ALL_DONE"], "Ok")) {
            error("Form not filled correctly.");
        }
        // ������� ���� ��������� �����
        if (!isset($_POST["email"], $_POST["surname"], $_POST["name"], $_POST["middlename"], $_POST["gender"],
            $_POST["birthday"], $_POST["class"], $_POST["school"], $_POST["city"], $_POST["country"], $_POST["langs"],
            $_POST["tel"], $_POST["notes"])) {
            error("No data found: " . print_r($_POST));
        }
        // ���������� ����, ��������: ���� ����, ������ ����������� ��� ����
        $unique = md5($_POST["surname"] . $_POST["name"] . $_POST["middlename"] . $_POST["birthday"] . $_POST["email"]);
        $sql = "SELECT UniqueId, Surname, Name FROM registrations WHERE UniqueId='" . $unique . "'";
        if (!($result = $this->conn->query($sql))) {
            error("<br>Something wrong");
        } elseif ($result->rowCount() != 0) {
            $this->status = DB_ADD_DUP;
            return $unique;
        }
        // ����������� ����
        $email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL);
        if (!$email) {
            error("<br>Invalid e-mail");
            $email = "noemail@found.com";
        } else {
            $email = substr(trim($email), 0, 50);
        }
        $phone = substr(trim(filter_var($_POST["tel"], FILTER_SANITIZE_STRING)), 0, 20);
        $surname = substr(trim(filter_var($_POST["surname"], FILTER_SANITIZE_STRING)), 0, 30);
        $name = substr(trim(filter_var($_POST["name"], FILTER_SANITIZE_STRING)), 0, 30);
        $middlename = substr(trim(filter_var($_POST["middlename"], FILTER_SANITIZE_STRING)), 0, 30);
        if ($_POST["gender"] === "f") {
            $gender = 'f';
        } else {
            $gender = 'm';
        }
        if ($birthday = date_create_from_format("d/m/Y", $_POST["birthday"])) {
            $birthday = $birthday->format("Y-m-d");
        } else {
            error("Invalid Date of Birthday field");
            $birthday = "2000-01-01";
        }
        if ($_POST["class"] === '') {
            $_POST["class"] = 0;
        }
        $class = substr(trim(filter_var($_POST["class"], FILTER_SANITIZE_STRING)), 0, 2);
        $school = substr(trim(filter_var($_POST["school"], FILTER_SANITIZE_STRING)), 0, 70);
        $city = substr(trim(filter_var($_POST["city"], FILTER_SANITIZE_STRING)), 0, 40);
        $country = substr(trim(filter_var($_POST["country"], FILTER_SANITIZE_STRING)), 0, 20);
        $langs = substr(trim(filter_var($_POST["langs"], FILTER_SANITIZE_STRING)), 0, 60);
        $notes = substr(trim(filter_var($_POST["notes"], FILTER_SANITIZE_STRING)), 0, 1024);
        // ���������� � ����
        $sql = "INSERT INTO registrations (UniqueId, UserIP, Email, Surname, Name, MiddleName, Gender, Birthday,
              Class, School, City, Country, Languages, Tel, Notes)
              VALUES ('$unique', '$_SERVER[REMOTE_ADDR]', '$email', '$surname', '$name',
              '$middlename', '$gender', '$birthday', '$class', '$school', '$city',
              '$country', '$langs', '$phone', '$notes')";
        try {
            $this->conn->exec($sql);
            $this->dbLog("����� �����������: $name $surname $email", $unique);
            $this->status = DB_ADD_OK;
        }
        catch (PDOException $exception) {
            error("Error in database update: " . $exception);
        }
        return $unique;
    }

    /*
     * ���������� ������ �� ����� ������ ��� ����������� �������� �� ���� �����������
     */
    public function getPerson($uniqueId)
    {
        $sql = "SELECT * FROM registrations WHERE UniqueId='" . $uniqueId . "'";
        try {
            $result = $this->conn->query($sql);
            if ($result->rowCount() == 1) {
                return $result->fetch();
            } else {
                $this->status = DB_NOT_FOUND;
            }
            return false;
        }
        catch (PDOException $exception){
            error("Something wrong when getting records in function getPerson, UID=" . $uniqueId . "; error: " . $exception);
            return false;
        }
    }

    // ������������� ���� status ��� ����������� �����������
    // -1 - ����� �� �����������;
    // 0 - ������ ��� ������;
    // 1 - ������ � ������������ �������;
    // 2 - ������� ���������;
    // 3 - �������� ������� ���������;
    public function setAppStatus($uniqueId, $flag) {
        $sql = "UPDATE registrations SET AppStatus=" . $flag . " WHERE UniqueId='" . $uniqueId ."'";
        try {
            return $this->conn->query($sql);
        }
        catch (PDOException $exception) {
            error("setAppStatus error: ". $exception);
        }
    }

    // ������������� ���� ������� ��������� � ���������� AppStatus=2
    public function setWorkDaySent($UID, $date = ""){
        if ($date == "") {
            $date = date("Y-m-d");
        }

        $sql = "UPDATE registrations SET AppStatus='2', DateOfWorkSent='$date' WHERE UniqueId='$UID'";
        try {
            $this->conn->query($sql);
        }
        catch (PDOException $exception) {
            error("setWorkDaySent error: $exception");
        }
    }

    // ���������� ������ ���������������������
    public function getAppStatus($UID) {
        $sql = "SELECT AppStatus FROM registrations WHERE UniqueId='$UID'";
        try {
            $result = $this->conn->query($sql);
        }
        catch (PDOException $exception) {
            error("getAppStatus error: $exception");
        }

        if ($result->rowCount() == 1) {
            foreach ($result as $row) {
                return $row["AppStatus"];
            }
        }

        return -1;
    }

    // ���������� ��������� ������� ���� 'SELECT $list_of_fields FROM "registations" ORDER BY $sort_by' � ������ ���� �������
    // ��������� �������� - ����� ������ �� �������.
    public function getStudentsList($list_of_fields, $sort_by, $where = 'AppStatus >= 0') {
        $sql = "SELECT $list_of_fields FROM registrations WHERE $where ORDER BY $sort_by";
        return $this->conn->query($sql);
    }

    // ���������� UniqueID ��������� ������ ����� $UID � ������� registrations � �������� $where, ��������������� �� ���� $sortBy
    public function getNextUID($UID, $sort_by, $where = 'AppStatus >= 0'){
        // ���������� ����� ����� �������
        $maxRecord = 0;
        $sql = "SELECT COUNT(*) AS rows FROM registrations WHERE $where";
        try {
            $result = $this->conn->query($sql);
        }
        catch (PDOException $exception) {
            error("getNextUID() error in COUNT: $exception");
        }
        $row = $result->fetch();
        $maxRecord = $row['rows'];

        // ���������� ���������� ����� ������ � ������ $UID
        $sql = "SELECT row FROM (SELECT @rownum := @rownum + 1 row, t.UniqueId FROM registrations t, (SELECT @rownum := 0) r WHERE $where ORDER BY $sort_by) AS rows WHERE UniqueId = '$UID'";
        try {
            $result = $this->conn->query($sql);
        }
        catch (PDOException $exception) {
            error("getNextUID() error in SELECT: $exception");
        }
        $row = $result->fetch();
        $record = $row['row'];
        if ($record >= $maxRecord)
            return $UID;
        else $record++;


        // ���������� $UID ��� ��������� ������
        $sql = "SELECT UniqueId FROM (SELECT @rownum := @rownum + 1 row, t.UniqueId FROM registrations t, (SELECT @rownum := 0) r WHERE $where ORDER BY $sort_by) AS rows WHERE row = $record";
        try {
            $result = $this->conn->query($sql);
        }
        catch (PDOException $exception) {
            error("getNextUID() error in SELECT: $exception");
        }
        $row = $result->fetch();

        return $row['UniqueId'];
    }

    // ���������� UniqueID ���������� ������ ����� $UID � ������� registrations � �������� $where, ��������������� �� ���� $sortBy
    public function getPrevUID($UID, $sort_by, $where = 'AppStatus >= 10'){
        // ���������� ���������� ����� ������ � ������ $UID
        $sql = "SELECT row FROM (SELECT @rownum := @rownum + 1 row, t.UniqueId FROM registrations t, (SELECT @rownum := 0) r WHERE $where ORDER BY $sort_by) AS rows WHERE UniqueId = '$UID'";
        try {
            $result = $this->conn->query($sql);
        }
        catch (PDOException $exception) {
            error("getNextUID() error in SELECT: $exception");
        }
        $row = $result->fetch();
        $record = $row['row'];
        if ($record == 1)
            return $UID;
        else $record--;


        // ���������� $UID ��� ��������� ������
        $sql = "SELECT UniqueId FROM (SELECT @rownum := @rownum + 1 row, t.UniqueId FROM registrations t, (SELECT @rownum := 0) r WHERE $where ORDER BY $sort_by) AS rows WHERE row = $record";
        try {
            $result = $this->conn->query($sql);
        }
        catch (PDOException $exception) {
            error("getNextUID() error in SELECT: $exception");
        }
        $row = $result->fetch();

        return $row['UniqueId'];
    }
/*
 *
 *  � � � � � �
 *
 */
    // ������ � ���� ����������� �������� ������
    public function setOwnTel($UID, $ownTel){
        $ownTel = substr(trim(filter_var(iconv("UTF-8", "WINDOWS-1251", $ownTel), FILTER_SANITIZE_STRING)), 0, 20);
        $sql = "UPDATE registrations SET OwnTel='$ownTel' WHERE UniqueId='$UID'";
        $this->conn->exec($sql);
    }

    public function getOwnTel($UID) {
        $sql = "SELECT OwnTel FROM registrations WHERE UniqueId='$UID'";
        return $this->conn->query($sql);
    }

    // ������ � ���� ����������� ������� ��������
    public function setComingDetails($UID, $coming_with, $coming_date, $coming_time, $coming_flight, $coming_place){
        $coming_date = substr(trim(filter_var(iconv("UTF-8", "WINDOWS-1251", $coming_date), FILTER_SANITIZE_STRING)), 0, 20);
        $coming_time = substr(trim(filter_var(iconv("UTF-8", "WINDOWS-1251", $coming_time), FILTER_SANITIZE_STRING)), 0, 20);
        $coming_flight = substr(trim(filter_var(iconv("UTF-8", "WINDOWS-1251", $coming_flight), FILTER_SANITIZE_STRING)), 0, 20);
        $sql = "UPDATE registrations SET ComingWith='$coming_with', ComingDate='$coming_date', ComingTime='$coming_time',
          ComingFlight='$coming_flight', ComingPlace='$coming_place' WHERE UniqueId='$UID'";
        $this->conn->exec($sql);
    }

    // ����� ������ �� �������� � ������
    public function getComingDetails($UID) {
        $sql = "SELECT ComingWith, ComingDate, ComingTime, ComingFlight, ComingPlace FROM registrations WHERE UniqueId='$UID'";
        return $this->conn->query($sql);
    }
    
    // ������ � ���� ����������� ������� �������
    public function setLeavingDetails($UID, $leaving_with, $leaving_date, $leaving_time, $leaving_flight, $leaving_place){
        $leaving_date = substr(trim(filter_var(iconv("UTF-8", "WINDOWS-1251", $leaving_date), FILTER_SANITIZE_STRING)), 0, 20);
        $leaving_time = substr(trim(filter_var(iconv("UTF-8", "WINDOWS-1251", $leaving_time), FILTER_SANITIZE_STRING)), 0, 20);
        $leaving_flight = substr(trim(filter_var(iconv("UTF-8", "WINDOWS-1251", $leaving_flight), FILTER_SANITIZE_STRING)), 0, 20);
        $sql = "UPDATE registrations SET LeavingWith='$leaving_with', LeavingDate='$leaving_date', LeavingTime='$leaving_time',
          LeavingFlight='$leaving_flight', LeavingPlace='$leaving_place' WHERE UniqueId='$UID'";
        $this->conn->exec($sql);
    }

    // ����� ������ �� �������� � ������
    public function getLeavingDetails($UID) {
        $sql = "SELECT LeavingWith, LeavingDate, LeavingTime, LeavingFlight, LeavingPlace FROM registrations WHERE UniqueId='$UID'";
        return $this->conn->query($sql);
    }

    // ��������
    public function setHealthDetails($UID, $health) {
        $health = filter_var(iconv("UTF-8", "WINDOWS-1251", $health), FILTER_SANITIZE_STRING);
        $sql = "UPDATE registrations SET Health='$health' WHERE UniqueId='$UID'";
        $this->conn->exec($sql);
    }
    public function getHealthDetails($UID) {
        $sql = "SELECT Health FROM registrations WHERE UniqueId='$UID'";
        return $this->conn->query($sql);
    }

    // ���������
    public function setInshuranceDetails($UID, $insurance) {
        $s = filter_var(iconv("UTF-8", "WINDOWS-1251", $insurance), FILTER_SANITIZE_STRING);
        if($s) $insurance = $s;
        $sql = "UPDATE registrations SET Insurance='$insurance' WHERE UniqueId='$UID'";
        $this->conn->exec($sql);
    }
    public function getInsuranceDetails($UID) {
        $sql = "SELECT Insurance FROM registrations WHERE UniqueId='$UID'";
        return $this->conn->query($sql);
    }

    // ����-�����
    public function setNotesDetails($UID, $notes) {
        $notes = filter_var(iconv("UTF-8", "WINDOWS-1251", $notes), FILTER_SANITIZE_STRING);
        $sql = "UPDATE registrations SET NotesText='$notes' WHERE UniqueId='$UID'";
        $this->conn->exec($sql);
    }
    public function getNotesDetails($UID) {
        $sql = "SELECT NotesText FROM registrations WHERE UniqueId='$UID'";
        return $this->conn->query($sql);
    }

    // �������������� ������
    public function setOtherDetails($UID, $certLang, $certName, $visa, $notebook, $shirt) {
        $certName = substr(trim(filter_var($certName, FILTER_SANITIZE_STRING)), 0, 255);
        $sql = "UPDATE registrations SET";
        $comma = 0;
        if ($certLang !== "") {
            $sql .= " CertLang='$certLang', CertName='$certName'";
            $comma = 1;
        }
        if ($visa !== "") {
            if ($comma == 1) $sql .= ",";
            $sql .= " Visa='$visa'";
            $comma = 1;
        }
        if ($notebook !== "") {
            if ($comma == 1) $sql .= ",";
            $sql .= " Notebook='$notebook'";
            $comma = 1;
        }
        if ($shirt !== "") {
            if ($comma == 1) $sql .= ",";
            $sql .= " Shirt='$shirt'";
        }
        $sql .= " WHERE UniqueId='$UID'";
        $this->conn->exec($sql);
    }
    public function getOtherDetails($UID){
        $sql = "SELECT CertLang, CertName, Visa, Notebook, Shirt FROM registrations WHERE UniqueId='$UID'";
        return $this->conn->query($sql);
    }

    /*
     *
     *  � � � � � � �
     *
     */

    // ���������� �� ������� news $newsPerPage �������� ��� �������� $page
    public function getNews($page, $newsPerPage) {
        $start = ($page - 1) * $newsPerPage;

        try {
            $sth = $this->conn->prepare("SELECT Top, Datetime, Text, Picture FROM news ORDER BY Top DESC, Datetime DESC 
            LIMIT " . $start . "," . $newsPerPage);
            $sth->execute();
        }
        catch (PDOException $exception) {
            error("getNews error: " . $exception);
        }
        if($sth->rowCount() == 0) {
            return false;
        }
        $row = array();
        for($i=0; $i<$sth->rowCount(); $i++){
            $row[] = $sth->fetch(PDO::FETCH_ASSOC);
        }
        return $row;
    }

/*
 *
 *  ������ � �������� �������
 *
 */
    // �������� �����
    // @param char[32] $UID Unique ID
    // @param string $PASS - Password
    // @return int
    // 0: wrong pass;
    // 1: success;
    // -1: wrong UID
    public function admCheck($UID, $PASS = null)
    {
        $sql = "SELECT UID, password FROM admin WHERE UID='$UID'";
        $result = $this->conn->query($sql);

        if($result->rowCount() == 0) { // Wrong UID
            return -1;
        }

        $row[] = $result->fetch();

        $db_pass = $row[0]['password'];

        if ($db_pass == null && $PASS == null) { // Also when password not set
            return 1;
        }

        if (!strcmp($db_pass, md5($PASS))) {
            return 1;
        }

        // �������� � PHP 5.5+
        // if (password_verify($PASS, $db_pass)) {
        //     return 1;
        // }

        return 0;
    }

    // ��������� ������ (������ � ��� ������, ���� �� ��� �� ����������
    // @param char[32] $UID md5 hash
    // @param string $pass - ������
    // @return int
    // -1: wrong UID;
    // 0: password already set;
    // 1: success
    public function admSetPass($UID, $pass){
        $sql = "SELECT password FROM admin WHERE UID = '$UID'";
        $result = $this->conn->query($sql);

        if($result->rowCount() == 0) { // Wrong UID
            return -1;
        }

        $row[] = $result->fetch();

        if ($row[0]['password'] != null) {
            return 0;
        }

        // �������� � PHP 5.5+
        // $password = password_hash($pass, PASSWORD_DEFAULT);
        $password = md5($pass);
        $sql = "UPDATE admin SET password = '$password' WHERE UID = '$UID'";
        $this->conn->exec($sql);
        return 1;
    }

    // �������� ������ ������
    // @param char[32] $UID md5 hash
    // @return array $row
    public function admGet($UID) {
        $sql = "SELECT * FROM admin WHERE UID='$UID'";
        $result = $this->conn->query($sql);
        return $result->fetch();
    }

/*
*
*   �������������
*
*/

    // regTeacher() - put registration data from _POST array into the 'teachers' table
    // and return Unique ID
    public function regTeacher()
    {
        // ������� ���� ��������� �����
        if (!isset($_POST["surname"], $_POST["name"], $_POST["email"], $_POST["phone"])) {
            error("No data found: " . print_r($_POST));
        }

        // ���������� ����, ��������: ���� ����, ������ ����������� ��� ����
        $UID = md5($_POST["surname"] . $_POST["name"] . $_POST["email"] . $_POST["phone"]);
        $sql = "SELECT UID, Surname, Name FROM teachers WHERE UID='$UID'";
        if (!($result = $this->conn->query($sql))) {
            error("<br>Something wrong");
        } elseif ($result->rowCount() != 0) {
            $this->status = DB_ADD_DUP;
            return $UID;
        }
        // ����������� ����
        $surname = substr(trim(filter_var($_POST["surname"], FILTER_SANITIZE_STRING)), 0, 30);
        $name = substr(trim(filter_var($_POST["name"], FILTER_SANITIZE_STRING)), 0, 30);
        $email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL);
        if (!$email) {
            error("<br>Invalid e-mail");
            $email = "noemail@found.com";
        } else {
            $email = substr(trim($email), 0, 50);
        }
        $phone = substr(trim(filter_var($_POST["phone"], FILTER_SANITIZE_STRING)), 0, 20);
        // ���������� � ����
        $sql = "INSERT INTO teachers (UID, Surname, Name, Phone, Email)
              VALUES ('$UID', '$surname', '$name', '$phone', '$email')";
        try {
            $this->conn->exec($sql);
            $this->dbLog("����� �������������: $name $surname $email", $UID);
            $this->status = DB_ADD_OK;
        }
        catch (PDOException $exception) {
            error("Error in database 'teachers' update: " . $exception);
        }
        return $UID;
    }

    // ���������� ������ ���� ��������������
    public function getTeachers() {
        $sql = "SELECT * FROM teachers ORDER BY Surname;";
        return $this->conn->query($sql);
    }

    // ���������� ������ ����������� �������������
    public function getTeacher($UID) {
        $sql = "SELECT * FROM teachers WHERE UID='$UID';";
        return $this->conn->query($sql);
    }

/*
 *  ������� � �����
 */
    // ������� ������
    public function getCoursesTable()
    {
        $sql = "SELECT * FROM teachers_and_courses";
        return $this->conn->query($sql);
    }
    // ����������� �����
    public function addCourse($courseRus, $courseGer, $courseEng) {
        $courseRus = substr(trim(filter_var($courseRus, FILTER_SANITIZE_STRING)), 0, 255);
        $courseGer = substr(trim(filter_var($courseGer, FILTER_SANITIZE_STRING)), 0, 255);
        $courseEng = substr(trim(filter_var($courseEng, FILTER_SANITIZE_STRING)), 0, 255);

        if (($courseRus . $courseGer . $courseEng) == "") {
            exit();
        }

        $fields = "";
        $values = "";
        if ($courseRus != "") {
            $values .= "'$courseRus'";
            $fields .= "NameRus";
            if ($courseGer != "" || $courseEng != "") {
                $values .= ", ";
                $fields .= ", ";
            }
        }

        if ($courseGer != "") {
            $values .= "'$courseGer'";
            $fields .= "NameGer";
            if ($courseEng != "") {
                $values .= ", ";
                $fields .= ", ";
            }
        }

        if ($courseEng != "") {
            $values .= "'$courseEng'";
            $fields .= "NameEng";
        }

        $sql = "INSERT INTO courses ($fields) VALUES ($values)";
        try {
            $this->conn->exec($sql);
            $CID = $this->conn->lastInsertId();
            $this->dbLog("�������� ����: $courseRus");
        } catch (PDOException $exception) {
            error("Error in courses update: $courseRus");
        }
        return $CID; // ���������� ������������������� ����
    }

    // ���������� ������������� (TID) � ����� (CID)
    public function addTeacherToCourse($CID, $TID) {
        $sql = "INSERT INTO teach2course (Teacher_ID, Course_ID) VALUES ('$TID', '$CID')";
        try {
            $this->conn->exec($sql);
            $this->dbLog("�������� ������������� $TID � ����� $CID");
        } catch (PDOException $exception) {
            error("Error in addTeacherToCourse: $exception");
        }
    }

    // ���������� ����� � ����������
    public function addCourseToTimetable($CID, $time) {
        $sql = "INSERT INTO timetable (Course_ID, Time) VALUES ('$CID', '$time')";
        try {
            $this->conn->exec($sql);
            $this->dbLog("�������� ���� $CID, �����: $time");
        } catch (PDOException $exception) {
            error("Error in addCourseToTimetable: $exception");
        }
    }

    // ��������� ���������� ��� �����
    public function updateCourseToTimetable ($CID, $T0, $T11, $T21, $T31, $T12, $T22, $T32) {
        try {
            $this->conn->exec("DELETE FROM timetable WHERE Course_ID = '$CID'");
            if ($T0) $this->conn->exec("INSERT INTO timetable (Course_ID, Time) VALUES ('$CID', '0')");
            if ($T11) $this->conn->exec("INSERT INTO timetable (Course_ID, Time) VALUES ('$CID', '11')");
            if ($T21) $this->conn->exec("INSERT INTO timetable (Course_ID, Time) VALUES ('$CID', '21')");
            if ($T31) $this->conn->exec("INSERT INTO timetable (Course_ID, Time) VALUES ('$CID', '31')");
            if ($T12) $this->conn->exec("INSERT INTO timetable (Course_ID, Time) VALUES ('$CID', '12')");
            if ($T22) $this->conn->exec("INSERT INTO timetable (Course_ID, Time) VALUES ('$CID', '22')");
            if ($T32) $this->conn->exec("INSERT INTO timetable (Course_ID, Time) VALUES ('$CID', '32')");
        } catch (PDOException $exception) {
            error("Error in update CourseToTimetable: $exception");
        }
    }

    // ������ �����; ���������� ������ ���� [���_���, ���_���, ���_���, [������_�������������� "Teachers[]"], [������_��� "TimeSlots[]']]
    public function getCourseDetails($CID)
    {
        $course = array();

        $sql = "SELECT * FROM courses WHERE ID='$CID'";
        try {
            $result = $this->conn->query($sql);
        } catch (PDOException $exception) {
            error("Error in getCourseDetails 1: $exception");
        }
        if($result->rowCount() == 0) return $course; // ���� �������� CID - ���������� ������ ������
        $row = $result->fetch();
        $course["NameRus"] = $row["NameRus"];
        $course["NameGer"] = $row["NameGer"];
        $course["NameEng"] = $row["NameEng"];

        $course["Teachers"] = array();
        $sql = "SELECT * FROM teach2course WHERE Course_ID='$CID'";
        try {
            $result = $this->conn->query($sql);
            foreach ($result as $row) $course["Teachers"][] = $row['Teacher_ID'];
        } catch (PDOException $exception) {
            error("Error in getCourseDetails 2: $exception");
        }

        $course["TimeSlots"] = array();
        $sql = "SELECT * FROM timetable WHERE Course_ID='$CID'";
        try {
            $result = $this->conn->query($sql);
            foreach ($result as $row) $course["TimeSlots"][] = $row["Time"];
        } catch (PDOException $exception) {
            error("Error in getCourseDetails 4: $exception");
        }

        return $course;
    }

    // ��������� �������� ����� (��������, ���� ��� �������� �������)
    public function updateCourse($id, $courseRus, $courseGer, $courseEng) {
        $courseRus = substr(trim(filter_var($courseRus, FILTER_SANITIZE_STRING)), 0, 255);
        $courseGer = substr(trim(filter_var($courseGer, FILTER_SANITIZE_STRING)), 0, 255);
        $courseEng = substr(trim(filter_var($courseEng, FILTER_SANITIZE_STRING)), 0, 255);

        if($courseRus !== "" || $courseGer !== "" || $courseEng !== "") {
            $sql = "UPDATE courses SET NameRus = '$courseRus', NameGer = '$courseGer', NameEng = '$courseEng' WHERE ID = '$id'";
        } else {
            $sql = "DELETE FROM courses WHERE ID = '$id'";
        }
        try {
            $this->conn->exec($sql);
            $this->dbLog("������ ����: $courseRus");
        } catch (PDOException $exception) {
            error("Error in courses update: $courseRus");
        }
    }

    // ���������� true, ���� ������� (UID) ������� �� ���� (CID) �� ����� (Time)
    // ����� ���������� false
    public function isStudentInCourse($UID, $CID, $Time)
    {
        $sql = "SELECT COUNT(*) FROM student_in_course WHERE UID = '$UID' AND CID = '$CID' AND Time = '$Time'";
        try {
            $result = $this->conn->query($sql)->fetch();
            if($result['COUNT(*)'] == 1) return true;
        } catch (PDOException $exception) {
            error("Error in isStudentInCourse: $exception");
        }
        return false;
    }

    // �������� ���������� (timeSlot) ��� ������� ����� (CID)
    public function cleanCourse($CID, $timeSlot)
    {
        try {
            $result = $this->conn->query("SELECT ID FROM timetable WHERE Course_ID = '$CID' AND Time = '$timeSlot'");
            $TID = $result->fetch();
            $TID = $TID['ID'];
            $this->conn->exec("DELETE FROM student2time WHERE Time_ID = $TID");
        } catch (PDOException $exception) {
            error("Error in cleanCourse: $exception");
        }
    }

    // �������� �������� (UID) �� ���� (CID) � ����������(timeSlot)
    public function addStudentToCourse($UID, $CID, $timeSlot)
    {
        try {
            $result = $this->conn->query("SELECT ID FROM timetable WHERE Course_ID = '$CID' AND Time = '$timeSlot'");
            $TID = $result->fetch();
            $TID = $TID['ID'];
            $this->conn->exec("INSERT INTO student2time (Student_ID, Time_ID) VALUES ('$UID', '$TID')");
        } catch (PDOException $exception) {
            error("Error in addStudentToCourse: $exception");
        }
    }

/*
 *  Tools
 */

    // ���������� ������
    public function dbLog($text, $UserID = "")
    {
        $text = filter_var($text);
        $sql = "INSERT INTO log (UserID, text) VALUES ('$UserID' , '$text')";
        try {
            $this->conn->exec($sql);
        }
        catch (PDOException $exception) {
            error("dbLog error: " . $exception);
        }
    }

    // ��������� ������ � ����� ������
    // DB_NOT_FOUND, DB_ADD_OK, DB_ADD_DUP � �.�.
    public function getStatus()
    {
        return $this->status;
    }

    function __destruct()
    {
        $this->conn = null;
    }
}