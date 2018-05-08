// JavaScript functions for registration in Delta Summer Camp '18

// var startDay = new Date('2018-07-16'); - перенесено в common.js
var MIN_AGE = 11, MAX_AGE = 18;
var MIN_YEAR = 1950, MAX_YEAR = 2018;

// Вешаем обработчики событий (с проверкой IE > 9)
document.addEventListener("DOMContentLoaded", function (e) {
    if (is.not.ie() || is.ie(9)) {
        bdayInput = document.getElementById("birthday");
        bdayInput.onkeydown = kdHandler;
        bdayInput.onfocus = focus;
        bdayInput.onblur = blur;
    }
});

var focus = function(e) {
    if (e.target.value == "") {
        e.target.value = "__/__/____";
        setCaretPosition(e.target, 0);
    }
};

var blur = function(e) {
    checkDate(e.target);
};

// Возвращает позицию курсора в текстовом поле ввода
// Взято тут: https://stackoverflow.com/questions/10761508/change-html-textbox-overwrite-instead-of-insert-as-user-types
function getCaret(elem) {
    if (elem.selectionStart) {
        return elem.selectionStart;
    } else if (document.selection) {
        elem.focus();

        var r = document.selection.createRange();
        if (r == null) {
            return 0;
        }

        var re = elem.createTextRange(),
            rc = re.duplicate();
        re.moveToBookmark(r.getBookmark());
        rc.setEndPoint('EndToStart', re);

        return rc.text.length;
    }
    return 0;
}

// Устанавливает курсор в определённую позицию текстового поля
// Взято тут: https://stackoverflow.com/questions/10761508/change-html-textbox-overwrite-instead-of-insert-as-user-types
function setCaretPosition(elem, caretPos) {
    if (elem != null) {
        if ('selectionStart' in elem) {
            elem.focus();
            elem.setSelectionRange(caretPos, caretPos);
        } else { // IE 9-
            var range = elem.createTextRange();
            range.move('character', caretPos);
            range.select();
        }
    }
}

// Обработка ввода символов в поле Дня рождения
// В режиме замены формирует строку даты dd/mm/yyyy
// По следам https://stackoverflow.com/questions/10761508/change-html-textbox-overwrite-instead-of-insert-as-user-types
var kdHandler = function (e) {
    var key = e.key;
    var text = e.target.value;
    var caret = getCaret(e.target);

    // Для Safary и IE 8-
    if (key === undefined) {
        var code = e.keyCode;
        switch (code) {
            case 37:
                key = "ArrowLeft";
                break;
            case 39:
                key = "ArrowRight";
                break;
            case 8:
                key = "Backspace";
                break;
            case 46:
                key = "Del";
                break;
            case 13:
                key = "Enter";
                break;
            case 111:
            case 191:
                key = "/";
                break;
            case 109:
            case 189:
                key = "-";
                break;
            case 110:
            case 190:
                key = ".";
                break;
            case 9:
                key = "Tab";
                break;
            case 35:
                key = "End";
                break;
            case 36:
                key = "Home";
                break;
        }

        if (code >= 48 && code <= 57 ) {
            key = (code-48).toString();
        } else if (code >= 96 && code <= 105) {
            key = (code-96).toString();
        }
    }

    // 1. Убираем разделители и корректируем положение курсора
    var parts = text.match(/[\d_]+/g);
    var i;

    text = "";
    for (i = 0; i < parts.length; ++i) {
        text += parts[i];
    }
    if (caret > 5) {
        caret -= 2;
    } else if (caret > 2) {
        caret--;
    }
    var output = text; // то, что потом выводим

    // 2. Анализ введённого символа
    // 2.1 Если нажата цифра - вводим её в режиме замены
    if (/[0-9]/.test(key)) {
        output = text.substring(0, caret);
        // 2.1.1 Если цифра на нулевой позиции и > 3 - переделываем день в '0x'
        if (caret == 0 && key > 3) {
            caret = 2;
            output = '0' + key + text.substring(caret);
        }
        // 2.1.2 Если цифра на позиции 2 и > 1 - переделываем месяц в '0x'
        else if (caret == 2 && key > 1) {
            caret = 4;
            output += '0' + key + text.substring(caret);
        } else if (caret < 8) {
            output = output + key + text.substring(++caret);

        }
    }

    // 2.2 Если нажат разделитель - анализируем день и месяц: если введена лишь первая цифра, ставим перед ней "0"
    else if (/[\/\-\.]/.test(key) || key == "Divide" || key == "Substract" || key == "Decimal") {
        if (caret == 1) {
            output = '0' + text.substring(0, 1) + text.substring(2);
            caret++;
        } else if (caret == 3) {
            output = text.substring(0, 2) + '0' + text.substring(2, 3) + text.substring(4);
            caret++;
        }

    }

    // 2.3 Если нажата стрелка влево - переводим курсор влево, если не на нулевой позиции
    else if ((key == "ArrowLeft" || key == "Left") && caret > 0) {
        caret--;
    }

    // 2.4 Если нажата стрелка вправо - преводим курсор вправо, если не в конце строки
    else if ((key == "ArrowRight" || key == "Right") && caret < 8) {
        caret++;
    }

    // 2.3.1 Если нажата кнопка 'Home' - переводим курсор влево в нулевую позицию
    else if (key == "Home") {
        caret = 0;
    }

    // 2.4.1 Если нажата кнопка 'End' - преводим курсор вправо в 8-ю позицию
    else if (key == "End") {
        caret = 8;
    }

    // 2.5 Если нажата клавиша "забой" - заменяем текущий сивол на "_" и сдвигаем курсор влево, если не на нулевой позиции
    else if (key == "Backspace" && caret > 0) {
        caret--;
        output = text.substring(0, caret) + "_" + text.substring(caret+1);
    }

    // 2.6 Если нажата клавиша "Del" - заменяем текущий символ на "_"
    else if (key == "Del" || key == "Delete") {
        output = text.substring(0, caret) + "_" + text.substring(caret+1);
    }

    // 2.7 Если нажаты клавишы "Tab", "Shift-Tab" или "Enter" - причёсываем и возвращаем true
    else if (key == "Enter" || key == "Tab") {
        return true;
    }
    // 3. Восстанавливаем разделители и корректируем положение курсора
    output = output.substring(0, 2) + "/" + output.substring(2, 4) + "/" + output.substring(4);
    if (caret > 3) {
        caret += 2;
    } else if (caret > 1) {
        caret++;
    }

    e.target.value = output;
    setCaretPosition(e.target, caret);


    return false;
};

