function showChange() {
    $("[id^='CID-']").removeClass('hidden');
    $("#change_button").addClass('hidden');
    $("#add_course").removeClass('hidden');
}

// Иммитация POST-запроса
// Взято отсюда: https://developer.mozilla.org/en-US/docs/Learn/HTML/Forms/Sending_forms_through_JavaScript
// data - массив пар имя:дата
// url - адрес запроса
// Usage: onclick="postDataToUrl({test:'ok'}, 'http://www.test.com')
function postDataToUrl(data, url) {
    var XHR = new XMLHttpRequest();
    var urlEncodedData = "";
    var urlEncodedDataPairs = [];
    var name;

    // Turn the data object into an array of URL-encoded key/value pairs.
    for(name in data) {
        urlEncodedDataPairs.push(encodeURIComponent(name) + '=' + encodeURIComponent(data[name]));
    }

    // Combine the pairs into a single string and replace all %-encoded spaces to
    // the '+' character; matches the behaviour of browser form submissions.
    urlEncodedData = urlEncodedDataPairs.join('&').replace(/%20/g, '+');

    // Define what happens on successful data submission
    XHR.addEventListener('load', function(event) {
        alert('Yeah! Data sent and response loaded.');
    });

    // Define what happens in case of error
    XHR.addEventListener('error', function(event) {
        alert('Oops! Something goes wrong.');
    });

    // Set up our request
    XHR.open('POST', url);

    // Add the required HTTP header for form data POST requests
    XHR.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    // Finally, send our data.
    XHR.send(urlEncodedData);
}

function goToTimetable(CID) {
    postDataToUrl({CID:CID}, 'timetable.php');
}

$(document).ready(function () {

    // Валидация формы
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
            alert('Нет названия курса');
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