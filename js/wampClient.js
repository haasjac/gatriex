"use strict";

var wampClient = {};

$(function () {

    wampClient.OpenAndSubscribe = function (topic, onMessage) {
        var connection = new autobahn.Connection({ url: 'ws://' + TwigOptions.domain + ':8888', realm: 'realm1' });

        connection.onopen = function (session) {
            session.subscribe(topic, onMessage);
        };

        connection.open();

        console.log("opening " + topic + " for connection " + 'ws://' + TwigOptions.domain + ':8888');
    };

    wampClient.EncodeGuid = function (guid) {
        return guid.replace(/-/g, '.');
    };

});