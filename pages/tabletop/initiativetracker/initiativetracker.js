/* global dataRequester */

$(function () {
    "use strict";

    var Characters = {}, CharacterInfo = [], Factions = {}, selectedCharacter;

    function init() {
        setEventHandlers();
        getContent();
    }
    
    function getContent() {
        if (TwigOptions.CampaignGuid) {
            dataRequester.apiCall('/api/tabletop/initiativetracker/GetCampaign.php', "GET", { "CampaignGuid": TwigOptions.CampaignGuid }, function (response) {
                if (response.valid) {
                    Characters = response.data.Characters;
                    CharacterInfo = response.data.CharacterInfo || [];
                    Factions = response.data.Factions;
                    if (response.data.CurrentCharacter !== null) {
                        selectedCharacter = Number(response.data.CurrentCharacter);
                    }
                    validateCharacterInfo();
                    createFactionList();
                    createCharacterList();
                    createInitList();
                } else {
                    $('#dialogMessage').html('<i class="fas fa-exclamation-triangle"></i> Error: ' + response.data.Error);
                }
            });
        } else {
            dataRequester.apiCall('/api/tabletop/mycampaigns/GetCampaigns.php', "GET", null, function (response) {
                if (response.valid) {
                    createCampaignList(response.data.Campaigns);
                } else {
                    $('#dialogMessage').html('<i class="fas fa-exclamation-triangle"></i> Error: ' + response.data.Error);
                }
            });
        }
    }

    function validateCharacterInfo() {
        var changeMade = false;

        $.each(CharacterInfo, function (index, character) {
            var valid = false;

            if (character && character.Guid) {
                if (Characters.hasOwnProperty(character.Guid)) {
                    valid = true;

                    if (!character.hasOwnProperty('Initiative') || !$.isNumeric(character.Initiative)) {
                        character.Initiative = 0;
                        changeMade = true;
                    }
                }
            }

            if (valid === false) {
                CharacterInfo.splice(index, 1);

                if (selectedCharacter > index) {
                    selectedCharacter--;
                }
                else if (index === selectedCharacter) {
                    selectedCharacter = undefined;
                }

                changeMade = true;
            }
        });

        if (selectedCharacter < 0 || selectedCharacter >= CharacterInfo.length) {
            selectedCharacter = undefined;
            changeMade = true;
        }

        if (changeMade) {
            saveCampaign();
        }
    }

    function createFactionList() {
        $.each(Factions, function (id, faction) {
            $("#addCharacterSelect").append('<optgroup id="optgroup_' + faction.Name + '" label="' + faction.Name + '" class="faction-' + faction.Name + '"></optgroup>');
        });
    }

    function createInitList() {
        if (!CharacterInfo) {
            return;
        }
        
        $.each(CharacterInfo, function (index, character) {
            addCharacter(Characters[character.Guid], character.Initiative);
        });

        if (selectedCharacter >= 0 && selectedCharacter < CharacterInfo.length) {
            $("#li_" + CharacterInfo[selectedCharacter].Guid).addClass("initSelected");
        }
    }

    function createCharacterList() {
        $.each(Characters, function (guid, character) {
            $("#optgroup_" + character.FactionName).append('<option value="' + guid + '">' + character.Name + '</option>');
        });        
    }

    function createCampaignList(Campaigns) {
        if (!Campaigns) {
            return;
        }

        $.each(Campaigns, function (index, Campaign) {
            addCampaign(Campaign);
        });
    }

    function setEventHandlers() {
        $("#addCharacterButton").click(function () {
            $("#addCharacterButton").toggle();
            $("#addSaveCharacterButton").toggle();
            $("#addDiscardCharacterButton").toggle();
            $("#addCharacterSelect").toggle();
        });

        $("#addSaveCharacterButton").click(function () {
            var guid = $("#addCharacterSelect").val();
            if (!guid) {
                return;
            }

            CharacterInfo.push({ "Guid": guid, "Initiative": "0" });
            addCharacter(Characters[guid], 0);
            saveCampaign();

            $("#addCharacterButton").toggle();
            $("#addSaveCharacterButton").toggle();
            $("#addDiscardCharacterButton").toggle();
            $("#addCharacterSelect").toggle();
        });

        $("#addDiscardCharacterButton").click(function () {
            $("#addCharacterSelect option[value='']").prop("selected", true);

            $("#addCharacterButton").toggle();
            $("#addSaveCharacterButton").toggle();
            $("#addDiscardCharacterButton").toggle();
            $("#addCharacterSelect").toggle();
        });
        
        $("#rollInitButton").click(function () {
            rollInit();
            sortList();
            saveCampaign();
        });

        $("#nextInitButton").click(function () {
            if (!CharacterInfo) {
                return;
            }

            if (selectedCharacter === undefined) {
                selectedCharacter = CharacterInfo.length - 1;
            }

            $("#li_" + CharacterInfo[selectedCharacter].Guid).removeClass("initSelected");
            selectedCharacter += 1;
            if (selectedCharacter >= CharacterInfo.length) {
                selectedCharacter = 0;
            }
            $("#li_" + CharacterInfo[selectedCharacter].Guid).addClass("initSelected");
            saveSelected();
        });

        $("#prevInitButton").click(function () {
            if (!CharacterInfo) {
                return;
            }

            if (selectedCharacter === undefined) {
                selectedCharacter = 0;
            }

            $("#li_" + CharacterInfo[selectedCharacter].Guid).removeClass("initSelected");
            selectedCharacter -= 1;
            if (selectedCharacter < 0) {
                selectedCharacter = CharacterInfo.length - 1;
            }
            $("#li_" + CharacterInfo[selectedCharacter].Guid).addClass("initSelected");
            saveSelected();
        });

        $("#clearInitButton").click(function () {
            if (!CharacterInfo) {
                return;
            }

            clearInitSelected();
            saveSelected();
        });

        $("#initList").on("click", ".editCharacterButton", function () {
            var guid = $(this).attr("data-guid");

            $("#editCharacterButton_" + guid).toggle();
            $("#removeCharacterButton_" + guid).toggle();
            $("#saveCharacterButton_" + guid).toggle();
            $("#discardCharacterButton_" + guid).toggle();

            $("#characterInit_" + guid).toggle();
            $("#characterInitInput_" + guid).toggle();
        });
        
        $("#initList").on("click", ".removeCharacterButton", function () {
            var guid = $(this).attr("data-guid");
            $.each(CharacterInfo, function (index, character) {
                if (character.Guid === guid) {
                    if (selectedCharacter !== undefined) {
                        if (selectedCharacter === index) {
                            clearInitSelected();
                        } else if (selectedCharacter > index) {
                            selectedCharacter -= 1;
                        }                        
                    }
                    CharacterInfo.splice(index, 1);
                    return false;
                }
            });

            $("#li_" + guid).remove();
            $("#addCharacterSelect option[value='" + guid + "']").prop("hidden", false);
            saveCampaign();
        });

        $("#initList").on("click", ".saveCharacterButton", function () {
            var guid = $(this).attr("data-guid");

            $("#characterInit_" + guid).text(Number($("#characterInitInput_" + guid).val()));
            $.each(CharacterInfo, function (index, character) {
                if (character.Guid === guid) {
                    character.Initiative = Number($("#characterInitInput_" + guid).val());
                    return false;
                }
            });

            $("#editCharacterButton_" + guid).toggle();
            $("#removeCharacterButton_" + guid).toggle();
            $("#saveCharacterButton_" + guid).toggle();
            $("#discardCharacterButton_" + guid).toggle();

            $("#characterInit_" + guid).toggle();
            $("#characterInitInput_" + guid).toggle();

            sortList();
            saveCampaign();
        });

        $("#initList").on("click", ".discardCharacterButton", function () {
            var guid = $(this).attr("data-guid");

            $("#characterInitInput_" + guid).val($("#characterInit_" + guid).text());

            $("#editCharacterButton_" + guid).toggle();
            $("#removeCharacterButton_" + guid).toggle();
            $("#saveCharacterButton_" + guid).toggle();
            $("#discardCharacterButton_" + guid).toggle();

            $("#characterInit_" + guid).toggle();
            $("#characterInitInput_" + guid).toggle();
        }); 

        $("#initList").on("keydown", ".characterInit", function (e) {
            if (e.which === 13) {
                var guid = $(this).attr("data-guid");
                $("#saveCharacterButton_" + guid).click();
            }
        });  
    }

    function sortList() {
        if (!CharacterInfo) {
            return;
        }

        var initList = $('#initList');

        var listitems = $('li', initList);
        
        listitems.sort(function (a, b) {
            var A = {}, B = {};

            A.Guid = $(a).attr("data-guid");
            A.Init = Number($("#characterInitInput_" + A.Guid).val());
            A.Faction = Characters[A.Guid].FactionPrecedence;
            A.Bonus = Characters[A.Guid].InitiativeBonus;

            B.Guid = $(b).attr("data-guid");
            B.Init = Number($("#characterInitInput_" + B.Guid).val());
            B.Faction = Characters[B.Guid].FactionPrecedence;
            B.Bonus = Characters[B.Guid].InitiativeBonus;

            if (A.Init !== B.Init) {
                return A.Init < B.Init ? 1 : -1;
            }
            else if (A.Faction !== B.Faction) {
                return A.Faction < B.Faction ? 1 : -1;
            }
            else {
                return A.Bonus < B.Bonus ? 1 : -1;
            }
        });

        var charInfo = [];

        $.each(listitems, function (index, element) {
            var character = {};
            character.Guid = $(element).attr("data-guid");
            character.Initiative = Number($("#characterInitInput_" + character.Guid).val());
            
            charInfo.push(character);

            if ($(element).hasClass("initSelected")) {
                selectedCharacter = charInfo.length - 1;
            }
        });

        CharacterInfo = charInfo;

        initList.append(listitems);
    }

    function rollInit() {
        if (!CharacterInfo) {
            return;
        }

        var initList = $('#initList');

        var listitems = $('li', initList);

        $.each(CharacterInfo, function (index, character) {
            var roll = rollDice(20);
            var advantage = !!Characters[character.Guid].InitiativeAdvantage;
            var bonus = Characters[character.Guid].InitiativeBonus;
            
            if (advantage) {
                roll = Math.max(roll, rollDice(20));
            }

            roll = roll + bonus;

            character.Initiative = roll;
            $("#characterInit_" + character.Guid).text(roll);
            $("#characterInitInput_" + character.Guid).val(roll);
        });

        clearInitSelected();
    }

    function clearInitSelected() {
        $(".initSelected").removeClass("initSelected");
        selectedCharacter = undefined;
    }

    function rollDice(sides) {
        var min = 1;
        var max = sides;

        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    function addCharacter(character, initiative) {
        $("#addCharacterSelect option[value='" + character.Guid + "']").prop("hidden", true);
        $("#addCharacterSelect option[value='']").prop("selected", true);

        var item = $('<li id="li_' + character.Guid + '" class="ui-state-default" data-guid="' + character.Guid + '"></li>');

        var portrait;
        if (character.Portrait) {
            portrait = '<span><img class="characterPortrait" src="/userdata/tabletop/characters/' + character.Guid + '/' + character.Portrait + '" /> </span>';
        }
        else {
            portrait = '<i class="fas fa-fw ' + character.FactionIcon + '"></i> ';
        }

        var div = $('<div class="characterDiv faction-' + character.FactionName + '">' +
            '<span class="characterSelectedSpan">' +
            '<i class="selectedIcon fas fa-chevron-double-right"> </i>' +
            '</span>' +

            '<span class="characterSpan">' +
            portrait +
            '<span class="characterName">' + character.Name + ' </span>' +
            '</span>' +

            '<span class="characterInitSpan">' +
            '<i class="fas fa-dice"></i> ' +
            '<span id="characterInit_' + character.Guid + '">' + initiative + '</span>' +
            '<input id="characterInitInput_' + character.Guid + '" type="number" class="characterInit hide" value="' + initiative + '" data-guid="' + character.Guid + '" />' +
            '</span>' +

            '<span class="characterButtonSpan">' +
            '<button id="editCharacterButton_' + character.Guid + '" class="ui-button editCharacterButton" data-guid="' + character.Guid + '">' +
                '<i class="fas fa-pencil-alt blueButton"></i>' +
            '</button>' +
            ' <button id="removeCharacterButton_' + character.Guid + '" class="ui-button removeCharacterButton" data-guid="' + character.Guid + '">' +
                '<i class="fas fa-trash-alt redButton"></i>' +
            '</button>' +
            '<button id="saveCharacterButton_' + character.Guid + '" class="ui-button saveCharacterButton hide" data-guid="' + character.Guid + '">' +
                '<i class="fas fa-save greenButton"></i>' +
            '</button>' +
            ' <button id="discardCharacterButton_' + character.Guid + '" class="ui-button discardCharacterButton hide" data-guid="' + character.Guid + '">' +
                '<i class="fas fa-undo-alt redButton"></i>' +
            '</button>' +
            '</span>' +

            '</div>');
        $('#initList').append(item.append(div));
    }

    function addCampaign(Campaign) {
        var item = $('<li id="li_' + Campaign.Guid + '" class="ui-state-default"></li>');
        var div = $('<a href="?id=' + Campaign.Guid + '">' +
            '<div class="campaignDiv">' +            
            '<span class="campaignSpan">' +
            '<i class="fas fa-book"></i>' +
            '<span class="campaignName"> ' + Campaign.CampaignName + '</span>' +
            '</span>' +
            '</div>' +
            '</a>');
        $('#campaignList').append(item.append(div));
    }

    function saveSelected() {
        var data = {};

        data.CampaignGuid = TwigOptions.CampaignGuid;
        data.CurrentCharacter = selectedCharacter;

        dataRequester.apiCall('/api/tabletop/initiativetracker/SaveCurrentCharacter.php', "PUT", JSON.stringify(data), function (response) {
            if (!response.valid) {
                $('#dialogMessage').html('<i class="fas fa-exclamation-triangle"></i> Error: ' + response.data.Error);
            }
        });
    }

    function saveCampaign() {        
        var data = {};

        data.CampaignGuid = TwigOptions.CampaignGuid;
        data.CharacterInfo = CharacterInfo;
        data.CurrentCharacter = selectedCharacter;
        
        dataRequester.apiCall('/api/tabletop/initiativetracker/SaveCampaign.php', "PUT", JSON.stringify(data), function (response) {
            if (response.valid) {
                //$('#dialogMessage').html('<i class="fas fa-save"></i>');
                //setTimeout(function () { $('#dialogMessage').html(''); }, 1000);
            } else {
                $('#dialogMessage').html('<i class="fas fa-exclamation-triangle"></i> Error: ' + response.data.Error);
            }
        });
    }

    init();
});
