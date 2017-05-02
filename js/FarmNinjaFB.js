var FarmNinjaFB = {
    user_sid: false,
    swf: {},
    version: -1,
    language: 1,

    getVersion: function() {
        $.ajax({
            type:'post',
            url:'../php/api-v1-0/getVersionClient.php',
            data: "channelId=4",
            response:'text',
            success:function (v) {
                console.log('current version: ' + v);
                FarmNinjaFB.setVersion(v);
            },
            errrep:true,
            error:function(num) {
                alert('error get client version');
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
            swfobject.embedSWF('client_fb/farm' + this.version + '.swf', 'flash_container', '100%', '100%', '13.0', null, flashvars, params, attributes, this.callbackFn);
        }
    },

    callbackFn: function (e) {
        if (!e.success) {
            console.log('bad with load swf');
            $('#loader').css('display', 'none');
            $('#no_player').css('display', 'block');
        }
        else {
            FarmNinjaFB.bodyResize();
            window.onresize = FarmNinjaFB.bodyResize;
        }
    },
    
    bodyResize: function(event) {
        var h = $('.game_body').height() - 37;
        if (h < 600) h = 600;
        if (h > 1000) h = 1000;
        console.log('h: ' + h);
        $('#farm_game').height(h);
    },

    reload: function () {
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
        console.log('checkUserLanguageForIFrame for userSocialId: ' + userSocialId);
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
                FarmNinjaFB.setLanguage(2);
            }
        })
    },

    setLanguage: function(v) {
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
    }
};