// Проверка на корректность даты
// @param input field with date value
// Взято отсюда: https://javascript.ru/forum/events/29223-validnost-daty.html
function checkDate(dateInput) {
    var input = dateInput.value.match(/\d+/g);
    var error =  false;

    if (input.length != 3) {
        error = true;
    }

    if (!error) {
        // Для извращенцев, написавших год одной или двумя цифрами
        if (input[2] < 10) {
            input[2] = '200' + input[2];
        } else if (input[2] < 50) {
            input[2] = '20' + input[2];
        } else if (input[2] < 100) {
            input[2] = '19' + input[2];
        }
        var date = new Date(input[2], input[1] - 1, input[0]);
        if (date.getFullYear() != input[2] || date.getDate() != input[0] || date.getMonth() != input[1] - 1) {
            error = true;
        } else { // Если смогли дату распознать, строим канонический вид даты dd/mm/yyyy и помещаем его в поле формы
            dateInput.value = ('0' + input[0]).slice(-2) + '/' + ('0' + input[1]).slice(-2) + '/' + input[2];
        }

        if (!error && input[2] > MIN_YEAR && input[2] < MAX_YEAR) {
            dateInput.style.border = "solid 1px gray";
            document.getElementById("date_error").classList.remove("showed");
            var childAge = age(date, startDay);
            if (childAge < MIN_AGE || childAge > MAX_AGE) {
                document.getElementById("age").style.color = 'red';
            } else {
                document.getElementById("age").style.color = '';
            }
            document.getElementById("age").innerHTML = "<br>Возраст на начало лагеря: " + childAge;
            document.getElementById("ALL_DONE").value = "Ok";
        } else {
            error = true;
        }
    }

    if (error) {
            dateInput.style.border = "dotted 1px red";
            document.getElementById("age").innerHTML = "";
            document.getElementById("date_error").classList.add("showed");
            document.getElementById("ALL_DONE").value = "Error";
    }

    return error;
}

// Проверка на правильность заполнения формы перед отправкой
// Специально для браузеров, не поддерживающих HTML5!
function checkForm() {
    var result = true;
    var i, formInputs = document.getElementsByTagName("input");
    var gdr = false; // Пол определён
    var agreed = false; // Дано согласие на обработку данных
    for (i = 0; i < formInputs.length; i++ ) {
        var inpt = formInputs[i];
        inpt.style.border = "thin solid green";
        document.getElementById("gender").style.paddingLeft = "10px";
        document.getElementById("gender").style.border = "thin solid green";

        if (inpt.required && (inpt.type === "text" || inpt.type === "email" || inpt.type === "tel") &&
            inpt.value === "") {
            inpt.style.border = "thin solid red";
            result = false;
        }
        // проверка правильности даты
        if (checkDate(document.getElementById("birthday"))) {
            result = false;
        }

        // проверка определённости пола и согласия
        else if (inpt.required && inpt.type === "radio" && inpt.checked) {
            gdr = true;
        }
        else if (inpt.type === "checkbox" && inpt.value === "agree" && inpt.checked) {
            agreed = true;
        }
    }
    if (!gdr) {
        document.getElementById("gender").style.border = "thin solid red";
        result = false;
    }
    if (!agreed) {
        document.getElementById("agree").style.border = "thin solid red";
        result = false;
    } else {
        document.getElementById("agree").style.border = "thin solid green";
        result = true;
    }

    if (!result) {
        alert("Обязательное поле не заполнено!");
    }
    if (result) {
        document.getElementById("ALL_DONE").value = "Ok";
    }
    else {
        document.getElementById("ALL_DONE").value = "Error";
    }
    return result;
}