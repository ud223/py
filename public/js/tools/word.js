
function loadMeetWords(data) {
    $('#view-word-list').html('');

    if (data.length == 0) {
        $('#view-test-tt').html('');//.html('暂无留言');
        $('#view-word-list').hide();

        return;
    }
    else {
        $('#view-test-tt').html('留言');//.html('暂无留言');
        $('#view-word-list').show();
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

        $('#view-word-list').append(node);
    }

    $('#vote-word-list').html('');

    if (data.length == 0) {
        $('#vote-test-tt').html('');//.html('暂无留言');
        $('#vote-word-list').hide();

        return;
    }
    else {
        $('#vote-test-tt').html('留言');//.html('暂无留言');
        $('#vote-word-list').show();
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

        $('#vote-word-list').append(node);
    }
}