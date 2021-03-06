var FarmNinjaFB = {
    user_sid: false,
    swf: {},
    version: -1,
    language: 1,

    getVersion: function(uSocialId) {
        $.ajax({
            type:'post',
            url:'../php/api-v1-0/getVersionClient.php',
            data: "channelId=4&userSocialId="+uSocialId,
            response:'text',
            success:function (v) {
                console.log('current version: ' + v);
                FarmNinjaFB.setVersion(v);
            },
            errrep:true,
            error:function(num) {
                console.log('error get client version');
            }
        })
    },

    setVersion: function(v) {
        this.version = v;
        this.init();
    },

    init: function () {
        if (this.version == '0') {
            $('#gameContainer').html('<div id="flash_container">' +
                '<div id="404">' +
                '<img src="https://505.ninja/images/404/window404.png" alt="На ремонте" />' +
                '</div>' +
                '</div>');
        } else {
            var url = document.location.toString().split('?');
            var flashvars = {
                data: (url[1] ? '&' + url[1] : ''),
                protocol: (document.location.protocol == 'https:') ? 'https' : 'http',
                channel: 4,
                gacid: this.getUserGAcid()
            };

            var params = {
                allowFullscreen: "true",
                allowFullScreenInteractive: "true",
                allowScriptAccess: "always",
                wmode: "direct",
                flashvars: flashvars
            };
            var attributes = {
                id: "farm_game",
                name: "farm_game"
            };
            swfobject.embedSWF('client_fb/farm' + this.version + '.swf', 'flash_container', '100%', '640px', '13.0', null, flashvars, params, attributes, this.callbackFn);
        }
    },

    callbackFn: function (e) {
        if (!e.success) {
            console.log('bad with load swf');
            $('#loader').css('display', 'none');
            $('#no_player').css('display', 'block');
        }
        else {
            window.onresize = FarmNinjaFB.bodyResize;
            setTimeout(FarmNinjaFB.bodyResize, 500);
        }
    },
    
    bodyResize: function(event) {
        var h = $('.game_body').height() - 28;
        if (h < 500) h = 500;
        if (h > 1000) h = 1000;
        console.log('h: ' + h);
        $('#farm_game').height(h);
    },

    reload: function () {
        console.log('reload game');
        window.onresize = null;
        $('#gameContainer').html('<div id="flash_container">' +
            '<div id="loader">' +
            '<img src="/images/ajax-loader.gif" />' +
            '</div>' +
            '<div id="no_player">' +
            '<a target="_blank" href="http://www.adobe.com/go/getflashplayer">' +
            '<img src="https://505.ninja/images/up_flash.jpg" alt="Get Adobe Flash player" />' +
            '</a>' +
            '</div>' +
            '</div>');
        this.init();
    },

    getUserGAcid: function () {
        var match = document.cookie.match('(?:^|;)\\s*_ga=([^;]*)');
        var raw = (match) ? decodeURIComponent(match[1]) : null;
        if (raw) {
            match = raw.match(/(\d+\.\d+)$/);
        } else return 'unknown';
        var gacid = (match) ? match[1] : null;
        if (gacid) {
            return gacid;
        } else return 'unknown';
    },

    getUserGAcidForAS: function () {
        var gacid = this.getUserGAcid();
        var flash =	document.getElementById("farm_game");
        flash.sendGAcidToAS(gacid);
    },

    checkUserLanguageForIFrame: function(userSocialId) {
        $.ajax({
            type:'post',
            url:'../php/api-v1-0/getUserLanguage.php',
            data: {channelId: 4, userSocialId: userSocialId},
            response:'text',
            success:function (v) {
                console.log('iframe language: ' + v);
                FarmNinjaFB.setLanguage(v);
            },
            errrep:true,
            error:function(num) {
                console.error('error get user language with NUM error: ' + num);
            }
        })
    },

    setLanguage: function(v) {
        console.log('setLanguage: ' + v);
        v = parseInt(v);
        this.language = v;
        var bRU = document.getElementsByClassName('ru');
        var bENG = document.getElementsByClassName('eng');
        var langRU = document.getElementsByClassName('lang');
        var langENG = document.getElementsByClassName('langENG');
        langRU[0].style.display = 'block';
        langENG[0].style.display = 'block';
        if (v == 2) {
            bRU[0].style.display = 'none';
            bENG[0].style.display = 'block';
        } else {
            bRU[0].style.display = 'block';
            bENG[0].style.display = 'none';
        }
    },

    showLanguage: function() {
        var dChange = document.getElementById('change_language');
        if (dChange) dChange.style.display = 'block';
        document.getElementById("farm_game").onOpenLanguage();
    },

    hideLanguage: function() {
        var dChange = document.getElementById('change_language');
        if (dChange) dChange.style.display = 'none';
    },

    chooseLanguage: function(v) {
        hideLanguage();
        if (v != this.language) {
            console.log('change language to: ' + v);
            document.getElementById("farm_game").changeLanguage(v);
        }
    },

    saveTransaction: function(usid, packId, requestId, browserName, versionBrowser, OS) {
        $.ajax({
            type:'post',
            url:'../php/api-v1-0/onFBTransactionSave.php',
            data: {userSocialId: usid, packId: packId, requestId: requestId, browserName: browserName, versionBrowser: versionBrowser, OS: OS},
            response:'text',
            success:function (v) {
                console.log('on save transaction');
            },
            errrep:true,
            error:function(num) {
                console.error('error saveTransaction with NUM error: ' + num);
            }
        })
    },

    finishTransaction: function(requestId, status) {
        $.ajax({
            type:'post',
            url:'../php/api-v1-0/onFBTransactionRelease.php',
            data: {requestId: requestId, status: status},
            response:'text',
            success:function (v) {
                console.log('on release transaction');
            },
            errrep:true,
            error:function(num) {
                console.error('error releaseTransaction with NUM error: ' + num);
            }
        })
    },

    getVersionForItem: function(name, callback) {
        $.ajax({
            type:'post',
            url:'../php/api-v1-0/getVersionForItem.php',
            data: {item: name},
            response:'text',
            success:function (v) {
                console.log(name + ' version: ' + v);
               callback(v);
            },
            errrep:true,
            error:function(num) {
                console.error('error getVersionForItem with NUM error: ' + num);
                callback(1);
            }
        })
    }
    
};