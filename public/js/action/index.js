$(document).ready(function() {
    $(".glyphicon-home").tap(function () {
        localStorage.setItem('share_id', '');
        location.href = '/';
    })

    $(".glyphicon-user").tap(function () {
        $.alertbox({ msg:'我的好友:功能暂部署!' });
    })

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

    $("#share").tap(function () {
        var user_id = localStorage.getItem('user_id');

        location.href = '/share/'+ user_id;
    })

    $("#pending").tap(function () {
        location.href = '/meet/pending';
    })
})

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
    alert(day);
    var d = $('#day_'+ day);

    if (d) {
        d.addClass('pass');
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
