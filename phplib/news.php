<?php
define('NEWS_PER_PAGE', '5'); // ���������� ������� �������� ����������� �� ���� ���
/*
 * pageEnd() - ��������� �������� ��������
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
 * boolean showNewsFeed($page) - ����� �������� $page ��������� �����.
 * ���������� true, ���� �� �������� ��������� �������
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
        printf("<img src=\"/pictures/%s\" alt=\"����� � ���������� ��������\">", $row['Picture']);
        echo '</div>';
    }

    if ($db->getNews($page+1, NEWS_PER_PAGE)) {
        return true;
    }

    return false;
}
?>