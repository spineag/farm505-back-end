"use strict";
//OLD SN FUNCTIONS
var setLog = function(value)
{
    $('input[name=log]').val(value);
};
//
//var getOffers;
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

    //this.canReloadGame = function() {
    //    var reloadCountStart = document.cookie.indexOf("game_reload=");
    //    if (reloadCountStart != -1) {
    //        var reloadCount = document.cookie.substring(reloadCountStart + "game_reload=".length, reloadCountStart + "game_reload=".length + 1);
    //        if (parseInt(reloadCount) >= 5) {
    //            return false;
    //        } else {
    //            var value = parseInt(reloadCount) + 1;
    //
    //            var expirationDateStart = document.cookie.indexOf("game_exp=");
    //            if (expirationDateStart == -1) {
    //                return false;
    //            }
    //            var expirationDateEnd  = document.cookie.indexOf(";", expirationDateStart);
    //            if (expirationDateEnd == -1) {
    //                expirationDateEnd = document.cookie.length;
    //            }
    //            var expirationDate = document.cookie.substring(expirationDateStart, expirationDateEnd);
    //            document.cookie = 'game_reload='  + value + '; expires=' + expirationDate + '; path=/';
    //            return true;
    //        }
    //    } else {
    //        var now = new Date();
    //        var time = now.getTime();
    //        var reload = 1;
    //        time += 3600 * 1000;
    //        now.setTime(time);
    //        document.cookie = 'game_reload='  + reload + '; expires=' + now.toUTCString() + '; path=/';
    //        document.cookie = 'game_exp='  + now.toUTCString() + '; expires=' + now.toUTCString() + '; path=/';
    //        return true;
    //    }
    //};
    //
    //this.reload = function ()
    //{
    //    if (that.canReloadGame() == false) {
    //        return;
    //    }
    //    var guid = (function() {
    //        function s4() {
    //            return Math.floor((1 + Math.random()) * 0x10000)
    //                .toString(16)
    //                .substring(1);
    //        }
    //        return function() {
    //            return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
    //                s4() + '-' + s4() + s4() + s4();
    //        };
    //    })();
    //
    //    SN_CONFIG.flashvars.session_guid = guid();
    //
    //    $('#gameContainer').html('<div id="flash_container">'+
    //        '<div id="loader">'+
    //            '<img src="/images/ajax-loader.gif" />'+
    //        '</div>'+
    //        '<div id="no_player">'+
    //            '<h1>Update Flash Player!</h1>'+
    //            '<p>'+
    //                '<a target="_blank" href="http://www.adobe.com/go/getflashplayer">'+
    //                    '<img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" />'+
    //                '</a>'+
    //            '</p>'+
    //        '</div>'+
    //    '</div>');
    //
    //    FarmNinja.init();
    //};

    /**
     *  VKONTAKTE SOCIAL NETWORK
     */
    that.load('//vk.com/js/api/xd_connection.js?2', function() {
        getOffers = function () {
            VK.api('account.getActiveOffers', {v: '5.2'}, function(data) {
                if (data.response) {
                    document.bt_game.useActiveOffers(data.response);
                }
            });
        };
        VK.api('users.get', { uids: SN_CONFIG.user_id, fields: 'first_name, last_name, bdate' },
        function(data)
        {
            if (data.response)
            {
                var $help = $('#help');
                if(typeof data.response[0].bdate !== "undefined")
                    {
                    $.ajax({
                    url: SN_CONFIG.serverUrl + 'tools/update_birthdate.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {social_id: SN_CONFIG.user_id, birth_date: data.response[0].bdate},
                    success: function (rdata)
                        {
                         console.log(rdata);
                        }
                    });
                    }

                    $help.find('input[name=fullname]').val(data.response[0].first_name + " " + data.response[0].last_name);
                    $help.find('input[name=uid]').val(SN_CONFIG.user_id);
                    $help.find('#social_id').val(SN_CONFIG.user_id);
                    }
                    });
                    that.addToMenu = function ()
                    {
                        VK.callMethod("showSettingsBox", 256);
                    };
                    that.showInviteBox = function ()
                    {
                        that.play();
                        VK.callMethod('showInviteBox');
                    };

                    VK.init({apiId: 2647146, onlyWidgets: true});

                    VK.Widgets.Like("vk_like", {type: "button"});
                    VK.Widgets.Subscribe("vk_subscribe", {}, -38679323);
                });
                that.shop = function() {
                    window.open('http://birdstownshop.prostoprint.com/pages/main/?utm_source='+SN_CONFIG.sn+'_panel');
                };
                that.openCommunity = function ()
                {
                    window.open('http://vk.com/club38679323');
                };
                that.showPayments = function ()
                {
                    that.play();
                    document.bt_game.showPayment("show");
                };
    };

