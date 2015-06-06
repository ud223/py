$(document).ready(function() {
    var user_id = localStorage.getItem('user_id');
    alert(user_id);
    initBtnBack();

    queryPendingMeet(user_id);
})

//初始化后退按钮
function initBtnBack() {
    $('#pge-cover-back').tap(function() {
        location.href = "/";
    })
}

function queryPendingMeet(user_id) {
    alert(0);
    var meet = new Meet();

    meet.pending(user_id, loadMeets);
}