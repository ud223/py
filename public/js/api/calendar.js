/**
 * Created by Administrator on 2015/5/28 0028.
 */
//获取日程安排
function initSchedule(year, month, user_id) {
    var url = '/api/schedule/get';

    var  data = { 'user_id': user_id, 'year': year, 'month': month };

    $.ajax({
        url: url,
        dataType: 'json',
        data: data,
        method: 'get',
        success: function (response) {
            if (response.code == 200) {
                if (response.data.length == 0) {
                    return false;
                }
                else {
                    //设置日期占用样式
                    setCalendarOpt(response.data);
                }

                return response.data;
            }
            else {
                //alert(response.data);
            }
        },
        error: function () {

        }
    });
}