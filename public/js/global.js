
function removeObject($this, url, containerSelector, valSelector) {
    if (!confirm('确认删除该对象？')) {
        return;
    }
    if (!containerSelector) {
        containerSelector = '.itm';
    }
    if (!valSelector) {
        valSelector = '.tmp';
    }
    var container = $this.closest(containerSelector);
    var id = container.find(valSelector).val();
    var data = {id: id};
    $.ajax({
        url: url,
        dataType: 'json',
        data: data,
        type: 'POST',
        success: function(response) {
            if (response) {
                container.fadeOut(400, function() {
                    container.remove();
                });
            } else {
                alert('删除失败');
            }
        }
    });
}
Number.prototype.timeFormat = function() {
    var _SPM = 60;
    var integer = parseInt(this);
    var min = parseInt(integer / _SPM);
    var sec = integer - min * _SPM;

    if (min < 10) {
        min = "0" + min;
    }
    if (sec < 10) {
        sec = "0" + sec;
    }
    var result = min + ":" + sec;
    return result;
};
function inArray(val, arr) {
    var result = false;
    for(var k in arr) {
        if(arr[k] === val) {
            result = true;
            break;
        }
    }
    return result;
}
(function($) {

    $.fn.clickToggle = function(option) {
        var setting = {
            closeBtnSelector: '.close-btn',
            action: 'show'
        };
        var $this = $(this);
        $.extend(setting, option);
        $this.data('setting', setting);
        $(event).stopPropagation();
        var stopPropFunc = function() {
            $(event).stopPropagation();
        };
        $this.bind('click', stopPropFunc);
        $this.toggle();
        var toggleFunc = function() {
            $this.hide();
            $this.unbind('click', stopPropFunc);
            $(window).unbind('click', toggleFunc);
        };
        $this.find(setting.closeBtnSelector).click(function() {
            toggleFunc();
        });
        $(window).bind('click', toggleFunc);
    };
    $.fn.autoToggle = function(option) {
        var setting = {
            delay: 2000,
            animationDuration: 300,
            animationDirection: 'top',
            animationDistance: '90',
            beforeHide: false,
            beforeDisplay: false
        };
        $.extend(setting, option);

        // append "null cursor" style into body for once ...
        var style_id = 'ct-null-cursor-style';
        if (!$('body').data(style_id)) {
            var style = $("<style>.null-cursor {cursor: none;}</style>");
            // 360浏览器里面不能使用下面方式去掉鼠标，否则360会强制添加鼠标图案导致画面不停上下切换
            $('body').append(style);
            $('body').data(style_id, 'done');
        }
        var $this = $(this);
        $(window).on('mousemove', function() {
            var prev_mouse = $this.data('prev_mouse');
            var current_mouse = {
                x: event.clientX,
                y: event.clientY
            };
            if (prev_mouse) {
                var MIN_DIST = 20;
                if (Math.abs(prev_mouse.x - current_mouse.x) < MIN_DIST && Math.abs(prev_mouse.y - current_mouse.y) < MIN_DIST) {
                    return;
                } else {
                    // nothing to do for now ...
                }
            }
            $this.data('prev_mouse', current_mouse);

            var delay = setting.delay;
            var animationDuration = setting.animationDuration;
            // display
            var display = {};
            display[setting.animationDirection] = 0;
            var hide = {};
            hide[setting.animationDirection] = '-' + setting.animationDistance + 'px';
            var position = {
                display: display,
                hide: hide
            };
            if ($this.data('doing') !== 'true') {
                if (setting.beforeDisplay && typeof (setting.beforeDisplay) === 'function')
                    setting.beforeDisplay();
                $this.data('doing', 'true');
                $this.stop().animate(position.display, animationDuration, 'linear', function() {
                    $this.data('doing', 'false');
                });
            }
            // hide
            var hide_time_out = $this.attr('id') + 'hidetimeout';
            var hide_time_out_id = $('body').data(hide_time_out);
            if (hide_time_out_id) {
                clearTimeout(hide_time_out_id);
            }
            $this.parent().removeClass('null-cursor');
            var e = event;
            $('body').data(hide_time_out, setTimeout(function() {
                if (setting.beforeHide && typeof (setting.beforeHide) === 'function')
                    setting.beforeHide();
                $this.parent().addClass('null-cursor');
                $this.stop().animate(position.hide, animationDuration, 'linear');
            }, delay));
        });
    };

    var videoMethods = {
        init: function(option) {
            var setting = {
                src: 'default path',
                total: "#t3",
                current: "#t1",
                seekbar: "#tv-prog-seekbar",
                loaded: "#tv-prog-loaded",
                progress: "#tv-prog",
                speakerButton: "#volume-speaker",
                volumeBar: "#volume-holder",
                fullscreenButton: "#fullscreen",
                container: "#tv",
                onPlay: false,
                onPause: false,
                onEnded: false
            };
            var $this = $(this);
            var self = $this.get(0);
            $.extend(setting, option);
            $this.data('setting', setting);
            if (self.tagName.toLowerCase() !== 'video') {
                return;
            }
            $this.find('source').attr('src', setting.src);
            $this.video('update');
            var xClick = function(obj) {
                var x = event.clientX - $(obj).offset().left;
                var w = $(obj).width();
                var perc = Math.ceil(x / w * 100);
                if (perc >= 99) {
                    perc = 99;
                }
                return perc;
            };
            $(setting.seekbar).on('click', function() {
                $this.video('seek', xClick(this));
            });
            $(setting.speakerButton).on('click', function() {
                $this.video('switchSpeaker');
            });
            $(setting.volumeBar).on('click', function() {
                $this.video('adjustVolume', xClick(this));
            });
            $(setting.fullscreenButton).click(function() {
                $this.video("fullscreen");
                $(this).toggleClass('on');
            });
            if (setting.onPlay && typeof (setting.onPlay) === 'function') {
                $this.on('play', setting.onPlay);
            }
            if (setting.onPause && typeof (setting.onPause) === 'function') {
                $this.on('pause', setting.onPause);
            }
            if (setting.onEnded && typeof (setting.onEnded) === 'function') {
                $this.on('ended', setting.onEnded);
            }
        },
        play: function(callback) {
            var self = $(this).get(0), setting = $(this).data('setting');
            self.play();
            if (callback) {
                callback();
            }
        },
        pause: function(callback) {
            var self = $(this).get(0), setting = $(this).data('setting');
            self.pause();
            if (callback) {
                callback();
            }
        },
        restart: function(callback, src) {
            var self = $(this).get(0), setting = $(this).data('setting');
            setting.src = src;
            $(this).find('source').attr('src', src);
            self.load();
            self.play();
            if (callback) {
                callback();
            }
        },
        update: function(option) {
            var self = $(this).get(0), setting = $(this).data('setting');
            $.extend(setting, option);
            var update_id = setInterval(function() {
                // update buffer
                var currentTag = $(setting.current);
                var totalTag = $(setting.total);
                // update time
                if (self.duration) {
                    currentTag.html(self.currentTime.timeFormat());
                    totalTag.html(self.duration.timeFormat());
                    $(setting.loaded).css('width', (self.currentTime / self.duration) * 100 + '%');
                }
            }, 200);
            $('body').data('update_id', update_id);
        },
        seek: function(perc) {
            var self = $(this).get(0), setting = $(this).data('setting');
            self.currentTime = self.duration * (perc / 100);
        },
        switchSpeaker: function() {
            var self = $(this).get(0), setting = $(this).data('setting');
            self.muted = !self.muted;
            $(setting.speakerButton).toggleClass('disable');
            if (self.muted) {
                $($(setting.volumeBar).children().get(0)).css('width', 0);
            } else {
                var w = self.volume * 100 + "%";
                $($(setting.volumeBar).children().get(0)).css('width', w);
            }
        },
        adjustVolume: function(perc) {
            var self = $(this).get(0), setting = $(this).data('setting');
            self.muted = false;
            $(setting.speakerButton).removeClass('disable');
            var v = Math.ceil(perc / 10);
            if (v < 1) {
                v = 1;
            }
            var w = v * 10 + "%";
            v = v / 10;
            self.volume = v;
            $($(setting.volumeBar).children().get(0)).css('width', w);
        },
        fullscreen: function() {
            if (!$(this).data('fullscreen'))
                $(this).data('fullscreen', false);
            var self = $(this).get(0), setting = $(this).data('setting');
            var docElm = $(setting.container).get(0);

            var isFullscreen = $(this).data('fullscreen');
            if (isFullscreen) {
                if (document.webkitCancelFullScreen) {
                    document.webkitCancelFullScreen();
                } else if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                }
            } else {
                if (docElm.requestFullscreen) {
                    docElm.requestFullscreen();
                } else if (docElm.mozRequestFullScreen) {
                    docElm.mozRequestFullScreen();
                } else if (docElm.webkitRequestFullScreen) {
                    docElm.webkitRequestFullScreen();
                }
            }
            $(this).data('fullscreen', !isFullscreen);
        }
    };
    /*
     * 启动方法
     * @param {type} method     传递字符串，将被识别为方法名; 传递对象，将作为init方法的参数（一般是option）, 并调用init方法
     * @returns {unresolved}
     */
    $.fn.video = function(method) {
        if (videoMethods[method]) {
            return videoMethods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return videoMethods.init.apply(this, arguments);
        } else {
            $.error('The method ' + method + ' does not exist in $.uploadify');
        }
    };

    $.fn.stopPropagation = function() {
        var e = $(this).get(0);
        if (e.stopPropagation) {
            e.stopPropagation();
        } else {
            e.cancelBubble = true;
        }
    };
})(jQuery);