/**
 *
 * @type {{send: send, returnResult: returnResult}}
 */
//var SUPPORT =
//{
//    themeChange: function(elem)
//    {
//        var $elem = $(elem);
//        if(elem.value == $elem.find('option').last().val())
//        {
//            $elem.remove();
//            $('#themeTarget').after('<input type="text" name="theme" />');
//        }
//    },
//    send: function (e)
//    {
//        $('#helpForm').css('display', 'none');
//        var $helpMessage = $('#helpMessage');
//
//        $.ajax({
//            type: 'POST',
//            url: SN_CONFIG.serverUrl + 'tools/support.php',
//            dataType: 'json',
//            data: {
//                user_name: $('input[name=fullname]').val(),
//                email: $('input[name=email]').val(),
//                description: $('textarea[name=description]').val(),
//                theme: (($('input[name=theme]').length != 0) ? $('input[name=theme]').val() : $('select[name=theme]').val()),
//                log: $('input[name=log]').val(),
//                uid: $('input[name=uid]').val(),
//                logStatus: logStatus
//            },
//            success: function(rdata)
//            {
//                if(rdata.success)
//                {
//                    $helpMessage.attr('class', '');
//                    $helpMessage.addClass('success');
//                    $helpMessage.html(SN_CONFIG.lng.successMessage + ' <a href="#" onclick="SUPPORT.sendMore(event);">'+SN_CONFIG.lng.sendMore+'</a>');
//                    $helpMessage.fadeIn(300);
//                }
//                else
//                {
//                    var errorMessage = '';
//                    switch(rdata.error_code)
//                    {
//                        case 1:
//                        case 4:
//                            errorMessage = SN_CONFIG.lng.errorEmpty;
//                        break;
//                        case 5:
//                            errorMessage = SN_CONFIG.lng.errorEmpty;
//                        break;
//                        case 10:
//                            errorMessage = SN_CONFIG.lng.errorEmpty;
//                        break;
//                        default:
//                            errorMessage = 'Error. ';
//                        break;
//                    }
//                    $helpMessage.attr('class', '');
//                    $helpMessage.addClass('error');
//                    $helpMessage.html(errorMessage + ' <a href="#" onclick="SUPPORT.sendMore(event);">'+SN_CONFIG.lng.tryAgain+'</a>');
//                    $helpMessage.fadeIn(300);
//                }
//
//            },
//            error:  function(xhr, str)
//            {
//                $helpMessage.attr('class', '');
//                $helpMessage.addClass('error');
//                $helpMessage.html(str+ ' <a href="#" onclick="SUPPORT.sendMore(event);">'+SN_CONFIG.lng.tryAgain+'</a>');
//                $helpMessage.fadeIn(300);
//            }
//        });
//    },
//    sendMore: function(e)
//    {
//        e.preventDefault();
//        $('#helpMessage').css('display', 'none');
//        $('#helpForm').fadeIn(300);
//    }
//};
//
//var LOCALE = {
//    change: function(lang) {
//        $.ajax({
//            url: SN_CONFIG.serverUrl + 'tools/fb/switchLanguage.php',
//            type: 'POST',
//            dataType: 'json',
//            data: {social_id: SN_CONFIG.user_id, gameLanguage: lang},
//            success: function (rdata) {
//                if (rdata.success) {
//                Analytics.sendActivity('loading', 'change_lng', rdata.previousLanguage + '_' + lang);
//                setTimeout(function() {location.reload();}, 800);
//                }
//            }
//        });
//    }
//};

/**
 * Game loader.
 * @type {{init: init, callbackFn: callbackFn}}
 */
var BirdsTown = {

    init: function() {
        //if(typeof SN_CONFIG.swf === "undefined")
        //{
        //    BT.load_jsonp(SN_CONFIG.serverUrl + 'get_game.php', {user_id: SN_CONFIG.user_id}, 'BirdsTown.embed');
        //    //logStatus = "swf undefined embed";
        //}
        //else
        //{
            this.embed();
            //logStatus = "swf manual embed";
        //}
        FARM505.play();
        //if (SN_CONFIG.sn == 'fb') {
        //   // _MauDauFB.init();
        //}
    },
    embed: function()
    {
        swfobject.embedSWF('/client/farm505.swf', 'flash_container', '100%', 660, '10.0', null, SN_CONFIG.flashvars, SN_CONFIG.params, SN_CONFIG.attributes, this.callbackFn);
        //logStatus = "swf embed";
    },
    callbackFn : function(e) {
        if(!e.success)
        {
            document.getElementById('no_player').style.display = 'block';
            document.getElementById('loader').style.display = 'none';
        }
    }
};