/*
 *  Установка пользователю UID флага 'ststus' в значение s
 */
function setDateWorkSent(UID) {
	var xhttp = new XMLHttpRequest();
	xhttp.open("GET", "mycabinet.php?id=" + UID + "&SetStatus=2", true);
	xhttp.send();
}

// encode(decode) html text into html entity
// from https://gist.github.com/CatTail/4174511
var decodeHtmlEntity = function(str) {
    return str.replace(/&#(\d+);/g, function(match, dec) {
        return String.fromCharCode(dec);
    });
};

var encodeHtmlEntity = function(str) {
    var buf = [];
    for (var i=str.length-1;i>=0;i--) {
        buf.unshift(['&#', str[i].charCodeAt(), ';'].join(''));
    }
    return buf.join('');
};


$(document).ready(function () {

    $("#fileuploader").uploadFile({ //Подключение библиотеки jquery.uploadfile.js

        fileName: "file",
        sequential: true,
        sequentialCount:4,
        dragDropStr: "",
        maxFileSize:10*1024*1024,
        uploadButtonClass: "uploader"
    });

    $("#imguploader").uploadFile({ // Подключаем библиотеку jquery.uploadfile.js для загрузки фото
        fileName: "file",
        multiple: false,
        maxFileCount: 1,
        dragDropStr: "",
        maxFileSize:10*1024*1024,
        onSuccess:function(files,data,xhr,pd){
            $(".ajax-file-upload-statusbar").hide();
            $("#photo").attr("src", $("#photo").attr("src") + "?" + new Date().getTime());
        }
    });

    /*
        Обработка подсветки элемента для передачи файлов при отсылке решений олимпиады
        .ajax-upload-dragdrop определяется в библиотеке jquery.uploadfile.js
     */

	var dropZone = $('.ajax-upload-dragdrop');

	dropZone[0].ondragover = function () {
        dropZone.addClass('hover');
        return false;
    };

	dropZone[0].ondragleave = function () {
        dropZone.removeClass('hover');
        return false;
    };

	dropZone[0].ondrop = function () {
		event.preventDefault();
        dropZone.removeClass('hover');
        return false;
    };

/*

	Функции заполнения анкеты

 */
    var options = {};
	$("#btnOwnTel").hide();
	$("#ownTelForm").removeClass("active");
	$("#btnComingDetails").hide();
    $("#comeInForm").removeClass("active");
	$("#btnLeavingDetails").hide();
    $("#leaveOutForm").removeClass("active");
	$("#btnHealthDetails").hide();
    $("#healthForm").removeClass("active");
	$("#btnInsuranceDetails").hide();
    $("#insuranceForm").removeClass("active");
	$("#btnNotesDetails").hide();
    $("#notesForm").removeClass("active");
	$("#btnOtherDetails").hide();
    $("#otherForm").removeClass("active");

/*

    Блок личного телефонного номера

 */
	options = {
	    success: function (data, status) {
            if (status === "success") {
                $("#btnOwnTel").hide();
                $("#ownTelForm").removeClass("active");
            }
        }
    };
	$("#ownTelForm").ajaxForm(options);

	$("#ownTel").on('input', function () {
        $("#btnOwnTel").show();
        $("#ownTelForm").addClass("active");
    });

/*

    Блок детали поездки туда

 */

    options = {
        beforeSubmit: function (arr, $form, options) { // Необходимо передать значения тех полей, которые могут быть disabled
            arr.push(
                {
                    name: "comingDate",
                    required: true,
                    type: "input",
                    value: $("#comingDate").val()
                },
                {
                    name: "comingTime",
                    required: true,
                    type: "input",
                    value: $("#comingTime").val()
                },
                {
                    name: "comingFlight",
                    required: true,
                    type: "input",
                    value: $("#comingFlight").val()
                },
                {
                    name: "comingPlace",
                    required: true,
                    type: "select-one",
                    value: $("#comingPlace").val()
                }
            );
        },
        success: function (data, status) {
            if (status === "success") {
                $("#btnComingDetails").hide();
                $("#comeInForm").removeClass("active");
            }
        }
    };
    $("#comeInForm").ajaxForm(options);

    $("#comingWith").on('change', function () {
        if (this.value === "DimaAnya") {
            $("#comingDate").val("15/07/2018").attr('disabled', 'disabled');
            $("#comingTime").val("13:10").attr('disabled', 'disabled');
            $("#comingPlace").append(new Option('Аэропорт "Домодедово"', 'DME', false, true)).attr('disabled', 'disabled');
            $("#comingFlight").val("LH2529").attr('disabled', 'disabled');
        } else if (this.value === "Dubeniuk") {
            $("#comingDate").val("16/07/2018").attr('disabled', 'disabled');
            $("#comingTime").val("7:30").attr('disabled', 'disabled');
            $("#comingPlace").append(new Option('Аэропорт "Внуково"', 'VKO', false, true)).attr('disabled', 'disabled');
            $("#comingFlight").val("UT799").attr('disabled', 'disabled');
        } else if (this.value === "WithParents") {
            $("#comingDate").val("16/07/2018").removeAttr('disabled');
            $("#comingTime").val("").removeAttr('disabled');
            $("#comingPlace").val("gorod").removeAttr('disabled');
            $("#comingFlight").val("-").attr('disabled', 'disabled');
        } else {
            $("#comingDate").val("16/07/2018").removeAttr('disabled');
            $("#comingTime").val("").removeAttr('disabled');
            $("#comingPlace").removeAttr('disabled');
            $("#comingFlight").val("").removeAttr('disabled');
            $("#comingPlace option[value='DME']").remove();
            $("#comingPlace option[value='VKO']").remove();
        }
        $("#btnComingDetails").show();
        $("#comeInForm").addClass("active");
    });

    $("#comingDate").on('input', function () {
        $("#btnComingDetails").show();
        $("#comeInForm").addClass("active");
    });
    
    $("#comingTime").on('input', function () {
        $("#btnComingDetails").show();
        $("#comeInForm").addClass("active");
    });
    
    $("#comingFlight").on('input', function () {
        $("#btnComingDetails").show();
        $("#comeInForm").addClass("active");
    });
    
    $("#comingPlace").on('change', function () {
        $("#btnComingDetails").show();
        $("#comeInForm").addClass("active");
    });

/*

    Блок деталей поездки обратно

 */

    options = {
        beforeSubmit: function (arr, $form, options) { // Необходимо передать значения тех полей, которые могут быть disabled
            arr.push(
                {
                    name: "leavingDate",
                    required: true,
                    type: "input",
                    value: $("#leavingDate").val()
                },
                {
                    name: "leavingTime",
                    required: true,
                    type: "input",
                    value: $("#leavingTime").val()
                },
                {
                    name: "leavingFlight",
                    required: true,
                    type: "input",
                    value: $("#leavingFlight").val()
                },
                {
                    name: "leavingPlace",
                    required: true,
                    type: "select-one",
                    value: $("#leavingPlace").val()
                }
            );
        },
        success: function (data, status) {
            if (status === "success") {
                $("#btnLeavingDetails").hide();
                $("#leaveOutForm").removeClass("active");
            }
        }
    };
    $("#leaveOutForm").ajaxForm(options);

    $("#leavingWith").on('change', function () {
        if (this.value === "DimaAnya") {
            $("#leavingDate").val("30/07/2018").attr('disabled', 'disabled');
            $("#leavingTime").val("23:55").attr('disabled', 'disabled');
            $("#leavingPlace").append(new Option('Аэропорт "Домодедово"', 'DME', false, true)).attr('disabled', 'disabled');
            $("#leavingFlight").val("LH2530").attr('disabled', 'disabled');
        } else if (this.value === "Dubeniuk") {
            $("#leavingDate").val("30/07/2018").attr('disabled', 'disabled');
            $("#leavingTime").val("17:40").attr('disabled', 'disabled');
            $("#leavingPlace").append(new Option('Аэропорт "Внуково"', 'VKO', false, true)).attr('disabled', 'disabled');
            $("#leavingFlight").val("UT800").attr('disabled', 'disabled');
        } else if (this.value === "WithParents") {
            $("#leavingDate").val("30/07/2018").removeAttr('disabled');
            $("#leavingTime").val("").removeAttr('disabled');
            $("#leavingPlace").val("gorod").removeAttr('disabled');
            $("#leavingFlight").val("-").attr('disabled', 'disabled');
        } else {
            $("#leavingDate").val("30/07/2018").removeAttr('disabled');
            $("#leavingTime").val("").removeAttr('disabled');
            $("#leavingPlace").removeAttr('disabled');
            $("#leavingFlight").val("").removeAttr('disabled');
            $("#leavingPlace option[value='DME']").remove();
            $("#leavingPlace option[value='VKO']").remove();
        }
        $("#btnLeavingDetails").show();
        $("#leaveOutForm").addClass("active");
    });

    $("#leavingDate").on('input', function () {
        $("#btnLeavingDetails").show();
        $("#leaveOutForm").addClass("active");
    });

    $("#leavingTime").on('input', function () {
        $("#btnLeavingDetails").show();
        $("#leaveOutForm").addClass("active");
    });

    $("#leavingFlight").on('input', function () {
        $("#btnLeavingDetails").show();
        $("#leaveOutForm").addClass("active");
    });

    $("#leavingPlace").on('change', function () {
        $("#btnLeavingDetails").show();
        $("#leaveOutForm").addClass("active");
    });

/*

    Блоки "здоровье", "Страховка", "Указания" и "Другое"

 */

    options = {
        success: function (data, status) {
            if (status === "success") {
                $("#btnHealthDetails").hide();
                $("#healthForm").removeClass("active");
            }
        }
    };
    $("#healthForm").ajaxForm(options);

    $("#healthText").on('input', function () {
        $("#btnHealthDetails").show();
        $("#healthForm").addClass("active");
    });

    options = {
        success: function (data, status) {
            if (status === "success") {
                $("#btnInsuranceDetails").hide();
                $("#insuranceForm").removeClass("active");
            }
        }
    };
    $("#insuranceForm").ajaxForm(options);

    $("#insuranceText").on('input', function () {
        $("#btnInsuranceDetails").show();
        $("#insuranceForm").addClass("active");
    });

    options = {
        success: function (data, status) {
            if (status === "success") {
                $("#btnNotesDetails").hide();
                $("#notesForm").removeClass("active");
            }
        }
    };
    $("#notesForm").ajaxForm(options);

    $("#notesText").on('input', function () {
        $("#btnNotesDetails").show();
        $("#notesForm").addClass("active");
    });

    options = {
        beforeSubmit: function (arr, $form, options) {
            arr[3].value = encodeHtmlEntity($("#certName").val());  // Важно! Сменить индекс при изменении полей формы!
        },
        success: function (data, status) {
            if (status === "success") {
                $("#btnOtherDetails").hide();
                $("#otherForm").removeClass("active");
            }
        }
    };
    $("#otherForm").ajaxForm(options);

    $("#certLang").on('change', function () {
        if ($("#certName").val() !== "") {
            $("#btnOtherDetails").show();
        }
        $("#otherForm").addClass("active");
    });
    $("#certName").on('input', function () {
        if ($("#certName").val() !== "") {
            $("#btnCertName").show();
        } else {
            $("#btnCertName").hide();
        }
        $("#otherForm").addClass("active");
    });
    $("#visa_no").on('change', function () {
        $("#btnOtherDetails").show();
        $("#otherForm").addClass("active");
    });
    $("#visa_yes").on('change', function () {
        $("#btnOtherDetails").show();
        $("#otherForm").addClass("active");
    });
    $("#notebook").on('change', function () {
        $("#btnOtherDetails").show();
        $("#otherForm").addClass("active");
    });
    $("#shirt").on('change', function () {
        $("#btnOtherDetails").show();
        $("#otherForm").addClass("active");
    });
});