function loadMeets(data, toUrl, day) {
    var today = new Date();
    var list = $('#mg-listc').find('.mg-listc').clone(true);
    var old_day = $(document).find('.node-list').attr('day');
    var user_id = localStorage.getItem('user_id');

    $(document).find('.node-list').remove();

    if (old_day == day) {
        list.attr('day', '');
        return;
    }

    if (day < today.getDate() && data.length == 0) {
        return;
    }

    $('.selected').removeClass('selected');
    $('#day_'+ day).addClass('selected');

    list.attr('day', day);
    list.addClass('node-list');

    var meet_date = $('#day_' + day).attr('month') + "月" + day + "日";

    if (data.length > 0) {
        meet_date = meet_date + " 活动";
    }
    else {
        meet_date = meet_date + " 无活动安排";
    }

    list.find('.meet_date').html(meet_date);

    if (data.length > 0) {
        $.each(data, function () {
            var type = 1;
            var node = $('#meet_model').clone(true);

            node.find('.mg-listc-btt').html(this.meet_text);

            var meet_id = this.id;

            if (cur_meet_id) {
                if (meet_id == cur_meet_id) {
                    node.find('.let-join').show();
                }
            }

            var users = "";
            var url = "";

            $.each(this.users, function() {
                var share_url = "/share/"+ this.openid;

                users = users + '<span class="sli"><a href="'+ share_url +'"><img src="'+ this.headimgurl +'"/></a></span>';
            })

            node.find('.mg-listc-usrs').html(users);

            if (this.seleted == 1)
                type = 1;
            else
                type = 2;

            node.tap(function() {
                loadMeetDetail(meet_id, type);
            });

            list.append(node);
        });

        $('#day_'+ day).parent().after(list);
    }

    if (is_share) {
        return;
    }

    if (day >= today.getDate()) {
        var node = $('#meet_add_model').clone(true);

        node.tap(function() {
            location.href = "/meet/add/"+ day;
        });

        list.append(node);
    }

    $('#day_'+ day).parent().after(list);
}

function loadMeetDetail(meet_id, type) {
    var user_id = localStorage.getItem('user_id');

    if (type == 1) {
        cur_meet_id = meet_id;
        $('#view_word_text').val('');
        loadThisMeet(meet_id, user_id);
        $('#show-meet-view').show();
    }
    else {
        cur_meet_id = meet_id;
        $('#vote_word_text').val('');
        loadThisMeet(meet_id, user_id);
        $('#show-meet-vote').show();
    }

    initMeetShareParam(meet_id, user_id);
}

function meetLoad(data) {
    document.title = nickname + "邀您参加活动";
    $(document).find('#title').html(data.meet_text);

    if (data.selected_date == 'false') {
        $(document).find('#start_date').html(data.start_date);
        $(document).find('#end_date').html(data.end_date);
        $(document).find('#start_date').attr('start_date', data.start_date);
        $(document).find('#end_date').attr('end_date', data.end_date);

        $(document).find('#vote_meet_text').html(data.meet_text);

        if (data.address)
            $(document).find('#vote_address').html(data.address);
        else {
            $(document).find('#tab-vote-address').hide();//.html("暂无地址");
        }


        initBtnCloseMeet(data.proposer_id, data.meet_id,  2);
    }
    else {
        $(document).find('#selected_date').html(data.year + "年" + data.month + "月" + data.day + "日");

        $(document).find('#view_meet_text').html(data.meet_text);

        if (data.address){
            $(document).find('#view_address').html(data.address);
        }
        else {
            $(document).find('#tab-view-address').hide();//html("暂无地址");
        }

        initBtnCloseMeet(data.proposer_id, data.meet_id,  1);
    }

    $(document).find('#remark').html(data.remark);

    $(document).find('.busr-lst').html("");
    var users_html = "";
    users_id = data.users_id;
    proposer_id = data.proposer_id;

    $.each(data.users, function() {
        var share_url = "/share/"+ this.openid;

        users_html = users_html + '<li><img src="'+ this.headimgurl +'"/></li>';//<a href="'+ share_url +'"></a>
    });

    $(document).find('.busr-lst').html(users_html);
    //alert(0);
    initBtnJoin();
}