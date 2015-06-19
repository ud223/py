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
            if (response.code == 200) {
                alert(JSON.stringify(response));
                //加载活动集合
                loadMeetWords(response.data);
            }
            else {
                $.alertbox({ msg:response.data });
            }
        },
        error: function () {

        }
    });
}
