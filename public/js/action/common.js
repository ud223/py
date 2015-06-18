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
    })
}

//加载活动信息
function loadThisMeet(meet_id, user_id) {
    var meet = new Meet();

    meet.load(user_id, meet_id);

    var word = new Word();

    word.load(meet_id);
}