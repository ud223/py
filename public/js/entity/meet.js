var Meet = function () {
    var obj = new Object();

    obj.start_date = false;
    obj.end_date = false;
    obj.selected_date = false;
    obj.user_id = false;
    obj.meet_text = false;
    obj.remark = false;
    obj.address = false;
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
    obj.select = function(t) {
        this.selected = t;
    }

    obj.check = function () {
        var today = new Date();

        if (this.selected) {
            if (!this.selected_date) {
                this.message = "请先选择活动日期!";

                return false;
            }
            else {
                var date = new Date(this.selected_date);

                if (date < today && date.getDate() != today.getDate()) {
                    this.message = "活动日期必须从今天开始!";

                    return false;
                }
            }
        }
        else {
            if (this.start_date == false || this.end_date == false) {
                this.message = "请先选择活动日期范围!";

                return false;
            }
            else {
                var d1 = new Date(this.start_date);
                var d2 = new Date(this.end_date);

                if (d1 >= d2) {
                    this.message = "结束日期必须大于开始日期";

                    return false;
                }
                else {
                    if (d1 < today && d1.getDate() != today.getDate()) {
                        this.message = "活动日期必须从今天开始!";

                        return false;
                    }
                }
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

        //if (!this.address) {
        //    this.message = "请先填写活动地点!";
        //
        //    return false;
        //}

        //if (!this.remark) {
        //    this.message = "请先填写活动描述!";
        //
        //    return false;
        //}

        return true;
    }

    obj.add = function () {
        if (!this.check()) {
            $.alertbox({ msg:this.message });

            return;
        }

        addMeet(this.start_date, this.end_date, this.selected_date, this.meet_text, this.address, this.remark, this.user_id);
    }

    obj.Query = function(user_id, year, month, day) {
        QueryMeet(user_id, year, month, day);
    }

    obj.pending = function(user_id, fun) {
        QueryPendingMeet(user_id, fun, '/meet/vote');
    }

    obj.load = function (user_id, meet_id) {
        loadMeet(user_id, meet_id);
    }

    obj.join = function(user_id, meet_id, fun) {
        joinMeet(user_id, meet_id, fun);
    }

    obj.leave = function(user_id, meet_id) {
        leaveMeet(user_id, meet_id);
    }

    obj.close = function(user_id, meet_id) {
        closeMeet(user_id, meet_id);
    }

    obj.vote = function(meet_id, date1, date2, user_id, fun) {
        voteMeet(meet_id, date1, date2, user_id, fun);
    }

    obj.setMeetDate = function(meet_id, fun) {
        setMeetDate(meet_id, fun);
    }

    obj.getMeetDate = function(meet_id, user_id) {
        alert(2);
        getMeetDate(meet_id, user_id);
    }

    return obj;
}