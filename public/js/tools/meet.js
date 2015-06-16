function loadMeets(data, toUrl, day) {
    var today = new Date();
    var list = $('#mg-listc').find('.mg-listc').clone(true);
    var old_day = $(document).find('.node-list').attr('day');

    $(document).find('.node-list').remove();

    if (old_day == day) {
        list.attr('day', '');
        return;
    }

    if (day < today.getDate() && data.length == 0) {
        return;
    }

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

            if (toUrl) {
                url = toUrl + "/" + meet_id;
            }
            else {
                if (this.seleted == 1)
                    url = "/meet/view/"+ meet_id;
                else
                    url = "/meet/vote/"+meet_id;
            }

            node.tap(function() {
                location.href = url;
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

function meetLoad(data) {
    $(document).find('#title').html(data.meet_text);
    $(document).find('#meet_text').html(data.meet_text);

    if (data.selected_date == 'false') {
        $(document).find('#start_date').html(data.start_date);
        $(document).find('#end_date').html(data.end_date);
        $(document).find('#start_date').attr('start_date', data.start_date);
        $(document).find('#end_date').attr('end_date', data.end_date);
    }
    else {
        $(document).find('#selected_date').html(data.year + "年" + data.month + "月" + data.day + "日");
    }

    if (data.address)
        $(document).find('#address').html(data.address);
    else
        $(document).find('#address').html("暂无地址");

    $(document).find('#remark').html(data.remark);

    $(document).find('.busr-lst').html("");
    var users_html = "";

    $.each(data.users, function() {
        var share_url = "/share/"+ this.openid;

        users_html = users_html + '<li><a href="'+ share_url +'"><img src="'+ this.headimgurl +'"/></a></li>';
    });

    $(document).find('.busr-lst').html(users_html);
}