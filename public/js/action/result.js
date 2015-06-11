$(document).ready(function () {
    initMyIndex();
    initShare();
});

function initShare() {
    $('#share').tap(function() {
        switchSharingBds();

        location.href = window.location.href + '#' +"/meet/view/" + meet_id;
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