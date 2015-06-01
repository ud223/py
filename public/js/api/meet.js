/**
 * Created by Administrator on 2015/5/28 0028.
 */
function addMeet(start_date, end_date, selected_date, meet_text, address, remark, user_id) {
    var url = '';

    var  data = { 'user_id': user_id, 'start_date': start_date, 'selected_date': selected_date, 'meet_text': meet_text, 'end_date': end_date, 'address': address, 'remark': remark }

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