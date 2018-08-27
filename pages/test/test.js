/* global dataRequester */

$(function() {
    atest("Test https://na.leagueoflegends.com/test Test");
    
    function atest(data) {
        var arr = data.split(/\s+/);
        
        //var urlregex = new RegExp("^(http:\/\/www.|https:\/\/www.|ftp:\/\/www.|www.){1}([0-9A-Za-z]+\.)");
        var jv = new RegExp(/^(?:(?:(?:https?|ftp):)?\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})).?)(?::\d{2,5})?(?:[/?#]\S*)?$/i);
        //var urlregex = new RegExp("(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;:/~\+#]*[\w\-\@?^=%&amp;/~\+#])?");
        
        for (var i = 0; i < arr.length; i++) {
            var a = arr[i];
            var t = jv.test(a);
            if (t) {
                data = data.replace(a, function (original) {
                    return "<a href='" + original + "'>" + original + "</a>";
                });
            }
            console.log(a + " - " + t);
            $("#response").append(a + " - " + t + "<br>");
        }
        
        var hmm = data.replace(jv, function (original) {
            return "<a href='" + original + "'>" + original + "</a>";
        });
        
        $("#response").append("<br><br>" + hmm);
    }
});

