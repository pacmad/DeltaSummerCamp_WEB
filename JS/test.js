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

// ���������� ������� ������� � ��������� ���� �����
// ����� ���: https://stackoverflow.com/questions/10761508/change-html-textbox-overwrite-instead-of-insert-as-user-types
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

// ������������� ������ � ����������� ������� ���������� ����
// ����� ���: https://stackoverflow.com/questions/10761508/change-html-textbox-overwrite-instead-of-insert-as-user-types
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

// ��������� ����� �������� � ���� ��� ��������
// � ������ ������ ��������� ������ ���� dd/mm/yyyy
// �� ������ https://stackoverflow.com/questions/10761508/change-html-textbox-overwrite-instead-of-insert-as-user-types
var kdHandler = function (e) {
    var key = e.key;
    var text = e.target.value;
    var caret = getCaret(e.target);

    // 1. ������� ����������� � ������������ ��������� �������
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

    // 2. ������ ��������� �������
    // 2.1 ���� ������ ����� - ������ � � ������ ������
    output = text.substring(0, caret);
    if (/[0-9]/.test(key)) {
        if (caret < 8) {
            output = output + key + text.substring(++caret);

        }
    }

    // 2.1.1 ���� ����� �� ������� ������� � > 3 - ������������ ���� � '0x'
    // 2.1.2 ���� ����� �� ������� 2 � > 1 - ������������ ����� � '0x'

    // 2.2 ���� ����� ����������� - ����������� ���� � �����: ���� ������� ���� ������ �����, ������ ����� ��� "0"
    else if (/[\/\-\.]/.test(key)) {
        if (caret == 1) {
            output = '0' + text.substring(0, 1) + text.substring(2);
            caret++;
        } else if (caret == 3) {
            output = text.substring(0, 2) + '0' + text.substring(2, 3) + text.substring(4);
            caret++;
        }

    }

    // 2.3 ���� ������ ������� ����� - ��������� ������ �����, ���� �� �� ������� �������
    // 2.4 ���� ������ ������� ������ - �������� ������ ������, ���� �� � ����� ������ (��� � ����� ����������� ������?
    // 2.5 ���� ������ ������� "�����" - �������� ������� ����� �� "_" � �������� ������ �����, ���� �� �� ������� �������
    // 2.6 ���� ������ ������� "Del" - �������� ������� ������ �� "_"
    // 2.7 ���� ������ ������� "Tab", "Shift-Tab" ��� "Enter" - ���������� true
    // 3. ��������������� ����������� � ������������ ��������� �������
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

// ��������� ������������ ������� � �.�.
function init(){
    document.getElementById("myDate").onkeydown = kdHandler;
}

// �������� �� ������������ ����
function checkDate(dateInput) { // ����� ������: https://javascript.ru/forum/events/29223-validnost-daty.html

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
        document.getElementById("age").innerHTML = "<br>������� �� ������ ������: " + child_age;
        document.getElementById("ALL_DONE").value = "Ok";
    }
}
