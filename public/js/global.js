(function(){
    // 全几乎的tab部分
    var the_tabs = $('.tab-bt.selected');
    //if(the_tabs.length) {
        var $ix = the_tabs.attr('rel');
        $('#' + $ix).show();

        $('body').on('touchstart', '.tab-bt', function(){
            var $this = $(this);
            if(!$this.hasClass('selected')) {
                var lastOne = $this.siblings('.tab-bt.selected');
                if(lastOne.length) {
                    lastOne.removeClass('selected');
                    var ct = $('#' + lastOne.attr('rel'));
                    ct.hide().removeClass('selected');
                }
                $this.addClass('selected');
                $('#' + $this.attr('rel')).show().addClass('selected');
            }
        });
    //}
})(jQuery);