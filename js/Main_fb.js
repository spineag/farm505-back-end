"use strict";

var SN = function (social) { // social == 4
    var that = this;
    var accessT = '';
    var uSocialId = '';

    console.log('init fb social');

    window.fbAsyncInit = function() {
        FB.init({
            appId      : '1936104599955682',
            xfbml      : false,
            cookie     : true,
            status     : true,
            version    : 'v2.8'
        });
        FB.AppEvents.logPageView();
        FB.login(function(response) {
            if (response.authResponse) {
                console.log(response);
                accessT = response.authResponse.accessToken;
                console.log('at: ' + accessT);
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
        console.log('FB: try get user profile with id: ' + userSocialId);
        FB.api("/me",
            // {fields: 'id,last_name,first_name,gender,picture,birthday'},
            {access_token: accessT},
            function (response) {
                console.log('getProfileCallback_1 response: ' + response);
                if (response && !response.error) {
                    userSocialId = response.id;
                    var u = {};
                    FB.api("/" + userSocialId,
                        {access_token: accessT},
                        {fields: 'last_name,first_name,gender,birthday,picture.width(100).height(100),locale'},
                        function (response) {
                            console.log('getProfileCallback_2 response: ' + response);
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
        console.log('FB: try get getAllFriends with id: ' + userSocialId);
        FB.api("/" + userSocialId + "/friends",
            {fields: 'id,last_name,first_name,picture.width(100).height(100)'},
            function (response) {
                console.log('getAllFriends response: ' + response);
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
        console.log('FB: try get getTempUsersInfoById');
        FB.api("/ids=" + ids,
            {fields: 'id,last_name,first_name,picture.width(100).height(100)'},
            function (response) {
                console.log('getTempUsersInfoByIdCallback result: ' + response);
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
        console.log('FB: try get getAppUsers');
        FB.api("/1936104599955682",
            {"fields": "context.fields(friends_using_app)"},
            function (response) {
                console.log('getAppUsersCallback data: ' + response);
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
        console.log('FB: try get getFriendsByIds');
        FB.api('/?ids='+ids,
            {fields: 'id,last_name,first_name,picture.width(100).height(100)'},
            function (response) {
                console.log('getFriendsByIds result: ' + response);
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

    that.showInviteWindowAll = function(userSocialId) {
        console.log('FB: try get showInviteWindowAll');
        FB.ui({method: 'apprequests',
            message: 'Давай играть вместе'
        }, function(response){
            console.log(response);
        });
    };

    that.makeWallPost = function(uid, message, url){
        console.log('FB: try get makeWallPost');
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
        console.log('FB: try isInGroup id: ' + groupId);
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
};


