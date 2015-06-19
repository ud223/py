//设置主页日期已被安排活动
function setCalendarOpt(data) {
    $.each(data, function () {
        var id = "day_"+ this.day;
        var identity ="";

        if (this.identity == 2) {
            identity = "<div class='date-padding'>"+ this.day +"</div><span class='cbooker high'></span>";
        }
        else {
            identity = "<div class='date-padding'>"+ this.day +"</div><span class='cbooker'></span>";
        }

        $('#'+ id).html(identity);

        //initCalendarClick(id);
    })
}

function getSchedule(uid) {
    var calendar = new Calendar();

    var user_id = localStorage.getItem('schedule_user_id');

    calendar.setYear($('#day_1').attr('year'));
    calendar.setMonth($('#day_1').attr('month'));
    calendar.setUser_Id(user_id);

    calendar.getSchedule();
}