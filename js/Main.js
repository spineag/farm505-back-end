"use strict";

var SN = function (social) {
    var that = this;
    if (social == 2) {// VK
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
        that.load($_GET('api_server') + 'js/fapi5.js', function() {
            FAPI.init($_GET('api_server'), $_GET('apiconnection'),
                function() {
                    //	 alert("connect ok");
                }, function(error){
                    alert("Forticom API initialization failed");
                });
            FAPI.Client.initialize();

            var $help = $('#help');
            $help.find('input[name=uid]').val(FAPI.Client.uid);
            $help.find('#social_id').val(FAPI.Client.uid);

            FAPI.Client.call({
                "method" : "users.getInfo",
                "uids" : FAPI.Client.uid,
                "fields" : "first_name,last_name,birthday"
            }, function(status, data, error) {
                if (status == "ok")
                {
                    if(typeof data[0].birthday !== "undefined")
                    {
                        //$.ajax({
                        //    url: SN_CONFIG.serverUrl + 'tools/update_birthdate.php',
                        //    type: 'POST',
                        //    dataType: 'json',
                        //    data: {social_id: SN_CONFIG.user_id, birth_date: data[0].birthday},
                        //    success: function (rdata)
                        //    {
                        //        console.log(rdata);
                        //    }
                        //});
                    }

                    //$help.find('input[name=fullname]').val(data[0].first_name + " " + data[0].last_name);
                }
            });
            that.showInviteBox = function ()
            {
                //that.play();
                FAPI.UI.showInvite('Приглашаю посетить игру Умелые Лапки.', 'customAttr=customValue');
            };

            var API_callback = function(method, status, data)
            {
                switch (method)
                {
                    case 'showNotification':
                        document.farm_game.showNotification({result: status, method: method, data: data});
                        break;
                    default:

                        break;
                }
            };

            that.okInvite = function(text, attr, uid)
            {
                FAPI.UI.showNotification(text, attr, uid);
            };
        });
    }
};


