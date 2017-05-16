"use strict";

var SN = function (social) { // social == 4
    var that = this;
    var accessT = '';
    var uSocialId = '';

    console.log('init fb social');

    window.fbAsyncInit = function() {
        FB.init({
            appId      : '1936104599955682',
            xfbml      : true,
            cookie     : true,
            status     : true,
            version    : 'v2.9'
        });
        FB.AppEvents.logPageView();
        FB.login(function(response) {
            if (response.authResponse) {
                console.log(response);
                accessT = response.authResponse.accessToken;
                uSocialId = response.authResponse.userID;
                try {
                    console.log('userSocialId: ' + uSocialId);
                    FarmNinjaFB.saveAccessToken(uSocialId, accessT);
                    FarmNinjaFB.getVersion();
                } catch(err) {
                    console.log('after init FB:: error with getVersion: ' + err);
                }

            } else {
                console.log('not auth');
            }
        }, {scope:'publish_actions,user_friends'});
    };

    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

    that.flash = function(){
        return document.getElementById("farm_game");
    };

    that.getProfile = function(userSocialId) {
        FB.api("/me",
            // {fields: 'id,last_name,first_name,gender,picture,birthday'},
            {access_token: accessT},
            function (response) {
                if (response && !response.error) {
                    userSocialId = response.id;
                    var u = {};
                    FB.api("/" + userSocialId,
                        {access_token: accessT},
                        {fields: 'last_name,first_name,gender,birthday,picture.width(100).height(100),locale'},
                        function (response) {
                            if (response && !response.error) {
                                u.first_name = response.first_name;
                                u.last_name = response.last_name;
                                u.gender = response.gender;
                                u.birthday = response.birthday;
                                u.locale = response.locale;
                                u.picture = response.picture.data.url;
                                u.id = userSocialId;
                                try {
                                    that.flash().getProfileHandler(u);
                                } catch (err) {
                                    console.log('getProfileHandler error: ' + err)
                                }
                                console.log('locale: ' + response.locale);
                                // FB.api('/me/picture?type=normal', function (response) {
                                //     console.log('getProfileCallback_3 response: ' + response);
                                //     u.picture = response.data.url;
                                //     u.id = userSocialId;
                                //     that.flash().getProfileHandler(u);
                                // });
                            }
                        }
                    );
                }
            }
        );
    };

    that.getAllFriends = function(userSocialId) {
        FB.api("/" + userSocialId + "/friends",
            {fields: 'id,last_name,first_name,picture.width(100).height(100)'},
            function (response) {
                if (response && !response.error) {
                    try {
                        that.flash().getAllFriendsHandler(response);
                    } catch (err) {
                        console.log('getAllFriendsHandler error: ' + err)
                    }
                }
            }
        );
    };

    that.getTempUsersInfoById = function(uids) {
        var ids = uids.join();
        FB.api("/ids=" + ids,
            {fields: 'id,last_name,first_name,picture.width(100).height(100)'},
            function (response) {
                if (response && !response.error) {
                    try {
                        that.flash().getTempUsersInfoByIdHandler(response);
                    } catch (err) {
                        console.log('getTempUsersInfoById error: ' + err)
                    }
                }
            }
        );
    };

    that.getAppUsers = function(userSocialId) {
        FB.api("/1936104599955682",
            {"fields": "context.fields(friends_using_app)"},
            function (response) {
                if (response && !response.error) {
                    try {
                        that.flash().getAppUsersHandler(response);
                    } catch (err) {
                        console.log('getAppUsersHandler error: ' + err)
                    }
                }
            }
        );
    };

    that.getFriendsByIds = function(uids) {
        var ids = uids.join();
        FB.api('/?ids='+ids,
            {fields: 'id,last_name,first_name,picture.width(100).height(100)'},
            function (response) {
                if (response && !response.error) {
                    try {
                        that.flash().getFriendsByIdsHandler(response);
                    } catch (err) {
                        console.log('getFriendsByIdsHandler error: ' + err)
                    }
                }
            }
        );
    };

    that.showInviteWindowAll = function(lang) {
        var st ='Давай играть вместе';
        if (lang == 2) st = "Let's play together!";
        FB.ui({method: 'apprequests',
            message: st
        }, function(response){
            console.log(response);
        });
    };

    that.makeWallPost = function(uid, message, url){
        FB.api('me/feed',
            'post',
            {   message: '',
                picture :url,
                description : message,
                name: 'WoollyValley',
                link: 'https://apps.facebook.com/1936104599955682/'
            }, function(response) {
                console.log(response);
                if (response && !response.error) {
                    try {
                        that.flash().wallPostSave();
                    } catch (err) {
                        console.log('wallPostSave error: ' + err)
                    }
                } else {
                    try {
                        that.flash().wallPostCancel();
                    } catch (err) {
                        console.log('wallPostCancel error: ' + err)
                    }
                }
            }
        );
    };

    that.isInGroup = function(groupId, userId) {
        that.flash().isInGroupCallback(1);
        // FB.api(     ---> better use groupId/members?limit=400 and check all users
        //     "/" + userId + "/groups",
        //     function (response) {
        //         var status = 0;
        //         if (response && !response.error) {
        //             status = 1;
        //         }
        //         that.flash().isInGroupCallback(status);
        //     }
        // );

    };

    that.makePayment = function(packId, userSocialId) {
        var product = "https://505.ninja/php/api-v1-0/payment/fb/pack" + packId + ".html";
        console.log('payment product: ' + product);
        var requestID = String(userSocialId) + 'a' + String(Date.now());
        console.log('requestID: ' + requestID);
        FB.ui({
            method: 'pay',
            action: 'purchaseitem',
            product: product,
            request_id: requestID
        }, function(response) {
            console.log('Payment completed', response);
            if(response.status && response.status == 'completed') {
                that.flash().successPayment();
            } else {
                that.flash().failPayment();
            }
        } );
    }
};


