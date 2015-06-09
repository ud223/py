$(document).ready(function() {
    var user_id = localStorage.getItem('user_id');

    initBtnBack();

    queryPendingMeet(user_id);
})

//初始化后退按钮
function initBtnBack() {
    $('#pge-cover-back').tap(function() {
        localStorage.setItem('share_id', '');
        location.href = "/";
    })
}

function queryPendingMeet(user_id) {
    var meet = new Meet();

    meet.pending(user_id, loadMeets);
}