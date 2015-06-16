/**
 * Created by Administrator on 2015/6/1 0001.
 */
var meet = new Meet();

$(document).ready(function() {
    initFootbar();

    initSubmit();

    initBack();

    initMeetType();
})

function initFootbar() {
    $('#btn_home').removeClass('selected');
    $('#btn_add').addClass('selected');

    $('#btn_home').tap(function() {
        location.href = "/";
    })
}

function initDate() {
    if (day) {
        $('#day_'+ day).addClass('selected');
        $('#day_'+ day).addClass('node');

        var d =$('#day_'+ day).attr('year') + '-'+  $('#day_'+ day).attr('month') + '-' + $('#day_'+ day).attr('day');

        $('#show_seletced_date').html("活动将开始于: "+ $('#day_'+ day).attr('month') + '月' + $('#day_'+ day).attr('day') + '日')
        $('#selected_date').val(d);
    }
}

function initSubmit() {
    $('#pge-cover-save').tap(function() {
        //如果聚会确定日期
        if (meet.selected) {
            meet.setSelect_Date($('#selected_date').val());
        }
        else {//如果不确定聚会日期
            meet.setStart_Date($('#start_date').val());
            meet.setEnd_Date($('#end_date').val());
        }

        meet.setMeet_Text($('#meet_text').val());
        meet.address = $('#address').val();
        meet.remark = $('#remark').val();
        meet.user_id = localStorage.getItem('user_id');

        meet.add();
    })
}

function initBack() {
    $('#pge-cover-back').tap(function() {
        localStorage.setItem('share_id', '');
        location.href = "/";
    })
}

function initMeetType() {
    $('#add-tab-1').tap(function() {
        meet.select(true);
    })

    $('#add-tab-2').tap(function() {
        meet.select(false);
    })
}

function initCalendarClick() {
    $('.calendar-td').tap(function() {
        var date = $(this).attr('year') + '-'+  singleDateCheck($(this).attr('month')) + '-' + singleDateCheck($(this).attr('day'));

        var d1 =  new Date(date);
        var d2 = new Date();

        if (d1 > d2) {
            $('.node').removeClass('selected');
            $('.node').removeClass('node');
            $('#day_'+ $(this).attr('day')).addClass('selected');
            $('#day_'+ $(this).attr('day')).addClass('node');

            $('#selected_date').val(date);
        }
        else {
            $('#selected_date').val('');
            $('#checked-time-solid span').html('');
        }
    })
}
