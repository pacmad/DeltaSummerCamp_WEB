function Focus(elnt) {
    elnt.style.border = "thin solid blue";
    elnt.style.backgroundColor = "#CFC";

    if (elnt.value == "") {
        elnt.value = "__/__/____";
        setCaretPosition(elnt, 0);
    }
}

function Blur(elnt) {
    elnt.style.border = "thin solid gray";
    elnt.style.backgroundColor = "#FFF";
}

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
        if (elem.createTextRange) {
            var range = elem.createTextRange();
            range.move('character', caretPos);
            range.select();
        }
        else {
            if (elem.selectionStart) {
                elem.focus();
                elem.setSelectionRange(caretPos, caretPos);
            }
            else elem.focus();
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

    // 1. Убираем разделители и корректируем положение курсора
    var parts = text.match(/[\d_]+/g);
    var output = '';
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

    // 2. Анализ введённого символа
    // 2.1 Если нажата цифра - вводим её в режиме замены
    output = text.substring(0, caret);
    if (/[0-9]/.test(key)) {
        if (caret < 8) {
            output = output + key + text.substring(++caret);

        }
    }

    // 2.1.1 Если цифра на нулевой позиции и > 3 - переделываем день в '0x'
    // 2.1.2 Если цифра на позиции 2 и > 1 - переделываем месяц в '0x'

    // 2.2 Если нажат разделитель - анализируем день и месяц: если введена лишь первая цифра, ставим перед ней "0"
    else if (/[\/\-\.]/.test(key)) {
        if (caret == 1) {
            output = '0' + text.substring(0, 1) + text.substring(2);
            caret++;
        } else if (caret == 3) {
            output = text.substring(0, 2) + '0' + text.substring(2, 3) + text.substring(4);
            caret++;
        }

    }

    // 2.3 Если нажата стрелка влево - переводим курсор влево, если не на нулевой позиции
    // 2.4 Если нажата стрелка вправо - преводим курсор вправо, если не в конце строки (или в конце заполненной строки?
    // 2.5 Если нажата клавиша "забой" - заменяем текущий сивол на "_" и сдвигаем курсор влево, если не на нулевой позиции
    // 2.6 Если нажата клавиша "Del" - заменяем текущий символ на "_"
    // 2.7 Если нажаты клавишы "Tab", "Shift-Tab" или "Enter" - возвращаем true
    // 3. Восстанавливаем разделители и корректируем положение курсора
    output = output.substring(0, 2) + "/" + output.substring(2, 4) + "/" + output.substring(4);
    if (caret > 3) {
        caret += 2;
    } else if (caret > 1) {
        caret++;
    }

    e.target.value = output;
    setCaretPosition(e.target, caret);


/*
    var output = text.substring(0, caret);
    if (/[0-9]/.test(key)) {
        if (caret < 10) {
            e.target.value = output + key + text.substring(caret + 1);
            setCaretPosition(e.target, ++caret);
            if (caret == 2 || caret == 5) {
                setCaretPosition(e.target, caret + 1);
            }
        }
    } else if (key == '/' || key == '.' || key == '-') {
        if (caret == 1) {
            e.target.value = '0' +  output + text.substring(caret+1);
            caret += 2;
        } else if (caret == 4) {
            e.target.value = text.substring(0, caret-1) + '0' + text.substring(caret-1,caret) + text.substring(caret+1);
            caret += 2;
        }
        setCaretPosition(e.target, caret);
    } else if (key == "Backspace") {
        if (caret == 3 || caret == 6) {
            setCaretPosition(e.target, caret - 1);
        } else if (caret > 0) {
            var parts = text.match(/\d+/g);
            output = '';
            var i, index = caret;
            for (i = 0; i < parts.length; ++i) {
                output += parts[i];
            }
            if (caret > 5) {
                index -= 2;
            } else if (caret > 2) {
                index--;
            }
            output = output.substring(0, index - 1) + output.substring(index + 1);
            for (i=output.length; i < 8; ++i) {
                output += '_';
            }
            output = output.substring(0, 2) + '/' + output.substring(2, 4) + '/' + output.substring(4);
            e.target.value = output;
            --caret;
            if (caret == 3 || caret == 6) {
                --caret;
            }
            setCaretPosition(e.target, caret);
        }
    } else if (key == "ArrowLeft") {
        if (caret > 0) {
            --caret;
            if (caret == 3 || caret == 6) {
                --caret;
            }
            setCaretPosition(e.target, caret);
        }
    }
    */
    return false;
};

// Установка обработчиков событий и т.д.
function init(){
    document.getElementById("myDate").onkeydown = kdHandler;
}

// Проверка на корректность даты
function checkDate(dateInput) { // Взято отсюда: https://javascript.ru/forum/events/29223-validnost-daty.html

    alert("Hi");

    var input = dateInput.value.match(/\d+/g), date;

    if (input.length == 3) {
        date = new Date(input[2], input[1]-1, input[0]);
    }

    if (!(input.length == 3 && date.getFullYear() == input[2] && date.getDate() == input[0] && date.getMonth() == input[1] - 1)) {
        date_input.style.border = "dotted 1px red";
        document.getElementById("age").innerHTML = "";
        document.getElementById("date_error").classList.add("showed");
        document.getElementById("ALL_DONE").value = "Error";
    } else {
        date_input.style.border = "none 1px gray";
        document.getElementById("date_error").classList.remove("showed");
        var child_age = age(date, beginday);
        if (child_age < MIN_AGE || child_age > MAX_AGE) {
            document.getElementById("age").style.color = 'red';
        } else {
            document.getElementById("age").style.color = '';
        }
        document.getElementById("age").innerHTML = "<br>Возраст на начало лагеря: " + child_age;
        document.getElementById("ALL_DONE").value = "Ok";
    }
}
