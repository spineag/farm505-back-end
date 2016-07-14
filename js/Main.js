"use strict";

var SN = function (social)
{
    /**
     * Load scripts asynchronously.
     * @param url
     * @param callback
     */
    this.load = function(url, callback, id) {
        var s = document.createElement('script'); s.type = 'text/javascript'; s.src = url;
        var script = document.getElementsByTagName('script')[1];
        script.parentNode.insertBefore(s, script);
        if(typeof id !== 'undefined') {
            script.setAttribute("id", id);
        }

        if(typeof callback === 'function') {
            s.addEventListener('load', function () { callback(); }, false);
        }
    };

    /**
     *  VKONTAKTE SOCIAL NETWORK
     */
    var that = this;
    that.load('//vk.com/js/api/xd_connection.js?2', function() {
        // getOffers = function () {
        //     VK.api('account.getActiveOffers', {v: '5.2'}, function(data) {
        //         if (data.response) {
        //             document.bt_game.useActiveOffers(data.response);
        //         }
        //     });
        // };
        // VK.api('users.get', { uids: SN_CONFIG.user_id, fields: 'first_name, last_name, bdate' },
        //     function(data)
        //     {
        //         if (data.response)
        //         {
        //             var $help = $('#help');
        //             if(typeof data.response[0].bdate !== "undefined")
        //             {
        //                 $.ajax({
        //                     url: SN_CONFIG.serverUrl + 'tools/update_birthdate.php',
        //                     type: 'POST',
        //                     dataType: 'json',
        //                     data: {social_id: SN_CONFIG.user_id, birth_date: data.response[0].bdate},
        //                     success: function (rdata)
        //                     {
        //                         console.log(rdata);
        //                     }
        //                 });
        //             }
        //
        //             $help.find('input[name=fullname]').val(data.response[0].first_name + " " + data.response[0].last_name);
        //             $help.find('input[name=uid]').val(SN_CONFIG.user_id);
        //             $help.find('#social_id').val(SN_CONFIG.user_id);
        //         }
        //     });
        // that.addToMenu = function ()
        // {
        //     VK.callMethod("showSettingsBox", 256);
        // };
        that.showInviteBox = function ()
        {
            // that.play();
            VK.callMethod('showInviteBox');
        };

        VK.init({apiId: 5448769, onlyWidgets: true});

        // VK.Widgets.Like("vk_like", {type: "button"});
        // VK.Widgets.Subscribe("vk_subscribe", {}, -38679323);
    });
};


