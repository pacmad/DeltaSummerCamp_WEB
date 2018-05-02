var Sort = '';

// Вывод данных участников лагеря в элемент страницы list_data (см. файл admMain.inc)
function fetchData(SortType) {
    $.post("phplib/admFetch.php",
        {SortBy: SortType},
        function (data) {
            $("#list_data").html(data);
        },
        "html");
}

// Вывод карточки участника
function showDetails(UID) {
    $.post("phplib/admFetch.php",
        {View: "Details", ID: UID},
        function (data) {
            $("#list_data").html(data);
        },
        "html");
}

// Вывод следующей карточки с учётом сортировки
function showNext() {
    $.post("phplib/admFetch.php",
        {View: "Details", ID: "+1"},
        function (data) {
            $("#list_data").html(data);
        },
        "html");
}

// Вывод предыдущей карточки с учётом сортировки
function showPrev() {
    $.post("phplib/admFetch.php",
        {View: "Details", ID: "-1"},
        function (data) {
            $("#list_data").html(data);
        },
        "html");
}

function showList(ID) {
    $.post("phplib/admFetch.php",
        {View: "List", ID: ID},
        function (data) {
            $("#list_data").html(data);
        },
        "html");
}

// Инициализация странички
$(document).ready(function () {
    fetchData("Name");

});

// Отмечает или снимает отметку строки при тыке
function checkIt(ID) {
    var chkBox = document.getElementById('cb-' + ID);

    chkBox.checked = !chkBox.checked;
}

// Показывает/скрывает дополнительную информацию в карточке участника
function showMoreInfo() {
    $("#more_info").toggle(300);
}
