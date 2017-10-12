/* global dataRequester */

"use strict";

//global variables


//On load
$(function () {
    
	setEventHandlers();

    function setEventHandlers() {
        $("#createForm").submit(function () {
            register();
            return false; 
        });
        
        $("#recoverUsernameForm").submit(function () {
            return false; 
        });
        
        $("#recoverPasswordForm").submit(function () {
            return false; 
        });
        
        $("#resetForm").submit(function () {
            return false; 
        });
        
        $("#recoverUsernameButton").click(function () {
            $("#recoverUsernameMessage").html("");
            var data = { 
                "email": $("#recoverUsernameEmail").val()
            };
            dataRequester.apiCall('/api/account/recoverusername.php', "POST", data, function (response) {
                if (response.valid) {
                    $("#recoverUsernameMessage").html("<i class='fa fa-check-circle'></i> Email Sent.");
                } else {
                    $("#recoverUsernameMessage").html("<i class='fa fa-exclamation-triangle'></i> " + response.data.Error);
                }
            });
        });
        
        $("#recoverPasswordButton").click(function () {
            $("#recoverPasswordMessage").html("");
            var data = { 
                "username": $("#recoverPasswordUsername").val()
            };
            dataRequester.apiCall('/api/account/recoverpassword.php', "POST", data, function (response) {
                if (response.valid) {
                    $("#recoverPasswordMessage").html("<i class='fa fa-check-circle'></i> Email Sent.");
                } else {
                    $("#recoverPasswordMessage").html("<i class='fa fa-exclamation-triangle'></i> " + response.data.Error);
                }
            });
        });
        
        $("#resetButton").click(function () {
            $("#resetMessage").html("");
            var data = { 
                "username": $("#resetUsername").val(),
                "password": $("#resetPassword").val(),
                "confirmPassword": $("#resetConfirmPassword").val(),
                "token": $("#resetToken").val()
            };
            dataRequester.apiCall('/api/account/resetpassword.php', "POST", data, function (response) {
                if (response.valid) {
                    $("#resetMessage").html("<i class='fa fa-check-circle'></i> Password Reset.");
                    $("#resetPassword").val("");
                    $("#resetConfrimPassword").val("");
                } else {
                    $("#resetMessage").html("<i class='fa fa-exclamation-triangle'></i> " + response.data.Error);
                }
            });
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
            "summoner": $("#createSummoner").val(),
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