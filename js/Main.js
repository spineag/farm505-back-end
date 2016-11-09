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

        
        // smth old
        // that.load($_GET('api_server') + 'js/fapi5.js', function() {
        //     FAPI.init($_GET('api_server'), $_GET('apiconnection'),
        //         function() {
        //             //	 alert("connect ok");
        //         }, function(error){
        //             alert("Forticom API initialization failed");
        //         });
        //     FAPI.Client.initialize();
        //
        //     var $help = $('#help');
        //     $help.find('input[name=uid]').val(FAPI.Client.uid);
        //     $help.find('#social_id').val(FAPI.Client.uid);
        //
        //     FAPI.Client.call({
        //         "method" : "users.getInfo",
        //         "uids" : FAPI.Client.uid,
        //         "fields" : "first_name,last_name,birthday"
        //     }, function(status, data, error) {
        //         if (status == "ok")
        //         {
        //             if(typeof data[0].birthday !== "undefined")
        //             {
        //                 //$.ajax({
        //                 //    url: SN_CONFIG.serverUrl + 'tools/update_birthdate.php',
        //                 //    type: 'POST',
        //                 //    dataType: 'json',
        //                 //    data: {social_id: SN_CONFIG.user_id, birth_date: data[0].birthday},
        //                 //    success: function (rdata)
        //                 //    {
        //                 //        console.log(rdata);
        //                 //    }
        //                 //});
        //             }
        //
        //             //$help.find('input[name=fullname]').val(data[0].first_name + " " + data[0].last_name);
        //         }
        //     });
        //     that.showInviteBox = function ()
        //     {
        //         //that.play();
        //         FAPI.UI.showInvite('Приглашаю посетить игру Умелые Лапки.', 'customAttr=customValue');
        //     };
        //
        //     var API_callback = function(method, status, data)
        //     {
        //         switch (method)
        //         {
        //             case 'showNotification':
        //                 document.farm_game.showNotification({result: status, method: method, data: data});
        //                 break;
        //             default:
        //
        //                 break;
        //         }
        //     };
        //
        //     that.okInvite = function(text, attr, uid)
        //     {
        //         FAPI.UI.showNotification(text, attr, uid);
        //     };
        // });
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



    // that.API_callback = function(method, result, data){ // for OK
    //     console.log("Method " + method + " finished with result " + result + ", data:" + data);
    // }
};


