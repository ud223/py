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

function wx_Login(toUrl, fun_test_A, fun_test_B) {
    var env = validEnvironment();
    alert(toUrl);
    //微信开发环境
    if (env == 1) {
        var tmp_toUrl = encodeURI(toUrl.split("?")[0]);
        alert(tmp_toUrl);
        var url = "http://cbook.test.angelhere.cn/reg?web_url=" + tmp_toUrl;
        alert(url);
        location.href = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" + appid + "&redirect_uri=" + url + "&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";

        return 1;
    }
    else {
        if (env == 2) {
            initTestUser_A(fun_test_A);
        }
        else {
            initTestUser_B(fun_test_B);
        }

        return 0;
    }
}

function initTestUser_A(fun) {
    if (fun) {
        fun();
    }
    else {
        localStorage.setItem('user_id', 'a12345678')
    }
}

function initTestUser_B(fun) {
    if (fun) {
        fun();
    }
    else {
        localStorage.setItem('user_id', 'b12345678')
    }
}