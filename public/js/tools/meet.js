function loadMeets(data, toUrl) {
    $('#meet-list').html('');
    alert(toUrl);
    $.each(data, function () {
        var node = $('#meet_model').clone(true);

        node.find('.mg-listc-btt').html(this.meet_text);
        var id = this.id;
        var users = "";

        $.each(this.users, function() {
            users = users + '<span class="sli"><img src="'+ this.headimgurl +'"/></span>';
        })

        node.find('.mg-listc-usrs').html(users);

        if (toUrl) {
            toUrl = toUrl + "/" + id;
        }
        else {
            toUrl = "/meet/view/"+ id;
        }

        node.tap(function() {
           location.href = toUrl;
        });

        $('#meet-list').append(node);
    })
}

function meetLoad(data) {
    $(document).find('#title').html(data.meet_text);
    $(document).find('#meet_text').html(data.meet_text);
    $(document).find('#selected_date').html(data.year + "年" + data.month + "月" + data.day + "日");
    $(document).find('#address').html(data.address);
    $(document).find('#remark').html(data.remark);

    $(document).find('.busr-lst').html("");
    var users_html = "";

    $.each(data.users, function() {
        users_html = users_html + '<li><img src="'+ this.headimgurl +'"/></li>';
    });

    $(document).find('.busr-lst').html(users_html);
}