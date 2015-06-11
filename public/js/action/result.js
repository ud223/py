$(document).ready(function () {
    initMyIndex();
    initShare();
});

function initShare() {
    $('#share').tap(function() {
        switchSharingBds();

        location.href = "/meet/vote/" + meet_id;
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