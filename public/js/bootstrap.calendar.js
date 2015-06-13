/*!
 * bootstrap-calendar plugin
 * Original author: @ahmontero
 * Licensed under the MIT license
 *
 * jQuery lightweight plugin boilerplate
 * Original author: @ajpiano
 * Further changes, comments: @addyosmani
 * Licensed under the MIT license
 */

// the semi-colon before the function invocation is a safety
// net against concatenated scripts and/or other plugins
// that are not closed properly.
;(function($, window, document, undefined) {

    // undefined is used here as the undefined global
    // variable in ECMAScript 3 and is mutable (i.e. it can
    // be changed by someone else). undefined isn't really
    // being passed in so we can ensure that its value is
    // truly undefined. In ES5, undefined can no longer be
    // modified.

    // window and document are passed through as local
    // variables rather than as globals, because this (slightly)
    // quickens the resolution process and can be more
    // efficiently minified (especially when both are
    // regularly referenced in your plugin).

    // Create the defaults once
    var pluginName = 'Calendar', defaults = {
        weekStart : 1,
        msg_days : ["天", "一", "二", "三", "四", "五", "六"],
        msg_months : ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
        msg_today : '今天',
        msg_events_header : 'Events Today',
        events : null,
        past_date:null,
        date : new Date()
    }, template = '' + '<div class="calendar" id="calendar">' + '<div class="calendar-header"></div>' + '<div class="calendar-body"></div>' + '</div>' + '', daysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31], today = new Date();

    // The actual plugin constructor
    function Plugin(element, options) {
        this.element = $(element);

        // jQuery has an extend method that merges the
        // contents of two or more objects, storing the
        // result in the first object. The first object
        // is generally empty because we don't want to alter
        // the default options for future instances of the plugin
        this.options = $.extend({}, defaults, options);

        this._defaults = defaults;
        this._name = pluginName;

        this.init();
    }


    Plugin.prototype.init = function() {
        // Place initialization logic here
        // You already have access to the DOM element and
        // the options via the instance, e.g. this.element
        // and this.options
        this.weekStart = this.options.weekStart || 1;
        this.days = this.options.msg_days;
        this.months = this.options.msg_months;
        this.msg_today = this.options.msg_today;
        this.msg_events_hdr = this.options.msg_events_header;
        this.events = this.options.events;
        // this.date = this.options.date;
        this.past_date = this.options.past_date;


//		this.calendar = $(template.replace("%msg_today%", this.msg_today)).appendTo(this.element).on({
//			click : $.proxy(this.click, this)
//		});

        this.calendar = $(template.replace("%msg_today%", this.msg_today)).appendTo(this.element).tap($.proxy(this.click, this));

        this.live_date = new Date();

        var now = this.options.date;

        this.mm = now.getMonth();
        this.yy = now.getFullYear();

        var mon = new Date(this.yy, this.mm, 1);
        this.yp = mon.getFullYear();
        this.yn = mon.getFullYear();

        if (this.component) {
            this.component.on('click', $.proxy(this.show, this));
        } else {
            this.element.on('click', $.proxy(this.show, this));
        }

        this.renderCalendar(now);

        var context = this;

        $('#to-last-month').click(function(){
            context.update_date('prv');
            var prv = new Date(context.yp, context.mm, 1);
            context.live_date = prv;
            context.renderCalendar(prv);

            $('#jijj').html(context.mm + 1 + '月-' + context.yn);
            alert(now.getDay());

            if (context.yn == now.getFullYear() && context.mm == now.getMonth()) {
                $('#day_'+ now.getDay()).addClass('today');
            }

            getSchedule();
        });

        $('#to-next-month').click(function(){
            context.update_date('nxt');
            var nxt = new Date(context.yn, context.mm, 1);
            context.live_date = nxt;
            context.renderCalendar(nxt);

            $('#jijj').html(context.mm + 1 + '月-' + context.yn);
            alert(now.getDay());

            if (context.yn == now.getFullYear() && context.mm == now.getMonth()) {
                $('#day_'+ now.getDay()).addClass('today');
            }

            getSchedule();
        });
    };

    Plugin.prototype.renderEvents = function(events, elem) {
        var live_date = this.live_date;
        var msg_evnts_hdr = this.msg_events_hdr;
        for (var i = 1; i <= daysInMonth[live_date.getMonth()]; i++) {
            $.each(events.event, function() {
                var year = 1900 + live_date.getYear();
                var month = live_date.getMonth();

                var view_date = new Date(year, month, i, 0, 0, 0, 0);
                var event_date = new Date(this.date);

                if (event_date.getDate() == view_date.getDate() && event_date.getMonth() == view_date.getMonth() && event_date.getFullYear() == view_date.getFullYear()) {
                    elem.parent('div:first').find('#day_' + i).removeClass("day").addClass('holiday').empty().append('<span class="weekday">' + i + '</span>').popover({
                        'title' : msg_evnts_hdr,
                        'content' : 'You have ' + this.title + ' appointments',
                        'delay' : {
                            'show' : 250,
                            'hide' : 250
                        }
                    });
                }
            });
        }
    };

    Plugin.prototype.loadEvents = function() {
        if (!(this.events === null)) {
            if ( typeof this.events == 'function') {
                this.renderEvents(this.events.apply(this, []), this.calendar);
            }
        }
    };

    Plugin.prototype.renderCalendar = function(date) {
        var mon = new Date(this.yy, this.mm, 1);
        var live_date = this.live_date;

        //this.element.parent('div:first').find('.year').empty();
        //this.element.parent('div:first').find('.month').empty();

        //this.element.parent('div:first').find('.year').append(mon.getFullYear());
        //this.element.parent('div:first').find('.month').append(this.months[mon.getMonth()]);
//		this.element.find('.yearandmonth').empty();
//		this.element.find('.yearandmonth').append(mon.getFullYear() + "年" + (mon.getMonth() + 1) + "月");

        this.element.attr('id', 'month_' + mon.getFullYear() + '_' + (mon.getMonth() + 1))

        if (this.isLeapYear(date.getYear())) {
            daysInMonth[1] = 29;
        } else {
            daysInMonth[1] = 28;
        }

        this.calendar.find('.calendar-header').empty();
        this.calendar.find('.calendar-body').empty();

        // Render Days of Week
        this.renderDays();

        var fdom = mon.getDay();
        // First day of month
        var mwks = 6// Weeks in month

        // Render days
        var dow = 0;
        var first = 0;
        var last = 0;
        for (var i = 0; i >= last; i++) {

            var _html = "";

            for (var j = this.weekStart; j < this.days.length + this.weekStart; j++) {

                cls = "";
                msg = "";
                id = "";

                // Determine if we have reached the first of the month
                if (first >= daysInMonth[mon.getMonth()]) {
                    dow = 0;
                } else if (((dow % 7) > 0 && (first % 7) > 0) || ((j % 7) == (fdom % 7))) {
                    dow++;
                    first++;
                }

                // Get last day of month
                if (dow == daysInMonth[mon.getMonth()]) {
                    last = daysInMonth[mon.getMonth()];
                }

                // 修改：5月30日（start）
                // Set class
                if (cls.length == 0) {

                    if (j % 7 == 0 || j % 7 == 6) {
                        cls = "day weekend";
                    } else {
                        cls = "day";
                    }
                    if (today.getDate() == date.getDate() && dow == date.getDate() && today.getMonth() == date.getMonth() && today.getFullYear() == date.getFullYear()) {
                        cls += " today";
                    }
                }
                // 修改：5月30日（end）

                // Set ID
                id = "day_" + dow;

                month_ = date.getMonth() + 1;
                year = date.getFullYear();

                if(dow){
                    if(new Date(date.getFullYear(),date.getMonth(),dow,0,0,0).valueOf() < this.past_date){
                        cls +=" past ";
                    }
                }


                // Render HTML
                if (dow == 0) {
                    _html += '<div class="calendar-td">&nbsp;</div>';
                } else if (msg.length > 0) {
                    _html += '<div class="calendar-td ' + cls + '" id="' + id + '" year="' + year + '" month="' + month_ + '" day="' + dow + '"><span class="weekday">' + '<div class="date-padding">' + dow + '</div>' + '</span></div>';
                } else {
                    _html += '<div class="calendar-td ' + cls + '" id="' + id + '" year="' + year + '" month="' + month_ + '" day="' + dow + '">' + '<div class="date-padding">' + dow + '</div>' + '</div>';
                }

            }
            _html = "<div class='calendar-week clearfix'>" + _html + "</div>";
            this.calendar.find('.calendar-body').append(_html);
        }
        this.loadEvents();
    };

    Plugin.prototype.renderDays = function() {
        var html = '';
        for (var j = this.weekStart; j < this.weekStart + 7; j++) {
            html += "<div class='calendar-th'>" + this.days[j % 7] + "</div>";
        }

        var _html = "<div class='calendar-week clearfix'>" + html + "</div>";
        this.calendar.find('.calendar-header').append(_html);
    };

    Plugin.prototype.click = function(e) {

        e.stopPropagation();
        e.preventDefault();
        var target = $(e.target).closest('.calendar-td, .calendar-th');
        var nodeName = "";
        if (target.hasClass('calendar-td')) {
            nodeName = 'calendar-td'
        }
        if (target.hasClass('calendar-th')) {
            nodeName = 'calendar-th'
        }
        if (target.length == 1) {
            switch(nodeName) {
                case 'calendar-td':
                    if (target.is('.day')) {
                        var day = parseInt(target.attr('day'), 10) || 1;
                        var month = parseInt(target.attr('month'), 10) || 1;
                        var year = parseInt(target.attr('year'), 10) || 1;

                        this.element.trigger({
                            type : 'changeDay',
                            day : day,
                            month : month,
                            year : year,
                        });
                    } else if (target.is('.holiday')) {
                        var day = parseInt(target.attr('day'), 10) || 1;
                        var month = parseInt(target.attr('month'), 10) || 1;
                        var year = parseInt(target.attr('year'), 10) || 1;

                        this.element.trigger({
                            type : 'onEvent',
                            day : day,
                            month : month,
                            year : year,
                        });
                    } else if (target.is('.today')) {
                        var day = parseInt(target.attr('day'), 10) || 1;
                        var month = parseInt(target.attr('month'), 10) || 1;
                        var year = parseInt(target.attr('year'), 10) || 1;

                        this.element.trigger({
                            type : 'changeDay',
                            day : day,
                            month : month,
                            year : year,
                        });
                    }
                    break;
                case 'calendar-th':
                    if (target.is('.sel')) {
                        switch(target.attr("id")) {
                            case 'last':
                                this.update_date('prv');
                                var prv = new Date(this.yp, this.mm, 1);
                                this.live_date = prv;
                                this.renderCalendar(prv, this.events);
                                this.element.trigger({
                                    type : 'onPrev',
                                });
                                break;
                            case 'current':
                                this.update_date('crt');
                                var now = new Date();
                                this.live_date = now;
                                this.renderCalendar(now, this.events);
                                this.element.trigger({
                                    type : 'onCurrent',
                                });
                                break;
                            case 'next':
                                this.update_date('nxt');
                                var nxt = new Date(this.yn, this.mm, 1);
                                this.live_date = nxt;
                                this.renderCalendar(nxt, this.events);
                                this.element.trigger({
                                    type : 'onNext',
                                });
                                break;
                        }
                    }
                    break;
            }
        }
    };

    Plugin.prototype.update_date = function(action) {
        var now = new Date();

        switch(action) {
            case 'prv':
                now = new Date(this.yy, this.mm - 1, 1);
                break;
            case 'nxt':
                now = new Date(this.yy, this.mm + 1, 1);
                break;
            case 'crt':
                break;
        }

        this.mm = now.getMonth();
        this.yy = now.getFullYear();

        var mon = new Date(this.yy, this.mm, 1);
        this.yp = mon.getFullYear();
        this.yn = mon.getFullYear();
    };

    Plugin.prototype.isLeapYear = function(year) {
        return (((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0))
    };

    // A really lightweight plugin wrapper around the constructor,
    // preventing against multiple instantiations
    $.fn[pluginName] = function(options) {
        return this.each(function() {
            if (!$.data(this, 'plugin_' + pluginName)) {
                $.data(this, 'plugin_' + pluginName, new Plugin(this, options));
            }
        });
    }
})(jQuery, window, document);

