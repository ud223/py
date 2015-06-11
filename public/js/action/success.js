$(document).ready(function () {
    hrefUrl();
    initMyIndex();
    initShare();
});

function hrefUrl() {
    var url = window.location.href;

    var urls = url.split("#");

    if (urls.length > 1) {
        location.href = urls[1];
    }
}

function initShare() {
    $('#share').tap(function() {
        switchSharingBds();

        if (view_type == 1) {
            url = window.location.href + '#' +"/meet/view/" + meet_id;
        }
        else {
            url = window.location.href + '#' +"/meet/vote/" + meet_id;
        }

        location.href = url;
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