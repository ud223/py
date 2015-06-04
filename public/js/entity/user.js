var User = function () {
    var obj = new Object();

    obj.subscribe = false;
    obj.openid = false;
    obj.nickname = false;
    obj.sex = false;
    obj.language = false;
    obj.city = false;
    obj.province = false;
    //true:选择固定日期; false:选择日期区间
    obj.selected = true;
    //操作信息
    obj.message = '操作正常';

    obj.setStart_Date = function (start_date) {
        this.start_date = start_date;
    }

    obj.setEnd_Date = function (end_date) {
        this.end_date = end_date;
    }

    obj.setSelect_Date = function (select_date) {
        this.selected_date = select_date;
    }

    obj.setUser_Id = function (user_id) {
        this.user_id = user_id;
    }

    obj.setMeet_Text = function (meet_text) {
        this.meet_text = meet_text;
    }

    obj.setRemark = function (remark) {
        this.remark = remark;
    }

    obj.setAddress = function (address) {
        this.address = address;
    }
    //切换聚会日期选择方式激发方法
    //特定日期聚会申请为true, 日期区间聚会申请为false
    obj.regUser = function() {

    }

    obj.check = function () {
        if (this.selected) {
            if (!this.selected_date) {
                this.message = "请先选择活动日期!";

                return false;
            }
            else {
                this.message = "请先选择活动日期范围!";

                return false;
            }
        }

        if (!this.user_id) {
            this.message = "没有获取到活动申请人信息!";

            return false;
        }

        if (!this.meet_text) {
            this.message = "请先填写活动主题!";

            return false;
        }

        if (!this.address) {
            this.message = "请先填写活动地点!";

            return false;
        }

        if (!this.remark) {
            this.message = "请先填写活动描述!";

            return false;
        }
    }

    obj.Login = function () {
        if (!this.check()) {
            alert(this.message);

            return;;
        }

        addMeet(this.start_date, this.end_date, this.selected_date, this.meet_text, this.address, this.remark, this.user_id);
    }

    obj.valid = function () {
        var user_id = localStorage.getItem('user_id');

        if (user_id) {
            if (!this.user_id) {
                this.user_id = user_id;
            }

            return true;
        }
        else {
            if (this.user_id) {
                localStorage.setItem('user_id', this.user_id);

                return true;
            }

            return false;
        }
    }

    return obj;
}