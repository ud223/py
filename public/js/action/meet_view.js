$(document).ready(function() {
    //localStorage.clear(); return;

    var user_id = localStorage.getItem('user_id');

    validUser(user_id)

    toShareUrl(user_id);
    initShareParam(user_id);

    loadThisMeet(user_id);
    loadProposerInfo(user_id);

    initBtnShare();
    initBtnWord(user_id);
    initBtnBack(user_id);
    initBtnCloseMeet(user_id);
    initBtnJoin(user_id);
})

function toShareUrl(user_id) {
    if (user_id == proposer_id) {
        return;
    }

    var url = location.href;

    var strUrl = url.split("#");

    if (strUrl.length > 1) {
        location.href = strUrl[1];
    }
}

function initShareParam(user_id) {
    var user_id = localStorage.getItem('user_id');
    var url = window.location.href;

    var urls = url.split("#");

    if (urls.length < 2) {
        location.href = urls +  "#/share/" + user_id +"/"+ meet_id;
    }
}

//function initBtnShare() {
//    $('.glyphicon-share').tap(function () {
//       shareMeet();
//    });
//}

//function shareMeet(user_id){
//    $('#sharing-bds').show();
//    $('#sharing-bds').tap(function(){
//        $('#sharing-bds').hide();
//    });
//
//    //var share_param = "#/share/" + user_id +"/"+ meet_id;
//    //var share_url = window.location.href
//    //
//    //if (share_url.indexOf(share_param) < 0) {
//    //    share_url = share_url + share_param;
//    //}
//    //
//    //location.href = share_url;
//}

function validUser(user_id) {
    //如果从缓存和后台都没有获取到用户id，就重新登录再返回到这里
    if (!user_id) {
        wx_Login(window.location.href);
    }
}

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

        word.setMeet_Id(meet_id);
        word.setUser_Id(user_id);
        word.setText($('#word_text').val());

        word.add();
    });
}
//初始化后退按钮
function initBtnBack(user_id) {
    $('#pge-cover-back').tap(function() {
        localStorage.setItem('share_id', '');

        if (user_id == proposer_id) {
            location.href = "/";
        }
        else {
            location.href = "/share/"+ proposer_id;
        }
    })
}
//初始化关闭活动按钮
function initBtnCloseMeet(user_id) {
    if (user_id == proposer_id) {
        $('#close_meet').show();

        $('#close_meet').tap(function() {
            var meet = new Meet();

            meet.close(user_id, meet_id);
        })
    }
}
//初始化加入按钮
function initBtnJoin(user_id) {
    if (users_id.indexOf(user_id) > -1)  {
        if (user_id == proposer_id)
            return;

        $('#btn_leave').show();

        $('#btn_leave').tap(function() {
            var meet = new Meet();

            meet.leave(user_id, meet_id);
        })
    }
    else  {
        $('#btn_join').show();

        $('#btn_join').tap(function() {
            var meet = new Meet();

            meet.join(user_id, meet_id, afterJoin);
        })
    }
}

function afterJoin(data, msg) {
    if (data) {
        localStorage.setItem('share_id', '');
        location.href = "/";
    }
    else {
        //alert(msg);
    }
}

function loadProposerInfo(user_id) {
    if (proposer_id != user_id) {
        //$('#proposer_info').show();

        $('#nickname').html(nickname);
        $('#headimgurl').attr("src", headimgurl);
        $('#create_date').html("发起于 "+ dateToZhcn(create_date));

        $('#master_schedule').tap(function() {
            location.href = "/share/" + proposer_id;
        });
    }
}