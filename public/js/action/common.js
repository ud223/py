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

    $("#btn_share_calendar").tap(function () {
        $('#sharing-bds').show();
        $('#sharing-bds').tap(function () {
            $('#sharing-bds').hide();
        });
    });

    $("#view_meet_share").tap(function () {
        $('#sharing-bds').show();
        $('#sharing-bds').tap(function () {
            $('#sharing-bds').hide();
        });
    });

    $("#vote_meet_share").tap(function () {
        $('#sharing-bds').show();
        $('#sharing-bds').tap(function () {
            $('#sharing-bds').hide();
        });
    });
}

function initBack() {
    $('#pge-cover-back').tap(function() {
        localStorage.setItem('share_id', '');
        location.href = "/";
    })
}

function closeMeetView() {
    var user_id = localStorage.getItem('user_id');

    $('.glyphicon-remove').tap(function() {
        $('#show-meet-view').hide();
        $('#show-meet-vote').hide();
        cur_meet_id = false;
        removeMeetShareParam(user_id);
    })
}

//加载活动信息
function loadThisMeet(meet_id, user_id) {
    var meet = new Meet();

    meet.load(user_id, meet_id);
    meet.getMeetDate(meet_id, user_id)

    var word = new Word();

    word.load(meet_id);
}

function initMeetShareParam(meet_id, user_id) {
    var url = location.href;

    if (url.indexOf("#") > -1) {
        url = url.split("#")[0];
    }

    if (url.indexOf("?") > -1) {
        url = url.split("?")[0]
    }

    url = url + "#" + user_id + "/" + meet_id;

    location.href = url;
}

function removeMeetShareParam(user_id) {
    var url = location.href;

    url = url.split("#")[0];
    url = url + "#" + user_id + "/none";

    location.href = url;
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

//初始化关闭活动按钮
function initBtnCloseMeet(proposer_id, meet_id,  type) {
    var user_id = localStorage.getItem('user_id');

    if (user_id == proposer_id) {
        if (type == 1) {
            $('#view_close_meet').show();

            $('#view_close_meet').tap(function() {
                var meet = new Meet();

                meet.close(user_id, meet_id);
            })
        }
        else {
            $('#vote_close_meet').show();

            $('#vote_close_meet').tap(function() {
                var meet = new Meet();

                meet.close(user_id, meet_id);
            })
        }
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

//初始化活动日期投票按钮
function initVoteSubmit() {
    $('#vote_submit-vote').tap(function() {
        var user_id = localStorage.getItem('user_id');
        //如果是创建者,就跳过参加活动的环节
        if (users_id.indexOf(user_id) > -1)  {
            voteDate(true, "");
        }
        else {
            var meet = new Meet();
            //先参加活动,成功后再提交投票
            meet.join(user_id, cur_meet_id, voteDate) ;
        }
    });
}

function voteDate(data, msg) {
    if (!data) {
        //alert(msg);
        return;
    }

    var user_id = localStorage.getItem('user_id');
    var strFirst_date = $('#first_date').val();
    var strSecond_date = $('#second_date').val();

    //投票验证失败, return
    if (!validDateRange())
        return;

    var meet = new Meet();

    meet.vote(cur_meet_id, strFirst_date, strSecond_date, user_id, clearVote);
}

//确认设置活动日期
function initSetMeetDate(isVote) {
    var user_id = localStorage.getItem('user_id');
    alert(proposer_id);
    alert(user_id);
    alert(isVote);
    if (user_id == proposer_id && isVote > 0) {
        $('#close-vote').show();

        $('#close-vote').tap(function () {
            var meet = new Meet();

            meet.setMeetDate(cur_meet_id, setMeetSelectedDate);
        });
    }
}

function clearVote() {
    location.reload();
}

function setVoteDate(first_date, second_date, isVote) {
    if (isVote) {
        $('#first_date').val(first_date);
        $('#second_date').val(second_date);

        initSetMeetDate(isVote);
    }
    else {
        $('#vote_submit-vote').show();
        $('#ps_text').show();

        initVoteSubmit();
    }
}

function validDateRange() {
    var strStart_date = $('#start_date').attr("start_date");
    var strEnd_date = $('#end_date').attr("end_date");
    var strFirst_date = $('#first_date').val();
    var strSecond_date = $('#second_date').val();

    var start_date = new Date(strStart_date);
    var end_date = new Date(strEnd_date);

    if (strFirst_date == '' && strSecond_date == '') {
        $.alertbox({ msg:'投票日期不能都为空!' });

        return false;
    }

    if (strFirst_date == '' && strSecond_date != '') {
        strFirst_date = strSecond_date;
    }

    if (strFirst_date != '' && strSecond_date == '') {
        strSecond_date = strFirst_date;
    }

    var first_date = new Date(strFirst_date);
    var second_date = new Date(strSecond_date);

    if (first_date > end_date || first_date < start_date) {
        $.alertbox({ msg:'超出选择日期范围!' });

        return false;
    }

    if (second_date > end_date || second_date < start_date) {
        $.alertbox({ msg:'超出选择日期范围!' });

        return false;
    }

    return true;
}