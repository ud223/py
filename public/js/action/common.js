function initBtnShare() {
    $("#btn_share").tap(function () {
        $('#sharing-bds').show();
        $('#sharing-bds').tap(function(){
            $('#sharing-bds').hide();
        });

        //var user_id = localStorage.getItem('user_id');
        //
        //var url = window.location.href;
        //var param = '#/share/'+ user_id;
        //
        //if (url.indexOf(param) < 0) {
        //    url = url + param;
        //}
        //
        //location.href = url;
    })
}

function initBack() {
    $('#pge-cover-back').tap(function() {
        localStorage.setItem('share_id', '');
        location.href = "/";
    })
}

function closeMeetView() {
    $('.glyphicon-remove').tap(function() {
        $('#show-meet-view').hide();
        $('#show-meet-vote').hide();
        cur_meet_id = false;
    })
}

//加载活动信息
function loadThisMeet(meet_id, user_id) {
    var meet = new Meet();

    meet.load(user_id, meet_id);

    var word = new Word();

    word.load(meet_id);
}

//初始化留言提交按钮事件
function initBtnWord(user_id) {
    $(document).on('click', '#view_word_submit', function () {
        subitWord(1);
    });

    $(document).on('click', '#vote_word_submit', function () {
        subitWord(2);
    });
}

function subitWord(type) {
    var word = new Word();
    var user_id = localStorage.getItem('user_id');

    word.setMeet_Id(cur_meet_id);
    word.setUser_Id(user_id);

    if (type == 1)
        word.setText($('#view_word_text').val());
    else
        word.setText($('#vote_word_text').val());

    word.add();
}