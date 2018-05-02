var Sort = '';

// ����� ������ ���������� ������ � ������� �������� list_data (��. ���� admMain.inc)
function fetchData(SortType) {
    $.post("phplib/admFetch.php",
        {SortBy: SortType},
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
        {View: "List", ID: ID},
        function (data) {
            $("#list_data").html(data);
        },
        "html");
}

// ������������� ���������
$(document).ready(function () {
    fetchData("Name");

});

// �������� ��� ������� ������� ������ ��� ����
function checkIt(ID) {
    var chkBox = document.getElementById('cb-' + ID);

    chkBox.checked = !chkBox.checked;
}

// ����������/�������� �������������� ���������� � �������� ���������
function showMoreInfo() {
    $("#more_info").toggle(300);
}
