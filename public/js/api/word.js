/**
 * Created by Administrator on 2015/5/28 0028.
 */
/**
 * Created by Administrator on 2015/5/28 0028.
 */
function addWord(user_id, word_text, meet_id) {
    var url = '/api/word/add';

    var  data = { 'user_id': user_id, 'word_text': word_text, 'meet_id': meet_id }

    $.ajax({
        url: url,
        dataType: 'json',
        data: data,
        method: 'post',
        success: function (response) {
            if (response.code == 200) {
                //location.reload();
                var word = new Word();

                word.load(meet_id);

                $('#view_word_text').val('');
                $('#vote_word_text').val('');
            }
            else {
                $.alertbox({ msg:response.data });
            }
        },
        error: function () {

        }
    });
}

function queryWord(meet_id) {
    var url = '/api/word/get';

    var  data = { 'meet_id': meet_id }

    $.ajax({
        url: url,
        dataType: 'json',
        data: data,
        method: 'get',
        success: function (response) {
            alert(JSON.stringify(response));
            if (response.code == 200) {
                alert(99);
                //加载活动集合
                loadMeetWords(response.data);
                alert(66);
            }
            else {
                $.alertbox({ msg:response.data });
            }
        },
        error: function () {

        }
    });
}

function loadMeetWords(data) {
    alert(0);
    $('#view-word-list').html('');
    alert(1);
    alert(data.length);
    if (data.length == 0) {
        alert(3);
        $('#view-test-tt').html('');//.html('暂无留言');
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

    $('#vote-word-list').html('');

    if (data.length == 0) {
        alert(4);
        $('#vote-test-tt').html('');//.html('暂无留言');
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