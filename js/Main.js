"use strict";

var SN = function (social) {
    var that = this;
    if (social == 2) {// VK
        console.log('init vk social');
        that.load = function (url, callback, id) {
            var s = document.createElement('script');
            s.type = 'text/javascript';
            s.src = url;
            var script = document.getElementsByTagName('script')[1];
            script.parentNode.insertBefore(s, script);
            if (typeof id !== 'undefined') { script.setAttribute("id", id); }
            if (typeof callback === 'function') {
                s.addEventListener('load', function () {
                    callback();
                }, false);
            }
        };

        that.load('//vk.com/js/api/xd_connection.js?2', function () {
            that.showInviteBox = function () {
                VK.callMethod('showInviteBox');
            };

            VK.init({apiId: 5448769, onlyWidgets: true});

            // VK.Widgets.Like("vk_like", {type: "button"});
            // VK.Widgets.Subscribe("vk_subscribe", {}, -38679323);
        });

    } else if (social == 3) { //OK
        console.log('init ok social');
        var rParams = FAPI.Util.getRequestParameters();
        FAPI.init(rParams["api_server"], rParams["apiconnection"],
            function() {
                console.log("Инициализация прошла успешно");
                //FAPI.UI.setWindowSize(717, 1400); !!!!!
            }, function(error) {
                console.log("Ошибка инициализации");
            });
    }

    that.flash = function(){
        return document.getElementById("farm_game");
    };

    that.getProfile = function(userSocialId, params) {
        var fields = params.join();
        console.log('OK: try get user profile');
        FAPI.Client.call({"method":"users.getCurrentUser", "fields":fields}, that.getProfileCallback);
        // FAPI.Client.call({"method":"users.getInfo", "uids":[userSocialId], "fields":params}, that.API_callback);
    };
    that.getProfileCallback = function(result, data) {
        console.log('getProfileCallback result: ' + result);
        that.flash().getProfileHandler(data);
    };

    that.getAllFriends = function(userSocialId) {
        console.log('OK: try get getAllFriends');
        FAPI.Client.call({"method":"friends.get", "uid":userSocialId}, that.getAllFriendsCallback);
    };
    that.getAllFriendsCallback = function(result, data) {
        console.log('getAllFriendsCallback result: ' + result);
        that.flash().getAllFriendsHandler(data);
    };

    that.getUsersInfo = function(uids, params) {
        var ids = uids.join();
        var fields = params.join();
        console.log('OK: try get getUsersInfo');
        FAPI.Client.call({"method":"users.getInfo", "uids":ids, "fields":fields}, that.getUsersInfoCallback);
    };
    that.getUsersInfoCallback = function(result, data) {
        console.log('getUsersInfoCallback result: ' + result);
        that.flash().getUsersInfoHandler(data);
    };
    
    that.getFriendsByIds = function(uids, params) {
        var ids = uids.join();
        var fields = params.join();
        console.log('OK: try get getFriendsByIds');
        FAPI.Client.call({"method":"users.getInfo", "uids":ids, "fields":fields}, that.getFriendsByIdsCallback);
    };
    that.getFriendsByIdsCallback = function(result, data) {
        console.log('getFriendsByIdsCallback result: ' + result);
        that.flash().getFriendsByIdsHandler(data);
    };

    that.getAppUsers = function(userSocialId) {
        console.log('OK: try get getAppUsers');
        FAPI.Client.call({"method":"friends.getAppUsers"}, that.getAppUsersCallback);
    };
    that.getAppUsersCallback = function(result, data) {
        console.log('getAppUsersCallback result: ' + result);
        console.log('getAppUsersCallback data: ' + data);
        that.flash().getAppUsersHandler(data);
    };

    that.showInviteWindowAll = function(userSocialId) {
        console.log('OK: try get showInviteWindowAll');
        FAPI.UI.showInvite("Приглашаю посетить игру Умелые Лапки.");
    };

    that.showPayment = function(txt, txt2, id, price) {
        console.log('OK: try get showPayment');
        FAPI.UI.showPayment(txt, txt2, id, price, null, null, "ok", "true");
    };

    that.makeWallPost = function(uid, message, url){
        console.log('OK: try get makeWallPost');
        var attachment = {caption: 'Умелые Лапки', media:[{ images:[{ url: url, title: message }] }]};
        FAPI.Client.call({"method":"mediatopic.post", "uid":uid, "type":'USER', "attachment":attachment});
    };

};

function API_callback(method, result, data) {
    console.log('api_callback: 3');
    console.log("Method "+method+" finished with result "+result+", "+data);
    if (method == "showConfirmation" && result == "ok") {
        //FAPI.Client.call(feedPostingObject, function(status, data, error) {
        //    console.log(status + "   " + data + " " + error["error_msg"]);
        //}, data);
    } else if (method == "showPayment" && result == "ok") {
        document.getElementById("farm_game").onPaymentCallback(result);
    }
}

