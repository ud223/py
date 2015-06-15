function loadWords(data) {
    $('#word-list').html('');

    if (data.length == 0) {
        alert($('.word-title').html());
        $('.word-title').html('暂无评论');

        return;
    }

    for (i = 0; i < data.length; i++) {
        var node = $('#word_model').clone(true);

        var date = data[i].date.date;

        date = date.replace('-', '年');
        date = date.replace('-', '月');
        date = date.replace(' ', '日');

        node.find('.ctxx').html(data[i].text);
        node.find('.uune').html(data[i].user.nickname);
        node.find('.untme').html(date);
        node.find('.uxer').attr('src', data[i].user.headimgurl)

        $('#word-list').append(node);
    }
}