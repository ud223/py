var Word = function () {
    var obj = new Object();

    obj.meet_id = false;
    obj.text = false;
    obj.user_id = false;
    //操作信息
    obj.message = '操作正常';

    obj.setMeet_Id = function (meet_id) {
        this.meet_id = meet_id;
    }

    obj.setUser_Id = function (user_id) {
        this.user_id = user_id;
    }

    obj.setText = function (text) {
        this.text = text;
    }

    obj.check = function () {
        if (!this.text) {
            this.message = "留言不能为空!";

            return false;
        }

        if (!this.meet_id) {
            this.message = "没有设置获取信息!";

            return false;
        }

        if (!this.user_id) {
            this.message = "没有获取到留言人信息!";

            return false;
        }

        return true;
    }

    obj.add = function () {
        if (!this.check()) {
            $.alertbox({ msg:this.message });

            return;;
        }

        addWord(this.user_id, this.text, this.meet_id);
    }

    obj.load = function (meet_id) {
        alerlt(1);
        queryWord(meet_id);
        alert(2);
    }

    return obj;
}