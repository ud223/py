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
    location.href = window.location.href + '#' +"/meet/view/" + meet_id;
    
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