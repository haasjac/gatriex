/* global dataRequester */

"use strict";

//global variables


//On load
$(function () {
    
    setEventHandlers();

    function setEventHandlers() {        
        $("#recoverUsernameForm").submit(function () {
            return false; 
        });
        
        $("#recoverPasswordForm").submit(function () {
            return false; 
        });
        
        $("#recoverUsernameButton").click(function () {
            $("#recoverUsernameMessage").html("");
            var data = { 
                "email": $("#recoverUsernameEmail").val()
            };
            dataRequester.apiCall('/api/account/recoverusername.php', "POST", data, function (response) {
                if (response.valid) {
                    $("#recoverUsernameMessage").html("<i class='fas fa-check-circle'></i> Email Sent.");
                } else {
                    $("#recoverUsernameMessage").html("<i class='fas fa-exclamation-triangle'></i> " + response.data.Error);
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
                    $("#recoverPasswordMessage").html("<i class='fas fa-check-circle'></i> Email Sent.");
                } else {
                    $("#recoverPasswordMessage").html("<i class='fas fa-exclamation-triangle'></i> " + response.data.Error);
                }
            });
        });
    }
});