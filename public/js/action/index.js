$(document).ready(function() {
    var now = new Date();

    vaildUser();
    //分享跳转
    hrefUrl();
    initShareParam();
    //加载日历
    loadCalendar();

    if (share_id) {
        localStorage.setItem('share_id', share_id);
    }
    //初始化过期日历样式
    initPassCalendar(now.getFullYear(), now.getMonth() + 1);
    //加载日程安排
    loadSchedule();
    //初始化按钮事件
    initBtnWord();
    initBtnFriend();
    initBtnAdd();
    initBtnShare();
    initBtnPending();
    queryPendingMeet();
    closeMeetView();
    initVoteSubmit();
})

function initShareParam() {
    var user_id = localStorage.getItem('user_id');
    var url = window.location.href;

    var urls = url.split("#");

    if (urls.length < 2) {
        location.href = urls + "#" + user_id;
    }
}

function hrefUrl() {
    var url = window.location.href;
    //去掉附属参数
    var url = url.replace("/none", "");
    var user_id = localStorage.getItem('user_id');
    var urls = url.split("#");
    var param = "";

    if (urls.length > 1) {
        var strUrl = urls[1];

        if (strUrl.indexOf("?") > -1) {
            param = strUrl.split("?")[1];
        }
        else{
            param = strUrl;
        }
    }

    if (param != "") {
        if (user_id != param) {
            location.href = "/share/"+ param;
        }
    }
}

function loadCalendar() {
    var now = new Date();

    $('#real-calendar div').Calendar({
        'weekStart' : 1,
        'date' : now
    }).on('changeDay', function(event) {
        var load_date = event.day.valueOf() + '-' + event.month.valueOf() + '-' + event.year.valueOf();

        $('.touch-date-grid-color').removeClass('touch-date-grid-color');

        $(this).addClass('touch-date-grid-color');

        //$('#jijj').html(load_date);

        var meetModel = new Meet();

        var user_id = localStorage.getItem('user_id');

        meetModel.Query(user_id, event.year.valueOf(), event.month.valueOf(),  event.day.valueOf());
    });

    //$('每个日子的class').on('touchstart', function(){
    //    $('.touch-date-grid-color').removeClass('touch-date-grid-color');
    //    $(this).addClass('touch-date-grid-color');
    //});

    var tmp_month = now.getMonth() + 1;

    $('#jijj').html(now.getFullYear() + '年' + tmp_month + '月');
}

function vaildUser() {
    var user_id = localStorage.getItem('user_id');

    if (!user_id) {
        wx_Login(window.location.href);
    }
    else {
        loadUser(user_id);
    }
}

function loadUser(user_id) {
    var user = new User();

    user.load(user_id);
}

function loadSchedule(user_id) {
    var user_id = localStorage.getItem('user_id');
    var share_id = localStorage.getItem('share_id');
    //如果分享id不为空，则用分享id替换用户id
    if (share_id) {
        user_id = share_id;
    }

    localStorage.setItem('schedule_user_id', user_id);

    getSchedule();
}

function initBtnFriend() {
    $(".glyphicon-user").tap(function () {
        $.alertbox({ msg:'我的好友:功能暂部署!' });
    })
}

function initBtnAdd() {
    $('#btn_add').tap(function () {
        var user = new User();

        var user_id = localStorage.getItem('user_id');

        user.setUser_Id(user_id)

        if (!user.valid()) {
            location.href = "/";
        }
        else {
            location.href = '/meet/add';
        }
    })
}

function initBtnPending() {
    $("#pending").tap(function () {
        location.href = '/meet/pending';
    })
}

//function initPassCalendar(year, month) {
//    var d = new Date();
//
//    if (year < d.getFullYear()) {
//        for (i = 1; i < 32; i++) {
//            addPassStyel(i);
//        }
//    }
//    else if (year == d.getFullYear() && month < d.getMonth() + 1) {
//        for (i = 1; i < 32; i++) {
//            addPassStyel(i);
//        }
//    }
//    else if (year == d.getFullYear() && month == d.getMonth() + 1) {
//        for (i = 1; i < d.getDate(); i++) {
//            addPassStyel(i);
//        }
//    }
//}
//
//function addPassStyel(day) {
//    var d = $('#day_'+ day);
//
//    if (d) {
//        $('#day_'+ day).addClass('past');
//    }
//}


function initCalendarClick(id, year, month, day) {
    $( '#'+id).tap(function () {
        var meetModel = new Meet();

        var user_id = localStorage.getItem('user_id');
        var year = $('#'+ id).attr('year');
        var month = $('#'+ id).attr('month');
        var day = $('#'+ id).attr('day');

        meetModel.Query(user_id, year, month, day);
    })
}

function loadUserInfo(data) {
    headimgurl = data.headimgurl;
    nickname = data.nickname;

    document.title = nickname + "的云步客";
    $('#share-headimg').attr('src', headimgurl);
}

function queryPendingMeet(user_id) {
    var meet = new Meet();

    meet.pending(user_id, pendingOpt);
}

function pendingOpt(data) {
    if (data.length > 0) {
        $('#pending').show();
    }
}
