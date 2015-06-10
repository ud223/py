$(document).ready(function () {
    initMyIndex();
    initShare();
});

function initShare() {
    $('#share').tap(function() {

    });
}

function initMyIndex() {
    $('#myIndex').tap(function() {
        location.href = "/";
    });
}