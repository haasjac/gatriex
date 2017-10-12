"use strict";

var dataRequester = {};

$(function() {
    
    $("#dataRequesterDialogBox").dialog({
        autoOpen: false,
        modal: true,
        width: 500,
        maxHeight: 650,
        buttons: {
            Close: function () {
                $(this).dialog("close");
                $("#dataRequesterDialogError").html("");
                $("#dataRequesterDialogMessage").html("");
            }
        }
    });
     
    dataRequester.apiCall = function (url, method, data, callback) {
        $.ajax({
            url: url,
            method: method,
            data: data,
        	success: function(data) {
                try {
                    var result = JSON.parse(data);
                } catch (ex) {
                    var result = {
                        "valid": false,
                        "data": { "Error": "Error handling request." }
                    };
                    dataRequester.log({ "data": { "ex": ex, "response": data } });
                }
        	    callback(result);
        	},
        	error: function(xhr) {
                $("#dataRequesterDialogError").html("Error " + xhr.status + ": " + xhr.statusText);
                if (xhr.status !== 404) {
                    $("#dataRequesterDialogMessage").html(xhr.responseText);
                }
                $("#dataRequesterDialogBox").dialog("open");
        	}
        });
    };
    
    dataRequester.riotApiCall = function (url, method, data, callback) {
        $.ajax({
            url: url,
            method: method,
            data: data,
        	success: function(data) {
                try {
                    var result = JSON.parse(data);
                    result.data.Response = JSON.parse(result.data.Response);
                    if (result.valid) {
                        var response = result.data.Response;
                        if (response.status && response.status.message && response.status.status_code) {
                            result.valid = false;
                            result.data.Error = response.status.message;
                        }
                    } else {
                        var result = {
                            "valid": false,
                            "data": { "Error": "Error handling request." }
                        };
                        dataRequester.log({ "data": { "error": result.data.Error, "response": data } });
                    }
                } catch (ex) {
                    var result = {
                        "valid": false,
                        "data": { "Error": "Error handling request." }
                    };
                    dataRequester.log({ "data": { "error": ex, "response": data } });
                }
        	    callback(result);
        	},
        	error: function(xhr) {
                $("#dataRequesterDialogError").html("Error " + xhr.status + ": " + xhr.statusText);
                if (xhr.status !== 404) {
                    $("#dataRequesterDialogMessage").html(xhr.responseText);
                }
                $("#dataRequesterDialogBox").dialog("open");
        	}
        });
    };
    
    dataRequester.log = function (data) {
        $.ajax({
            url: "/api/log/log.php",
            method: "POST",
            data: data,
        	success: function() {
                console.log("Error has been logged.");
        	},
        	error: function() {
                console.log("Error failed to be logged.");
        	}
        });
    }
   
});
