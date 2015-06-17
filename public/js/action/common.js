function initBtnShare() {
    $("#btn_share").tap(function () {
        $('#sharing-bds').show();
        $('#sharing-bds').tap(function(){
            $('#sharing-bds').hide();
        });

        //var user_id = localStorage.getItem('user_id');
        //
        //var url = window.location.href;
        //var param = '#/share/'+ user_id;
        //
        //if (url.indexOf(param) < 0) {
        //    url = url + param;
        //}
        //
        //location.href = url;
    })
}