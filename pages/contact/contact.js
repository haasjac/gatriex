/* global dataRequester, username */

"use strict";

//global variables


//On load
$(function () {
    
	var validator;
    
    setValidator();
	setEventHandlers();
    
    function setValidator() {
        $.validator.messages.required = "Required.";
        
        validator = $("#contactForm").validate({
            rules: {
                contactName: {
                    required: true
                },
                contactEmail: {
                    required: true,
                    email: true
                },
                contactSubject: {
                    required: true
                },
                contactMessage: {
                    required: true
                }
            },
            messages: {
            },
            showErrors: function(errorMap, errorList) {
                for (var i = 0; i < errorList.length; i++) {
                    errorList[i].message = "<i class='fa fa-exclamation-triangle'></i> " + errorList[i].message;
                }
                this.defaultShowErrors();
            }
        });
    }

    function setEventHandlers() {
        
        $("#contactForm").submit(function () {
            if (validator.form()) {
                sendEmail();
            }
            return false; 
        });
        
    }
    
    function sendEmail() {
        var data = {
            name: $("#contactName").val(),
            email: $("#contactEmail").val(),
            subject: $("#contactSubject").val(),
            message: $("#contactMessage").val()
        };
        dataRequester.apiCall("/api/log/contact.php", "POST", data, function (response) {
            if (response.valid) {
                console.log(response);
            } else {
                console.log(response);
            }
        });
    }
});