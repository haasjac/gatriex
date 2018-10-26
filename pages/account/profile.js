/* global dataRequester, TwigOptions */

//On load
$(function () {
    "use strict";

    var validator;

    function init() {
        setValidator();
        setEventHandlers();
    }

    function setValidator() {
        $.validator.messages.required = "Required.";
        
        validator = $("#profileForm").validate({
            rules: {
                profilePasswordInput: {
                    required: true,
                    minlength: 8
                },
                profilePasswordConfirm: {
                    required: true,
                    equalTo: profilePasswordInput
                },
                profileEmailInput: {
                    required: true,
                    email: true,
                    remote: {
                        url: "/api/validation/EmailFree.php",
                        data: { email: function () { return $("#profileEmailInput").val(); } },
                        method: "GET"
                    }
                },
                profileSummonerInput: {
                    required: true
                }
            },
            messages: {
                profilePasswordInput: {
                    minlength: "Must be at least 8 characters."
                },
                profilePasswordConfirm: {
                    equalTo: "Passwords do not match."
                },
                profileEmailInput: {
                    remote: "This Email is unavailable."
                }
            },
            showErrors: function (errorMap, errorList) {
                for (var i = 0; i < errorList.length; i += 1) {
                    errorList[i].message = "<i class='fas fa-exclamation-triangle'></i> " + errorList[i].message;
                }
                this.defaultShowErrors();
            }
        });
    }

    function setEventHandlers() {
        
        $("#profileForm").submit(function () {
            return false; 
        });
        
        $(".profileEdit").click(function () {
            showEdit($(this).attr("data-parent"));
        });
        
        $(".profileCancel").click(function () {
            hideEdit($(this).attr("data-parent"));
        });
        
        $(".profileSave").click(function () {
            if (validator.form()) {
                updateField($(this).attr("data-parent")); 
            }
        });
    }
    
    function showEdit(id) {
        $("." + id + "Edit").show();
        $("#" + id + "Input").show();
        $("#" + id + "Span").hide();
        $("#" + id + "Pencil").hide();
        
        if ($("#" + id + "Input").attr("type") !== "password") {
            $("#" + id + "Input").val($("#" + id + "Span").text());
        } else {
            $("#" + id + "Input").val("");
        }
    }
    
    function hideEdit(id) {
        $("." + id + "Edit").hide();
        $("#" + id + "Input").hide();
        $("#" + id + "Span").show();
        $("#" + id + "Pencil").show();
        
        $("#" + id + "Input").removeClass("error");
    }
    
    function updateField(id) {
        var data = JSON.stringify({
            username: TwigOptions.Username,
            value: $("#" + id + "Input").val(),
            field: $("#" + id + "Field").val(),
            confirmValue: $("#" + id + "Confirm").val()
        });
        
        dataRequester.apiCall("/api/account/profile.php", "POST", data, function (response) {
            if (response.valid) {
                if ($("#" + id + "Input").attr("type") !== "password") {
                    $("#" + id + "Span").text($("#" + id + "Input").val());
                }
                hideEdit(id);
            } else {
                $("#" + id + "Input-error").html("<i class='fas fa-exclamation-triangle'></i> " + response.data.Error);
                $("#" + id + "Input-error").show();
            }
        });
    }

    init();
});