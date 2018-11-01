/*
 *  Личный кабинет
 */
// Установка пользователю UID флага 'status' в значение 2
function setDateWorkSent(UID) {
	let xhttp = new XMLHttpRequest();
	xhttp.open("GET", "mycabinet.php?id=" + UID + "&SetStatus=2", true);
	xhttp.send();
}

// encode html text into html entity
// from https://gist.github.com/CatTail/4174511
const encodeHtmlEntity = function(str) {
    let buf = [];
    for (let i=str.length-1;i>=0;i--) {
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

	const dropZone = $('.ajax-upload-dragdrop');

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
    let ownTelTimer = 0;
	let ownTelOptions = {
	    success: function (data, status) {
            if (status === "success") {
                if(ownTelTimer !== 0) {
                    clearTimeout(ownTelTimer);
                    ownTelTimer = 0;
                }
                $("#btnOwnTel").hide();
                $("#ownTelForm").removeClass("active");
            }
        }
    };
	$("#ownTelForm").ajaxForm(ownTelOptions);

	$("#ownTel").on('input', function () {
        if (ownTelTimer === 0) {
            ownTelTimer = setTimeout(function () {
                $("#ownTelForm").ajaxSubmit(ownTelOptions);
            }, 3500);
        }
        $("#btnOwnTel").show();
        $("#ownTelForm").addClass("active");
    });

/*

    Блок детали поездки туда

 */
    let comeInTimer = 0;
    let comeInOptions = {
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
                if(comeInTimer !== 0) {
                    clearTimeout(comeInTimer);
                    comeInTimer = 0;
                }
                $("#btnComingDetails").hide();
                $("#comeInForm").removeClass("active");
            }
        }
    };
    $("#comeInForm").ajaxForm(comeInOptions);

    $("#comingWith").on('change', function () {
        if (this.value === "DimaAnya") {
            $("#comingDate").val("15/07/2018").attr('disabled', 'disabled');
            $("#comingTime").val("13:15").attr('disabled', 'disabled');
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
        if (comeInTimer === 0) {
            comeInTimer = setTimeout(function () {
                $("#comeInForm").ajaxSubmit(comeInOptions);
            }, 3500);
        }
        $("#btnComingDetails").show();
        $("#comeInForm").addClass("active");
    });

    $("#comingDate").on('input', function () {
        if (comeInTimer === 0) {
            comeInTimer = setTimeout(function () {
                $("#comeInForm").ajaxSubmit(comeInOptions);
            }, 3500);
        }
        $("#btnComingDetails").show();
        $("#comeInForm").addClass("active");
    });
    
    $("#comingTime").on('input', function () {
        if (comeInTimer === 0) {
            comeInTimer = setTimeout(function () {
                $("#comeInForm").ajaxSubmit(comeInOptions);
            }, 3500);
        }
        $("#btnComingDetails").show();
        $("#comeInForm").addClass("active");
    });
    
    $("#comingFlight").on('input', function () {
        if (comeInTimer === 0) {
            comeInTimer = setTimeout(function () {
                $("#comeInForm").ajaxSubmit(comeInOptions);
            }, 3500);
        }
        $("#btnComingDetails").show();
        $("#comeInForm").addClass("active");
    });
    
    $("#comingPlace").on('change', function () {
        if (comeInTimer === 0) {
            comeInTimer = setTimeout(function () {
                $("#comeInForm").ajaxSubmit(comeInOptions);
            }, 3500);
        }
        $("#btnComingDetails").show();
        $("#comeInForm").addClass("active");
    });

/*

    Блок деталей поездки обратно

 */
    let leaveOutTimer = 0;
    let leaveOutOptions = {
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
                if(leaveOutTimer !== 0) {
                    clearTimeout(leaveOutTimer);
                    leaveOutTimer = 0;
                }
                $("#btnLeavingDetails").hide();
                $("#leaveOutForm").removeClass("active");
            }
        }
    };
    $("#leaveOutForm").ajaxForm(leaveOutOptions);

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
        if (leaveOutTimer === 0) {
            leaveOutTimer = setTimeout(function () {
                $("#leaveOutForm").ajaxSubmit(leaveOutOptions);
            }, 3500);
        }
        $("#btnLeavingDetails").show();
        $("#leaveOutForm").addClass("active");
    });

    $("#leavingDate").on('input', function () {
        if (leaveOutTimer === 0) {
            leaveOutTimer = setTimeout(function () {
                $("#leaveOutForm").ajaxSubmit(leaveOutOptions);
            }, 3500);
        }
        $("#btnLeavingDetails").show();
        $("#leaveOutForm").addClass("active");
    });

    $("#leavingTime").on('input', function () {
        if (leaveOutTimer === 0) {
            leaveOutTimer = setTimeout(function () {
                $("#leaveOutForm").ajaxSubmit(leaveOutOptions);
            }, 3500);
        }
        $("#btnLeavingDetails").show();
        $("#leaveOutForm").addClass("active");
    });

    $("#leavingFlight").on('input', function () {
        if (leaveOutTimer === 0) {
            leaveOutTimer = setTimeout(function () {
                $("#leaveOutForm").ajaxSubmit(leaveOutOptions);
            }, 3500);
        }
        $("#btnLeavingDetails").show();
        $("#leaveOutForm").addClass("active");
    });

    $("#leavingPlace").on('change', function () {
        if (leaveOutTimer === 0) {
            leaveOutTimer = setTimeout(function () {
                $("#leaveOutForm").ajaxSubmit(leaveOutOptions);
            }, 3500);
        }
        $("#btnLeavingDetails").show();
        $("#leaveOutForm").addClass("active");
    });

