function userLogin() {
    var user_id = localStorage.getItem('user_id');
    var url = "http://cbook.test.angelhere.cn/reg";

    if (!user_id) {
        location.href = "https://open.weixin.qq.com/connect/oauth2/authorize?appid="+ appid +"&redirect_uri="+ url +"&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
    }
    else {

    }
}

function userLoginToMeet(meet_id) {
    var url = "http://cbook.test.angelhere.cn/reg/"+meet_id;

    location.href = "https://open.weixin.qq.com/connect/oauth2/authorize?appid="+ appid +"&redirect_uri="+ url +"&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
}

function wx_Login(toUrl) {
    var tmp_toUrl = encodeURI(toUrl);

    var url = "http://cbook.test.angelhere.cn/reg?web_url="+ tmp_toUrl;

    location.href = "https://open.weixin.qq.com/connect/oauth2/authorize?appid="+ appid +"&redirect_uri="+ url +"&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
}