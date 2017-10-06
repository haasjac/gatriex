"use strict";

//global variables

//On load
$(function () {
	setEventHandlers();
});

function setEventHandlers() {
    $("#createForm").submit(function () {
        return false;
    });
    
    $("#loginForm").submit(function () {
        return false;
    });
    
    $("#forgetForm").submit(function () {
        return false;
    });
    
    $("#resetForm").submit(function () {
        return false;
    });
    
    $("#createButton").click(function () {
        console.log("create"); 
        
        var data = { 
            "username": $("#createUsername").val(),
            "password": $("#createPassword").val(),
            "email": $("#createEmail").val(),
            "summoner": $("#createSummoner").val(),
        };
        $.ajax({
            url: "/account/createaccount.php",
            method: "POST",
            data: data,
        	success: function(data) {
        	    console.log(data);
        	},
        	error: function(xhr, status, error) {
        		console.log(error);
        	}
        });
    });
    
    $("#loginButton").click(function () {
        console.log("login");
        
        var data = { 
            "username": $("#loginUsername").val(), 
            "password": $("#loginPassword").val() 
        };
        $.ajax({
            url: "/account/login.php",
            method: "POST",
            data: data,
        	success: function(data) {
        	    var result = JSON.parse(data);
        	    if (!result.valid) {
        	        $("#userInfo").html("Invalid Username/Password.");    
        	    } else {
        	        $("#userInfo").html("Logged in as " + result.data.Username); 
        	    }
        	},
        	error: function(xhr, status, error) {
        		console.log(error);
        	}
        });
    });
    
    $("#logoutButton").click(function () {
        console.log("logout"); 
        $.ajax({
            url: "/account/logout.php",
        	success: function(data) {
        	    $("#userInfo").html("Logged Out");
        	},
        	error: function(xhr, status, error) {
        		console.log(error);
        	}
        });
    });
    
    $("#forgetUsernameButton").click(function () {
        console.log("username"); 
        
        var data = { 
            "email": $("#forgetEmail").val()
        };
        $.ajax({
            url: "/account/forgotusername.php",
            method: "POST",
            data: data,
        	success: function(data) {
        	    console.log(data);
        	},
        	error: function(xhr, status, error) {
        		console.log(error);
        	}
        });
    });
    
    $("#forgetPasswordButton").click(function () {
        console.log("password"); 
        
        var data = { 
            "username": $("#forgetUsername").val()
        };
        $.ajax({
            url: "/account/forgotpassword.php",
            method: "POST",
            data: data,
        	success: function(data) {
        	    console.log(data);
        	},
        	error: function(xhr, status, error) {
        		console.log(error);
        	}
        });
    });
    
    $("#resetButton").click(function () {
        console.log("reset"); 
        
        var data = { 
            "username": $("#resetUsername").val(),
            "password": $("#resetPassword").val(),
            "token": $("#resetToken").val()
        };
        $.ajax({
            url: "/account/resetpasswordfromtoken.php",
            method: "POST",
            data: data,
        	success: function(data) {
        	    console.log(data);
        	},
        	error: function(xhr, status, error) {
        		console.log(error);
        	}
        });
    });
}