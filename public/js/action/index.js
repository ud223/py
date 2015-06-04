$(document).ready(function() {
    alert('新增按钮:'+ $('#btn_add').length);
    alert(1);
    $(document).on('click', '.glyphicon-home', function () {
        //alert(1);
        location.href = '/';
    })
    alert(2);
    $(document).on('click', '.glyphicon-user', function () {
        alert('好友');
    })
    alert(3);
    $('.add-cal').tap(function () {
        //alert(3);
        location.href = '/meet/add';
    })

    $(document).on('click', '.glyphicon-send', function () {
        alert('分享');
    })
})

function initCalendarClick(id) {
    $(document).on('click', '#'+id, function () {
        //localStorage.setItem("user_id", "123456");

        var meetModel = new Meet();

        var user_id = localStorage.getItem('user_id');
        var year = $('#'+ id).attr('year');
        var month = $('#'+ id).attr('month');
        var day = $('#'+ id).attr('day');

        meetModel.Query(user_id, year, month, day);
    })
}