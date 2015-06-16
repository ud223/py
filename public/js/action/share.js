$(document).ready(function() {
    var user_id = localStorage.getItem('user_id');

    validUser(user_id);

    if (user_id == proposer_id) {
        switchSharingBds();
    }

    //初始化按钮事件
    initBtnHome();
    initBtnFriend();
    initBtnAdd();
    initBtnBack();
})

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

//初始化后退按钮
function initBtnBack() {
    $('#pge-cover-back').tap(function() {
        localStorage.setItem('share_id', '');
        location.href = "/";
    })
}

function switchSharingBds(){
    $('#sharing-bds').show();
    $('#sharing-bds').tap(function(){
        $('#sharing-bds').hide();
    });
}

function validUser(user_id) {
    //如果从缓存和后台都没有获取到用户id，就重新登录再返回到这里
    if (!user_id) {
        //userLoginToMeet(meet_id);
        wx_Login(window.location.href);
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