/*

    Блоки "здоровье", "Страховка", "Указания" и "Другое"

 */
    let healthTimer = 0;
    let healthOptions = {
        success: function (data, status) {
            if (status === "success") {
                $("#btnHealthDetails").hide();
                $("#healthForm").removeClass("active");
                if(healthTimer !== 0) {
                    clearTimeout(healthTimer);
                    healthTimer = 0;
                }
            }
        }
    };
    $("#healthForm").ajaxForm(healthOptions);

    $("#healthText").on('input', function () {
        if (healthTimer === 0) {
            healthTimer = setTimeout(function () {
                $("#healthForm").ajaxSubmit(healthOptions);
            }, 3500);
        }
        $("#btnHealthDetails").show();
        $("#healthForm").addClass("active");
    });

    let insuranceTimer = 0;
    let insuranceOptions = {
        success: function (data, status) {
            if (status === "success") {
                $("#btnInsuranceDetails").hide();
                $("#insuranceForm").removeClass("active");
                if(insuranceTimer !== 0) {
                    clearTimeout(insuranceTimer);
                    insuranceTimer = 0;
                }
            }
        }
    };
    $("#insuranceForm").ajaxForm(insuranceOptions);

    $("#insuranceText").on('input', function () {
        if (insuranceTimer === 0) {
            insuranceTimer = setTimeout(function () {
                $("#insuranceForm").ajaxSubmit(insuranceOptions);
            }, 3500);
        }
        $("#btnInsuranceDetails").show();
        $("#insuranceForm").addClass("active");
    });

    let notesTimer = 0;
    let notesOptions = {
        success: function (data, status) {
            if (status === "success") {
                $("#btnNotesDetails").hide();
                $("#notesForm").removeClass("active");
                if(notesTimer !== 0) {
                    clearTimeout(notesTimer);
                    notesTimer = 0;
                }
            }
        }
    };
    $("#notesForm").ajaxForm(notesOptions);

    $("#notesText").on('input', function () {
        if (notesTimer === 0) {
            notesTimer = setTimeout(function () {
                $("#notesForm").ajaxSubmit(notesOptions);
            }, 3500);
        }
        $("#btnNotesDetails").show();
        $("#notesForm").addClass("active");
    });

    let otherTimer = 0;
    let otherOptions = {
        beforeSubmit: function (arr, $form, options) {
            arr[3].value = encodeHtmlEntity($("#certName").val());  // Важно! Сменить индекс при изменении полей формы!
        },
        success: function (data, status) {
            if (status === "success") {
                $("#btnOtherDetails").hide();
                $("#otherForm").removeClass("active");
                if(otherTimer !== 0) {
                    clearTimeout(otherTimer);
                    otherTimer = 0;
                }
            }
        }
    };
    $("#otherForm").ajaxForm(otherOptions);

    $("#certLang").on('change', function () {
        if (otherTimer === 0) {
            otherTimer = setTimeout(function () {
                $("#otherForm").ajaxSubmit(otherOptions);
            }, 3500);
        }
        if ($("#certName").val() !== "") {
            $("#btnOtherDetails").show();
        }
        $("#otherForm").addClass("active");
    });
    $("#certName").on('input', function () {
        if (otherTimer === 0) {
            otherTimer = setTimeout(function () {
                $("#otherForm").ajaxSubmit(otherOptions);
            }, 3500);
        }
        if ($("#certName").val() !== "") {
            $("#btnOtherDetails").show();
        } else {
            $("#btnOtherDetails").hide();
        }
        $("#otherForm").addClass("active");
    });
    $("#visa_no").on('change', function () {
        if (otherTimer === 0) {
            otherTimer = setTimeout(function () {
                $("#otherForm").ajaxSubmit(otherOptions);
            }, 3500);
        }
        $("#btnOtherDetails").show();
        $("#otherForm").addClass("active");
    });
    $("#visa_yes").on('change', function () {
        if (otherTimer === 0) {
            otherTimer = setTimeout(function () {
                $("#otherForm").ajaxSubmit(otherOptions);
            }, 3500);
        }
        $("#btnOtherDetails").show();
        $("#otherForm").addClass("active");
    });
    $("#notebook").on('change', function () {
        if (otherTimer === 0) {
            otherTimer = setTimeout(function () {
                $("#otherForm").ajaxSubmit(otherOptions);
            }, 3500);
        }
        $("#btnOtherDetails").show();
        $("#otherForm").addClass("active");
    });
    $("#shirt").on('change', function () {
        if (otherTimer === 0) {
            otherTimer = setTimeout(function () {
                $("#otherForm").ajaxSubmit(otherOptions);
            }, 3500);
        }
        $("#btnOtherDetails").show();
        $("#otherForm").addClass("active");
    });
});

// Генератор "Приложения 2" к Договору
function createPDF(UID) {
    $.get(
        "mycabinet.php",
        {
            id: UID,
            app: "true"
        },
        function (text) {
            alert("Письмо отправлено!");
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            alert("Возникли проблемы с отправкой файла-приложения. Свяжитесь, пожалуйста, с организаторами!");
        })
    ;
}