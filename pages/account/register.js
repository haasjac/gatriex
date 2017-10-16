/* global dataRequester */

"use strict";

//global variables


//On load
$(function () {
    
    var validator;
    
    setValidator();
	setEventHandlers();
    
    function setValidator() {
        $.validator.messages.required = "Required.";
        
        validator = $("#createForm").validate({
            rules: {
                createUsername: {
                    required: true,
                    remote: "/api/validation/UsernameFree.php"
                },
                createPassword: {
                    required: true,
                    minlength: 8
                },
                createConfirmPassword: {
                    required: true,
                    equalTo: createPassword
                },
                createEmail: {
                    required: true,
                    email: true,
                    remote: "/api/validation/EmailFree.php"
                },
                createConfirmEmail: {
                    required: true,
                    equalTo: createEmail
                },
                createSummoner: {
                    
                }
            },
            messages: {
                createUsername: {
                    remote: "This Username is unavailable."
                },
                createPassword: {
                    minlength: "Must be at least 8 characters."
                },
                createConfirmPassword: {
                    equalTo: "Passwords do not match."
                },
                createEmail: {
                    remote: "This Email is unavailable."
                },
                createConfirmEmail: {
                    equalTo: "Emails do not match."
                }
            },
            showErrors: function(errorMap, errorList) {
                for (var i = 0; i < errorList.length; i++) {
                    errorList[i].message = "<i class='fa fa-exclamation-triangle'></i> " + errorList[i].message;
                }
                this.defaultShowErrors();
            },
            success: function(label) {
                label.html('<i class="fa fa-check-circle"></i>');
            }
        });
    }

    function setEventHandlers() {
        $("#createForm").submit(function () {
            if (validator.form()) {
                register();
            }
            return false; 
        });
        
        $("#createDialogBox").dialog({
        	autoOpen: false,
        	modal: true,
        	buttons: {
        		Close: function () {
        			$(this).dialog("close");
        			$("#createDialogMessage").html("");
        		}
        	}
        });
    }
    
    function register() {
        var data = { 
            "username": $("#createUsername").val(), 
            "password": $("#createPassword").val(),
            "confirmPassword": $("#createConfirmPassword").val(),
            "email": $("#createEmail").val(),
            "confirmEmail": $("#createConfirmEmail").val(),
            "summoner": $("#createSummoner").val()
        };
        dataRequester.apiCall('/api/account/register.php', "POST", data, function (response) {
            if (response.valid) {
                $("#createForm").hide();
                $("#createSuccess").show();
            } else {
                $("#createDialogMessage").html(response.data.Error);
                $("#createDialogBox").dialog("open");
            }
        });
    }
});