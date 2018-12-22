<!--?php header('Content-type: text/html; charset=windows-1251'); ?-->
<!doctype html>
<html>
<head>
<!--meta charset="windows-1251"-->
<meta charset="UTF-8">
<?php
include 'phplib/yandex.metrika.php';
include 'phplib/google.analytics.php';
?>

<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
if (isset($_GET['page'])) {
    if ($_GET['page'] == 'founders') {
        echo '<title>&Delta;elta - организаторы</title>';
    }
    elseif ($_GET['page'] == 'team') {
        echo '<title>&Delta;elta - команда</title>';
    }
    elseif ($_GET['page'] == 'projects') {
        echo '<title>&Delta;elta - курсы и проекты</title>';
    }
    elseif ($_GET['page'] == 'environment') {
        echo '<title>&Delta;elta - жизнь в лагере</title>';
    }
    elseif ($_GET['page'] == 'photos') {
        echo '<title>&Delta;elta - фотоальбомы</title>';
    }
} else {
    echo '<title>&Delta;elta - летний физико-математический лагерь в Мюнхене</title>';
}
?>
<meta name="description" content="Международный летний детский лагерь в Мюнхене. Отдых со смыслом: математика, физика, программирование. Сроки: 22 июля - 5 августа 2019 г. Стоимость: 850 Евро.">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="CSS/common.css" rel="stylesheet" type="text/css">
<link href="CSS/index.css" rel="stylesheet" type="text/css">
<script src="JS/lib/masonry.pkgd.min.js"></script>
<script src="JS/index.js"></script>
<?php
require_once 'phplib/dbConnect.php';
require_once 'phplib/news.php';
?>
</head>
<body>
<div class="title">
    <div class="icons">
        <div class="tooltip">
            <a href="https://www.facebook.com/Summer.Camp.Delta/" target="_blank">
            <div class="iconbox"><span class="icon fa fa-facebook"></span></div>
            <span class="tooltiptext">Мы на Facebook</span>
            </a>
        </div>
        <?php
        /*
        <div class="tooltip">
            <a href="https://www.instagram.com/delta.summer.camp/" target="_blank">
            <div class="iconbox"><span class="icon fa fa-instagram"></span></div>
            <span class="tooltiptext">Мы в Instagram</span>
            </a>
        </div>
        */
        ?>
        <div class="tooltip">
            <a href="https://vk.com/summer_camp_delta" target="_blank">
            <div class="iconbox"><span class="icon fa fa-vk"></span></div>
            <span class="tooltiptext">Группа в ВК</span>
            </a>
        </div>
        <div class="tooltip">
            <a href="feedback.php">
            <div class="iconbox"><span class="icon fa fa-envelope-o"></span></div>
            <span class="tooltiptext">Обратная связь</span>
            </a>
        </div>
        <div class="tooltip">
            <a href="feedback.php#persons">
            <div class="iconbox"><span class="icon fa fa-phone"></span></div>
            <span class="tooltiptext">Обратная связь</span>
            </a>
        </div>
        <div class="tooltip">
            <a href="feedback.php#persons">
            <div class="iconbox"><span class="icon fa fa-skype"></span></div>
            <span class="tooltiptext">Обратная связь</span>
            </a>
        </div>
        <div class="tooltip">
            <a href="feedback.php#persons">
            <div class="iconbox"><span class="icon fa fa-whatsapp"></span></div>
            <span class="tooltiptext">Обратная связь</span>
            </a>
        </div>
        <div class="tooltip">
            <a href="feedback.php#persons">
            <div class="iconbox"><span class="icon fa fa-telegram"></span></div>
            <span class="tooltiptext">Обратная связь</span>
            </a>
        </div>
    </div>
    <a href="index.php">
        <img class="banner-140" src="images/title-140px.jpg" alt="Delta">
        <img class="banner-75" src="images/title-75px.jpg" alt="Delta">
    </a>
</div>
<div class="bgrass">
    <div class="buttonholder">
        <a href="registration.php">
            <div class="hot button"><p>Регистрация</p></div>
        </a>
        <a href="index.php?page=founders">
            <div class="button"><p>Организаторы</p></div>
        </a>
        <a href="index.php?page=team">
            <div class="button"><p>Преподаватели</p></div>
        </a>
        <a href="index.php?page=projects">
            <div class="button"><p>Курсы и<br>проекты</p></div>
        </a>
        <a href="index.php?page=environment">
            <div class="button"><p>Жизнь в<br>лагере</p></div>
        </a>
        <a href="index.php?page=photos">
            <div class="button"><p>Фотоальбом</p></div>
        </a>
    </div>
    <div class="news">
        <div class="top"></div>
        <?php
        settype($page, "int");
        if (isset($_GET['page'])) {
            if ($_GET['page'] == 'founders') {
                include 'founders.php';
                pageEnd();
            }
            elseif ($_GET['page'] == 'team') {
                include 'team.php';
                pageEnd();
            }
            elseif ($_GET['page'] == 'projects') {
                include 'projects.php';
                pageEnd();
            }
            elseif ($_GET['page'] == 'environment') {
                include 'environment.php';
                pageEnd();
            }
            elseif ($_GET['page'] == 'photos') {
                include "photos.php";
                pageEnd();
            }
            else {
                $page = (int)$_GET['page'];
                if (!$page) {
                    $page = 1;
                }
            }
        } else {
            $page = 1;
        }
        $isNext = showNewsFeed($page);

        // Вывод внизу ссылок на предыдущую/следующую страницы
        echo '<div class="newsblock">';
        if ($page > 1) {
            echo '<a href="index.php?page=' . ($page-1) . '">&lt; назад</a>';
        } else {
            echo '<span class="inactive">&lt; назад</span>';
        }
        echo ' | ';
        if ($isNext) {
            echo '<a href="index.php?page=' . ($page+1) . '">вперёд &gt;</a>';
        } else {
            echo '<span class="inactive">вперёд &gt;</span>';
        }
        echo '</div>';
        ?>
        <div class="bottom">&copy; 2017 dmitry@ablov.ru</div>
    </div> <!-- news -->
</div> <!-- bgrass -->
</body>
</html>