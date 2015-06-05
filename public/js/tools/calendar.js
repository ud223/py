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

        initCalendarClick(id);
    })
}