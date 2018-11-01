<?php
define('NEWS_PER_PAGE', '5'); // Количество записей новостей взвращаемых за один раз
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
 * Возвращает true, если не выведена последняя записть
 */
function showNewsFeed($page) {
    $db = new dbConnect();
    $result = $db->getNews($page, NEWS_PER_PAGE);

    foreach($result as $row) {
        $id = $row['Datetime'];
        echo "<div class='newsblock' id='$id' onclick='showMore(this);'>";
        if ($row['Top'] != 1) {
            print('<p class="newsdate">' . date("d/m/Y", strtotime($row['Datetime'])) . '</p>');
        }
        print($row['Text']);
        printf("<img src=\"/pictures/%s\" alt=\"Отдых и интересное обучение\">", $row['Picture']);
        echo '</div>';
    }

    if ($db->getNews($page+1, NEWS_PER_PAGE)) {
        return true;
    }

    return false;
}
?>