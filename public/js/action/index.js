$(document).ready(function() {
    $(document).on('click', '.glyphicon-home', function () {
        location.href = '/';
    })

    $(document).on('click', '.glyphicon-user', function () {
        alert('好友');
    })

    $(document).on('click', '.glyphicon-plus', function () {
        location.href = '/meet/add';
    })

    $(document).on('click', '.glyphicon-send', function () {
        alert('分享');
    })
})