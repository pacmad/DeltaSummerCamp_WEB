// Инициализация странички
$(document).ready(function () {
    // Обработка выхода в список через 'back'
    window.addEventListener('popstate', function (ev) {
        if (ev.state != null) {
            if (ev.state.view === "list" || ev.state.view === "init") {
                showList(0);
            }
            else if (ev.state.view === "detail") {
                showDetails(0);
            }
        }
    });

    // @ToDo: сделать обработку стрелок "влево" и "вправо"
    //window.addEventListener()

    if (document.getElementById("details") != null) { // никогда не сработает, т.к. $(document).ready срабатывает лишь при первой загрузке списка.
/* @ToDo: сделать обработчик листаний влево-вправо
        if (is.touchDevice()) {        // вешаем обработчики листаний влево-вправо и
        }
*/

    }
    // Вывод данных
    fetchData("Surname");
});

// Вывод данных участников лагеря в элемент страницы list_data (см. файл admMain.inc)
function fetchData(SortType) {
    if (JSON.stringify(history.state) === 'null') { // позади что-то типа about:blank или чужая страница
        history.pushState({
            'view': 'init'
        }, 'Уголок администратора, список участников', 'admin.php');
    }
    $.post("phplib/admFetch.php",
        {
            SortBy: SortType,
            View: 'List',
            Init: 'Init'
        },
        function (data) {
            $("#list_data").html(data);
        },
        "html");
}

// Вывод карточки участника
function showDetails(UID) {
    if (JSON.stringify(history.state) == null || history.state.view === "init") {
        history.pushState({
            'view': 'detail'
        }, 'Уголок администратора, карточка участника', 'admin.php');
    } else {
        history.replaceState({
            'view': 'detail'
        }, 'Уголок администратора, карточка участника', 'admin.php');
    }
    $.post("phplib/admFetch.php",
        {View: "Details", ID: UID},
        function (data) {
            $("#list_data").html(data);
        },
        "html");
}

// Вывод списка со скроллом до записи с данным ID. Если ID=0 - будет использовано значение, сохранённое в сессии.
function showList(ID) {
    if (JSON.stringify(history.state) == null || history.state.view === "init") {
        history.pushState({
            'view': 'list'
        }, 'Уголок администратора, список участников', 'admin.php');
    } else {
        history.replaceState({
            'view': 'list'
        }, 'Уголок администратора, список участников', 'admin.php');
    }

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