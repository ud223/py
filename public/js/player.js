$(document).ready(function() {

});

function sharingIt(obj) {
    var $this = $(obj).closest('.list-item');
    var sid = $this.attr('sid');
    if (sid) {
        // sharing special
        alert('sharing special');
    } else {
        // sharing program
        var pid = $this.attr('id');
        sid = $('#tv-listbar').attr('sid');
        alert('sharing program')
    }
    var content = $('.sharing-popup').clone(true);
    $.popup({
        content: content,
        containerBoxSelector: '#tv',
        width: 260});
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
    seek(xClick(obj));
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