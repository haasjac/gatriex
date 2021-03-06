/* global dataRequester */

//On load
$(function () {
    "use strict";

    var fetchingSummoner = false;
    var fetchingStatus = false;

    var TierENUM = {
        "IRON": 1,
        "BRONZE": 2,
        "SILVER": 3,
        "GOLD": 4,
        "PLATINUM": 5,
        "DIAMOND": 6,
        "MASTER": 7,
        "GRANDMASTER": 8,
        "CHALLENGER": 9
    };

    var RankENUM = {
        "IV": 1,
        "III": 2,
        "II": 3,
        "I": 4
    };


    function init() {
        getNavigation();
        setEventHandlers();
        getSummoner();
        getStatus();
        setInterval(getSummoner, 300000); // 5 min
        setInterval(getStatus, 300000); // 5 min
    }

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
        
        $("#editRegion").click(function () {
            $("#nameRegion").hide();
            $("#editRegion").hide();
            $("#selectRegion").show();
            $("#cancelRegion").show();
        });
        
        $("#cancelRegion").click(function () {
            $("#nameRegion").show();
            $("#editRegion").show();
            $("#selectRegion").hide();
            $("#cancelRegion").hide();
        });
        
        $("#selectRegion").change(function () {
            $("#nameRegion").html($("#selectRegion option:selected").text());
            
            $("#nameRegion").show();
            $("#editRegion").show();
            $("#selectRegion").hide();
            $("#cancelRegion").hide();
            
            getSummoner();
            getStatus();
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
        if (searchName === "" && TwigOptions.SummonerName) {
            searchName = TwigOptions.SummonerName.split(" ").join("");
        }
        if (searchName === "") {
            return;
        }
        
        fetchingSummoner = true;
        $("#refreshSummoner").addClass("fa-spin");
        $('#SummonerError').html('');
        
        
        var data = { 
            "region": $("#selectRegion").val(),
            "summonerName": searchName
        };
        
        dataRequester.apiCall("/api/riot/GetSummonerData.php", "GET", data, function (response) {
            if (response.valid) {
                displaySummoner(response.data);
            } else {
                $('#SummonerError').html('<i class="fas fa-exclamation-triangle"></i>');
                $('#SummonerError').attr('data-message', response.data.Error);
            }
            
            fetchingSummoner = false;
            $("#refreshSummoner").removeClass("fa-spin");
        }, function () {
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
        
        var data = {
            "region": $("#selectRegion").val()
        };        
        
        dataRequester.apiCall("/api/riot/GetStatusData.php", "GET", data, function (response) {
            if (response.valid) {
                displayStatus(response.data.Response);
            } else {
                $('#StatusError').html('<i class="fas fa-exclamation-triangle"></i>');
                $('#StatusError').attr('data-message', response.data.Error);
            }
            
            fetchingStatus = false;
            $("#refreshStatus").removeClass("fa-spin");
        }, function () {
            fetchingStatus = false;
            $("#refreshStatus").removeClass("fa-spin");
        });
    }
    
    function displayStatus(data) {
        var servicesList = $('<ul id="services" class="fa-ul"></ul>');
        var services = data.services;
        
        for (var serviceIndex = 0; serviceIndex < services.length; serviceIndex += 1) {
            var status = '<i class="fas fa-li ' + (services[serviceIndex].status === "online" ? "fa-check-circle" : "fa-question-circle") + '" data-status="' + services[serviceIndex].status + '"></i>';
            var service = $('<li>' + status + '<span class="service">' + services[serviceIndex].name + " - " + capitalize(services[serviceIndex].status) + '</span></li>');
            var incidentsList = $('<ul class="incidents fa-ul"></ul>');
            var incidents = services[serviceIndex].incidents;
            
            for (var incidentIndex = 0; incidentIndex < incidents.length; incidentIndex += 1) {
                if (incidents[incidentIndex].active) {
                    var updates = incidents[incidentIndex].updates;
                    if (updates.length <= 0) {
                        continue;
                    }
                    var fa;
                    switch (updates[0].severity) {
                        case "info":
                            fa = "fa-info-circle";
                            break;
                        case "warn":
                            fa = "fa-exclamation-circle";
                            break;
                        case "error":
                            fa = "fa-exclamation-triangle";
                            break;
                        default:
                            fa = "fa-question-circle";
                            break;
                    }
                    var severity = '<i class="fas fa-li ' + fa + '" data-severity="' + updates[0].severity + '"></i>';
                    var timestamp = formatTime(updates[0]);    
                    
                    var incident = $("<li>" + severity + " <span>" + addHTML(updates[0].content) + "</span>" + timestamp + "</li>");
                    var updatesList = $('<ul class="updates fa-ul"></ul>');
                    
                    for (var updateIndex = 1; updateIndex < updates.length; updateIndex += 1) {
                        var sev = updates[updateIndex].severity === "info" ? "fa-info-circle" : "fa-question-circle";
                        severity = '<i class="fas fa-li ' + sev + '" data-severity="' + updates[updateIndex].severity + '"></i>';
                        timestamp = formatTime(updates[updateIndex]);
                        updatesList.append("<li>" + severity + " <span>" + addHTML(updates[updateIndex].content) + "</span>" + timestamp + "</li>");
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

        data.League.sort(function (a, b) {
            if (a.queueType === "RANKED_SOLO_5x5" && b.queueType === "RANKED_SOLO_5x5") {
                if (TierENUM[a.tier] === TierENUM[b.tier]) {
                    if (RankENUM[a.tier] === RankENUM[b.tier]) {
                        return b.leaguePoints - a.leaguePoints;
                    }
                    else {
                        return RankENUM[b.tier] - RankENUM[a.tier];
                    }
                }
                else {
                    return TierENUM[b.tier] - TierENUM[a.tier];
                }
            }
            else if (a.queueType === "RANKED_SOLO_5x5") {
                // a is ranked, b is not
                return -1;
            }
            else {
                // b is ranked, a is not
                return 1;
            }
        });

        if (data.League.length > 0 && data.League[0].queueType === "RANKED_SOLO_5x5") {
            if (data.League[0].miniSeries) {
                mini = data.League[0].miniSeries.progress;
                mini = mini.replace(/W/g, "<i class='fas fa-check-circle'></i> ");
                mini = mini.replace(/N/g, "<i class='fas fa-minus-circle'></i> ");
                mini = mini.replace(/L/g, "<i class='fas fa-times-circle'></i> ");
                mini = mini.trim();
            } else {
                mini = data.League[0].leaguePoints + " LP";
            }
            league = capitalize(data.League[0].tier) + " " + data.League[0].rank;
        } else {
            mini = "";
            league = "";
        }
        $("#MiniSeries").html(mini);
        $("#SummonerLevel").html(data.Summoner.summonerLevel);
        $("#SummonerName").html(data.Summoner.name);
        $("#League").html(league);
        
        for (var i = 0; i < data.Mastery.length; i += 1) {
            url =  "https://ddragon.leagueoflegends.com/cdn/" + data.Version + "/img/champion/" + data.Champions[i] + ".png";
            $("#champ" + i).attr("src", url);
            $("#champ" + i).show();
        }
    }
    
    function clearSummonerData() {
        $("#MiniSeries").html("");
        $("#SummonerLevel").html("");
        $("#SummonerName").html("");
        $("#League").html("");
        $("#SummonerIcon").attr("src", "/images/Logo.png");
    }
    
    //gets navigation buttons
    function getNavigation() {
        dataRequester.apiCall("/api/edit/GetLinks.php", "GET", null, function (response) {
            if (response.valid) {
                for (var i = 0; i < response.data.Links.length; i += 1) {
                    var data = response.data.Links[i];
                    $('#navList').append('<dt data-catid="' + i + '" class="navButton"><i class="fas fa-caret-right fa-fw"></i> ' + data.header + '</dt>');
                    for (var j = 0; j < data.items.length; j += 1) {
                        var item = data.items[j];
                        var link = '<dd class="item item' + i + '" style="display:none;"><a href=' + item.link + ' target="_blank" ><i class="far fa-bookmark"></i> ' + item.text + '</a></dd>';
                        $('#navList').append(link);
                    }
                }
                
                $('.navButton').click(function () {
                    $('.item' + $(this).attr('data-catid')).toggle();
                    $(this).find(".fas").toggleClass("fa-caret-right");
                    $(this).find(".fas").toggleClass("fa-caret-down");
                });
            } else {
                $('#navList').html('<i class="fas fa-exclamation-triangle"></i> ' + response.data.Error);
            }
        });
    }
    
    
    /* HELPER FUNCTIONS */

    
    function capitalize(word) {
        var str = word.toLowerCase().replace(/\b[a-z]/g, function (letter) {
            return letter.toUpperCase();
        });
        return str;
    }
    
    function formatTime(update) {
        var timestamp = $("<div class='timestamp'><i class='far fa-clock'></i> </div>");

        /*ignore jslint start*/
        var time = "";
        if (update.updated_at) {
            time = update.updated_at;
        } else if (update.created_at) {
            time = update.created_at;
        } else {
            timestamp.append("unknown");
            return timestamp.html();
        }
        /*ignore jslint end*/
        
        var d = new Date(time);
        timestamp.append(d.toLocaleTimeString() + ", " + d.toLocaleDateString());
        return timestamp.prop('outerHTML');
    }
    
    function addHTML(data) {
        var arr = data.split(/\s+/);

        /*ignore jslint start*/
        var urlRegex = new RegExp(/^(?:(?:(?:https?|ftp):)?\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})).?)(?::\d{2,5})?(?:[/?#]\S*)?$/i);
                
        for (var i = 0; i < arr.length; i += 1) {
            var item = arr[i];
            if (urlRegex.test(item)) {
                data = data.replace(item, function (original) {
                    return "<a target='_blank' href='" + original + "'>" + original + "</a>";
                });
            }
        }
        /*ignore jslint end*/
        
        data = data.replace(/(?:\r\n|\r|\n)/g, '<br />');
        
        return data;
    }

    init();
});