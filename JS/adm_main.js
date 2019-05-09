// Инициализация странички
let $tr = null;
let filter = -99;

$(document).ready(function () {
    $tr = $("[id^='tr-']");
    $summary = $("#summary");
    $("#settings_button").click(
        function () {
            $(".settings_wrapper").removeClass("hidden").fadeIn();
        }
    );
    $("#closeSettings").click(
        function () {
            $(".settings_wrapper").addClass("hidden").fadeOut();
        }
    );
    $("#saveSettings", ".settings").click(
        function () {
            $.post("admin.php", 
                {Save: true},
                function (data, textStatus, jqXHR) {
                    if (data != null) {
                        $('#saveSettings', '.settings').children('span').css('color', '#00AA00');
                    }
                });
    });

    // Включаем/выключаем показ Total:
    function showStat() {
        if($summary.is(':checked')) {
            $("#stat_line").removeClass('invisible');
        } else {
            $("#stat_line").addClass('invisible');
        }
    }

    $summary.click(function () {
        if($summary.is(':checked')) {
            $.post("admin.php", {Stat: true});
        } else {
            $.post("admin.php", {Stat: false});
        }
        showStat();
    });

    let $filters = $("input[type='radio']", "#settings");
    $filters.click(
        function () {
            filter = $filters.filter(':checked').val();
            $.post("admin.php", {AppStatus: filter});
            setFilter(filter);
            setBgColor();
        }
    );

    // Учитываем фильтры
    filter = parseInt($("#appStat").text());
    $filters.val([filter]);
    if ($("#stat").text() === "1") {
        $summary.prop('checked', true);
    } else {
        $summary.prop('checked', false);
    }
    setFilter(filter);
    showStat();
    setBgColor();
    setStat();
});

/*
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
*/

// Вывод карточки участника, если replace <> 0 замещаем предыдущую карточку новой
function showDetails(UID, replace = 0) {
    if (replace) location.replace("student.php?ID=" + UID);
    else document.location.assign("student.php?ID=" + UID);
}

/*
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
*/

// Вывод статистики
function stat(g) {
    let count = 0;
    if (g === 'f') {
        count = $(".female:visible", $("#list_data")).length;
    } else if (g === 'm') {
        count = $(".male:visible", $("#list_data")).length;
    }
    return count;
}
function setStat() {
    $("#fem_total").text(stat('f'));
    $("#male_total").text(stat('m'));
}

// Установка фильтра
function setFilter(flt) {
    $tr.show();
    $.each($tr, function (key, value) {
        let appStatus = $('#appStatus', value).html();
        if (parseInt(appStatus) < parseInt(flt))
            $(this).hide();
    });
    setStat();
}

// Поиск по списку
function doSearch() {
    if($(".search .fa").hasClass("fa-times")) {
        $("#search_string").val("");
    }
    search();
}
// Обработка поиска:
// - накладываем фильтр
// - скрываем неподошедшее
// - обновляем фон
function search() {
    const searchString = $("#search_string").focus().val().toLowerCase();
    setFilter(filter);
    if (searchString !== "") {
        $tr.filter(function () {
            let result = this.innerText.toLowerCase().includes(searchString);
            return !result;
        }).hide();
/*
        $tr.filter(function () {
            let result = this.innerText.toLowerCase().includes(searchString);
            return result;
        }).show();
*/
        $(".search .fa.fa-search").removeClass("fa-search").addClass("fa-times");
    } else {
        $tr.show();
        setFilter(filter);
        $(".search .fa.fa-times").removeClass("fa-times").addClass("fa-search");
    }
    setBgColor();
    setStat();
}

function setBgColor() {
    $("[id^='tr-']:visible:even").css("background-color", "#BBB");
    $("[id^='tr-']:visible:odd").css("background-color", "#EEE");
}