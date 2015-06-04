function loadMeets(data) {
    $('#meet-list').html('');

    $.each(data, function () {
        var node = $('.mg-listc-blk').clone(true);

        node.find('.mg-listc-btt').html(this.meet_text);

        var id = this.id;

        node.on('click', '.mg-listc-rarr', function() {
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