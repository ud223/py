$(document).ready(function() {
    var now = new Date();

    vaildUser();
    //加载日历
    loadCalendar();

    if (share_id) {
        localStorage.setItem('share_id', share_id);
    }
    //初始化当前年月日到标题文本框
    $('#jijj').html(now.getMonth() + 1 + "月-" + now.getFullYear() + "年");

    //初始化过期日历样式
    initPassCalendar(now.getFullYear(), now.getMonth() + 1);
    //加载日程安排
    loadSchedule();
    //初始化按钮事件
    initBtnHome();
    initBtnFriend();
    initBtnAdd();
    initBtnShare();
    initBtnPending();
    queryPendingMeet();
})

function loadCalendar() {
    var now = new Date();

    $('#real-calendar div').Calendar({
        'weekStart' : 1,
        'date' : now
    }).on('changeDay', function(event) {
        var load_date = event.day.valueOf() + '-' + event.month.valueOf() + '-' + event.year.valueOf();

        $('#jijj').html(load_date);

        $('.selected').removeClass('selected');
        $('#day_'+ event.day.valueOf()).addClass('selected');

        var meetModel = new Meet();

        var user_id = localStorage.getItem('user_id');

        meetModel.Query(user_id, event.year.valueOf(), event.month.valueOf(),  event.day.valueOf());
    });
}

function vaildUser() {
    var user_id = localStorage.getItem('user_id');

    if (!user_id) {
        wx_Login(window.location.href);
    }
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

function initBtnHome() {
    $(".glyphicon-home").tap(function () {
        localStorage.setItem('share_id', '');
        location.href = '/';
    })
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

function initBtnShare() {
    $("#share").tap(function () {
        var user_id = localStorage.getItem('user_id');

        location.href = '/share/'+ user_id;
    })
}

function initBtnPending() {
    $("#pending").tap(function () {
        location.href = '/meet/pending';
    })
}

function initPassCalendar(year, month) {
    var d = new Date();

    if (year < d.getFullYear()) {
        for (i = 1; i < 32; i++) {
            addPassStyel(i);
        }
    }
    else if (year == d.getFullYear() && month < d.getMonth() + 1) {
        for (i = 1; i < 32; i++) {
            addPassStyel(i);
        }
    }
    else if (year == d.getFullYear() && month == d.getMonth() + 1) {
        for (i = 1; i < d.getDate(); i++) {
            addPassStyel(i);
        }
    }
}

function addPassStyel(day) {
    var d = $('#day_'+ day);

    if (d) {
        $('#day_'+ day).addClass('past');
    }
}


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
