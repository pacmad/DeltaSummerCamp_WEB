$(document).ready(function () {
    $('#add_course_form').on('submit', function (e) {
        e.preventDefault();

        let names = $("[id^='course-']")
            .map(function () {
                return this.value;
            })
            .get()
            .join("");
        let teachers = $("input[type='checkbox']:checked");
        if (names === "") {
            if(!confirm('Нет названия курса. Удалить курс?'))
                return;
        }
        if (teachers.length === 0) {
            alert('Не выделено ни одного преподавателя');
            return;
        }

        this.submit();
    });

    // Изменения в таблице расписания
    $('#t0').on('click', function (e) {
        if (Number($('#tt0').val()) === 0) {
            $('#t0').addClass('course-icon-project');
            $('#tt0').val(1);
        } else {
            $('#t0').removeClass('course-icon-project');
            $('#tt0').val(0);
        }
    });

    $('#t11').on('click', function (e) {
        if (Number($('#tt11').val()) === 0) {
            $('#t11').addClass('course-icon-1');
            $('#tt11').val(1);
        } else {
            $('#t11').removeClass('course-icon-1');
            $('#tt11').val(0);
        }
    });
    $('#t21').on('click', function (e) {
        if (Number($('#tt21').val()) === 0) {
            $('#t21').addClass('course-icon-1');
            $('#tt21').val(1);
        } else {
            $('#t21').removeClass('course-icon-1');
            $('#tt21').val(0);
        }
    });
    $('#t31').on('click', function (e) {
        if (Number($('#tt31').val()) === 0) {
            $('#t31').addClass('course-icon-1');
            $('#tt31').val(1);
        } else {
            $('#t31').removeClass('course-icon-1');
            $('#tt31').val(0);
        }
    });
    $('#t12').on('click', function (e) {
        if (Number($('#tt12').val()) === 0) {
            $('#t12').addClass('course-icon-1');
            $('#tt12').val(1);
        } else {
            $('#t12').removeClass('course-icon-1');
            $('#tt12').val(0);
        }
    });
    $('#t22').on('click', function (e) {
        if (Number($('#tt22').val()) === 0) {
            $('#t22').addClass('course-icon-1');
            $('#tt22').val(1);
        } else {
            $('#t22').removeClass('course-icon-1');
            $('#tt22').val(0);
        }
    });
    $('#t32').on('click', function (e) {
        if (Number($('#tt32').val()) === 0) {
            $('#t32').addClass('course-icon-1');
            $('#tt32').val(1);
        } else {
            $('#t32').removeClass('course-icon-1');
            $('#tt32').val(0);
        }
    });
});