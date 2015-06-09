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

        if (!user.valid()) {
            location.href = "/";
        }
        else {
            location.href = '/meet/add';
        }
    })

    $("#share").tap(function () {
        //location.href = "/?share_id="+ user_id;
        alert(nickname);
        var wx = new _WXShare(headimgurl, 50, 50, nickname + "分享了他的日程安排", "", "/?share_id="+ user_id, "");

        wx._ShareFriend();
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