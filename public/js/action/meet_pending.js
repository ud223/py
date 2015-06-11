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

    meet.pending(user_id, loadPendingList);
}

function loadPendingList(data, toUrl, day) {
    alert(JSON.stringify(data));

    $.each(data, function () {
        var node = $('#meet_model').clone(true);

        node.find('.mg-listc-btt').html(this.meet_text);

        var meet_id = this.id;

        var users = "";
        var url = "";

        $.each(this.users, function() {
            var share_url = "/share/"+ this.openid;

            users = users + '<span class="sli"><a href="'+ share_url +'"><img src="'+ this.headimgurl +'"/></a></span>';
        })

        node.find('.mg-listc-usrs').html(users);

        if (toUrl) {
            url = toUrl + "/" + meet_id;
        }
        else {
            url = "/meet/view/"+ meet_id;
        }

        node.tap(function() {
            location.href = url;
        });

        $('#meet-list').append(node);
    });
}