/* global dataRequester */

/*$(function() {
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
});*/

/*
// Create WebSocket connection.
const socket = new WebSocket('ws://localhost:8888');

// Connection opened
socket.addEventListener('open', function (event) {
    console.log(event);
    socket.send('Hello Server!');
});

// Listen for messages
socket.addEventListener('message', function (event) {
    console.log('Message from server ', event.data);
});

// Listen for messages
socket.addEventListener('error', function (event) {
    console.log('Error from server ', event.data);
});

// Listen for messages
socket.addEventListener('close', function (event) {
    console.log('Close from server ', event.data);
});
*/

/*
var conn = new ab.Session('ws://localhost:8888',
    function () {
        conn.subscribe('InitiativeTracker/5', function (topic, data) {
            // This is where you would add the new article to the DOM (beyond the scope of this tutorial)
            console.log('New article published to category "' + topic + '" : ' + data.test);
        });
    },
    function () {
        console.warn('WebSocket connection closed');
    },
    { 'skipSubprotocolCheck': true }
);
*/

$(function () {
    var connection = new autobahn.Connection({ url: 'ws://localhost:8888', realm: 'realm1' });

    connection.onopen = function (session) {
        session.subscribe('test', onMessage);
    };

    connection.open();
});

function onMessage($message) {
    console.log($message);
}

