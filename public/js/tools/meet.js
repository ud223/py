function loadMeets(data, toUrl, day) {
    var list = $('#mg-listc').find('.mg-listc').clone(true);
    var old_day = $(document).find('.node-list').attr('day');

    $(document).find('.node-list').remove();

    if (old_day == day) {
        list.attr('day', '');
        return;
    }

    list.addClass('node-list');

    if (data.length == 0) {
        if (is_share) {
            return;
        }

        var node = $('#meet_add_model').clone(true);

        node.find('.mg-listc-btt').html("当天没有活动安排");

        node.tap(function() {
            location.href = "/meet/add";
        });

        list.append(node);

        $('#day_'+ day).parent().after(list);
    }
    else {
        list.attr('day', day);
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

            list.append(node);
        });

        $('#day_'+ day).parent().after(list);
    }
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

    $(document).find('#address').html(data.address);
    $(document).find('#remark').html(data.remark);

    $(document).find('.busr-lst').html("");
    var users_html = "";

    $.each(data.users, function() {
        var share_url = "/share/"+ this.openid;

        users_html = users_html + '<li><a href="'+ share_url +'"><img src="'+ this.headimgurl +'"/></a></li>';
    });

    $(document).find('.busr-lst').html(users_html);
}