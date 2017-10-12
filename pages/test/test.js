/* global dataRequester */

$(function() {
    dataRequester.apiCall("/api/riot/GetSummonerData.php", "GET", { summonerName: "Gatriex" }, function (response) {
        $("#response").html(JSON.stringify(response));
        console.log(response);
    });
});

