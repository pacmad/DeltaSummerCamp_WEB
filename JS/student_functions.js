/*
    Функции карточки участника
 */
// Инициализация странички
$(document).ready(function () {
    let appStatusOptions = {
        success: function (data, status) {
            if(status === "success") {
                $("#status").html(data);
            }
        }
    };
    $("#SetAppStatus").ajaxForm(appStatusOptions);
//     // @ToDo: сделать обработку стрелок "влево" и "вправо"
//     //window.addEventListener()
//
//     if (document.getElementById("details") != null) { // никогда не сработает, т.к. $(document).ready срабатывает лишь при первой загрузке списка.
// /* @ToDo: сделать обработчик листаний влево-вправо
//         if (is.touchDevice()) {        // вешаем обработчики листаний влево-вправо и
//         }
// */
});

// Вывод карточки участника, если replace <> 0 замещаем предыдущую карточку новой
function showDetails(UID, replace = 0) {
    if (replace) location.replace("student.php?ID=" + UID);
    else document.location.assign("student.php?ID=" + UID);
}

// Вывод следующей карточки с учётом сортировки
function showNext() {
    $.post("student.php",
        {ID: "+1"},
        function (data) {
            $("#list_data").html(data);
        },
        "html");
}

// Вывод предыдущей карточки с учётом сортировки
function showPrev() {
    $.post("student.php",
        {ID: "-1"},
        function (data) {
            $("#list_data").html(data);
        },
        "html");
}
