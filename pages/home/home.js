"use strict";

//global variables
var version;
var summonerData;
var leagueData;
var navContentData = {};
var fetchingSummoner = false;
var fetchingStatus = false;

//On load
$(function () {
	getNavigation();
	setEventHandlers();
	getSummoner();
	getStatus();
	setInterval(getSummoner, 300000); // 5 min
	setInterval(getStatus, 300000); // 5 min


    function setEventHandlers() {
        $("#refreshSummoner").click(function () {
            if (fetchSummonerStart()) {
                refreshSummonerData();
            }
        });
        
        $("#refreshStatus").click(function () {
            if (fetchStatusStart()) {
                refreshStatusData();
            }
        });
        
        $("#searchButton").click(function () {
           clearSummonerData();
           getSummoner();
        });
    }
    
    function getSummoner() {
        if (fetchSummonerStart()) {
            getId(summonerReady);
        }
    }
    
    function getStatus() {
        if (fetchStatusStart()) {
            getStatusData();
        }
    }
    
    function summonerReady() {
        getIcon();
        getSummonerData();
    }
    
    function getIcon() {
        getVersion(displayIcon);
    }
    
    function getVersion(callback) {
    	$.ajax({
    		url: "/api/riot/GetVersion.php",
    		success: function(data) {
    		    if (!checkData("Summoner", $.parseJSON(data))) {
    		        fetchSummonerEnd();
    		        return;
    		    }
    			version = $.parseJSON(data)[0];
    			callback();
    		},
    		error: function(xhr, status, error) {
    			fetchSummonerEnd();
    		}
    	});
    }
    
    function getId(callback) {
        var searchName = $("#searchSummonerName").val().split(" ").join("");
        if (searchName === "") {
            searchName = $("#userSummonerName").val().split(" ").join("")
        }
        if (searchName === "") {
            fetchSummonerEnd();
            return;
        }
        
    	$.ajax({
    		url: "/api/riot/GetId.php?urlName=" + searchName,
    		success: function(data) {
    		    if (!checkData("Summoner", $.parseJSON(data))) {
    		        fetchSummonerEnd();
    		        return;
    		    }
    			summonerData = $.parseJSON(data);
    			callback();
    		},
    		error: function(xhr, status, error) {
    			fetchSummonerEnd();
    		}
    	});
    }
    
    function getSummonerData() {
    	$.ajax({
    		url: "/api/riot/GetSummonerData.php?summonerID=" + summonerData.id,
    		success: function(data) {
    		    if (!checkData("Summoner", $.parseJSON(data))) {
    		        fetchSummonerEnd();
    		        return;
    		    }
    			leagueData = $.parseJSON(data);
    			displayData();
    		},
    		error: function(xhr, status, error) {
    			fetchSummonerEnd();
    		}
    	});
    }
    
    function getStatusData() {
    	$.ajax({
    		url: "/api/riot/GetStatusData.php",
    		success: function(data) {
    		    if (!checkData("Status", $.parseJSON(data))) {
    		        fetchStatusEnd();
    		        return;
    		    }
    			data = $.parseJSON(data);
    			displayStatus(data);
    		},
    		error: function(xhr, status, error) {
    			fetchStatusEnd();
    		}
    	});
    }
    
    function displayStatus(data) {
        $("#Status").html(" - " + data.name);
        var servicesList = $('<ul id="services" class="fa-ul"></ul>');
        var services = data.services;
        
        for (var serviceIndex = 0; serviceIndex < services.length; serviceIndex++) {
            var status = '<i class="fa fa-li ' + (services[serviceIndex].status == "online" ? "fa-check-circle" : "fa-question-circle") + '" data-status="' + services[serviceIndex].status + '"></i>';
            var service = $('<li>' + status + '<span class="service">' + services[serviceIndex].name + " - " + capitalize(services[serviceIndex].status) + '</span></li>');
            var incidentsList = $('<ul class="incidents fa-ul"></ul>');
            var incidents = services[serviceIndex].incidents;
            
            for (var incidentIndex = 0; incidentIndex < incidents.length; incidentIndex++) {
                if (incidents[incidentIndex].active) {
                    var updates = incidents[incidentIndex].updates;
                    var fa;
                    switch (updates[0].severity) {
                        case "info":
                            fa = "fa-info-circle";
                            break;
                        case "warn":
                            fa = "fa-exclamation-circle";
                            break;
                        default:
                            fa = "fa-question-circle";
                            break;
                    }
                    var severity = '<i class="fa fa-li ' + fa + '" data-severity="' + updates[0].severity + '"></i>';
                    var timestamp = formatTime(updates[0]);
                    var incident = $("<li>" + severity + " <span>" + updates[0].content + "</span>" + timestamp + "</li>");
                    var updatesList = $('<ul class="updates fa-ul"></ul>');
                    
    			    for (var updateIndex = 1; updateIndex < updates.length; updateIndex++) {
    			        severity = '<i class="fa fa-li ' + (updates[updateIndex].severity == "info" ? "fa-info-circle" : "fa-question-circle") + '" data-severity="' + updates[updateIndex].severity + '"></i>';
    			        var timestamp = formatTime(updates[updateIndex]);
    			        updatesList.append("<li>" + severity + " <span>" + updates[updateIndex].content + "</span>" + timestamp + "</li>");
    			    }
    			    
    				incident.append(updatesList);
    				incidentsList.append(incident);
    			}
            }
            
            service.append(incidentsList);
            servicesList.append(service);
        }
        $("#Incidents").html("");
        $("#Incidents").append(servicesList);
    	fetchStatusEnd();
    }
    
    function displayData() {
    	var mini = "";
    	var league = "";
    	if (leagueData.length > 0 && leagueData[0].queueType === "RANKED_SOLO_5x5") {
        	if (leagueData[0].miniSeries){
        		mini = leagueData[0].miniSeries.progress;
        		mini = mini.replace(/W/g,"<i class='fa fa-check-circle'></i> ");
        		mini = mini.replace(/N/g,"<i class='fa fa-minus-circle'></i> ");
        		mini = mini.replace(/L/g,"<i class='fa fa-times-circle'></i> ");
        		mini = mini.trim();
        	} else {
        	    mini = leagueData[0].leaguePoints + " LP";
        	}
        	league = capitalize(leagueData[0].tier) + " " + leagueData[0].rank;
    	} else {
    	    mini = "";
    	    league = "Level: " + summonerData.summonerLevel;
    	}
    	$("#MiniSeries").html(mini);
    	$("#SummonerName").html(summonerData.name);
    	$("#League").html(league);
    	fetchSummonerEnd();
    }
    
    function clearSummonerData() {
        $("#MiniSeries").html("");
    	$("#SummonerName").html("");
    	$("#League").html("");
    	$("#SummonerIcon").attr("src", "/images/Logo.png");
    }
    
    function displayIcon() {
        $("#SummonerIcon").attr("src", "https://ddragon.leagueoflegends.com/cdn/" + version + "/img/profileicon/" + summonerData.profileIconId + ".png");
    }
    
    //gets navigation buttons
    function getNavigation() {
        $.ajax({
    		url: '/api/edit/GetLinks.php',
    		type: 'GET',
    		dataType: 'json',
    		success: function (data) {
    			navContentData = data;
    			NavDataReady();
    		},
    		error: function (xhr, status, error) {
    			$('#error').html(error);
    			$('#error-details').html(xhr.responseText);
    			$('#error-message').dialog("open");
    		}
    	});
    }
    
    function NavDataReady() {
        for (var i = 0; i < navContentData.length; i++) {
            var data = navContentData[i];
            $('#navList').append('<dt data-catid="' + i + '" class="navButton"><i class="fa fa-caret-right"></i> ' + data.header + '</dt>');
            for (var j = 0; j < data.items.length; j++) {
                var item = data.items[j];
                var link = '<dd class="item item' + i + '" style="display:none;"><a href=' + item.link + ' target="_blank" ><i class="fa fa-bookmark-o"></i> ' + item.text + '</a></dd>';
                $('#navList').append(link);
            }
        }
        $('.navButton').click(function () {
            $('.item' + $(this).attr('data-catid')).toggle();
            $(this).find(".fa").toggleClass("fa-caret-right");
            $(this).find(".fa").toggleClass("fa-caret-down");
        });
        
        $('#SummonerError').click(function () {
            $("#errorCode").html($(this).attr('data-status_code'));
            $("#dialogMessage").html($(this).attr('data-message'));
            $('#dialogBox').dialog("open");
        });
        
        $('#StatusError').click(function () {
            $("#errorCode").html($(this).attr('data-status_code'));
            $("#dialogMessage").html($(this).attr('data-message'));
            $('#dialogBox').dialog("open");
        });
        
        $("#dialogBox").dialog({
        	autoOpen: false,
        	modal: true,
        	buttons: {
        		OK: function () {
        			$(this).dialog("close");
        			$("#errorCode").html("");
                    $("#dialogMessage").html("");
        		}
        	}
        });
    }
    
    function refreshSummonerData() {
        getId(refreshSummonerReady);
    }
    
    function refreshSummonerReady() {
        getIcon();
        getSummonerData();
    }
    
    function refreshStatusData() {
        getStatusData();
    }
    
    /* HELPER FUNCTIONS */
    
    function checkData(section, data) {
        console.log(data);
        if (data.status && data.status.message && data.status.status_code) {
            $('#' + section + 'Error').html('<i class="fa fa-exclamation-triangle"></i>');
            $('#' + section + 'Error').attr('data-message', data.status.message);
            $('#' + section + 'Error').attr('data-status_code', data.status.status_code);
            console.log("hmm");
            return false;
        } else {
            $('#' + section + 'Error').html("");
            $('#' + section + 'Error').attr('data-message', "");
            $('#' + section + 'Error').attr('data-status_code', "");
            return true;
        }
    }
    
    function capitalize(word) {
        var str = word.toLowerCase().replace(/\b[a-z]/g, function(letter) {
            return letter.toUpperCase();
        });
        return str;
    }
    
    function formatTime (update) {
        var timestamp = $("<div class='timestamp'><i class='fa fa-clock-o'></i> </div>");
        
        var time = "";
        if (update.updated_at) {
            time = update.updated_at;
        } else if (update.created_at) {
            time = update.created_at;
        } else {
            timestamp.append("unknown");
            return timestamp.html();
        }
        
        var d = new Date(time);
        timestamp.append(d.toLocaleTimeString() + ", " + d.toLocaleDateString());
        return timestamp.prop('outerHTML');
    }
    
    function fetchSummonerStart() {
        if (fetchingSummoner) {
            return false;
        } else {
            fetchingSummoner = true;
            $("#refreshSummoner").addClass("fa-spin");
            return true;
        }
    }
    
    function fetchSummonerEnd() {
        fetchingSummoner = false;
        $("#refreshSummoner").removeClass("fa-spin");
    }
    
    function fetchStatusStart() {
        if (fetchingStatus) {
            return false;
        } else {
            fetchingStatus = true;
            $("#refreshStatus").addClass("fa-spin");
            return true;
        }
    }
    
    function fetchStatusEnd() {
        fetchingStatus = false;
        $("#refreshStatus").removeClass("fa-spin");
    }
});