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
                $('#dialogMessage').html('<i class="fa fa-exclamation-triangle"></i> Error: ' + response.data.Error);
            }
        });
    }

    function createCampaignList() {
        $('#campaignList').empty();

        $.each(contentData, function (index, Campaign) {
            var campaign = $('<li id="campaign_' + index + '" class="ui-state-default campaign"></li>');

            var div = $('<div>' +
                '<span id="campaignDisplay_' + index + '">' + Campaign.CampaignName + '</span>' +
                ' <a href="/tabletop/campaign?id=' + Campaign.Guid + '" data-index="' + index + '" class="ui-button ui-button-fa linkCampaignButton campaignButton_' + index + '">' +
                '<i class="fa fa-users"></i>' +
                '</a>' +
                ' <a href="/tabletop/initiativeTracker?id=' + Campaign.Guid + '" data-index="' + index + '" class="ui-button ui-button-fa linkCampaignButton campaignButton_' + index + '">' +
                '<i class="fa fa-list-ol"></i>' +
                '</a>' +
                '<input name="campaignName_' + index + '" id="campaignName_' + index + '" type="hidden" value="' + Campaign.CampaignName + '" />' +
                '<input name="campaignGuid_' + index + '" id="campaignGuid_' + index + '" type="hidden" value="' + Campaign.Guid + '" />' +
                '</div>');

            var cancelButton = $('<button data-index="' + index + '" class="ui-button ui-button-fa cancelEditButton campaignButton_' + index + ' hide"></button>');
            var saveButton = $('<button data-index="' + index + '" class="ui-button ui-button-fa saveEditButton campaignButton_' + index + ' hide"></button>');
            var deleteButton = $('<button data-index="' + index + '" class="ui-button ui-button-fa deleteCampaignButton campaignButton_' + index + '"></button>');
            var editButton = $('<button data-index="' + index + '" class="ui-button ui-button-fa editCampaignButton campaignButton_' + index + '"></button>');

            cancelButton.append('<span class="redButton"><i class="fa fa-ban"></i> Discard</span>');
            saveButton.append('<span class="greenButton"><i class="fa fa-check"></i> Save</span>');
            deleteButton.append('<span class="redButton"><i class="fa fa-remove"></i> Remove</span>');
            editButton.append('<span class=""><i class="fa fa-pencil"></i> Edit</span>');

            div.append(cancelButton).append(saveButton).append(deleteButton).append(editButton);

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
                $('#dialogMessage').html('<i class="fa fa-exclamation-triangle"></i> Campaign Name cannot be empty.');
                return;
            }

            var postData = { "data": data };
            
            dataRequester.apiCall('/api/tabletop/mycampaigns/AddCampaign.php', "POST", postData, function (response) {
                if (response.valid) {
                    getContent(function () {
                        $('#dialogMessage').html('<i class="fa fa-check-circle"></i> Changes saved.');
                    });
                } else {
                    $('#dialogMessage').html('<i class="fa fa-exclamation-triangle"></i> Error: ' + response.data.Error);
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

            var data = {};
            data.CampaignName = $('#campaignName_' + index).val();
            data.Guid = $('#campaignGuid_' + index).val();
            
            var postData = { "data": data };
            dataRequester.apiCall('/api/tabletop/mycampaigns/RemoveCampaign.php', "POST", postData, function (response) {
                if (response.valid) {
                    $('#campaign_' + index).remove();
                    $('#dialogMessage').html('<i class="fa fa-check-circle"></i> Changes saved.');
                } else {
                    $('#dialogMessage').html('<i class="fa fa-exclamation-triangle"></i> Error: ' + response.data.Error);
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
                    $('#dialogMessage').html('<i class="fa fa-check-circle"></i> Changes saved.');
                } else {
                    $('#dialogMessage').html('<i class="fa fa-exclamation-triangle"></i> Error: ' + response.data.Error);
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