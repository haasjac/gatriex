"use strict";

//global variables
var username;

//On load
$(function () {
    
    username = $('#headerUsername').text();
	setEventHandlers();

    function setEventHandlers() {
        $("#loginForm").submit(function () {
            login();
            return false; 
        });
        
        $("#headerLog").click(function () {
            if ($(this).attr("data-log") === "login") {
                $('#loginDialogBox').dialog("open");
            } else if ($(this).attr("data-log") === "logout") {
                $('#logoutDialogBox').dialog("open");
            }
        });
        
        $("#loginDialogBox").dialog({
        	autoOpen: false,
        	modal: true,
        	width: 650,
        	buttons: {
        	    Login: function () {
        	      login();  
        	    },
        		Cancel: function () {
        			$(this).dialog("close");
        		}
        	},
        	open: function () {
        	    $("#loginDialogMessage").html("");
        	}
        });
        
        $("#logoutDialogBox").dialog({
        	autoOpen: false,
        	modal: true,
        	buttons: {
        		Logout: function () {
        			logout();
        		},
        		Cancel: function () {
        		    $(this).dialog("close");
        		}
        	}
        });
    }
    
    function login() {
        $("#loginDialogMessage").html("");
        var data = { 
            "username": $("#loginUsername").val(), 
            "password": $("#loginPassword").val(),
            "remember": $("#loginRemember").val() 
        };
        $.ajax({
            url: "/api/account/login.php",
            method: "POST",
            data: data,
        	success: function(data) {
        	    var result = JSON.parse(data);
        	    if (!result.valid) {
        	        $("#loginDialogMessage").html("<i class='fa fa-exclamation-triangle'></i> Invalid Username/Password."); 
        	    } else {
        	        location.reload(true);
        	    }
        	},
        	error: function(xhr, status, error) {
        		console.log(error);
        	}
        });
    }
    
    function logout() {
    	$.ajax({
            url: "/api/account/logout.php",
        	success: function(data) {
        	    location.reload(true);
        	},
        	error: function(xhr, status, error) {
        		console.log(error);
        	}
        });
    }
});