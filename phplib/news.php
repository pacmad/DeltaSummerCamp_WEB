<?php
define('NEWS_PER_PAGE', '5'); //  оличество записей новостей взвращаемых за один раз
/*
 * pageEnd() - завершает страницу новостей
 */
function pageEnd() {
    echo '<div class="bottom">&copy; 2017 dmitry@ablov.ru</div>
        </div>
        </div>
        </body>
        </html>';
    exit();
}
/*
 * boolean showNewsFeed($page) - вывод страницы $page новостной ленты.
 * ¬озвращает true, если не выведена последн€€ записть
 */
function showNewsFeed($page) {
    $db = new dbConnect();
    $result = $db->getNews($page, NEWS_PER_PAGE);

    foreach($result as $row) {
        echo '<div class="newsblock" onclick="showMore(this);">';
        if ($row['Top'] != 1) {
            print('<p class="newsdate">' . date("d/m/Y", strtotime($row['Datetime'])) . '</p>');
        }
        print($row['Text']);
        printf("<img src=\"/pictures/%s\" alt=\"ќтдых и интересное обучение\">", $row['Picture']);
        echo '</div>';
    }

    if ($db->getNews($page+1, NEWS_PER_PAGE)) {
        return true;
    }

    return false;
}
?>