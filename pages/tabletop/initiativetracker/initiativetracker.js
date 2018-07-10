/* global dataRequester */

$(function () {
    "use strict";

	var listCount = 0, editMode = false, pendingChanges = false;

    function init() {
        setEventHandlers();
        getContent();
    }
    
    function getContent() {
        if (TwigOptions.CampaignGuid) {
            dataRequester.apiCall('/api/tabletop/initiativetracker/GetCampaign.php', "GET", { "CampaignGuid": TwigOptions.CampaignGuid }, function (response) {
                if (response.valid) {
                    createInitList(response.data.Campaign.CharacterInfo, response.data.Campaign.CurrentCharacter);
                } else {
                    $('#dialogMessage').html('<i class="fa fa-exclamation-triangle"></i> Error: ' + response.data.Error);
                }
            });
        }
	}

    function createInitList(CharacterInfo, CurrentCharacter) {
        if (!CharacterInfo) {
            return;
        }

        listCount = Number(CharacterInfo.listCount) || 0;

        $.each(CharacterInfo.players, function (index, player) {
            addPlayer(player.id, player, CurrentCharacter === player.id);
        });
        
        setSortable();
	}

    function setSortable() {
        if (editMode) {
            $("#initList").sortable({
                placeholder: "ui-state-highlight",
                start: function (e, ui) {
                    ui.placeholder.height(ui.item.height());
                },
                update: function () {
                    setPendingChanges();
                },
                disabled: false
            });
        }
        else {
            $("#initList").sortable({
                placeholder: "ui-state-highlight",
                start: function (e, ui) {
                    ui.placeholder.height(ui.item.height());
                },
                disabled: true
            });
        }
    }

	function setEventHandlers() {
        $("#form").submit(function (e) {
            e.preventDefault();
            return false;
        });
        
        $(".addCategoryButton").click(function () {
            listCount += 1;
            addPlayer(listCount);
            setSortable();
            setPendingChanges();
        });

        $("#sortButton").click(function () {
            sortList();
            setSortable();
            setPendingChanges();
        });
        
		$("#initList").on("click", ".deleteCategoryButton", function () {
			var item = $(this).attr("id").replace(/button/g, "li");
            $('#' + item).remove();
            setPendingChanges();
		});
		
		$("#initList").on("click", "li", function () {
            if (!editMode) {
                $("#initList li").each(function () {
                    $(this).removeClass("initSelected");
                    $(this).find(".playerSelected").val(false);
                });
                $(this).addClass("initSelected");
                $(this).find(".playerSelected").val(true);
            }
		});
		
		$("#editButton").click(function () {
            editMode = !editMode;
            setSortable();
            $(".addCategoryButton").toggleClass("hide"); 
            $(".deleteCategoryButton").toggleClass("hide");
            $("#sortButton").toggleClass("hide");

            if (!editMode && TwigOptions.CampaignOwner && pendingChanges) {
                saveChanges();
            }
        });

        if (TwigOptions.CampaignOwner) {
            $("#editButton").removeClass("hide");
        }
		
		$("#initList").on("click", ".profile", function () {
            if (editMode) {
                $(this).toggleClass("red blue");
                if ($(this).hasClass("blue")) {
                    $(this).find(".playerTeam").val("blue");
                }
                else {
                    $(this).find(".playerTeam").val("red");
                }
            }
        });

        $("#initList").on("change", "input", function () {
            setPendingChanges();
        });

        
	}

    function sortList() {
        var initList = $('#initList');

        var listitems = $('li', initList);

        listitems.sort(function (a, b) {
            var initA = Number($(a).find(".playerInitiative").val());
            var initB = Number($(b).find(".playerInitiative").val());

            if (initA === initB) {
                var teamA = $(a).find(".playerTeam").hasClass("blue");
                var teamB = $(b).find(".playerTeam").hasClass("blue");
                if (teamA === teamB) {
                    return 0;
                }
                return teamA ? -1 : 1;
            }
            else {
                return initA < initB ? 1 : -1;
            }
        });

        initList.append(listitems);
    }

    function addPlayer(id, data, selected) {        
        data = data || {};
        var playerTeamValue = data.team || "blue";
        var playerNameValue = data.name || "";
        var playerInitiativeValue = Number(data.initiative || 0);
        
        var playerId = '<input class="playerId" name="playerId_' + id + '" type="hidden" value="' + id + '" />';
        var playerSelected = '<input class="playerSelected" name="playerSelected_' + id + '" type="hidden" value="' + selected + '" />';
        var playerTeam = '<input class="playerTeam" name="playerTeam_' + id + '" type="hidden" value="' + playerTeamValue + '" />';
        var playerName = '<input class="playerName" name="playerName_' + id + '" style="width:50%" type="text" value="' + playerNameValue + '" placeholder="Player Name" />';
        var playerInitiative = '<input class="playerInitiative" name="playerInitiative_' + id + '" style="width:10%" type="number" value="' + playerInitiativeValue + '" />';

        var item = $('<li id="li_' + id + '" class="ui-state-default' + (selected ? ' initSelected' : '') + '"></li>');
        var div = $('<div class="person"></div>');
        var img = $('<i class="fa fa-user fa-2x profile ' + playerTeamValue + '">' + playerTeam + '</i>');
        var name = $('<div class="playerDiv">' + playerName + ' ' + playerInitiative + '</div>');
        var removeButton = $(' <button id="button_' + id + '" class="ui-button deleteCategoryButton' + (editMode ? '' : ' hide') + '"><i class="fa fa-minus"></i></button>');
        div.append(playerId).append(playerSelected).append(img).append(name).append(removeButton);
        $('#initList').append(item.append(div));
    }

    function setPendingChanges() {
        if (TwigOptions.CampaignOwner) {
            $('#dialogMessage').html('<i class="fa fa-spin fa-circle-o"></i>');
            pendingChanges = true;
        }
    }

    function saveChanges() {
        $('#dialogMessage').html('<i class="fa fa-spin fa-circle-o-notch"></i>');

        var CharacterInfo = {};
        var CurrentCharacter;

        CharacterInfo.listCount = listCount;

        CharacterInfo.players = [];

        var initList = $('#initList');

        var listitems = $('li', initList);

        $.each(listitems, function (index, element) {
            var player = {};
            player.id = $(element).find(".playerId").val();
            player.team = $(element).find(".playerTeam").val();
            player.name = $(element).find(".playerName").val();
            player.initiative = $(element).find(".playerInitiative").val();

            CharacterInfo.players.push(player);


            if ($(element).find(".playerSelected").val()) {
                CurrentCharacter = player.id;
            }
        });
        
        var data = {};

        data.CampaignGuid = TwigOptions.CampaignGuid;
        data.CharacterInfo = CharacterInfo;
        data.CurrentCharacter = CurrentCharacter;

        var postData = {
            "data": data
        };

        dataRequester.apiCall('/api/tabletop/initiativetracker/SaveCampaign.php', "POST", postData, function (response) {
            if (response.valid) {
                $('#dialogMessage').html('<i class="fa fa-floppy-o"></i>');
                pendingChanges = false;
                setTimeout(function () { $('#dialogMessage').html(''); }, 1000);
            } else {
                $('#dialogMessage').html('<i class="fa fa-exclamation-triangle"></i> Error: ' + response.data.Error);
            }
        });
    }

    init();
});
