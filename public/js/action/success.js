$(document).ready(function () {
    initMyIndex();
    initShare();
});

function initShare() {
    $('#share').tap(function() {
        if (view_type == 1) {
            location.href = "/meet/view/" + meet_id;
        }
        else {
            location.href = "/meet/vote/" + meet_id;
        }
    });
}

function initMyIndex() {
    $('#myIndex').tap(function() {
        location.href = "/";
    });
}