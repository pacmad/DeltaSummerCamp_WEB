// Инициализация странички
$(document).ready(function () {
    // ToDo: установить лиснер быстрого поиска
});

// Вывод данных участников лагеря в элемент страницы list_data (см. файл admMain.inc)
function fetchData(SortType) {
    $.post("phplib/admFetch.php",
        {
            SortBy: SortType,
        },
        function (data) {
            $("#list_data").html(data);
        },
        "html");
}

// Вывод карточки участника, если replace <> 0 замещаем предыдущую карточку новой
function showDetails(UID, replace = 0) {
    if (replace) location.replace("student.php?ID=" + UID);
    else document.location.assign("student.php?ID=" + UID);
}

// Вывод списка со скроллом до записи с данным ID. Если ID=0 - будет использовано значение, сохранённое в сессии.
function showList(ID) {
    $.post("phplib/admFetch.php",
        {
            ID: ID,
        },
        function (data) {
            $("#list_data").html(data);
        },
        "html");
}


// Поиск по списку
function doSearch() {
    if($(".search .fa").hasClass("fa-times")) {
        $("#search_string").val("");
    }
    search();
}
function search() {
    const searchString = $("#search_string").focus().val().toLowerCase();
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