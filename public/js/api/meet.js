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
            if (response.code == 200) {
                location.href = '/success/' + response.data;
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
                //加载活动集合
                loadMeets(response.data, false, day);
            }
            else {
                //alert(response.data);
            }
        },
        error: function () {

        }
    });
}

//加载某天聚会集合方法
function QueryPendingMeet(user_id, fun, toUrl) {
    var url = '/api/meet/pending';

    var  data = { 'user_id': user_id  }

    $.ajax({
        url: url,
        dataType: 'json',
        data: data,
        method: 'get',
        success: function (response) {
            if (response.code == 200) {
                //加载活动集合
                fun(response.data, toUrl);
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
            alert(JSON.stringify(response));
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
//加入活动
function joinMeet(user_id, meet_id, fun) {
    var url = '/api/meet/join';

    var  data = { 'meet_id': meet_id, 'user_id': user_id }

    var result = false;

    $.ajax({
        url: url,
        dataType: 'json',
        data: data,
        method: 'post',
        success: function (response) {
            //alert(JSON.stringify(response));
            if (response.code == 200) {
                if (fun) {
                    fun(true, "");
                }
            }
            else {
                fun(false, response.data);
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
        method: 'post',
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

//关闭活动
function closeMeet(user_id, meet_id) {
    var url = '/api/meet/close';

    var  data = { 'meet_id': meet_id, 'user_id': user_id }

    $.ajax({
        url: url,
        dataType: 'json',
        data: data,
        method: 'post',
        success: function (response) {
            //alert(JSON.stringify(response));
            if (response.code == 200) {
                location.href = "/";
            }
            else {
                alert(response.data);
            }
        },
        error: function () {

        }
    });
}

//活动日期投票
function voteMeet(meet_id, date1, date2, user_id, fun) {
    var url = '/api/meet/vote/add';

    var  data = { 'meet_id': meet_id, 'date1': date1, 'date2': date2, 'user_id': user_id };
    //alert(JSON.stringify(data));// return;
    $.ajax({
        url: url,
        dataType: 'json',
        data: data,
        method: 'post',
        success: function (response) {
            if (response.code == 200) {
                //alert(response.data);

                if (fun) {
                    fun();
                }
            }
            else {
                //alert(response.data);
            }
        },
        error: function () {

        }
    });
}
//设定投票最多的日期为活动日期
function setMeetDate(meet_id, fun) {
    var url = '/api/meet/vote/set';

    var  data = { 'meet_id': meet_id };

    $.ajax({
        url: url,
        dataType: 'json',
        data: data,
        method: 'post',
        success: function (response) {
            //alert(JSON.stringify(response.data)); //return;
            if (response.code == 200) {

                //
                //location.href = "/";
                if (fun) {
                    fun(meet_id);
                }
            }
            else {
                //alert(response.data);
            }
        },
        error: function () {

        }
    });
}

function getMeetDate(meet_id, user_id) {
    var url = '/api/date/vote/get';

    var  data = { 'meet_id': meet_id, 'user_id': user_id};

    $.ajax({
        url: url,
        dataType: 'json',
        data: data,
        method: 'post',
        success: function (response) {
            if (response.code == 200) {
                setVoteDate(response.data.first_date, response.data.second_date, response.data.isVote);
            }
            else {
                alert(response.data);
            }
        },
        error: function () {

        }
    });
}