function loadMeets(data, toUrl) {
    $('#meet-list').html('');

    $.each(data, function () {
        var node = $('#meet_model').clone(true);

        node.find('.mg-listc-btt').html(this.meet_text);
        var meet_id = this.id;
        var users = "";
        var url = toUrl;

        $.each(this.users, function() {
            users = users + '<span class="sli"><img src="'+ this.headimgurl +'"/></span>';
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
    })
}

function meetLoad(data) {
    alert(1);
    $(document).find('#title').html(data.meet_text);
    $(document).find('#meet_text').html(data.meet_text);

    if (data.selected_date == 'false') {
        $(document).find('#start_date').html(data.start_date);
        $(document).find('#end_date').html(data.end_date);
    }
    else {
        $(document).find('#selected_date').html(data.year + "年" + data.month + "月" + data.day + "日");
    }

    $(document).find('#address').html(data.address);
    $(document).find('#remark').html(data.remark);

    $(document).find('.busr-lst').html("");
    var users_html = "";

    $.each(data.users, function() {
        users_html = users_html + '<li><img src="'+ this.headimgurl +'"/></li>';
    });

    $(document).find('.busr-lst').html(users_html);
}