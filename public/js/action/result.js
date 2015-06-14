$(document).ready(function () {
    hrefUrl();
    initMyIndex();
    initShare();
});

function hrefUrl() {
    var url = window.location.href;
    alert("网址："+url);
    return;
    var urls = url.split("#");
    alert(urls[1]);
    if (urls.length > 1) {
        location.href = urls[1];

        return;
    }
}

function initShare() {
    var user_id = localStorage.getItem('user_id');
    alert(1);
    url =  window.location.href + '#' +"/share/"+ user_id +"/" + meet_id;
    alert(url);
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