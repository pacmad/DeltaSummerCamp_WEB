/*
* ������� ������ � ���������������� ������
*/

// �������� ������������ �������� ����� ������� � ������ � ���� ���� jQuery
function saveNewPass(UID) {
    var pass1 = document.getElementById("pass1").value;
    var pass2 = document.getElementById("pass2").value;

    if (pass1 !== pass2) {
        alert("�������� ������ ����������!");
        document.getElementById("newpass").reset();
        return;
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4) {
            if (this.status == 201) { // Created
                alert("������ ����������!");
                location.reload(true);
            } else if (this.status == 400) { // Bad request
                alert("��������, ������ �� ����������!");
                document.getElementById("newpass").reset();
            }
        }
    };
    xhttp.open("POST", "phplib/admSetPass.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("UID=" + UID + "&PASS=" + pass1);
}

// ��������� ����� �������� ������ ����� JQuery ������.
// � ������ ����� � ������ admChkPass.php ����������� ������
// � � ������ �������� ��������� UID
function chkPass(UID) {
    var pass = document.getElementById("pass").value;
    if (pass === "") {
        return false;
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4) {
            if (this.status == 202) { // Accepted
                location.replace(location.origin + location.pathname);
            } else if (this.status == 401) { // Unauthorized
                alert("�������� ������!");
                document.getElementById("passform").reset();
            } else {
                alert("��������� ������, http_response_code: " + this.status);
            }
        }
    };
    xhttp.open("POST", "phplib/admChkPass.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("UID=" + UID + "&PASS=" + pass);

    return false;
}