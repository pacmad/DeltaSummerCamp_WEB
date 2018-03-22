$(document).ready(function () {
    function fetch_data() {
        $.post("phplib/admFetch.php",
            {SortBy: "Name"},
            function (data) {
                $('#list_data').html(data);
            },
            "html");
    }

    fetch_data();
});

// Смена сортировки
function changeSort(SortType) {
    $.post("phplib/admFetch.php",
        {SortBy: SortType},
        function (data) {
            $('#list_data').html(data);
        },
        "html");
}

// Отмечает или снимает отметку строки при тыке
function checkIt(ID) {
    var chkBox = document.getElementById('cb-' + ID);

    chkBox.checked = !chkBox.checked;
}
