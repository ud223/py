$(document).ready(function() {
    $(document).on('click', '.glyphicon-home', function () {
        //alert(1);
        location.href = '/';
    })

    $(document).on('click', '.glyphicon-user', function () {
        alert('好友');
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

    $(document).on('click', '#share', function () {
        alert('分享');
    })

    $(document).on('click', '#pending', function () {
        alert(1);
        location.href = '/meet/pending';
    })

    //initBtnPending();
})

function initBtnPending() {
    alert(0);
    alert($('#pending').length);
    $(document).on("click", "#pending", function () {
        alert(1);
        location.href = '/meet/pending';
    })
}

function initCalendarClick(id) {
    $( '#'+id).tap(function () {
        var meetModel = new Meet();

        var user_id = localStorage.getItem('user_id');
        var year = $('#'+ id).attr('year');
        var month = $('#'+ id).attr('month');
        var day = $('#'+ id).attr('day');

        meetModel.Query(user_id, year, month, day);
    })
}