/* global dataRequester */

$(function () {
    "use strict";

    var contentData = {}, factionOptions = "";

    function init() {
        setEventHandlers();
        getContent();
    }

    function dataReady() {
        createCharacterList();
    }

    function getContent() {
        if (TwigOptions.CampaignGuid) {
            dataRequester.apiCall('/api/tabletop/campaign/GetCampaign.php', "GET", { "CampaignGuid": TwigOptions.CampaignGuid }, function (response) {
                if (response.valid) {
                    contentData = response.data;
                    dataReady();
                } else {
                    $('#dialogMessage').html('<i class="fas fa-exclamation-triangle"></i> Error: ' + response.data.Error);
                }
            });
        }
    }

    function createCharacterList() {
        $('#characterList').empty();

        factionOptions = "";

        $.each(contentData.Factions, function (index, faction) {
            var factionDiv = $('<div id="' + faction.Name + 'Div" class="factionDiv"></div>');

            var factionHeader = $('<div id="' + faction.Name + 'Header" class="factionHeader">' +
                '<i class="fas fa-fw ' + faction.Icon + ' faction-' + faction.Name + '"></i> ' + faction.Name +
                '</div>');

            var factionul = $('<ul id="' + faction.Name + 'List" class="characterList"></ul>');

            factionDiv.append(factionHeader).append(factionul);
            $('#listDiv').append(factionDiv);

            factionOptions += '<option value="' + faction.Id + '">' + faction.Name + '</option>';
        });

        $('#editAddCharacterFaction').append(factionOptions);

        $.each(contentData.Characters, function (guid, character) {
            displayCharacter(guid, character);
        });
    }

    function setEventHandlers() {
        $("#form").submit(function (e) {
            e.preventDefault();
            return false;
        });

        $("#removeCharacterDialogBox").dialog({
            autoOpen: false,
            modal: true,
            width: 500,
            buttons: {
                Close: function () {
                    $(this).dialog("close");
                }
            }
        });
        
        $("#addCharacterButton").click(function () {
            $('#editAddCharacter').toggle();

            $('#addCharacterButton').toggle();
            $('#saveAddCharacterButton').toggle();
            $('#discardAddCharacterButton').toggle();
        });

        $("#saveAddCharacterButton").click(function () {
            var data = {};
            data.Faction = $("#editAddCharacterFaction").val();
            data.Name = $("#editAddCharacterName").val();
            data.InitiativeBonus = Number($("#editAddCharacterInitBonus").val());
            data.InitiativeAdvantage = $("#editAddCharacterInitAdvantage:checked").val() ? 1 : 0;
            data.CampaignGuid = TwigOptions.CampaignGuid;

            var postData = { "data": data };
            dataRequester.apiCall('/api/tabletop/campaign/AddCharacter.php', "POST", postData, function (response) {
                if (response.valid) {
                    var character = response.data.Character;
                    displayCharacter(character.Guid, character);

                    $("#editAddCharacterFaction option:first-child").attr("selected", "selected");
                    $("#editAddCharacterName").val("Barry Bluejeans");
                    $("#editAddCharacterInitBonus").val(0);
                    $("#editAddCharacterInitAdvantage").prop("checked", false);
                    $("#fakeeditAddCharacterInitAdvantage").addClass("fa-square");
                    $("#fakeeditAddCharacterInitAdvantage").removeClass("fa-check-square");

                    $('#editAddCharacter').toggle();

                    $('#addCharacterButton').toggle();
                    $('#saveAddCharacterButton').toggle();
                    $('#discardAddCharacterButton').toggle();
                    $('#dialogMessage').html('<i class="fas fa-check-circle"></i> Changes saved.');
                } else {
                    $('#dialogMessage').html('<i class="fas fa-exclamation-triangle"></i> Error: ' + response.data.Error);
                }
            });
        });

        $("#discardAddCharacterButton").click(function () {
            $("#editAddCharacterFaction option:first-child").attr("selected", "selected");
            $("#editAddCharacterName").val("Barry Bluejeans");
            $("#editAddCharacterInitBonus").val(0);
            $("#editAddCharacterInitAdvantage").prop("checked", false);
            $("#fakeeditAddCharacterInitAdvantage").addClass("fa-square");
            $("#fakeeditAddCharacterInitAdvantage").removeClass("fa-check-square");
            
            $('#editAddCharacter').toggle();

            $('#addCharacterButton').toggle();
            $('#saveAddCharacterButton').toggle();
            $('#discardAddCharacterButton').toggle();
        });

        $("#listDiv").on("click", ".editCharacterButton", function () {
            var guid = $(this).attr("data-guid");
            if (!guid) {
                return;
            }

            $('#editCharacter_' + guid).toggle();

            $('#editCharacterButton_' + guid).toggle();
            $('#removeCharacterButton_' + guid).toggle();
            $('#saveCharacterButton_' + guid).toggle();
            $('#discardCharacterButton_' + guid).toggle();
        });

        $("#listDiv").on("click", ".removeCharacterButton", function () {
            var guid = $(this).attr("data-guid");
            if (!guid) {
                return;
            }

            $("#removeCharacterDialogName").html('<span>' + $("#characterName_" + guid).text() + '</span>');

            $("#removeCharacterDialogBox").dialog({
                autoOpen: false,
                modal: true,
                width: 500,
                buttons: {
                    Remove: function () {
                        var data = {};
                        data.Guid = guid;

                        var postData = { "data": data };
                        dataRequester.apiCall('/api/tabletop/campaign/RemoveCharacter.php', "POST", postData, function (response) {
                            if (response.valid) {
                                $('#character_' + guid).remove();
                                $('#editCharacter_' + guid).remove();
                                $('#dialogMessage').html('<i class="fas fa-check-circle"></i> Changes saved.');
                            } else {
                                $('#dialogMessage').html('<i class="fas fa-exclamation-triangle"></i> Error: ' + response.data.Error);
                            }
                        });
                        $(this).dialog("close");
                    },
                    Cancel: function () {
                        $(this).dialog("close");
                    }
                }
            });

            $("#removeCharacterDialogBox").dialog("open");
        });

        $("#listDiv").on("click", ".saveCharacterButton", function () {
            var guid = $(this).attr("data-guid");
            if (!guid) {
                return;
            }

            var data = {};
            data.Guid = guid;
            data.Faction = $("#editCharacterFaction_" + guid).val();
            data.Name = $("#editCharacterName_" + guid).val();
            data.InitiativeBonus = Number($("#editCharacterInitBonus_" + guid).val());
            data.InitiativeAdvantage = $("#editCharacterInitAdvantage_" + guid + ':checked').val() ? 1 : 0;

            var postData = { "data": data };
            dataRequester.apiCall('/api/tabletop/campaign/EditCharacter.php', "POST", postData, function (response) {
                if (response.valid) {

                    $("#characterName_" + guid).text($("#editCharacterName_" + guid).val());
                    $("#characterInitBonus_" + guid).html(displayInitBonus(data.InitiativeBonus));
                    $("#characterInitAdvantage_" + guid).html(displayInitAdvantage(data.InitiativeAdvantage));
                    $("#characterInitBonusValue_" + guid).text(data.InitiativeBonus);
                    $("#characterInitAdvantageValue_" + guid).text(data.InitiativeAdvantage);

                    if ($("#editCharacterFaction_" + guid).val() !== $("#characterFaction_" + guid).text()) {

                        var oldFactionName = contentData.Factions[$("#characterFaction_" + guid).text()].Name;
                        var newFactionName = contentData.Factions[$("#editCharacterFaction_" + guid).val()].Name;
                        var oldFactionIcon = contentData.Factions[$("#characterFaction_" + guid).text()].Icon;
                        var newFactionIcon = contentData.Factions[$("#editCharacterFaction_" + guid).val()].Icon;

                        $("#characterFaction_" + guid).text($("#editCharacterFaction_" + guid).val());

                        $("#factionIcon_" + guid).toggleClass('faction-' + oldFactionName + ' faction-' + newFactionName);
                        $("#initBonusIcon_" + guid).toggleClass('faction-' + oldFactionName + ' faction-' + newFactionName);
                        $("#initAdvantageIcon_" + guid).toggleClass('faction-' + oldFactionName + ' faction-' + newFactionName);
                        $("#editFactionIcon_" + guid).toggleClass('faction-' + oldFactionName + ' faction-' + newFactionName);
                        $("#editNameIcon_" + guid).toggleClass('faction-' + oldFactionName + ' faction-' + newFactionName);
                        $("#editInitBonusIcon_" + guid).toggleClass('faction-' + oldFactionName + ' faction-' + newFactionName);
                        $("#editInitAdvantageIcon_" + guid).toggleClass('faction-' + oldFactionName + ' faction-' + newFactionName);

                        $("#factionIcon_" + guid).toggleClass(oldFactionIcon + ' ' + newFactionIcon);
                        $("#editNameIcon_" + guid).toggleClass(oldFactionIcon + ' ' + newFactionIcon);

                        $("#character_" + guid).detach().appendTo('#' + newFactionName + 'List');
                        $("#editCharacter_" + guid).detach().appendTo('#' + newFactionName + 'List');
                    }

                    $('#editCharacter_' + guid).toggle();

                    $('#editCharacterButton_' + guid).toggle();
                    $('#removeCharacterButton_' + guid).toggle();
                    $('#saveCharacterButton_' + guid).toggle();
                    $('#discardCharacterButton_' + guid).toggle();
                    $('#dialogMessage').html('<i class="fas fa-check-circle"></i> Changes saved.');
                } else {
                    $('#dialogMessage').html('<i class="fas fa-exclamation-triangle"></i> Error: ' + response.data.Error);
                }
            });
        });

        $("#listDiv").on("click", ".discardCharacterButton", function () {
            var guid = $(this).attr("data-guid");
            if (!guid) {
                return;
            }

            $("#editCharacterFaction_" + guid).val($("#characterFaction_" + guid).text());
            $("#editCharacterName_" + guid).val($("#characterName_" + guid).text());
            $("#editCharacterInitBonus_" + guid).val($("#characterInitBonusValue_" + guid).text());
            if ($("#characterInitAdvantageValue_" + guid).text() === 0) {
                $("#fakeeditCharacterInitAdvantage_" + guid).addClass("fa-square-o");
                $("#fakeeditCharacterInitAdvantage_" + guid).removeClass("fa-check-square-o");
                $("#editCharacterInitAdvantage_" + guid).prop("checked", false);
            }
            else {
                $("#fakeeditCharacterInitAdvantage_" + guid).addClass("fa-check-square-o");
                $("#fakeeditCharacterInitAdvantage_" + guid).removeClass("fa-square-o");
                $("#editCharacterInitAdvantage_" + guid).prop("checked", true);
            }
            
            $('#editCharacter_' + guid).toggle();

            $('#editCharacterButton_' + guid).toggle();
            $('#removeCharacterButton_' + guid).toggle();
            $('#saveCharacterButton_' + guid).toggle();
            $('#discardCharacterButton_' + guid).toggle();
        });
    }

    function displayInitBonus(bonus) {
        var sign = '';
        if (bonus === 0) {
            sign = '\u00B1';
        } else if (bonus > 0) {
            sign = '+';
        }

        return sign + bonus;
    }

    function displayInitAdvantage(advantage) {
        if (advantage !== 0) {
            return '<i class="far fa-fw fa-check-square"></i>';
        }
        else {
            return '<i class="far fa-fw fa-square"></i>';
        }
    }

    function displayCharacter(guid, character) {
        var characterli = $('<li id="character_' + guid + '" class="ui-state-default character"></li>');
        var editcharacterli = $('<li id="editCharacter_' + guid + '" class="ui-state-default editCharacter hide"></li>');
            
        var div = $('<div class="characterInfo">' +

            '<span class="characterName">' +
                '<span id="characterFaction_' + guid + '" class="hide">' + character.FactionId + '</span>' +
                '<i id="factionIcon_' + guid + '" class="fas fa-fw ' + character.FactionIcon + ' faction-' + character.FactionName + '"></i> ' +
                '<span id="characterName_' + guid + '">' + character.Name + '</span>' +
            '</span>' +

            '<span class="characterInitiative">' +
                '<span id="characterInitBonusValue_' + guid + '" class="hide">' + character.InitiativeBonus + '</span>' +
                '<i id="initBonusIcon_' + guid + '" class="fas fa-fw fa-hourglass-half faction-' + character.FactionName + '"></i> ' +
                '<span id="characterInitBonus_' + guid + '">' + displayInitBonus(character.InitiativeBonus) + '</span>' +
            '</span>' +

            '<span class="characterInitiative">' +
                '<span id="characterInitAdvantageValue_' + guid + '" class="hide">' + character.InitiativeAdvantage + '</span>' +
                '<i id="initAdvantageIcon_' + guid + '" class="fas fa-fw fa-chevron-double-up faction-' + character.FactionName + '"></i> ' +
                '<span id="characterInitAdvantage_' + guid + '">' + displayInitAdvantage(character.InitiativeAdvantage) + '</span>' +
            '</span>' +

            '<span class="characterEdit">' +
                '<button id="editCharacterButton_' + guid + '" data-guid="' + guid + '" class="ui-button ui-button-fa editCharacterButton">' + 
                    '<i class="fas fa-fw fa-pencil-alt"></i> Edit' + 
                '</button> ' +

                '<button id="removeCharacterButton_' + guid + '" data-guid="' + guid + '" class="ui-button ui-button-fa removeCharacterButton">' +
                    '<span class="redButton"><i class="fas fa-fw fa-trash-alt"></i> Remove</span>' + 
                '</button>' +

                '<button id="saveCharacterButton_' + guid + '" data-guid="' + guid + '" class="ui-button ui-button-fa hide saveCharacterButton">' + 
                    '<span class="greenButton"><i class="fas fa-fw fa-save"></i> Save</span>' + 
                '</button> ' +

                '<button id="discardCharacterButton_' + guid + '" data-guid="' + guid + '" class="ui-button ui-button-fa hide discardCharacterButton">' + 
                    '<span class="redButton"><i class="fas fa-fw fa-undo-alt"></i> Discard</span>' + 
                '</button>' +
            '</span>' +

            '</div>');

        var editDiv = $('<div class="editCharacterInfo">' +

            '<div class="characterName">' +
                '<i id="editFactionIcon_' + guid + '" class="fas fa-fw fa-users faction-' + character.FactionName + '"></i> ' +
                '<span>Faction: </span>' +
                '<select name="editCharacterFaction_' + guid + '" id="editCharacterFaction_' + guid + '">' +
                factionOptions +
                '</select>' +
            '</div>' +

            '<div class="characterName">' +
                '<i id="editNameIcon_' + guid + '" class="fas fa-fw ' + character.FactionIcon + ' faction-' + character.FactionName + '"></i> ' +
                '<span>Name: </span>' +
                '<input name="editCharacterName_' + guid + '" id="editCharacterName_' + guid + '" type="text" value="' + character.Name + '" />' +
            '</div>' +

            '<div class="characterInitiative">' +
                '<i id="editInitBonusIcon_' + guid + '" class="fas fa-fw fa-hourglass-half faction-' + character.FactionName + '"></i> ' +
                '<span>Initiative Bonus: </span>' +
                '<input name="editCharacterInitBonus_' + guid + '" id="editCharacterInitBonus_' + guid + '" type="number" value="' + character.InitiativeBonus + '" />' +
            '</div>' +

            '<div class="characterInitiative">' +
            '<i id="editInitAdvantageIcon_' + guid + '" class="fas fa-fw fa-chevron-double-up faction-' + character.FactionName + '"></i> ' +
                '<span>Initiative Advantage: </span>' +
                '<i class="far ' + (character.InitiativeAdvantage !== 0 ? 'fa-check-square' : 'fa-square') + ' fakeCheck" data-realcheck="editCharacterInitAdvantage_' + guid + '" id="fakeeditCharacterInitAdvantage_' + guid + '"></i>' +
                '<input type="checkbox" name="editCharacterInitAdvantage_' + guid + '" id="editCharacterInitAdvantage_' + guid + '" ' + (character.InitiativeAdvantage !== 0 ? 'checked = "checked"' : '') + ' style = "display:none" />' +
            '</div>' +

            '</div>');

        characterli.append(div);
        editcharacterli.append(editDiv);
        $('#' + character.FactionName + 'List').append(characterli).append(editcharacterli);

        $('#editCharacterFaction_' + guid + ' option[value=' + character.FactionId + ']').attr('selected', 'selected');
    }

    init();
});