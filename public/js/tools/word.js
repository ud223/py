function loadWords(data) {
    $('#word-list').html('');

    //$.each(data, function () {
    //    var node = $('.words').clone(true);
    //
    //    var date = this.date.date;
    //
    //    date = date.replace('-', '年');
    //    date = date.replace('-', '月');
    //    date = date.replace(' ', '日');
    //
    //    node.find('.ctxx').html(this.text);
    //    node.find('.uune').html(this.user_id);
    //    node.find('.untme').html(date);
    //
    //    $('#word-list').append(node);
    //});

    for (i = 0; i < data.length; i++) {
        var node = $('#word_model').clone(true);

        var date = data[i].date.date;

        date = date.replace('-', '年');
        date = date.replace('-', '月');
        date = date.replace(' ', '日');

        node.find('.ctxx').html(data[i].text);
        node.find('.uune').html(data[i].user_id);
        node.find('.untme').html(date);

        $('#word-list').append(node);
    }
}