/**
 * Created by Administrator on 2015/6/1 0001.
 */
$(document).ready(function() {
    var meet = new Meet();

    $(document).on('click', '#pge-cover-save', function() {
        //如果聚会确定日期
        if (meet.selected) {
            meet.setSelect_Date($('#selected_date').val());
        }
        else {//如果不确定聚会日期
            meet.setStart_Date($('#start_date').val());
            meet.setEnd_Date($('#end_date').val());
        }
        //测试
        localStorage.setItem('user_id', '123456');

        meet.setMeet_Text($('#meet_text').val());
        meet.address = $('#address').val();
        meet.remark = $('#remark').val();
        meet.user_id = localStorage.getItem('user_id');

        meet.add();
    })

    $(document).on('click', 'pge-cover-back', function() {
       location.href = "/";
    })

    //$(document).on('click', '.glyphicon-calendar', function() {
    //    meet.select(true)
    //})
    //
    //$(document).on('click', '.glyphicon-bullhorn', function() {
    //    meet.select(false)
    //})

    $(document).on('click', '.calendar-td', function() {
        var date = $(this).attr('year') + '-'+  singleDateCheck($(this).attr('month')) + '-' + $(this).attr('day');

        $('#selected_date').val(date);
    })
})