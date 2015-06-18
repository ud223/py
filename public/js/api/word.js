/**
 * Created by Administrator on 2015/5/28 0028.
 */
/**
 * Created by Administrator on 2015/5/28 0028.
 */
function addWord(user_id, word_text, meet_id) {
    var url = '/api/word/add';

    var  data = { 'user_id': user_id, 'word_text': word_text, 'meet_id': meet_id }
    alert(JSON.stringify(data));
    $.ajax({
        url: url,
        dataType: 'json',
        data: data,
        method: 'post',
        success: function (response) {
            if (response.code == 200) {
                location.reload();
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
                //加载活动集合
                loadWords(response.data);
            }
            else {
                $.alertbox({ msg:response.data });
            }
        },
        error: function () {

        }
    });
}