function loadWords(data) {
    $('#view_word-list').html('');

    if (data.length == 0) {
        $('#view_test-tt').hide();//.html('暂无留言');
        $('#view_word-list').hide();

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

        $('#view_word-list').append(node);
    }

    $('#vote_word-list').html('');

    if (data.length == 0) {
        $('#vote_test-tt').hide();//.html('暂无留言');
        $('#vote_word-list')..hide();
        
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

        $('#vote_word-list').append(node);
    }
}