<?php
/*
 * Страница со списком преподавателей
 * @todo Сделать readnly-доступ к странице для всех (без возможности перехода на страницу детей или курсы!)
 */
include "phplib/validate.inc";
include_once "phplib/dbConnect.php";
?>

<!doctype html>
<html lang="ru">
<head>
    <meta charset="windows-1251">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Работа с преподавателями</title>
    <link href="CSS/common.css" rel="stylesheet" type="text/css">
    <link href="CSS/admin.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- jQuery
    <script src="https://code.jquery.com/jquery-3.3.0.min.js"
            integrity="sha256-RTQy8VOmNlT6b2PIRur37p6JEBZUE7o8wPgMvu18MC4="
            crossorigin="anonymous"></script>
    -->
    <?php
    $db = new dbConnect();
    $row = $db->admGet($_SESSION['UID']);
    $name = $row['Name'];
    $surname = $row['Surname'];

    if(isset($_POST['name'])) {
        $db->regTeacher();
    }
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
                    <a href="courses.php">
                        <div class="iconbox"><span class="fa fa-graduation-cap icon"></span></div>
                        <span class="tooltiptext">Курсы и проекты</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="main">
    <h3>Список преподавателей:</h3>
    <div class="table">
        <?php
        try {
            $result = $db->getTeachers();
        } catch (PDOException $exception) {
            error("getTeachers error: $exception");
        }
        if ($result->rowCount() > 0) {
            foreach ($result as $row) {
                $name = $row['Name'];
                $surname = $row['Surname'];
                $phone = $row['Phone'];
                $email = $row['Email'];
                $output = "
                    <div class='row table-row tch-row'>
                        <div class='table-cell l_name'><b>$surname $name</b></div>
                        <div class='table-cell l_tel'><a href='tel:$phone'>$phone</a></div>
                        <div class='table-cell l_mail'><a href='mailto:$email'>$email</a></div>
                    </div>
                ";
                echo $output;
            }
        }
        ?>
    </div>
    <!--
    @todo Сделать не только форму добавления, но и изменения
    -->
    <h3>Добавить преподавателя:</h3>
    <form id="add_teacher" class="table" method="post">
        <div class="row table-row tch-add">
            <div class="table-cell l_name">
                <b>Имя:</b>
                <input type="text" name="name">
            </div>
            <div class="table-cell l_name">
                <b>Фамилия:</b>
                <input type="text" name="surname">
            </div>
            <div class="table-cell l_tel">
                <b>Телефон:</b>
                <input type="tel" name="phone">
            </div>
            <div class="table-cell l_mail">
                <b>Email:</b>
                <input type="text" name="email">
            </div>
            <div class="table-cell l_submit">
                <input type="submit">
            </div>
        </div>
    </form>
</div>
</body>
</html>
