function validEnvironment() {
    var ua = window.navigator.userAgent.toLowerCase();

    //微信开发环境
    if(ua.match(/MicroMessenger/i) == 'micromessenger'){
       return 1;
    }
    //iphone6 模拟环境
    if (ua.indexOf('12a4345d') > -1) {
        return 2;
    }

    //android模拟环境
    if (ua.indexOf('android') > -1) {
        return 3;
    }
}