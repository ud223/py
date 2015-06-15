$(document).ready(function () {
    hrefUrl();
    initMyIndex();
    initShare();
    initMeetUrl();
});

function initMeetUrl() {
    var url = '';

    if (view_type == 1) {
        url = "/meet/view/" + meet_id;
    }
    else {
        url = "/meet/vote/" + meet_id;
    }

    $('#pgx3').attr('href', url)
}

function hrefUrl() {
    var url = window.location.href;

    var urls = url.split("#");

    if (urls.length > 1) {
        location.href = urls[1];
    }
}

function initShare() {

    var user_id = localStorage.getItem('user_id');

    url =  window.location.href + '#' +"/share/"+ user_id +"/" + meet_id;

    location.href = url;

    $('#share').tap(function() {
        switchSharingBds();
    });
}

function initMyIndex() {
    $('#myIndex').tap(function() {
        location.href = "/";
    });
}

function switchSharingBds(){
    $('#sharing-bds').show();
    $('#sharing-bds').tap(function(){
        $('#sharing-bds').hide();
    });
}