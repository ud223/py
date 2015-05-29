/**
 * Created by Administrator on 2015/5/28 0028.
 */
/**
 * Created by Administrator on 2015/5/28 0028.
 */
function addWord() {
    var url = '';

    var address = $('#word_text').val();
    var meet_id = $('#meet_id').val();

    var  data = { 'user_id': null, 'address': address, 'meet_id': meet_id }

    $.ajax({
        url: url,
        dataType: 'json',
        data: data,
        method: 'post',
        success: function (response) {
            if (response.code == 200) {

            }
            else {

            }
        },
        error: function () {

        }
    });
}