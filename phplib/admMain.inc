<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Уголок администратора</title>
    <link href="CSS/common.css" rel="stylesheet" type="text/css">
    <link href="CSS/admin.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- jQuery  -->
    <script src="https://code.jquery.com/jquery-3.3.0.min.js"
            integrity="sha256-RTQy8VOmNlT6b2PIRur37p6JEBZUE7o8wPgMvu18MC4="
            crossorigin="anonymous"></script>
    <!-- jQuery Form -->
    <script src="JS/lib/jquery.form.min.js"></script>
    <script src="JS/adm_functions.js"></script>
    <script src="JS/adm_main.js"></script>

    <?php
    $db = new dbConnect();
    $row = $db->admGet($_SESSION['UID']);
    $name = $row['Name'];
    $surname = $row['Surname'];
    $_SESSION['ReadOnly'] = $row['ReadOnly'];
    if (!isset($_SESSION['SortBy'])) {
        $_SESSION['SortBy'] = "Name"; // Сортировка по умолчанию - по фамилиям, переключение на сортировку по имени
    }
    if (!isset($_SESSION['View'])) {
        $_SESSION['View'] = "List"; // Тип вывода: List, Details, Edit
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
                    <a href="_2018/admin.php">
                        <div class="iconbox"><span class="fa fa-calendar icon"></span></div>
                        <span class="tooltiptext">Админка 2018</span>
                    </a>
                </div>
                <div class="tooltip">
                    <a href="teachers.php">
                        <div class="iconbox"><span class="fa fa-user icon"></span></div>
                        <span class="tooltiptext">Преподаватели</span>
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
    <div id="list_data"></div>
</div>
</body>
</html>
