<!DOCTYPE html>
<html>
<head>
<meta charset="windows-1251">
<?php require_once 'phplib/dbConnect.php' ?>
</head>
<body>
<h1>Эксперименты с базами данных</h1>
<?php
$servername = "localhost";
$username = "PHP";
$password = "DeltaDB";
try {
    $conn = new PDO("mysql:host=$servername;dbname=delta;charset=CP1251", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<br>Connected successfully";
}
catch (PDOException $e) {
    echo "<br>Connection failed: " . $e->getMessage();
}

try {
    $sql = "SELECT UniqueId, Surname, Name FROM registrations";
    if (!($result = $conn->query($sql))) {
        echo "<br>Nothing found";
    } else {
        foreach ($result as $row) {
            print_r('<br>' . $row['UniqueId'] . '&nbsp;' . $row['Surname'] . '&nbsp;' . $row['Name']);
        }

    }
}
catch (PDOException $e) {
    echo "<br>SELECT failed: " . $e->getMessage();
    return -1;
}

?>
</body>
</html>
