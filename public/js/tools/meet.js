function loadMeets(data) {
    $('#meet-list').html('');

    $.each(data, function () {
        var node = $('#meet_model').clone(true);

        node.find('.mg-listc-btt').html(this.meet_text);

        var id = this.id;

        node.tap(function() {
           location.href = "/meet/view/"+ id;
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
}