var Sort = '';

// Инициализация странички
$(document).ready(function () {
    // Вывод данных
    fetchData("Surname");
});

// Вывод данных участников лагеря в элемент страницы list_data (см. файл admMain.inc)
function fetchData(SortType) {
    $.post("phplib/admFetch.php",
        {
            SortBy: SortType,
            Init: "Init"
        },
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
        {
            View: "List",
            ID: ID,
            Init: "Init"
        },
        function (data) {
            $("#list_data").html(data);
        },
        "html");
}

// Показывает/скрывает дополнительную информацию в карточке участника
function showMoreInfo() {
    $("#more_info").toggle(300);
}

// Поиск по списку
function doSearch() {
    if($(".search .fa").hasClass("fa-times")) {
        $("#search_string").val("");
    }
    search();
}
function search() {
    var searchString = $("#search_string").focus().val().toLowerCase();
    if (searchString !== "") {
        $("[id^='tr-']").filter(function () {
            var result = this.innerText.toLowerCase().includes(searchString);
            return !result;
        }).hide(200);
        $("[id^='tr-']").filter(function () {
            var result = this.innerText.toLowerCase().includes(searchString);
            return result;
        }).show(200);
        $(".search .fa.fa-search").removeClass("fa-search").addClass("fa-times");
    } else {
        $("[id^='tr-']").show(200);
        $(".search .fa.fa-times").removeClass("fa-times").addClass("fa-search");
    }
}