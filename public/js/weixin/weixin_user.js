function userLogin() {
    alert(appid);
    var user_id = localStorage.getItem('user_id');
    var url = "http://cbook.test.angelhere.cn/reg";
    if (!user_id) {
        location.href = "https://open.weixin.qq.com/connect/oauth2/authorize?appid="+ appid +"&redirect_uri="+ url +"&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
    }
}
