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