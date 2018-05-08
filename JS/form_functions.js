// JavaScript functions for registration in Delta Summer Camp '18

// var startDay = new Date('2018-07-16'); - ���������� � common.js
var MIN_AGE = 11, MAX_AGE = 18;
var MIN_YEAR = 1950, MAX_YEAR = 2018;

// ������ ����������� ������� (� ��������� IE > 9)
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

    // 2.7 ���� ������ ������� "Tab", "Shift-Tab" ��� "Enter" - ����������� � ���������� true
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
// @param input field with date value
// ����� ������: https://javascript.ru/forum/events/29223-validnost-daty.html
function checkDate(dateInput) {
    var input = dateInput.value.match(/\d+/g);
    var error =  false;

    if (input.length != 3) {
        error = true;
    }

    if (!error) {
        // ��� �����������, ���������� ��� ����� ��� ����� �������
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
        } else { // ���� ������ ���� ����������, ������ ������������ ��� ���� dd/mm/yyyy � �������� ��� � ���� �����
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
            document.getElementById("age").innerHTML = "<br>������� �� ������ ������: " + childAge;
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

// �������� �� ������������ ���������� ����� ����� ���������
// ���������� ��� ���������, �� �������������� HTML5!
function checkForm() {
    var result = true;
    var i, formInputs = document.getElementsByTagName("input");
    var gdr = false; // ��� ��������
    var agreed = false; // ���� �������� �� ��������� ������
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
        // �������� ������������ ����
        if (checkDate(document.getElementById("birthday"))) {
            result = false;
        }

        // �������� ������������� ���� � ��������
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
        alert("������������ ���� �� ���������!");
    }
    if (result) {
        document.getElementById("ALL_DONE").value = "Ok";
    }
    else {
        document.getElementById("ALL_DONE").value = "Error";
    }
    return result;
}