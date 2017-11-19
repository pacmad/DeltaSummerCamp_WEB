<?php
/**
 * Created by PhpStorm.
 * User: Dima
 * Date: 05.10.2017
 * Time: 14:12
 */

require_once 'common.php';

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
            error("<br>Form not filled correctly.");
        }
        // Наличие всех ожидаемых полей
        if (!isset($_POST["email"], $_POST["surname"], $_POST["name"], $_POST["middlename"], $_POST["gender"],
            $_POST["birthday"], $_POST["class"], $_POST["school"], $_POST["city"], $_POST["country"], $_POST["langs"],
            $_POST["tel"], $_POST["notes"])) {
            error("<br>No data found.");
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
        }
        $phone = filter_var($_POST["tel"], FILTER_SANITIZE_STRING);
        $surname = filter_var($_POST["surname"], FILTER_SANITIZE_STRING);
        $name = filter_var($_POST["name"], FILTER_SANITIZE_STRING);
        $middlename = filter_var($_POST["middlename"], FILTER_SANITIZE_STRING);
        if ($_POST["gender"] === "f") {
            $gender = 'f';
        } else {
            $gender = 'm';
        }
        if ($birthday = date_create_from_format("d/m/Y", $_POST["birthday"])) {
            $birthday = $birthday->format("Y-m-d");
        } else {
            error("Invalid Date of Birthday field");
        }
        if ($_POST["class"] === '') {
            $_POST["class"] = 0;
        }
        $class = filter_var($_POST["class"], FILTER_SANITIZE_STRING);
        $school = filter_var($_POST["school"], FILTER_SANITIZE_STRING);
        $city = filter_var($_POST["city"], FILTER_SANITIZE_STRING);
        $country = filter_var($_POST["country"], FILTER_SANITIZE_STRING);
        $langs = filter_var($_POST["langs"], FILTER_SANITIZE_STRING);
        $notes = filter_var($_POST["notes"], FILTER_SANITIZE_STRING);
        // Записываем в базу
        $sql = "INSERT INTO registrations (UniqueId, UserIP, Email, Surname, Name, MiddleName, Gender, Birthday,
              Class, School, City, Country, Languages, Tel, Notes)
              VALUES ('$unique', '$_SERVER[REMOTE_ADDR]', '$email', '$surname', '$name',
              '$middlename', '$gender', '$birthday', '$class', '$school', '$city',
              '$country', '$langs', '$phone', '$notes')";
        $this->conn->exec($sql);
        $this->dbLog("New record created successfully: $name $surname $email");
        $this->status = DB_ADD_OK;
        return $unique;
    }

    /*
     * Возвращает массив со всеми полями для конкретного человека из базы регистраций
     */
    public function getPerson($uniqueId)
    {
        $sql = "SELECT * FROM registrations WHERE UniqueId='" . $uniqueId . "'";
        if (!($result = $this->conn->query($sql))) {
            error("Something wrong when getting records in function getPerson");
        }
        if ($result->rowCount() == 1) {
            foreach ($result as $row) {
                return $row;
            }
        }
        return false;
    }

    // Устанавливает флаг is_reg
    public function setReg($uniqueId) {
        $sql = "UPDATE registrations SET is_reg=1 WHERE UniqueId='" . $uniqueId ."'";
        return $this->conn->query($sql);
}

    // Внутренний журнал
    public function dbLog($text)
    {
        $sql = 'INSERT INTO log (text) VALUES ("' . $text . '")';
        $this->conn->exec($sql);
    }

    public function getStatus()
    {
        return $this->status;
    }

    function __destruct()
    {
        $this->conn = null;
    }
}