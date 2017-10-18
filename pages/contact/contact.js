/* global dataRequester, username */

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
                    errorList[i].message = "<i class='fa fa-exclamation-triangle'></i> " + errorList[i].message;
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
            if (validator.form()) {
                sendEmail();
            }
            return false; 
        });
        
        $("#fakecontactAnon").click( function () {
            console.log("hmm");
            if (!$("#contactAnon").prop("checked")) {
                lastName = $("#contactName").val();
                lastEmail = $("#contactEmail").val();
                $("#contactName").val("Annonymous");
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
        data.message = data.message.replace(/(?:\r\n|\r|\n)/g, '<br />');
        dataRequester.apiCall("/api/log/contact.php", "POST", data, function (response) {
            if (response.valid) {
                console.log(response);
            } else {
                console.log(response);
            }
        });
    }
});