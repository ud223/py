/**
 * Created by Administrator on 2015/5/28 0028.
 */
//加载聚会方法
function addMeet(start_date, end_date, selected_date, meet_text, address, remark, user_id) {
    var url = '/api/meet/add';

    var  data = { 'user_id': user_id, 'start_date': start_date, 'selected_date': selected_date, 'meet_text': meet_text, 'end_date': end_date, 'address': address, 'remark': remark }

    $.ajax({
       url: url,
        dataType: 'json',
        data: data,
        method: 'post',
        success: function (response) {
            alert(response.data);

            if (response.code == 200) {
                location.href = '/';
            }
        },
        error: function () {

        }
    });
}

//加载某天聚会集合方法
function QueryMeet(user_id, year, month, day) {
    var url = '/api/meet/get';

    var  data = { 'user_id': user_id, 'year': year, 'month': month, 'day': day }

    $.ajax({
        url: url,
        dataType: 'json',
        data: data,
        method: 'get',
        success: function (response) {
            if (response.code == 200) {

                //alert(JSON.stringify(response)); return;
                //加载活动集合
                loadMeets(response.data);
            }
            else {
                alert(response.data);
            }
        },
        error: function () {

        }
    });
}

//通过id加载聚会方法
function loadMeet(user_id, meet_id) {
    var url = '/api/meet/load';

    var  data = { 'id': meet_id, 'user_id': user_id }

    $.ajax({
        url: url,
        dataType: 'json',
        data: data,
        method: 'get',
        success: function (response) {
            if (response.code == 200) {
                //加载特定活动
                meetLoad(response.data);
            }
            else {
                alert(response.data);
            }
        },
        error: function () {

        }
    });
}
加入活动
function joinMeet(user_id, meet_id) {
    alert(4);
    var url = '/api/meet/join';

    var  data = { 'meet_id': meet_id, 'user_id': user_id }

    $.ajax({
        url: url,
        dataType: 'json',
        data: data,
        method: 'get',
        success: function (response) {
            if (response.code == 200) {
                location.reload();
            }
            else {
                alert(response.data);
            }
        },
        error: function () {

        }
    });
}
//退出活动
function leaveMeet(user_id, meet_id) {
    var url = '/api/meet/leave';

    var  data = { 'meet_id': meet_id, 'user_id': user_id }

    $.ajax({
        url: url,
        dataType: 'json',
        data: data,
        method: 'get',
        success: function (response) {
            if (response.code == 200) {
                location.reload();
            }
            else {
                alert(response.data);
            }
        },
        error: function () {

        }
    });
}