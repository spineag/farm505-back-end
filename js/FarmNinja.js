var FarmNinja = {
    user_sid: false,
    swf: {},
    version: 1,

    init: function () {
        var url = document.location.toString().split('?');
        var flashvars =
        {
            data: (url[1] ? '&' + url[1] : ''),
            protocol: (document.location.protocol == 'https:') ? 'https' : 'http',
            channel: this.channel,
            gacid: this.getUserGAcid()
        };

        var params =
        {
            allowFullscreen: "true",
            allowFullScreenInteractive: "true",
            allowScriptAccess: "always",
            wmode: "direct",
            flashvars: flashvars
        };
        var attributes =
        {
            id: "farm_game",
            name: "farm_game"
        };
        swfobject.embedSWF('/client/farm505.swf', 'flash_container', '100%', 640, '13.0', null, flashvars, params, attributes, this.callbackFn);
    },

    callbackFn: function (e) {
        if (!e.success) {
            console.log('bad with load swf');
            $('#loader').css('display', 'none');
            // $('#noFlash').find('center').css('display', 'block');
            $('#no_player').css('display', 'block');
        }
        else {
            document.getElementById("farm_game").style.display = "block";
            this.play();
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