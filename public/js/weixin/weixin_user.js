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
    alert(3);
    //user_id = localStorage.getItem('user_id');
    var url = "http://cbook.test.angelhere.cn/reg/"+meet_id;
    alert(url);
    //alert(user_id);
    alert(4);
    //if (!user_id) {
        alert(5);
        location.href = "https://open.weixin.qq.com/connect/oauth2/authorize?appid="+ appid +"&redirect_uri="+ url +"&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
    //}
    //else {
    //
    //}
}