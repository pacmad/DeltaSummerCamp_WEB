<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Ablov
 * Date: 05.10.2017
 * Time: 14:12
 */

require_once 'common.php';

define("DB_NOT_FOUND", "-1"); // Запись с таким UID не существует
define("DB_ADD_OK", "0"); // Добавление данных в баз упрошло успешно
define("DB_ADD_DUP", "1"); // Запись не добавлена - ключ UniqueId уже есть в базе

class dbConnect
{
    public $status = DB_ADD_OK; // Результат работы с базой данных
    private $conn; // Активное соединение

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
    }

    /**
     * putRegData() - put registration data from _POST array into the 'registrations' table
     * and return Unique ID
     */
    public function putRegData()
    {
        // Общее заполнение формы
        if (!isset($_POST["ALL_DONE"]) || strcmp($_POST["ALL_DONE"], "Ok")) {
            error("Form not filled correctly.");
        }
        // Наличие всех ожидаемых полей
        if (!isset($_POST["email"], $_POST["surname"], $_POST["name"], $_POST["middlename"], $_POST["gender"],
            $_POST["birthday"], $_POST["class"], $_POST["school"], $_POST["city"], $_POST["country"], $_POST["langs"],
            $_POST["tel"], $_POST["notes"])) {
            error("No data found: " . print_r($_POST));
        }
        // Уникальный ключ, проверка: если есть, значит регистрация уже была
        $unique = md5($_POST["surname"] . $_POST["name"] . $_POST["middlename"] . $_POST["birthday"] . $_POST["email"]);
        $sql = "SELECT UniqueId, Surname, Name FROM registrations WHERE UniqueId='" . $unique . "'";
        if (!($result = $this->conn->query($sql))) {
            error("<br>Something wrong");
        } elseif ($result->rowCount() != 0) {
            $this->status = DB_ADD_DUP;
            return $unique;
        }
        // Причёсываем поля
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
        // Записываем в базу
        $sql = "INSERT INTO registrations (UniqueId, UserIP, Email, Surname, Name, MiddleName, Gender, Birthday,
              Class, School, City, Country, Languages, Tel, Notes)
              VALUES ('$unique', '$_SERVER[REMOTE_ADDR]', '$email', '$surname', '$name',
              '$middlename', '$gender', '$birthday', '$class', '$school', '$city',
              '$country', '$langs', '$phone', '$notes')";
        try {
            $this->conn->exec($sql);
            $this->dbLog("New record created successfully: $name $surname $email");
            $this->status = DB_ADD_OK;
            return $unique;
        }
        catch (PDOException $exception) {
            error("Error in database update: " . $exception);
        }
    }

    /*
     * Возвращает массив со всеми полями для конкретного человека из базы регистраций
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

    // Устанавливает флаг status для конкретного абитуриента
    // 0 - только что создан; 1 -  входил в персональный кабинет и т.д.
    public function setAppStatus($uniqueId, $flag) {
        $sql = "UPDATE registrations SET AppStatus=" . $flag . " WHERE UniqueId='" . $uniqueId ."'";
        try {
            return $this->conn->query($sql);
        }
        catch (PDOException $exception) {
            error("setAppStatus error: ". $exception);
        }
    }

    // Возвращает из таблицы news $newsPerPage новостей для страницы $page
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

    // Внутренний журнал
    public function dbLog($text)
    {
        $text = filter_var($text);
        $sql = 'INSERT INTO log (text) VALUES ("' . $text . '")';
        try {
            $this->conn->exec($sql);
        }
        catch (PDOException $exception) {
            error("dbLog error: " . $exception);
        }
    }

    // результат работы с базой данных
    // DB_NOT_FOUND, DB_ADD_OK и т.д.
    public function getStatus()
    {
        return $this->status;
    }

    function __destruct()
    {
        $this->conn = null;
    }
}