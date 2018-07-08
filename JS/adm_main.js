// ������������� ���������
$(document).ready(function () {
    // ��������� ������ � ������ ����� 'back'
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

    if (document.getElementById("details") != null) { // ������� �� ���������, �.�. $(document).ready ����������� ���� ��� ������ �������� ������.
/* @ToDo: ������� ���������� �������� �����-������
        if (is.touchDevice()) {        // ������ ����������� �������� �����-������ �
        }
*/

    }
    // ����� ������
    fetchData("Surname");
});

// ����� ������ ���������� ������ � ������� �������� list_data (��. ���� admMain.inc)
function fetchData(SortType) {
    if (JSON.stringify(history.state) === 'null') { // ������ ���-�� ���� about:blank ��� ����� ��������
        history.pushState({
            'view': 'init'
        }, '������ ��������������, ������ ����������', 'admin.php');
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

// ����� �������� ���������
function showDetails(UID) {
    if (JSON.stringify(history.state) == null || history.state.view === "init") {
        history.pushState({
            'view': 'detail'
        }, '������ ��������������, �������� ���������', 'admin.php');
    } else {
        history.replaceState({
            'view': 'detail'
        }, '������ ��������������, �������� ���������', 'admin.php');
    }
    $.post("phplib/admFetch.php",
        {View: "Details", ID: UID},
        function (data) {
            $("#list_data").html(data);
        },
        "html");
}

// ����� ������ �� �������� �� ������ � ������ ID. ���� ID=0 - ����� ������������ ��������, ���������� � ������.
function showList(ID) {
    if (JSON.stringify(history.state) == null || history.state.view === "init") {
        history.pushState({
            'view': 'list'
        }, '������ ��������������, ������ ����������', 'admin.php');
    } else {
        history.replaceState({
            'view': 'list'
        }, '������ ��������������, ������ ����������', 'admin.php');
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

// ����� �� ������
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