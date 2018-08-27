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
        
        validator = $("#resetForm").validate({
            rules: {
                resetUsername: {
                    required: true
                },
                resetPassword: {
                    required: true,
                    minlength: 8
                },
                resetConfirmPassword: {
                    required: true,
                    equalTo: resetPassword
                }
            },
            messages: {
                resetPassword: {
                    minlength: "Must be at least 8 characters."
                },
                resetConfirmPassword: {
                    equalTo: "Passwords do not match."
                }
            },
            showErrors: function(errorMap, errorList) {
                for (var i = 0; i < errorList.length; i++) {
                    errorList[i].message = "<i class='fas fa-exclamation-triangle'></i> " + errorList[i].message;
                }
                this.defaultShowErrors();
            }
        });
    }

    function setEventHandlers() {
        
        $("#resetForm").submit(function () {
            $("#resetMessage").html("");
            if (validator.form()) {
                var data = { 
                    "username": $("#resetUsername").val(),
                    "password": $("#resetPassword").val(),
                    "confirmPassword": $("#resetConfirmPassword").val(),
                    "token": $("#resetToken").val()
                };
                dataRequester.apiCall('/api/account/resetpassword.php', "POST", data, function (response) {
                    if (response.valid) {
                        $("#resetMessage").html("<i class='fas fa-check-circle'></i> Password Reset.");
                        $("#resetPassword").val("");
                        $("#resetConfirmPassword").val("");
                    } else {
                        $("#resetMessage").html("<i class='fas fa-exclamation-triangle'></i> " + response.data.Error);
                    }
                });
            }
            return false; 
        });
        
        $("#resetButton").click(function () {
            
        });
    }
});