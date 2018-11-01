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
        $this->conn = new PDO("mysql:host=$servername;dbname=delta;charset=utf8", $username, $password);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // set the PDO error mode to exception
        $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

/*
 *
 *  Р Е Г И С Т Р А Ц И И
 *
 */


    /**
     * putRegData() - put registration data from _POST array into the 'registrations' table
     * and return Unique ID
     */
    public function putRegData()
    {
        // Общее заполнение формы
        if (!(isset($_POST["ALL_DONE"]) && (strcmp($_POST["ALL_DONE"], "Ok") == 0 || strcmp($_POST["ALL_DONE"], "Old") == 0))) {
            error("Form not filled correctly.");
        }
        // Наличие всех ожидаемых полей
        if (!isset($_POST["email"], $_POST["surname"], $_POST["name"], $_POST["middlename"], $_POST["gender"],
            $_POST["birthday"], $_POST["class"], $_POST["school"], $_POST["city"], $_POST["country"], $_POST["langs"],
            $_POST["tel"], $_POST["notes"])) {
            error("No data found: " . print_r($_POST));
        }
        // Уникальный ключ, проверка: если есть, значит регистрация уже была
        // Важно! По стравнению с 2018 годом убран e-mail и добавлен gender
        $unique = md5($_POST["surname"] . $_POST["name"] . $_POST["middlename"] . $_POST["birthday"] . $_POST["gender"]);
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
        $school = substr(trim(filter_var($_POST["school"], FILTER_SANITIZE_STRING)), 0, 70);
        $city = substr(trim(filter_var($_POST["city"], FILTER_SANITIZE_STRING)), 0, 40);
        $country = substr(trim(filter_var($_POST["country"], FILTER_SANITIZE_STRING)), 0, 20);
        $langs = substr(trim(filter_var($_POST["langs"], FILTER_SANITIZE_STRING)), 0, 60);
        $notes = substr(trim(filter_var($_POST["notes"], FILTER_SANITIZE_STRING)), 0, 1024);

        $dbFields = "UniqueId, UserIP, Email, Surname, Name, MiddleName, Gender, Birthday,
              Class, School, City, Country, Languages, Tel, Notes";
        $dbValues = "'$unique', '$_SERVER[REMOTE_ADDR]', '$email', '$surname', '$name',
              '$middlename', '$gender', '$birthday', '$class', '$school', '$city',
              '$country', '$langs', '$phone', '$notes'";

        // Учёт старой регистрации
        if (!strcmp($_POST["ALL_DONE"], "Old")) {
            $ownTel = substr(trim(filter_var($_POST["ownTel"], FILTER_SANITIZE_STRING)), 0, 20);
            $certLang = substr(trim(filter_var($_POST["certLang"], FILTER_SANITIZE_STRING)), 0, 2);
            $certName = substr(trim(filter_var($_POST["certName"], FILTER_SANITIZE_STRING)), 0, 255);
            $health = trim(filter_var($_POST["health"], FILTER_SANITIZE_STRING));
            $insurance = trim(filter_var($_POST["insurance"], FILTER_SANITIZE_STRING));
            $notesText = trim(filter_var($_POST["notesText"], FILTER_SANITIZE_STRING));
            $visa = filter_var($_POST["visa"], FILTER_SANITIZE_STRING);
            $notebook = filter_var($_POST["notebook"], FILTER_SANITIZE_STRING);
            $shirt = filter_var($_POST["shirt"], FILTER_SANITIZE_STRING);

            $dbFields .= ", AppStatus, OwnTel, CertLang, CertName, Health, Insurance, NotesText, Visa, Notebook, Shirt";
            $dbValues .= ", '5', '$ownTel', '$certLang', '$certName', '$health', '$insurance', '$notesText', '$visa', '$notebook', '$shirt'";
        }

        // Записываем в базу
        $sql = "INSERT INTO registrations ($dbFields)
              VALUES ($dbValues)";
        try {
            $this->conn->exec($sql);
            $this->dbLog("Новая регистрация: $name $surname $email", $unique);
            $this->status = DB_ADD_OK;
        }
        catch (PDOException $exception) {
            error("Error in database update: " . $exception);
        }
        return $unique;
    }

    // Проверка, не было ли регистрации в прошлые годы
    public function checkOldRegistration($surname, $name, $middleName, $birthday) {
        if ($birthday = date_create_from_format("d/m/Y", $birthday)) {
            $birthday = $birthday->format("Y-m-d");
        }
        $sql = "SELECT Period, Email, Gender, School, City, Country, Languages, Tel, OwnTel, Notes, CertLang, CertName, 
          Health, Insurance, NotesText, Visa, Notebook, Shirt
          FROM oldregs WHERE Surname = '$surname' AND Name = '$name' AND MiddleName = '$middleName' 
          AND Birthday = '$birthday'";
        try {
            $result = $this->conn->query($sql);
        } catch (PDOException $exception) {
            error("Error in checkOldRegistration(): $exception");
        }
        $records = $result->rowCount();
        if ($records === 1) {
            $row = $result->fetch();
            $row["ALL_DONE"] = "Old";
            return $row;
        }

        return false;
    }

    /*
     * Возвращает старый UID по новому
     */
    public function getOldUID ($UID)
    {
        $result = $this->conn->query("SELECT Surname, Name, MiddleName, Birthday FROM registrations WHERE UniqueId = '$UID'");
        if ($result->rowCount() === 1) {
            $row = $result->fetch();
            $birthday = $row["Birthday"];
//            $birthday = date_create_from_format("Y-m-d", $birthday);
//            $birthday = $birthday->format("d/m/Y");
            $surname = $row["Surname"];
            $name = $row["Name"];
            $middleName = $row["MiddleName"];
            $result = $this->conn->query("SELECT UniqueId FROM oldregs WHERE Surname = '$surname' AND Name = '$name' AND MiddleName = '$middleName' 
            AND Birthday = '$birthday'");
            if ($result->rowCount() === 1) {
                $row = $result->fetch();
                return $row["UniqueId"];
            }
        }
        return false;
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

    // Устанавливает флаг status для конкретного абитуриента
    // -5: не зашли в ЛК;
    // -4: не скачали олимпиаду;
    // -3: нехорошо себя повели;
    // -2: не прошёл по конкурсу;
    // -1: отказ от регистрации;
    // 0: только что создан;
    // 1: входил в персональный кабинет;
    // 2: выслана/скачана олимпиада;
    // 3: получено решение олимпиады;
    // 5: свой;
    // 6: послали "прнят";
    // 7-перекличка, свои;
    // 9-обещали;
    // 10-внесена предоплата
    public function setAppStatus($uniqueId, $flag) {
        $sql = "UPDATE registrations SET AppStatus=" . $flag . " WHERE UniqueId='" . $uniqueId ."'";
        try {
            return $this->conn->query($sql);
        }
        catch (PDOException $exception) {
            error("setAppStatus error: ". $exception);
        }
    }

    // Устанавливает дату отсылки олимпиады и выставляет AppStatus=2
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

    // Возвращает статус зарегистрировавшегося
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

    // Возвращает результат запроса вида 'SELECT $list_of_fields FROM "registations" ORDER BY $sort_by' с учётом типа статуса
    // Последний аргумент - порог выдачи по статусу.
    public function getStudentsList($list_of_fields, $sort_by, $where = 'AppStatus >= 0') {
        $sql = "SELECT $list_of_fields FROM registrations WHERE $where ORDER BY $sort_by";
        return $this->conn->query($sql);
    }

    // Возвращает UniqueID следующей записи после $UID в таблице registrations с условием $where, отсортированной по полю $sortBy
    public function getNextUID($UID, $sort_by, $where = 'AppStatus >= 0'){
        // Определяем общее число записей
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

        // Определяем порядковый номер записи с данным $UID
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


        // Определяем $UID для следующей записи
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

    // Возвращает UniqueID предыдущей записи после $UID в таблице registrations с условием $where, отсортированной по полю $sortBy
    public function getPrevUID($UID, $sort_by, $where = 'AppStatus >= 10'){
        // Определяем порядковый номер записи с данным $UID
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


        // Определяем $UID для следующей записи
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
 *  А Н К Е Т А
 *
 */
    // Запись в базу регистраций телефона ребёнка
    public function setOwnTel($UID, $ownTel){
        $ownTel = substr(trim(filter_var(iconv("UTF-8", "WINDOWS-1251", $ownTel), FILTER_SANITIZE_STRING)), 0, 20);
        $sql = "UPDATE registrations SET OwnTel='$ownTel' WHERE UniqueId='$UID'";
        $this->conn->exec($sql);
    }

    public function getOwnTel($UID) {
        $sql = "SELECT OwnTel FROM registrations WHERE UniqueId='$UID'";
        return $this->conn->query($sql);
    }

    // Запись в базу регистраций деталей прибытия
    public function setComingDetails($UID, $coming_with, $coming_date, $coming_time, $coming_flight, $coming_place){
        $coming_date = substr(trim(filter_var(iconv("UTF-8", "WINDOWS-1251", $coming_date), FILTER_SANITIZE_STRING)), 0, 20);
        $coming_time = substr(trim(filter_var(iconv("UTF-8", "WINDOWS-1251", $coming_time), FILTER_SANITIZE_STRING)), 0, 20);
        $coming_flight = substr(trim(filter_var(iconv("UTF-8", "WINDOWS-1251", $coming_flight), FILTER_SANITIZE_STRING)), 0, 20);
        $sql = "UPDATE registrations SET ComingWith='$coming_with', ComingDate='$coming_date', ComingTime='$coming_time',
          ComingFlight='$coming_flight', ComingPlace='$coming_place' WHERE UniqueId='$UID'";
        $this->conn->exec($sql);
    }

    // Вывод данных по прибытию в лагерь
    public function getComingDetails($UID) {
        $sql = "SELECT ComingWith, ComingDate, ComingTime, ComingFlight, ComingPlace FROM registrations WHERE UniqueId='$UID'";
        return $this->conn->query($sql);
    }
    
    // Запись в базу регистраций деталей отбытия
    public function setLeavingDetails($UID, $leaving_with, $leaving_date, $leaving_time, $leaving_flight, $leaving_place){
        $leaving_date = substr(trim(filter_var(iconv("UTF-8", "WINDOWS-1251", $leaving_date), FILTER_SANITIZE_STRING)), 0, 20);
        $leaving_time = substr(trim(filter_var(iconv("UTF-8", "WINDOWS-1251", $leaving_time), FILTER_SANITIZE_STRING)), 0, 20);
        $leaving_flight = substr(trim(filter_var(iconv("UTF-8", "WINDOWS-1251", $leaving_flight), FILTER_SANITIZE_STRING)), 0, 20);
        $sql = "UPDATE registrations SET LeavingWith='$leaving_with', LeavingDate='$leaving_date', LeavingTime='$leaving_time',
          LeavingFlight='$leaving_flight', LeavingPlace='$leaving_place' WHERE UniqueId='$UID'";
        $this->conn->exec($sql);
    }

    // Вывод данных по прибытию в лагерь
    public function getLeavingDetails($UID) {
        $sql = "SELECT LeavingWith, LeavingDate, LeavingTime, LeavingFlight, LeavingPlace FROM registrations WHERE UniqueId='$UID'";
        return $this->conn->query($sql);
    }

    // Здоровье
    public function setHealthDetails($UID, $health) {
        $health = filter_var(iconv("UTF-8", "WINDOWS-1251", $health), FILTER_SANITIZE_STRING);
        $sql = "UPDATE registrations SET Health='$health' WHERE UniqueId='$UID'";
        $this->conn->exec($sql);
    }
    public function getHealthDetails($UID) {
        $sql = "SELECT Health FROM registrations WHERE UniqueId='$UID'";
        return $this->conn->query($sql);
    }

    // Страховка
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

    // Форс-мажор
    public function setNotesDetails($UID, $notes) {
        $notes = filter_var(iconv("UTF-8", "WINDOWS-1251", $notes), FILTER_SANITIZE_STRING);
        $sql = "UPDATE registrations SET NotesText='$notes' WHERE UniqueId='$UID'";
        $this->conn->exec($sql);
    }
    public function getNotesDetails($UID) {
        $sql = "SELECT NotesText FROM registrations WHERE UniqueId='$UID'";
        return $this->conn->query($sql);
    }

    // Дополнительные данные
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
     *  Н О В О С Т И
     *
     */

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

/*
 *
 *  Работа с учётками админов
 *
 */
    // Проверка входа
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
            $now = date("Y-m-d H:i:s");
            $sql =  "UPDATE admin SET last = '$now' WHERE UID = '$UID'"; // Установка времени последнего входа (автоустановка при update)
            $this->conn->exec($sql);
            return 1;
        }

        // Работает с PHP 5.5+
        // if (password_verify($PASS, $db_pass)) {
        //     return 1;
        // }

        return 0;
    }

    // Установка пароля (только в том случае, если он уже не установлен
    // @param char[32] $UID md5 hash
    // @param string $pass - пароль
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

        // Работает с PHP 5.5+
        // $password = password_hash($pass, PASSWORD_DEFAULT);
        $password = md5($pass);
        $sql = "UPDATE admin SET password = '$password' WHERE UID = '$UID'";
        $this->conn->exec($sql);
        return 1;
    }

    // Получаем данные админа
    // @param char[32] $UID md5 hash
    // @return array $row
    public function admGet($UID) {
        $sql = "SELECT * FROM admin WHERE UID='$UID'";
        $result = $this->conn->query($sql);
        return $result->fetch();
    }

/*
*
*   Работа с базой заметок
*
*/
    public function addTechNote ($UID, $note)
    {
        $sql = "INSERT INTO technotes (UID, Note) VALUES ('$UID', '$note')";
        try {
            $this->conn->exec($sql);
        } catch (PDOException $exception) {
            error("Error in function addTechNote: $exception");
        }
    }

    public function getTechNotes($UID)
    {
        try {
            return $this->conn->query("SELECT Date, Note FROM technotes WHERE UID=$UID");
        } catch (PDOException $exception) {
            error("Error in function getTechNotes: $exception");
        }
        return null;
    }

/*
*
*   Преподаватели
*
*/

    // regTeacher() - put registration data from _POST array into the 'teachers' table
    // and return Unique ID
    public function regTeacher()
    {
        // Наличие всех ожидаемых полей
        if (!isset($_POST["surname"], $_POST["name"], $_POST["email"], $_POST["phone"])) {
            error("No data found: " . print_r($_POST));
        }

        // Уникальный ключ, проверка: если есть, значит регистрация уже была
        $UID = md5($_POST["surname"] . $_POST["name"] . $_POST["email"] . $_POST["phone"]);
        $sql = "SELECT UID, Surname, Name FROM teachers WHERE UID='$UID'";
        if (!($result = $this->conn->query($sql))) {
            error("<br>Something wrong");
        } elseif ($result->rowCount() != 0) {
            $this->status = DB_ADD_DUP;
            return $UID;
        }
        // Причёсываем поля
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
        // Записываем в базу
        $sql = "INSERT INTO teachers (UID, Surname, Name, Phone, Email)
              VALUES ('$UID', '$surname', '$name', '$phone', '$email')";
        try {
            $this->conn->exec($sql);
            $this->dbLog("Новый преподаватель: $name $surname $email", $UID);
            $this->status = DB_ADD_OK;
        }
        catch (PDOException $exception) {
            error("Error in database 'teachers' update: " . $exception);
        }
        return $UID;
    }

    // Возвращает список всех преподавателей
    public function getTeachers() {
        $sql = "SELECT * FROM teachers ORDER BY Surname;";
        return $this->conn->query($sql);
    }

    // Возвращает данные конкретного преподавателя
    public function getTeacher($UID) {
        $sql = "SELECT * FROM teachers WHERE UID='$UID';";
        return $this->conn->query($sql);
    }

/*
 *  Проекты и курсы
 */
    // Регистрация курса
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
            $this->dbLog("Добавлен курс: $courseRus");
        } catch (PDOException $exception) {
            error("Error in courses update: $courseRus");
        }
        return $CID; // Возвращает автосгенерированный ключ
    }

    // Добавление преподавателя (TID) к курсу (CID)
    public function addTeacherToCourse($CID, $TID) {
        $sql = "INSERT INTO teach2course (Teacher_ID, Course_ID) VALUES ('$TID', '$CID')";
        try {
            $this->conn->exec($sql);
            $this->dbLog("Добавлен преподаватель $TID к курсу $CID");
        } catch (PDOException $exception) {
            error("Error in addTeacherToCourse: $exception");
        }
    }

    // Удаление всех учителей у курса (CID)
    public function cleanTeachersFromCourse($CID) {
        $sql = "DELETE FROM teach2course WHERE Course_ID = '$CID'";
        try {
            $this->conn->exec($sql);
        } catch (PDOException $exception) {
            error("Error in function cleanTeachersFromCourse : $exception");
        }
    }

    // Добавление курса в расписание (с проверкой, если курс уже записан, ничего не делаем)
    // Возвращает TID - id курса в расписании
    public function addCourseToTimetable($CID, $time) {
        // Проверка, не существует ли уже запись
        $sql = "SELECT ID, COUNT(*) FROM timetable WHERE Course_ID = '$CID' AND Time = '$time'";
        try {
            $result = $this->conn->query($sql);
        } catch (PDOException $exception) {
            error("Error in SELECT statement of addCourseToTimetable: $exception");
            return false;
        }

        $row = $result->fetch();

        if ($row['COUNT(*)'] == '0') {
            $sql = "INSERT INTO timetable (Course_ID, Time) VALUES ('$CID', '$time')";
            try {
                $this->conn->exec($sql);
                $TID = $this->conn->lastInsertId();
                $this->dbLog("Добавлен курс $CID, время: $time");
                return $TID;
            } catch (PDOException $exception) {
                error("Error in addCourseToTimetable: $exception");
                return false;
            }
        } else return $row['ID'];
    }

    // Изменение расписания для курса
    public function updateCourseToTimetable ($CID, $T0, $T11, $T21, $T31, $T12, $T22, $T32) {
        // Получаем текущее расписание курса
        $courseDetail = $this->getCourseDetails($CID);

        // Чистим то, что отменили
        foreach ($courseDetail['TimeSlots'] as $time) {
            if ( ($time == '0' && $T0 == 0) ||
                ($time == '11' && $T11 == 0) ||
                ($time == '21' && $T21 == 0) ||
                ($time == '31' && $T31 == 0) ||
                ($time == '12' && $T12 == 0) ||
                ($time == '22' && $T22 == 0) ||
                ($time == '32' && $T32 == 0)
            ) $this->cleanCourse($CID, $time);
        }

        // Добавляем то, что добавили
        if ($T0 == 1) $this->addCourseToTimetable($CID, '0');
        if ($T11 == 1) $this->addCourseToTimetable($CID, '11');
        if ($T21 == 1) $this->addCourseToTimetable($CID, '21');
        if ($T31 == 1) $this->addCourseToTimetable($CID, '31');
        if ($T12 == 1) $this->addCourseToTimetable($CID, '12');
        if ($T22 == 1) $this->addCourseToTimetable($CID, '22');
        if ($T32 == 1) $this->addCourseToTimetable($CID, '32');

        // Если курс убран из расписания, добавляем "специальное" время '99', иначе - убираем это псевдо-время
        if (1 * ($T0 + $T11 + $T21 + $T31 + $T12 + $T22 + $T31) == 0)
            $this->addCourseToTimetable($CID, '99');
        else
            $this->cleanCourse($CID, '99');
    }

    // Детали курса; возвращает массив вида [имя_рус, имя_гер, имя_анг, [массив_преподавателей "Teachers[]"], [массив_пар "TimeSlots[]']]
    public function getCourseDetails($CID)
    {
        $course = array();

        $sql = "SELECT * FROM courses WHERE ID='$CID'";
        try {
            $result = $this->conn->query($sql);
        } catch (PDOException $exception) {
            error("Error in getCourseDetails 1: $exception");
        }
        if($result->rowCount() == 0) return $course; // Если неверный CID - возвращаем пустой массив
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
            error("Error in getCourseDetails 3: $exception");
        }

        return $course;
    }

    // Таблица курсов
    // Возвращает массив со всеми курсами getCourseDetails
    public function getCoursesTable()
    {
        $courses = array();
        try {
            $result = $this->conn->query("SELECT ID FROM courses ORDER BY NameRus");
        } catch (PDOException $exception) {
            error("Error in getCoursesTable");
            return null;
        }

        foreach ($result as $row) {
            $courses[$row['ID']] = $this->getCourseDetails($row['ID']);
        }

        return $courses;
    }

    // Учителя, ведущие курс CID
    public function getTeachersOfCourse ($CID) {
        return $this->conn->query("SELECT * FROM teachers_and_courses WHERE Course_ID = '$CID'");
    }

    // Изменение описания курса (удаление, если все описания нулевые)
    public function updateCourse($CID, $courseRus, $courseGer, $courseEng) {
        $courseRus = substr(trim(filter_var($courseRus, FILTER_SANITIZE_STRING)), 0, 255);
        $courseGer = substr(trim(filter_var($courseGer, FILTER_SANITIZE_STRING)), 0, 255);
        $courseEng = substr(trim(filter_var($courseEng, FILTER_SANITIZE_STRING)), 0, 255);

        if($courseRus !== "" || $courseGer !== "" || $courseEng !== "") {
            $sql = "UPDATE courses SET NameRus = '$courseRus', NameGer = '$courseGer', NameEng = '$courseEng' WHERE ID = '$CID'";
            try {
                $this->conn->exec($sql);
                $this->dbLog("Изменён курс: $courseRus");
            } catch (PDOException $exception) {
                error("Error in courses update: $courseRus");
            }
        } else { // Удаление курса
            try {
                $this->conn->exec("DELETE FROM teach2course WHERE Course_ID = '$CID'");
                $result = $this->conn->query("SELECT ID FROM timetable WHERE Course_ID = '$CID'");
                foreach ($result as $row) {
                    $TID = $row['ID'];
                    $this->conn->exec("DELETE FROM student2time WHERE Time_ID ='$TID'");
                }
                $this->conn->exec("DELETE FROM timetable WHERE Course_ID = '$CID'");
                $this->conn->exec("DELETE FROM courses WHERE ID = '$CID'");
            } catch (PDOException $exception) {
                error("Error in course delete part of updateCourse function: $exception, CID: '$CID");
            }
        }
    }

    // Возвращает true, если студент (UID) записан на курс (CID) на время (Time)
    // иначе возвращает false
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

    // Отчистка расписания (timeSlot) для данного курса (CID)
    public function cleanCourse($CID, $time)
    {
        try {
            $result = $this->conn->query("SELECT ID FROM timetable WHERE Course_ID = '$CID' AND Time = '$time'");
            $TID = $result->fetch();
            $TID = $TID['ID'];
            $this->conn->exec("DELETE FROM student2time WHERE Time_ID = $TID");
            $this->conn->exec("DELETE FROM timetable WHERE Course_ID = '$CID' AND Time = '$time'");
        } catch (PDOException $exception) {
            error("Error in cleanCourse: $exception");
        }
    }

    // Записать студента (UID) на курс (CID) в расписании(timeSlot)
    public function addStudentToCourse($UID, $CID, $time)
    {
        try {
            $result = $this->conn->query("SELECT ID FROM timetable WHERE Course_ID = '$CID' AND Time = '$time'");
            $TID = $result->fetch();
            if ($TID) {
                $TID = $TID['ID'];
            } else {
                $TID = $this->addCourseToTimetable($CID, $time);
            }
            $this->conn->exec("INSERT INTO student2time (Student_ID, Time_ID) VALUES ('$UID', '$TID')");
        } catch (PDOException $exception) {
            error("Error in addStudentToCourse: $exception");
        }
    }

    // Вывод списка курсов, на которые ходил студент
    public function getCoursesForStudent($UID)
    {
        $sql = "SELECT * FROM courses_by_student WHERE UID = '$UID'";

        try {
            return $this->conn->query($sql);
        } catch (PDOException $exception) {
            error("Error in getCoursesForStudent: $exception");
        }
        return false;
    }

    // Возвращает количество академических часов (одна пара - два часа), которые посетил студент (UID) на курсе CID
    public function getHoursOfCourse ($UID, $CID) {
        $sql = "SELECT COUNT(Time) AS Count FROM courses_by_student WHERE UID='$UID' AND CID='$CID'";
        try {
            $result = $this->conn->query($sql);
            $row = $result->fetch();
            return $row['Count'] * 2 * 3; // каждая пара - два часа, блок - три дня
        } catch (PDOException $exception) {
            error("Error in getHoursOfCourse: $exception");
        }
        return 0;
    }

    // Возвращает для студента UID список курсов (с учётом языка сертификата), которые он посетил,
    // и количество академических часов для каждого курса
    public function getCoursesForCert($UID) {
        $person = $this->getPerson($UID);
        $certLang = $person['CertLang'];

        $sql = "SELECT DISTINCT CID FROM courses_by_student WHERE UID='$UID' AND NOT Time = '0'"; // без проекта
        $result = $this->conn->query($sql);

        foreach ($result as $row) {
            $CID = $row['CID'];
            foreach ($this->getTeachersOfCourse($CID) AS $teacher) {
                if ($certLang == 'ru')
                    $teachers[] = $teacher['Name'] . " " . $teacher['Surname'];
                else
                    $teachers[] = $teacher['Name_en'] . " " . $teacher['Surname_en'];
            }

            switch ($certLang) {
                case 'ru':
                    $sql = "SELECT NameRus AS CourseName FROM courses WHERE ID = '$CID'";
                    break;
                case 'de':
                    $sql = "SELECT NameGer AS CourseName FROM courses WHERE ID = '$CID'";
                    break;
                case 'en':
                    $sql = "SELECT NameEng AS CourseName FROM courses WHERE ID = '$CID'";
                    break;
            }

            $result = $this->conn->query($sql);
            $row = $result->fetch();
            $courseName = iconv('Windows-1251', 'UTF-8', $row['CourseName']);
            $length = $this->getHoursOfCourse($UID, $CID);
            $courses[] = ["courseName" => $courseName, "length" => $length, "teachers" => $teachers];
        }

        return $courses;
    }

    // Возвращает прооект по студенту
    public function getProjectForCert($UID) {
        $sql = "SELECT CertLang, NameRus, NameGer, NameEng FROM courses_by_student WHERE UID='$UID' AND Time = '0'"; // проект
        $result = $this->conn->query($sql);
        $row = $result->fetch();
        switch ($row['CertLang']) {
            case 'ru':
                return iconv('Windows-1251', 'UTF-8', $row['NameRus']);
                break;
            case 'de':
                /* return iconv('Windows-1251', 'UTF-8', $row['NameGer']); */
                return $row['NameGer'];
                break;
            case 'en':
                return iconv('Windows-1251', 'UTF-8', $row['NameEng']);
                break;
        }

        return iconv('Windows-1251', 'UTF-8', $row['NameRus']);
    }

    // Возвращает руководителей проекта по имени студента
    public function getProjectLeader($UID) {
        $result = $this->conn->query("SELECT CID FROM courses_by_student WHERE UID='$UID' AND Time='0'");
        $row = $result->fetch();
        $CID = $row['CID'];
        return $this->conn->query("SELECT DISTINCT Surname, Name, Surname_en, Name_en FROM teachers_and_courses WHERE Course_ID = '$CID'");
    }

/*
 *  Tools
 */

    // Внутренний журнал
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

    // результат работы с базой данных
    // DB_NOT_FOUND, DB_ADD_OK, DB_ADD_DUP и т.д.
    public function getStatus()
    {
        return $this->status;
    }

    function __destruct()
    {
        $this->conn = null;
    }
}