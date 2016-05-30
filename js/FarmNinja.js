var FarmNinja = {
    user_sid: false,
    swf: {},
    version: 1,

    init: function() {
        var url = document.location.toString().split('?');
        var flashvars =
        {
            data: (url[1] ? '&' + url[1] : ''),
            protocol: (document.location.protocol == 'https:') ? 'https' : 'http',
            channel: this.channel
        };

        var params =
        {
            allowFullscreen: "true",
            allowScriptAccess: "always",
            wmode: "window",
            flashvars: flashvars
        };
        var attributes =
        {
            id: "farm_game",
            name: "farm_game"
        };
        swfobject.embedSWF('/client/farm505.swf', 'flash_container', '100%', 640, '13.0', null, flashvars, params, attributes, this.callbackFn);
    },
    callbackFn : function(e) {
        if(!e.success)
        {
            $('#ajaxLoader').css('display', 'none');
            $('#noFlash').find('center').css('display', 'block');
        }
        else
        {
            document.getElementById("farm_game").style.display = "block";
        }
    }
}