$(document).ready(function() {
    var meet = new Meet();
    //测试
    //localStorage.setItem('user_id', '123456');

    var user_id = localStorage.getItem('user_id');

    meet.load(user_id, id);

    var word = new Word();

    word.load(id);

    $(document).on('click', '#word_submit', function () {
        var word = new Word();

        word.setMeet_Id(id);
        word.setUser_Id(user_id);
        word.setText($('#word_text').val());

        word.add();
    });

    $('#pge-cover-back').tap(function() {
        location.href = "/";
    })
})