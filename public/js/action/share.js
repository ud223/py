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

    })

    $("#pending").tap(function () {
        location.href = '/meet/pending';
    })

    var user_id = localStorage.getItem('user_id');

    if (user_id == proposer_id) {
        switchSharingBds();
    }
})

function switchSharingBds(){
    $('#sharing-bds').show();
    $('#sharing-bds').tap(function(){
        $('#sharing-bds').hide();
    });
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