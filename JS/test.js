// ������ ����������� �������
document.addEventListener("DOMContentLoaded", function(e) {
    bdayInput = document.getElementById("myDate");
    bdayInput.onkeydown = kdHandler;
    bdayInput.onfocus = focus;
    bdayInput.onblur = blur;
});


var focus = function(e) {
    e.target.style.border = "thin solid blue";
    e.target.style.backgroundColor = "#CFC";

    if (e.target.value == "") {
        e.target.value = "__/__/____";
        setCaretPosition(e.target, 0);
    }
};

var blur = function(e) {
    e.target.style.border = "thin solid gray";
    e.target.style.backgroundColor = "#FFF";
    checkDate(e.target);
};

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

// ��������� ����� �������� � ���� ��� ��������
// � ������ ������ ��������� ������ ���� dd/mm/yyyy
// �� ������ https://stackoverflow.com/questions/10761508/change-html-textbox-overwrite-instead-of-insert-as-user-types
var kdHandler = function (e) {
    var key = e.key;
    var text = e.target.value;
    var caret = getCaret(e.target);

    // ��� Safary � IE 8-
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

    // 1. ������� ����������� � ������������ ��������� �������
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
    var output = text; // ��, ��� ����� �������

    // 2. ������ ��������� �������
    // 2.1 ���� ������ ����� - ������ � � ������ ������
    if (/[0-9]/.test(key)) {
        output = text.substring(0, caret);
    // 2.1.1 ���� ����� �� ������� ������� � > 3 - ������������ ���� � '0x'
        if (caret == 0 && key > 3) {
            caret = 2;
            output = '0' + key + text.substring(caret);
        }
    // 2.1.2 ���� ����� �� ������� 2 � > 1 - ������������ ����� � '0x'
        else if (caret == 2 && key > 1) {
            caret = 4;
            output += '0' + key + text.substring(caret);
        } else if (caret < 8) {
            output = output + key + text.substring(++caret);

        }
    }

    // 2.2 ���� ����� ����������� - ����������� ���� � �����: ���� ������� ���� ������ �����, ������ ����� ��� "0"
    else if (/[\/\-\.]/.test(key) || key == "Divide" || key == "Substract" || key == "Decimal") {
        if (caret == 1) {
            output = '0' + text.substring(0, 1) + text.substring(2);
            caret++;
        } else if (caret == 3) {
            output = text.substring(0, 2) + '0' + text.substring(2, 3) + text.substring(4);
            caret++;
        }

    }

    // 2.3 ���� ������ ������� ����� - ��������� ������ �����, ���� �� �� ������� �������
    else if ((key == "ArrowLeft" || key == "Left") && caret > 0) {
        caret--;
    }

    // 2.4 ���� ������ ������� ������ - �������� ������ ������, ���� �� � ����� ������
    else if ((key == "ArrowRight" || key == "Right") && caret < 8) {
        caret++;
    }

    // 2.3.1 ���� ������ ������ 'Home' - ��������� ������ ����� � ������� �������
    else if (key == "Home") {
        caret = 0;
    }

    // 2.4.1 ���� ������ ������ 'End' - �������� ������ ������ � 8-� �������
    else if (key == "End") {
        caret = 8;
    }

    // 2.5 ���� ������ ������� "�����" - �������� ������� ����� �� "_" � �������� ������ �����, ���� �� �� ������� �������
    else if (key == "Backspace" && caret > 0) {
        caret--;
        output = text.substring(0, caret) + "_" + text.substring(caret+1);
    }

    // 2.6 ���� ������ ������� "Del" - �������� ������� ������ �� "_"
    else if (key == "Del" || key == "Delete") {
        output = text.substring(0, caret) + "_" + text.substring(caret+1);
    }

    // 2.7 ���� ������ ������� "Tab", "Shift-Tab" ��� "Enter" - ���������� true
    else if (key == "Enter" || key == "Tab") {
        return true;
    }
    // 3. ��������������� ����������� � ������������ ��������� �������
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

// �������� �� ������������ ����
function checkDate(dateInput) { // ����� ������: https://javascript.ru/forum/events/29223-validnost-daty.html

    var input = dateInput.value.match(/\d+/g);

    if (input.length == 3) {
        var date = new Date(input[2], input[1] - 1, input[0]);
        if (date.getFullYear() == input[2] && date.getDate() == input[0] && date.getMonth() == input[1] - 1) {
            var year = date.getFullYear();
            if (year < 2018 && year > 1950) {
                dateInput.style.border = "solid 1px green";
            } else {
                alert('Wrong date');
                dateInput.style.border = "dotted 1px red";
            }
        } else {
            alert('Wrong date');
            dateInput.style.border = "dotted 1px red";
        }
    } else {
        alert('Wrong date');
        dateInput.style.border = "dotted 1px red";
    }
}
