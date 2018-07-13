/* global dataRequester */

$(function () {
    "use strict";

    var contentData = {};

    function init() {
        setEventHandlers();
        getContent();
    }

    function dataReady(callback) {
        createCampaignList();
        if (callback) {
            callback();
        }
    }

    function getContent(callback) {
        dataRequester.apiCall('/api/tabletop/mycampaigns/GetCampaigns.php', "GET", null, function (response) {
            if (response.valid) {
                contentData = response.data.Campaigns;
                dataReady(callback);
            } else {
                $('#dialogMessage').html('<i class="fas fa-exclamation-triangle"></i> Error: ' + response.data.Error);
            }
        });
    }

    function createCampaignList() {
        $('#campaignList').empty();

        $.each(contentData, function (index, Campaign) {
            var campaign = $('<li id="campaign_' + index + '" class="ui-state-default campaign"></li>');

            var div = $('<div class="campaignDiv">' +
                '<span class="campaignSpan">' +
                    '<i class="fas fa-fw fa-book"></i> ' +
                    '<span id="campaignDisplay_' + index + '">' + Campaign.CampaignName + '</span>' +
                    '<input name="campaignName_' + index + '" id="campaignName_' + index + '" type="hidden" value="' + Campaign.CampaignName + '" />' +
                    '<input name="campaignGuid_' + index + '" id="campaignGuid_' + index + '" type="hidden" value="' + Campaign.Guid + '" />' +
                '</span>' +
                '<span class="campaignSpan">' +
                    ' <a href="/tabletop/campaign?id=' + Campaign.Guid + '" data-index="' + index + '" class="ui-button ui-button-fa linkCampaignButton campaignButton_' + index + '">' +
                    '<i class="fas fa-fw fa-user-edit"></i>' +
                    '</a>' +
                '</span>' +
                '</div>');

            var cancelButton = $('<button data-index="' + index + '" class="ui-button ui-button-fa cancelEditButton campaignButton_' + index + ' hide"></button>');
            var saveButton = $('<button data-index="' + index + '" class="ui-button ui-button-fa saveEditButton campaignButton_' + index + ' hide"></button>');
            var deleteButton = $('<button data-index="' + index + '" class="ui-button ui-button-fa deleteCampaignButton campaignButton_' + index + '"></button>');
            var editButton = $('<button data-index="' + index + '" class="ui-button ui-button-fa editCampaignButton campaignButton_' + index + '"></button>');

            cancelButton.append('<span class="redButton"><i class="fas fa-fw fa-undo-alt"></i> Discard</span>');
            saveButton.append('<span class="greenButton"><i class="fas fa-fw fa-save"></i> Save</span>');
            deleteButton.append('<span class="redButton"><i class="fas fa-fw fa-trash-alt"></i> Remove</span>');
            editButton.append('<span class=""><i class="fas fa-fw fa-pencil-alt"></i> Edit</span>');

            div.append(editButton).append(deleteButton).append(saveButton).append(cancelButton);

            campaign.append(div);
            $('#campaignList').append(campaign);
        });
    }

    function setEventHandlers() {
        $("#form").submit(function (e) {
            e.preventDefault();
            return false;
        });
        
        $(".addCampaignButton").click(function () {
            var data = {};
            data.CampaignName = $("#addCampaignName").val();
            if (data.CampaignName === "") {
                $('#dialogMessage').html('<i class="fas fa-exclamation-triangle"></i> Campaign Name cannot be empty.');
                return;
            }

            var postData = { "data": data };
            
            dataRequester.apiCall('/api/tabletop/mycampaigns/AddCampaign.php', "POST", postData, function (response) {
                if (response.valid) {
                    $("#addCampaignName").val("");
                    getContent(function () {
                        $('#dialogMessage').html('<i class="fas fa-check-circle"></i> Changes saved.');
                    });
                } else {
                    $('#dialogMessage').html('<i class="fas fa-exclamation-triangle"></i> Error: ' + response.data.Error);
                }
            });
        });

        $("#campaignList").on("click", ".editCampaignButton", function () {
            var index = $(this).attr("data-index");

            $('#campaignName_' + index).attr("type", "text");

            $('#campaignDisplay_' + index).toggle();
            $('.campaignButton_' + index).toggle();
        });

        $("#campaignList").on("click", ".deleteCampaignButton", function () {
            var index = $(this).attr("data-index");
            if (!index) {
                return;
            }            

            $("#removeCampaignDialogName").html('<span>' + $('#campaignName_' + index).val() + '</span>');

            $("#removeCampaignDialogBox").dialog({
                autoOpen: false,
                modal: true,
                width: 500,
                buttons: {
                    Remove: function () {
                        var data = {};
                        data.CampaignName = $('#campaignName_' + index).val();
                        data.Guid = $('#campaignGuid_' + index).val();

                        var postData = { "data": data };
                        dataRequester.apiCall('/api/tabletop/mycampaigns/RemoveCampaign.php', "POST", postData, function (response) {
                            if (response.valid) {
                                $('#campaign_' + index).remove();
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

            $("#removeCampaignDialogBox").dialog("open");
            
            var postData = { "data": data };
            dataRequester.apiCall('/api/tabletop/mycampaigns/RemoveCampaign.php', "POST", postData, function (response) {
                if (response.valid) {
                    $('#campaign_' + index).remove();
                    $('#dialogMessage').html('<i class="fas fa-check-circle"></i> Changes saved.');
                } else {
                    $('#dialogMessage').html('<i class="fas fa-exclamation-triangle"></i> Error: ' + response.data.Error);
                }
            });

        });

        $("#campaignList").on("click", ".saveEditButton", function () {
            var index = $(this).attr("data-index");

            var data = {};
            data.CampaignName = $('#campaignName_' + index).val();
            data.Guid = $('#campaignGuid_' + index).val();

            var postData = { "data": data };
            dataRequester.apiCall('/api/tabletop/mycampaigns/EditCampaign.php', "POST", postData, function (response) {
                if (response.valid) {
                    $('#campaignName_' + index).attr("type", "hidden");
                    $('#campaignDisplay_' + index).text($('#campaignName_' + index).val());

                    $('#campaignDisplay_' + index).toggle();
                    $('.campaignButton_' + index).toggle();
                    $('#dialogMessage').html('<i class="fas fa-check-circle"></i> Changes saved.');
                } else {
                    $('#dialogMessage').html('<i class="fas fa-exclamation-triangle"></i> Error: ' + response.data.Error);
                }
            });
        });

        $("#campaignList").on("click", ".cancelEditButton", function () {
            var index = $(this).attr("data-index");

            $('#campaignName_' + index).attr("type", "hidden");
            $('#campaignName_' + index).val($('#campaignDisplay_' + index).text());

            $('#campaignDisplay_' + index).toggle();
            $('.campaignButton_' + index).toggle();
        });
    }

    init();
});