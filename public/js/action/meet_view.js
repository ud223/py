$(document).ready(function() {
    var user_id = localStorage.getItem('user_id');

    loadThisMeet(user_id);

    initBtnWord(user_id);
    initBtnBack();

    initBtnCloseMeet(user_id);
    initBtnJoin(user_id);
})

//加载活动信息
function loadThisMeet(user_id) {
    var meet = new Meet();

    meet.load(user_id, meet_id);

    var word = new Word();

    word.load(meet_id);
}
//初始化留言提交按钮事件
function initBtnWord(user_id) {
    $(document).on('click', '#word_submit', function () {
        var word = new Word();

        word.setMeet_Id(id);
        word.setUser_Id(user_id);
        word.setText($('#word_text').val());

        word.add();
    });
}
//初始化后退按钮
function initBtnBack() {
    $('#pge-cover-back').tap(function() {
        location.href = "/";
    })
}
//初始化关闭活动按钮
function initBtnCloseMeet(user_id) {
    if (user_id == proposer_id) {
        $('#close_meet').show();
    }
}
//初始化加入按钮
function initBtnJoin(user_id) {
    alert(users_id);
    alert(user_id);
    if (users_id.indexOf(user_id) > -1)  {
        $('#letmeleave').show();
    }
    else  {
        $('#letmejoin').show();
    }
}