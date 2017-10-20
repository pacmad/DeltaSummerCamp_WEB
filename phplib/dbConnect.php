<?php
/**
 * Created by PhpStorm.
 * User: Dima
 * Date: 05.10.2017
 * Time: 14:12
 */

class dbConnect {
    private $conn;

    /**
     * dbConnect constructor. Open 'delta' database.
     */
    function __construct() {
        $servername = "localhost";
        $username = "PHP";
        $password = "DeltaDB";
        try {
            $this->conn = new PDO("mysql:host=$servername;dbname=delta;charset=CP1251", $username, $password);
            // set the PDO error mode to exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "<br>Connected successfully";
        }
        catch (PDOException $e) {
            echo "<br>Connection failed: " . $e->getMessage();
        }
    }

    /**
     * putRegData() - put registration data from _POST array into the 'registrations' table
     */
    public function putRegData() {

        if (!isset($_POST["ALL_DONE"]) || strcmp($_POST["ALL_DONE"], "Ok")) {
            echo "<br>Form not filled correctly.";
            return -1;
        }

        if (!isset($_POST["email"], $_POST["surname"], $_POST["name"], $_POST["middlename"], $_POST["gender"],
            $_POST["birthday"], $_POST["class"], $_POST["school"], $_POST["city"], $_POST["country"], $_POST["langs"],
            $_POST["tel"], $_POST["notes"])) {
            echo "<br>No data found.";
            return -1;
        }

        $unique = md5($_POST["surname"] . $_POST["name"] . $_POST["middlename"] . $_POST["birthday"] . $_POST["email"]);
        try {
            $sql = "SELECT UniqueId, Surname, Name FROM registrations WHERE UniqueId='$unique'";
            if (!($result = $this->conn->query($sql))) {
                echo "<br>Already registered";
                return -1;
            }
        }
        catch (PDOException $e) {
            echo "<br>SELECT failed: " . $e->getMessage();
            return -1;
        }
        $email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL);
		if (!$email) {
			echo "<br>Invalid e-mail";
			return -1;
		}
		$birthday = date_create_from_format("d/m/Y", $_POST["birthday"])->format("Y-m-d");
        if ($_POST["gender"]==="f") {
            $gender = 'f';
        } else {
            $gender = 'm';
        }
        if ($_POST["class"]==='') {
            $_POST["class"] = 0;
        }
        try {
            $sql = "INSERT INTO registrations (UniqueId, UserIP, Email, Surname, Name, MiddleName, Gender, Birthday,
              Class, School, City, Country, Languages, Tel, Notes)
              VALUES ('$unique', '$_SERVER[REMOTE_ADDR]', '$email', '$_POST[surname]', '$_POST[name]',
              '$_POST[middlename]', '$gender', '$birthday', '$_POST[class]', '$_POST[school]', '$_POST[city]',
              '$_POST[country]', '$_POST[langs]', '$_POST[tel]', '$_POST[notes]')";
            $this->conn->exec($sql);
            echo "<br>New record created successfully";
        }
        catch (PDOException $e) {
            echo "<br>Table write failed: " . $e->getMessage();
            return -1;
        }
        return 0;
    }

}