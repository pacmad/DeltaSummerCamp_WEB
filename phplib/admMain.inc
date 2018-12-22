<?php
include "validate.inc";
/*
 * Кусочек для рассылки писем о регистрации всем, зарегистрировавшимся заранее
 */
if (isset($_GET['SentRegistrationMailForAllPreregitered']) && $_GET['SentRegistrationMailForAllPreregitered'] === "True") {
    include_once "mail.php";
    try {
        $db = new dbConnect();
        $result = $db->getPersonsWithStatus("0");

        foreach ($result as $person) {
            print "<p>" . $person['Surname'] . " " . $person['Name'] . " (" . $person['Email'] . "): " . sendRegMail($person) . "</p>";
        }
    } catch (PDOException $PDOException) {
        error("Error in PDO request in SentRegistrationMailForAllPreregitered mudule: " . $PDOException);
        print "Error in PDO request in SentRegistrationMailForAllPreregitered mudule: " . $PDOException;
        exit();
    } catch (\PHPMailer\PHPMailer\Exception $exception) {
        error("Error in PHPMailer in SentRegistrationMailForAllPreregitered mudule: \" . $exception");
        print "Error in PHPMailer in SentRegistrationMailForAllPreregitered mudule: \" . $exception";
        exit();
    }

    print "<br><p><b>" . $result->rowCount() . " mails have been sent</b></p>" ;
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
    if (!isset($_SESSION['WHERE'])) $_SESSION['WHERE'] = 'AppStatus >= 0';
    if (isset($_POST['WHERE'])) $_SESSION['WHERE'] = $_POST['WHERE'];
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
    <div id="list_data">
<?php
    $output = '
        <div class="table">
    ';

    try {
        $result = $db->getStudentsList("UniqueId, Surname, Name, Gender, Tel, OwnTel, Email", $_SESSION['SortBy']);
    } catch (PDOException $exception) {
        error("admFetch error: $exception");
    }
    if ($result->rowCount() > 0) {
        foreach ($result as $row) {
            $UID = $row['UniqueId'];
            $targetDir = 'photos/';
            $pattern = $targetDir . $UID . '.*';
            $photos = glob($pattern);

            if (isset($photos) && $photos != false && $photos !== '') {
                $photo = $photos[0];
            } else {
                $photo = $targetDir . $row['Gender'] . ".jpg";
            }

            $output .= '
            <div class="row table-row" id="tr-'.$row["UniqueId"].'">
                <div class="table-cell l_photo" onclick="showDetails(\''.$row["UniqueId"].'\')"><img src="' . $photo . '" alt="Photo"></div>
                <div class="table-info">
                    <div class="table-cell l_name" onclick="showDetails(\''.$row["UniqueId"].'\');"><b>' . $row["Surname"] . ' ' . $row["Name"] . '</b></div>
                    <div class="table-cell l_owntel"><a href="tel:' . $row["OwnTel"] . '">'. $row["OwnTel"] .'</a></div>
                    <div class="table-cell l_mail"><a href="mailto:'.$row["Email"].'">' . $row["Email"] . '</a></div>
                    <div class="table-cell l_tel"><p>Родители:</p><a href="tel:'.$row["Tel"].'">' . $row["Tel"] . '</a></div>
                    <div class="hidden">' . $UID . '</div>
                </div>
            </div>
            ';
        }
    } else {
        $output .= 'Данные не найдены';
    }
    $output .= '
    </div><!-- Table -->
    <div class="search-block">
        <div class="search"><input type="text" name="search_string" id="search_string" autofocus><span class="fa fa-search" onclick="doSearch()"></span></div>
    </div>
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
    header("Content-type: text/html; charset=UTF-8");
    echo $output;
?>
    </div>
</div>
</body>
</html>