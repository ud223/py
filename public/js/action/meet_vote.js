$(document).ready(function() {
    //判断是否从后台得到用户的id，如果得到就不在从缓存中取用户id
    if (!user_id) {
        user_id = localStorage.getItem('user_id');
    }
    else {
        localStorage.setItem('user_id', user_id);
    }

    validUser(user_id)

    loadThisMeet(user_id);
    loadMeetVoteDate(user_id);

    initBtnWord(user_id);
    initBtnBack();
    initBtnCloseMeet(user_id);
    initBtnJoin(user_id);
    //initVoteSubmit();
    initSetMeetDate();
})

function validUser(user_id) {
    //如果从缓存和后台都没有获取到用户id，就重新登录再返回到这里
    if (!user_id) {
        userLoginToMeet(meet_id);
    }
}
//初始化活动日期投票按钮
function initVoteSubmit() {
    $('#submit-vote').tap(function() {
        var date1 = $('#first_date').val();
        var date2 = $('#second_date').val();
        var user_id = localStorage.getItem('user_id');

        if (date1 == '' && date2 == '') {
            alert('投票日期不能都为空!');

            return;
        }

        if (date1 == '' && date2 != '') {
            date1 = date2;
        }

        if (date1 != '' && date2 == '') {
            date2 = date1;
        }

        var meet = new Meet();

        meet.vote(meet_id, date1, date2, user_id, null);
    });
}
//确认设置活动日期
function initSetMeetDate() {
    if (user_id == proposer_id) {
        $('#close-vote').show();

        $('#close-vote').tap(function () {
            var meet = new Meet();

            meet.setMeetDate(meet_id);
        });
    }
}

function clearVote() {
    $('#first_date').val('');
    $('#second_date').val('');
}

//加载活动信息
function loadThisMeet(user_id) {
    var meet = new Meet();

    meet.load(user_id, meet_id);

    var word = new Word();

    word.load(meet_id);
}

//加载用户已投票日期
function loadMeetVoteDate(user_id) {
    var meet = new Meet();

    meet.getMeetDate(meet_id, user_id);
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
function initBtnBack() {
    $('#pge-cover-back').tap(function() {
        location.href = "/";
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
        $('#letmeleave').show();

        $('#letmeleave').tap(function() {
            var meet = new Meet();

            meet.leave(user_id, meet_id);
        })
    }
    else  {
        $('#letmejoin').show();

        $('#letmejoin').tap(function() {
            var meet = new Meet();

            meet.join(user_id, meet_id);
        })
    }
}

function setVoteDate(first_date, second_date, isVote) {
    if (isVote) {
        $('#first_date').val(first_date);
        $('#second_date').val(second_date);
    }
    else {
        $('#submit-vote').show();

        initVoteSubmit();
    }
}