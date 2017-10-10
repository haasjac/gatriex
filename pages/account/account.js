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
        	$.ajax({
                url: "/api/account/recoverusername.php",
                method: "POST",
                data: data,
            	success: function(data) {
            	    var result = JSON.parse(data);
            	    if (result.valid) {
            	        $("#recoverUsernameMessage").html("<i class='fa fa-check-circle'></i> Email Sent.");
            	    } else {
            	        $("#recoverUsernameMessage").html("<i class='fa fa-exclamation-triangle'></i> " + result.data.Error);
            	    }
            	},
            	error: function(xhr, status, error) {
            		$("#recoverUsernameMessage").html("<i class='fa fa-exclamation-triangle'></i> " + error);
            	}
            });
        });
        
        $("#recoverPasswordButton").click(function () {
            $("#recoverPasswordMessage").html("");
            var data = { 
                "username": $("#recoverPasswordUsername").val()
            };
        	$.ajax({
                url: "/api/account/recoverpassword.php",
                method: "POST",
                data: data,
            	success: function(data) {
            	    var result = JSON.parse(data);
            	    if (result.valid) {
            	        $("#recoverPasswordMessage").html("<i class='fa fa-check-circle'></i> Email Sent.");
            	    } else {
            	        $("#recoverPasswordMessage").html("<i class='fa fa-exclamation-triangle'></i> " + result.data.Error);
            	    }
            	},
            	error: function(xhr, status, error) {
            		$("#recoverPasswordMessage").html("<i class='fa fa-exclamation-triangle'></i> " + error);
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
        	$.ajax({
                url: "/api/account/resetpassword.php",
                method: "POST",
                data: data,
            	success: function(data) {
            	    var result = JSON.parse(data);
            	    if (result.valid) {
            	        $("#resetMessage").html("<i class='fa fa-check-circle'></i> Password Reset.");
            	    } else {
            	        $("#resetMessage").html("<i class='fa fa-exclamation-triangle'></i> " + result.data.Error);
            	    }
            	},
            	error: function(xhr, status, error) {
            		$("#resetMessage").html("<i class='fa fa-exclamation-triangle'></i> " + error);
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
    	$.ajax({
            url: "/api/account/register.php",
            method: "POST",
            data: data,
        	success: function(data) {
        	    var result = JSON.parse(data);
        	    if (result.valid) {
        	        $("#createForm").hide();
        	        $("#createSuccess").show();
        	    } else {
        	        $("#createDialogMessage").html(result.data.Error);
        	        $("#createDialogBox").dialog("open");
        	    }
        	},
        	error: function(xhr, status, error) {
        		console.log(error);
        		$("#createDialogMessage").html(error);
    	        $("#createDialogBox").dialog("open");
        	}
        });
    }
});