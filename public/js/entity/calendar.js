/**
 * Created by Administrator on 2015/6/3 0003.
 */
var Calendar = function () {
    var obj = new Object();

    obj.year = false;
    obj.month = false;
    obj.user_id = false;
    //操作信息
    obj.message = '操作正常';

    obj.setYear = function (year) {
        this.year = year;
    }

    obj.setMonth = function (month) {
        this.month = month;
    }

    obj.setUser_Id = function (user_id) {
        this.user_id = user_id;
    }

    obj.check = function () {
        if (!this.year) {
            this.message = "没有设置当前日历年份!";

            return false;
        }

        if (!this.month) {
            this.message = "没有设置当前日历月份!";

            return false;
        }

        if (!this.user_id) {
            this.message = "没有获取到活动申请人信息!";

            return false;
        }

        return true;
    }

    obj.getSchedule = function () {
        if (!this.check()) {
            //alert(this.message);

            return;;
        }
        alert(2);
        initSchedule(this.year, this.month, this.user_id);
    }

    return obj;
}