var FarmNinja = {
    user_sid: false,
    swf: {},
    version: -1,
    channel: 2,
    language: 1,

    getVersion: function() {
        $.ajax({
            type:'post',
            url:'../php/api-v1-0/getVersionClient.php',
            data: "channelId=" + this.channel,
            response:'text',
            success:function (v) {
                console.log('current version: ' + v);
                FarmNinja.setVersion(v);
            },
            errrep:true,
            error:function(num) {
                alert('error get client version');
            }
        })
    },

    setChannel: function(v) {
        this.channel = v;
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
                channel: this.channel,
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
            var st;
            if (this.channel == 2) {
                st = '/client/farm' + this.version + '.swf';
            } else if (this.channel == 3) {
                st = 'client_ok/farm' + this.version + '.swf';
            } 
            swfobject.embedSWF(st, 'flash_container', '100%', 640, '13.0', null, flashvars, params, attributes, this.callbackFn);
        }
    },

    callbackFn: function (e) {
        if (!e.success) {
            console.log('bad with load swf');
            $('#loader').css('display', 'none');
            $('#no_player').css('display', 'block');
        }
        else {
            document.getElementById("farm_game").style.display = "block";
        }
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
    }
};