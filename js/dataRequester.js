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
     
    dataRequester.apiCall = function (url, method, data, callback, errorCallback) {
        //if (method.toLowerCase() === 'post') {
        //    data = JSON.stringify(data);
        //}

        $.ajax({
            url: url,
            method: method,
            data: data,
            success: function (data) {
                var result;
                try {
                    result = JSON.parse(data);
                } catch (ex) {
                    result = {
                        "valid": false,
                        "data": { "Error": "Error handling request." }
                    };
                    dataRequester.log({ "data": { "message": "Error in dataRequester: " + ex, "error": data } });
                }
                callback(result);
            },
            error: function (xhr) {
                if (errorCallback) {
                    errorCallback();
                }
                if (xhr.status && xhr.status !== 0) {
                    $("#dataRequesterDialogError").html("Error " + xhr.status + ": " + xhr.statusText);
                    if (xhr.status !== 404) {
                        $("#dataRequesterDialogMessage").html(xhr.responseText);
                    }
                    $("#dataRequesterDialogBox").dialog("open");
                }
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
    };
   
});
