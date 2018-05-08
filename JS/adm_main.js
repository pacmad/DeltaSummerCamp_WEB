var Sort = '';

// ������������� ���������
$(document).ready(function () {
    // ����� ������
    fetchData("Surname");
});

// ����� ������ ���������� ������ � ������� �������� list_data (��. ���� admMain.inc)
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

// ����� �������� ���������
function showDetails(UID) {
    $.post("phplib/admFetch.php",
        {View: "Details", ID: UID},
        function (data) {
            $("#list_data").html(data);
        },
        "html");
}

// ����� ��������� �������� � ������ ����������
function showNext() {
    $.post("phplib/admFetch.php",
        {View: "Details", ID: "+1"},
        function (data) {
            $("#list_data").html(data);
        },
        "html");
}

// ����� ���������� �������� � ������ ����������
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

// ����������/�������� �������������� ���������� � �������� ���������
function showMoreInfo() {
    $("#more_info").toggle(300);
}

// ����� �� ������
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