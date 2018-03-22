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
        $school = substr(trim(filter_var($_POST["school"], FILTER_SANITIZE_STRING)), 0, 30);
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
                foreach ($result as $row) {
                    return $row;
                }
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

        $sql = "UPDATE registrations SET AppStatus=\"2\", DateOfWorkSent=\"$date\" WHERE UniqueId=\"$UID\"";
        try {
            $this->conn->query($sql);
        }
        catch (PDOException $exception) {
            error("setWorkDaySent error: $exception");
        }
    }

    // ���������� ������ ���������������������
    public function getAppStatus($UID) {
        $sql = "SELECT AppStatus FROM registrations WHERE UniqueId=\"$UID\"";
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
        } else {
            return -1;
        }
    }

    // ���������� ��������� ������� ���� 'SELECT $list_of_fields FROM "registations" ORDER BY $sort_by'
    public function getStudentsList($list_of_fields, $sort_by, $minstatus = 0) {
        $sql = "SELECT $list_of_fields FROM registrations WHERE AppStatus >= $minstatus ORDER BY $sort_by";
        return $this->conn->query($sql);
    }
/*
 *
 *  � � � � � �
 *
 */
    // ������ � ���� ����������� �� ������ ����� ����������� � ��������� ������� � ����� �� ����� �����������
    public function setCertName($UID, $cert_lang, $cert_name) {
        $sql = "UPDATE registrations SET CertLang='$cert_lang', CertName='$cert_name' WHERE UniqueId='$UID'";
        $this->conn->query($sql);
    }

    // ����� ������ �� ����� ����������� ��� ������������ ��������
    public function getCertName($UID) {
        $sql = "SELECT CertLang, CertName FROM registrations WHERE UniqueId='$UID'";
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

    // ���������� ������
    public function dbLog($text, $UserID = "")
    {
        $text = filter_var($text);
        $sql = 'INSERT INTO log (UserID, text) VALUES ("' . $UserID . '", "' . $text . '")';
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

    /*
     *  ������ � �������� �������
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
        $sql = "SELECT UID, password FROM admin WHERE UID=\"$UID\"";
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
        $sql = "SELECT password FROM admin WHERE UID = \"$UID\"";
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
        $sql = "UPDATE admin SET password = \"$password\" WHERE UID = \"$UID\"";
        $this->conn->exec($sql);
        return 1;
    }

    // �������� ������ ������
    // @param char[32] $UID md5 hash
    // @return array $row
    public function admGet($UID) {
        $sql = "SELECT * FROM admin WHERE UID=\"$UID\"";
        $result = $this->conn->query($sql);
        return $result->fetch();
    }

    function __destruct()
    {
        $this->conn = null;
    }
}