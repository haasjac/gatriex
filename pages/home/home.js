/* global dataRequester */

"use strict";

//global variables

//On load
$(function () {
    var fetchingSummoner = false;
    var fetchingStatus = false;
    
	getNavigation();
	setEventHandlers();
	getSummoner();
	getStatus();
	setInterval(getSummoner, 300000); // 5 min
	setInterval(getStatus, 300000); // 5 min


    function setEventHandlers() {
        $("#refreshSummoner").click(function () {
            getSummoner();
        });
        
        $("#refreshStatus").click(function () {
            getStatus();
        });
        
        $("#searchForm").submit(function () {
           clearSummonerData();
           getSummoner();
           return false;
        });
        
        $('#SummonerError').click(function () {
            $("#dialogMessage").html($(this).attr('data-message'));
            $('#dialogBox').dialog("open");
        });
        
        $('#StatusError').click(function () {
            $("#dialogMessage").html($(this).attr('data-message'));
            $('#dialogBox').dialog("open");
        });
        
        $("#dialogBox").dialog({
        	autoOpen: false,
        	modal: true,
            width: 500,
        	buttons: {
        		OK: function () {
        			$(this).dialog("close");
                    $("#dialogMessage").html("");
        		}
        	}
        });
    }
    
    function getSummoner() {
        if (fetchingSummoner) {
            return;
        }
        
        var searchName = $("#searchSummonerName").val().split(" ").join("");
        if (searchName === "") {
            searchName = $("#userSummonerName").val().split(" ").join("");
        }
        if (searchName === "") {
            return;
        }
        
        fetchingSummoner = true;
        $("#refreshSummoner").addClass("fa-spin");
        $('#SummonerError').html('');
        
        
        var data = { "summonerName": searchName };
        
        dataRequester.apiCall("/api/riot/GetSummonerData.php", "GET", data, function (response) {
            if (response.valid) {
                displaySummoner(response.data);
            } else {
                $('#SummonerError').html('<i class="fa fa-exclamation-triangle"></i>');
                $('#SummonerError').attr('data-message', response.data.Error);
            }
            
            fetchingSummoner = false;
            $("#refreshSummoner").removeClass("fa-spin");
        });
    }
    
    function getStatus() {        
        if (fetchingStatus) {
            return;
        }
        
        fetchingStatus = true;
        $("#refreshStatus").addClass("fa-spin");
        $('#StatusError').html('');
        
        dataRequester.apiCall("/api/riot/GetStatusData.php", "GET", null, function (response) {
            if (response.valid) {
                displayStatus(response.data.Response);
            } else {
                $('#StatusError').html('<i class="fa fa-exclamation-triangle"></i>');
                $('#StatusError').attr('data-message', response.data.Error);
            }
            
            fetchingStatus = false;
            $("#refreshStatus").removeClass("fa-spin");
        });
    }
    
    function displayStatus(data) {
        $("#Status").html(" - " + data.name);
        var servicesList = $('<ul id="services" class="fa-ul"></ul>');
        var services = data.services;
        
        for (var serviceIndex = 0; serviceIndex < services.length; serviceIndex++) {
            var status = '<i class="fa fa-li ' + (services[serviceIndex].status === "online" ? "fa-check-circle" : "fa-question-circle") + '" data-status="' + services[serviceIndex].status + '"></i>';
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
    			        severity = '<i class="fa fa-li ' + (updates[updateIndex].severity === "info" ? "fa-info-circle" : "fa-question-circle") + '" data-severity="' + updates[updateIndex].severity + '"></i>';
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
    }
    
    function displaySummoner(data) {
        var url =  "https://ddragon.leagueoflegends.com/cdn/" + data.Version + "/img/profileicon/" + data.Summoner.profileIconId + ".png";
        $("#SummonerIcon").attr("src", url);
        
    	var mini = "";
    	var league = "";
    	if (data.League.length > 0 && data.League[0].queueType === "RANKED_SOLO_5x5") {
        	if (data.League[0].miniSeries){
        		mini = data.League[0].miniSeries.progress;
        		mini = mini.replace(/W/g,"<i class='fa fa-check-circle'></i> ");
        		mini = mini.replace(/N/g,"<i class='fa fa-minus-circle'></i> ");
        		mini = mini.replace(/L/g,"<i class='fa fa-times-circle'></i> ");
        		mini = mini.trim();
        	} else {
        	    mini = data.League[0].leaguePoints + " LP";
        	}
        	league = capitalize(data.League[0].tier) + " " + data.League[0].rank;
    	} else {
    	    mini = "";
    	    league = "Level: " + data.Summoner.summonerLevel;
    	}
    	$("#MiniSeries").html(mini);
    	$("#SummonerName").html(data.Summoner.name);
    	$("#League").html(league);
    }
    
    function clearSummonerData() {
        $("#MiniSeries").html("");
    	$("#SummonerName").html("");
    	$("#League").html("");
    	$("#SummonerIcon").attr("src", "/images/Logo.png");
    }
    
    //gets navigation buttons
    function getNavigation() {
        dataRequester.apiCall("/api/edit/GetLinks.php", "GET", null, function (response) {
            if (response.valid) {
                for (var i = 0; i < response.data.Links.length; i++) {
                    var data = response.data.Links[i];
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
            } else {
                $('#navList').html('<i class="fa fa-exclamation-triangle"></i> ' + response.data.Error);
            }
        });
    }
    
    
    /* HELPER FUNCTIONS */

    
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
});