// JavaScript functions for registration
var beginday = new Date('2018-07-16');
var MIN_AGE = 11, MAX_AGE = 18;

// Возвращает полное число лет от birthday до date
function age(birthday, beginday) {
	var years = beginday.getFullYear()-birthday.getFullYear();
	if ((beginday.getMonth()-birthday.getMonth())<0) {
	    return years-1;
    }
    if ((beginday.getMonth()-birthday.getMonth())==0 && (beginday.getDate()-birthday.getDate())<0) {
	        return years-1;
    }
	return years;
}

// Проверка на корректность даты
function checkDate(date_input) { // Взято отсюда: https://javascript.ru/forum/events/29223-validnost-daty.html
    var input = date_input.value.match(/\d+/g);

    if (input.length == 3) {
        var date = new Date(input[2], input[1] - 1, input[0]);
        if (date.getFullYear() == input[2] || date.getDate() == input[0] || date.getMonth() == input[1] - 1) {
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
    } else {
            date_input.style.border = "dotted 1px red";
            document.getElementById("age").innerHTML = "";
            document.getElementById("date_error").classList.add("showed");
            document.getElementById("ALL_DONE").value = "Error";
    }
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

        if (inpt.required &&
            (inpt.type === "text" || inpt.type === "email" || inpt.type === "tel") &&
            inpt.value === "")
        {
            inpt.style.border = "thin solid red";
            result = false;
        }
        // проверка определённости пола
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