/**
 * Created by Administrator on 2015/5/28 0028.
 */
function addMeet() {
    var url = '';

    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();
    var address = $('#address').val();
    var remark = $('#remark').val();

    var  data = { 'user_id': null, 'start_date': start_date, 'end_date': end_date, 'address': address, 'remark': remark }

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