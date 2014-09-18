
function sharingIt(obj) {
    var $this = $(obj).closest('.list-item');
    var sid = $this.attr('sid');
    var title = $this.find('.lt').html();
    var img = SERVER_URL + $('.alt-img').attr('src');
    var rlink;
    if (sid) {
        // sharing special
        rlink = generatePlayLink(sid, false, 1, true);
        img = $this.attr('sharing_photo');
    } else {
        // sharing program
        sid = $('#tv-listbar').attr('sid');
        var pid = $this.attr('id');
        rlink = generatePlayLink(sid, pid, 1, true);
    }
    var content = $('.sharing-popup').clone(true);
    title = "“" + title + "”";
    content.find('.cts-1 p span').html(title);
    title = "快来看芝士电视，几分钟了解这么多真相。 " + title;
    content.find('.weibo').click(function() {
//        PLAYER.pause();
        shareTSina(title, rlink, '', img);
    });
    content.find('.qqweibo').click(function() {
//        PLAYER.pause();
        shareToWb(title, rlink, '', img);
    });
    content.find('.weixin').click(function() {
        var container = $(this).closest('.sharing-popup');
        container.find('.hide-qrcode').show();
        new QRCode(container.find('.cts-2 .qr').empty().get(0), {
            text: rlink,
            width: 270,
            height: 270,
            colorDark: "#333333",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
        //qrcode.clear(); // clear the code. 
        //qrcode.makeCode("http://naver.com"); // make another code.

        container.find('.cts-2').animate({
            left: 0
        }, 200, 'linear');
    });
    $.popup({
        content: content,
        containerBoxSelector: '#tv',
        height: 382,
        modal: true,
        width: 352});
    return false;
}


function switchFullscreen() {
    var docElm = $('#tv').get(0);
    var ff = $('#tv-ctrl-fullscreen').attr('full');
    var isFullscreen = ff === 'yes' ? true : false;
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
    $('#tv-ctrl-fullscreen').attr('full', isFullscreen ? 'no' : 'yes');
}

function clickSeek(obj) {
    $.waiting('连接中，请稍候...');
    PLAYER.pause();
    seek(xClick(obj));

    setTimeout(function() {
        $.endWaiting();
        PLAYER.play();
    }, 2000);
}

function xClick(obj) {
    var x = event.clientX - $(obj).offset().left;
    var w = $(obj).width();
    var perc = Math.ceil(x / w * 100);
    if (perc >= 99) {
        perc = 99;
    }
    return perc;
}

function seek(perc, time) {
    var self = PLAYER;
    var seeking_id = setInterval(function() {
        var seeking_in_id = $('body').data('seeking_id');
        var readyState = self.readyState;
        if (readyState === 4) {
            $('body').data('seeking_id', null);
            clearInterval(seeking_in_id);

            var val = 0;
            if (perc) {
                val = self.duration * (perc / 100);
            } else if (time) {
                val = time;
            }
            self.currentTime = val;
        }
    }, 500);
    $('body').data('seeking_id', seeking_id);
}

function generatePlayLink(special, program, mode, isUrl) {
    // mode=1 : /play?special=xxx&program=xxx
    // mode=2 : /play/special/program
    if (!special)
        return false;

    if (!mode) {
        mode = 1;
    }
    var result = PLAY_PAGE_URL;
    if (isUrl) {
        result = SERVER_URL + PLAY_PAGE_URL;
    }
    if (mode === 1) {
        result = result + '?special=' + special;
        if (program)
            result = result + '&program=' + program;

    } else {
        result = result + '/' + special;
        if (program)
            result = result + '/' + program;
    }

    return result;
}


function loginTipFunc() {
    var param = {
        content: $('.login-tip').clone(true),
        width: 280
    };
    $.popup(param);
}