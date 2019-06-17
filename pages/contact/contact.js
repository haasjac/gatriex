/* global dataRequester, username*/

"use strict";

//global variables


//On load
$(function () {
    
    var validator;
    var lastName = "", lastEmail = "";
    
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
                    errorList[i].message = "<i class='fas fa-exclamation-triangle'></i> " + errorList[i].message;
                }
                this.defaultShowErrors();
            }
        });
    }

    function setEventHandlers() {
        
        $("#contactSubject").parent().parent().hide();
        
        $("#contactSubjectSelect").change(function () {
           if ($("#contactSubjectSelect").val() === "Other") {
               $("#contactSubject").val("");
               $("#contactSubject").parent().parent().show();
           } else {
               $("#contactSubject").parent().parent().hide();
               $("#contactSubject").val($("#contactSubjectSelect").val());
           }
        });
        
        $("#contactForm").submit(function () {
            $("#contactFeedbackMessage").html("");
            if (validator.form()) {
                sendEmail();
            }
            return false; 
        });
        
        $("#fakecontactAnon").click( function () {
            if (!$("#contactAnon").prop("checked")) {
                lastName = $("#contactName").val();
                lastEmail = $("#contactEmail").val();
                $("#contactName").val("Anonymous");
                $("#contactEmail").val("DoNotReply@gatriex.com");
            } else {
                $("#contactName").val(lastName);
                $("#contactEmail").val(lastEmail);
            }
        });        
    }
    
    function sendEmail() {
        var data = {
            name: $("#contactName").val(),
            email: $("#contactEmail").val(),
            subject: $("#contactSubject").val(),
            message: $("#contactMessage").val()
        };
        data = JSON.stringify(data);
        dataRequester.apiCall("/api/log/contact.php", "POST", data, function (response) {
            if (response.valid) {
                var message = '<i class="fas fa-check-circle"></i> Thank you for the feedback!';
                if (!$("#contactAnon").prop("checked")) {
                    message += " You should receive a reply within a couple of days.";
                }
                $("#contactFeedbackMessage").html(message);
                $("#contactForm").hide();
            } else {
                console.log(response.data.Error);
                message = '<i class="fas fa-exclamation-triangle"></i> ' + response.data.Error;
                $("#contactFeedbackMessage").html(message);                
            }
        });
    }
});