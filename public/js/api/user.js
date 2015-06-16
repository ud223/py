//加载某天聚会集合方法
function getUserInfo(user_id) {
    var url = '/api/user/get';

    var  data = { 'user_id': user_id }

    $.ajax({
        url: url,
        dataType: 'json',
        data: data,
        method: 'get',
        success: function (response) {
            alert(JSON.stringify(response));
            if (response.code == 200) {
                //加载活动集合
                loadUserInfo(response.data);
            }
            else {
                //alert(response.data);
            }
        },
        error: function () {

        }
    });
}