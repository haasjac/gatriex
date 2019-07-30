"use strict";

var dataRequester = {};

var baseId = 0;

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

        var id = 0 + baseId;
        baseId += 1;

        console.log("New Request [" + id + "]: \"" + url + "\"");        

        $.ajax({
            url: url,
            method: method,
            data: data,
            success: function (data) {
                console.log("Request [" + id + "] succeeded: \"" + url + "\"");

                var result;
                try {
                    result = JSON.parse(data.trim());
                } catch (ex) {
                    result = {
                        "valid": false,
                        "data": { "Error": "Error handling request." }
                    };
                    dataRequester.log(JSON.stringify({ "message": "Error in dataRequester: " + ex, "error": data }));
                }
                callback(result);
            },
            error: function (xhr) {
                console.log("Request [" + id + "] failed: \"" + url + "\"");

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

    dataRequester.apiCallFormData = function (url, method, formData, callback, errorCallback) {

        var id = 0 + baseId;
        baseId += 1;

        console.log("New Request [" + id + "]: \"" + url + "\"");

        $.ajax({
            url: url,
            method: method,
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                console.log("Request [" + id + "] succeeded: \"" + url + "\"");

                var result;
                try {
                    result = JSON.parse(data);
                } catch (ex) {
                    result = {
                        "valid": false,
                        "data": { "Error": "Error handling request." }
                    };
                    dataRequester.log(JSON.stringify({ "message": "Error in dataRequester: " + ex, "error": data }));
                }
                callback(result);
            },
            error: function (xhr) {
                console.log("Request [" + id + "] failed: \"" + url + "\"");

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
