<?php
include "validate.inc";
/*
 * Кусочек для рассылки писем о регистрации всем, зарегистрировавшимся заранее
 */
/*
if (isset($_GET['SentRegistrationMailForAllPreregistered']) && $_GET['SentRegistrationMailForAllPreregistered'] === "True") {
    include_once "mail.php";
    try {
        $db = new dbConnect();
        $result = $db->getPersonsWithStatus("0");

        foreach ($result as $person) {
            print "<p>" . $person['Surname'] . " " . $person['Name'] . " (" . $person['Email'] . "): " . sendRegMail($person) . "</p>";
        }
    } catch (PDOException $PDOException) {
        error("Error in PDO request in SentRegistrationMailForAllPreregistered module: " . $PDOException);
        print "Error in PDO request in SentRegistrationMailForAllPreregistered module: " . $PDOException;
        exit();
    } catch (\PHPMailer\PHPMailer\Exception $exception) {
        error("Error in PHPMailer in SentRegistrationMailForAllPreregistered module: \" . $exception");
        print "Error in PHPMailer in SentRegistrationMailForAllPreregistered module: \" . $exception";
        exit();
    }

    print "<br><p><b>" . $result->rowCount() . " mails have been sent</b></p>" ;
    exit();
}
*/

/**
 * Кусочек для генерации новой таблицы 'oldreg'
 *
 * Сначала вытягиваем участников предыдущего года, потом (только в этот раз) - добавляем участников прошлых лет из
 * базы oldregs_2019. В будущем надо будет просто обновлять тут oldregs и вбивать туда участников прошлого года.
 */
if (isset($_GET['UpdateOldRegsTable']) && $_GET['UpdateOldRegsTable'] === "Update") {
    try {
        $db = new dbConnect();
        echo <<<EOT1
        <!doctype html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Уголок администратора</title>
            <link href="CSS/common.css" rel="stylesheet" type="text/css">
            <link href="CSS/admin.css" rel="stylesheet" type="text/css">
        </head>
        
        <body>
        <div class="main">
            <h1>Результат выполнения обновления таблиц:</h1>
EOT1;
        $result = $db->updateOldregs("2002-05-01", true);
    } catch (PDOException $PDOException) {
        error("Error in PDO request in UpdateOldRegsTable module: " . $PDOException);
        print "Error in PDO request in UpdateOldRegsTable module: " . $PDOException;
        exit();
    }
    $deleted = $result['Deleted'];
    $added = $result['Added'];
    $updated = $result['Updated'];
    echo <<<EOT2
            <p>Удалено старых записей: $deleted</p>
            <p>Обновлено записей: $updated</p>
            <p>Добавлено записей: $added</p>
        </div>
        </body>
        </html>
EOT2;

    $db->dbLog("oldregs table has been updated. Deleted: $deleted. Updated: $updated. Added: $added");
    exit();
}

// Обработка AJAX-запросов.
if(isset($_POST['AppStatus'])) {
    $_SESSION['AppStatus'] = $_POST['AppStatus'];
    $_SESSION['WHERE'] = 'AppStatus >= ' . $_POST['AppStatus'];
    exit();
}
if(isset($_POST['Stat'])) {
    $_SESSION['Stat'] = $_POST['Stat'];
    exit();
}
if(isset($_POST['Save']) && $_POST['Save'] === 'true') {
    $db = new dbConnect();
    $db->setMinAppStatus($_SESSION['AppStatus']);
    $db->setShowStat($_SESSION['Stat']);
    echo "Done!";
    exit();
}
?>
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
    <script src="JS/adm_main.js"></script>
    <?php
    $db = new dbConnect();
    $row = $db->admGet($_SESSION['UID']);
    $name = $row['Name'];
    $surname = $row['Surname'];
    $_SESSION['ReadOnly'] = $row['ReadOnly'];
    if (!isset($_SESSION['SortBy'])) $_SESSION['SortBy'] = "Surname";
    $_SESSION['AppStatus'] = $db->getMinAppStatus();
    (isset($_SESSION['AppStatus'])) ? $appStatus = $_SESSION['AppStatus'] : $appStatus = -99;
    $_SESSION['WHERE'] = 'AppStatus >= ' . $appStatus;
    if (isset($_POST['WHERE'])) $_SESSION['WHERE'] = $_POST['WHERE'];
    $_SESSION['Stat'] = $db->getShowStat();
    if (!isset($_SESSION['Stat'])) $_SESSION['Stat'] = '0';
    $stat = $_SESSION['Stat'];
    ?>
</head>

