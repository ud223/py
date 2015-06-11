$(document).ready(function() {
    $(".glyphicon-home").tap(function () {
        localStorage.setItem('share_id', '');
        location.href = '/';
    })

    $(".glyphicon-user").tap(function () {
        //alert('我的好友:功能暂未');
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
        alert(1);
        location.href = "/share/"+ user_id;
    })

    $("#pending").tap(function () {
        location.href = '/meet/pending';
    })
})

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

function insertMeetList(ele) {
    var list = $('#mg-listc').clone(true);

    ele.aftr(list);
}

function clearMeetList() {

}