<body>
<div class="title">
    <div class="row">
        <div class="col-6 hello">
            <h1>Привет, <?php echo "$name $surname!"?></h1>
        </div>
        <div class="col-6">
            <div class="icons">
                <div class="tooltip">
                    <a id="settings_button">
                        <div class="iconbox"><span class="fa fa-sliders icon"></span></div>
                        <span class="tooltiptext">Настройки</span>
                    </a>
                </div>
                <div class="tooltip">
                    <a href="_2019/admin.php">
                        <div class="iconbox"><span class="fa fa-calendar icon"></span></div>
                        <span class="tooltiptext">Админка 2019</span>
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
<div class="settings_wrapper hidden">
<div class= "settings" id="settings">
    <div class="left">
        <i class="fa fa-filter" aria-hidden="true"></i>
        <span class="input-wrapper"><input name="filter" type="radio" required id="all" value="-99">
            <label for="all"><span><span></span></span>Все</label></span>
        <span class="input-wrapper"><input name="filter" type="radio" required id="live" value="0">
            <label for="all"><span><span></span></span>Живые</label></span>
        <span class="input-wrapper"><input name="filter" type="radio" required id="active" value="1">
            <label for="active"><span><span></span></span>Активные</label></span>
        <span class="input-wrapper"><input name="filter" type="radio" required id="olimp" value="3">
            <label for="olimp"><span><span></span></span>Прислали олимпиаду</label></span>
        <span class="input-wrapper"><input name="filter" type="radio" required id="invited" value="5">
            <label for="invited"><span><span></span></span>Приглашены</label></span>
        <span class="input-wrapper"><input name="filter" type="radio" required id="going" value="9">
            <label for="going"><span><span></span></span>Едут</label></span>
        <span class="input-wrapper"><input name="filter" type="radio" required id="member" value="15">
            <label for="member"><span><span></span></span>Участники</label></span>
        <span id="appStat" class="hidden"><?php echo "$appStatus"; ?></span>
        <br><br>
        <b>&Sigma;&thinsp;</b>
        <input name="summary" type="checkbox" id="summary" class="summary">
            <label for="summary"><span></span></label>
        <span id="stat" class="hidden"><?php echo "$stat"; ?></span>
    </div>
        <div class="iconbox right" id="closeSettings"><span class="fa fa-check icon" aria-hidden="true"></span></div>
    <?php if (!$_SESSION['ReadOnly'])
        echo '<div class="iconbox right" id="saveSettings"><span class="fa fa-floppy-o icon" aria-hidden="true"></span></div>'
    ?>
    <!--
    <div class="slidecontainer">
        <span class="smallfont">A</span>
        <input type="range" min="6" max="24" value="18" class="slider" id="fontSize">
        <span class="bigfont">A</span>
    </div>
    -->
</div></div>
<div class="main">
    <div id="list_data">
<?php
    $output = '
        <div class="table">
    ';

    try {
        $result = $db->getStudentsList("UniqueId, Surname, Name, Gender, Tel, OwnTel, Email, AppStatus",
            $_SESSION['SortBy'], 'AppStatus >= -99');
    } catch (PDOException $exception) {
        error("admFetch error: $exception");
    }
    if ($result->rowCount() > 0) {
        foreach ($result as $row) {
            $UID = $row['UniqueId'];
            $targetDir = 'photos/';
            $pattern = $targetDir . $UID . '.*';
            $photos = glob($pattern);
            $surname = $row['Surname'];
            $name = $row['Name'];
            $ownTel = $row['OwnTel'];
            $email = $row['Email'];
            $tel = $row['Tel'];
            $gender = ($row['Gender'] === 'f')? "female" : "male";
            $appStatus = $row['AppStatus'];

            if (isset($photos) && $photos != false && $photos !== '') {
                $photo = $photos[0];
            } else {
                $photo = $targetDir . $row['Gender'] . ".jpg";
            }

            $output .= "
            <div class='row table-row' id='tr-$UID'>
                <div class='table-cell l_photo' onclick='showDetails(\"$UID\")'><img src='$photo' alt='Photo'></div>
                <div class='table-info'>
                    <div class='table-cell l_name' onclick='showDetails(\"$UID\");'><b>$surname $name</b></div>
                    <div class='table-cell l_owntel'><a href='tel:$ownTel'>$ownTel</a></div>
                    <div class='table-cell l_mail'><a href='mailto:$email'>$email</a></div>
                    <div class='table-cell l_tel'><p>Родители:</p><a href='tel:.$tel'>$tel</a></div>
                    <div class='hidden'>$UID</div>
                    <div class='hidden' id='appStatus'>$appStatus</div>
                    <span class='$gender'></span>
                </div>
            </div>
            ";
        }
    } else {
        $output .= 'Данные не найдены';
    }
    $output .= '
    </div><!-- Table -->
    <div class="search-block">
        <div class="stat">
        <span class="invisible" id="stat_line">
            <span class="long-text">Девочек:</span>
            <span class="short-text">Д:</span>&nbsp;<span id="fem_total">0</span>
            &nbsp;|&nbsp;
            <span class="long-text">Мальчиков:</span>
            <span class="short-text">М:</span>&nbsp;<span id="male_total">0</span> 
        </span>
        </div>
        <div class="search"><input type="text" name="search_string" id="search_string" autofocus><span class="fa fa-search" onclick="doSearch()"></span></div>
    </div>';
    if (isset($_SESSION["ID"])) {
        $output .= '
    <script>
    $(function() {  // Прокрутка до активной записи при выходе из карточки участника
        $("html, body").animate({
            scrollTop: $("#tr-' . $_SESSION["ID"] . '").offset().top - 60
        }, 400);
    });
    $(function() {  // Обработка ввода в поле быстрого поиска по списку
        $("#search_string").focus().on("input", search);
    })
    </script>';
    }
    // header("Content-type: text/html; charset=UTF-8");
    echo $output;
?>
    </div>
</div>
</body>
</html>