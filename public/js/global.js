
/* 
 * manage-product-create
 * manage-product-save
 * 保存按钮前对选中图片信息的保存
 * 保存按钮前对选中仓储信息的保存
 */
function productSaveBtn(separator) {
    $('#save-btn').click(function() {
        // 保存选中的图片
        var save = $('#gy-photo').attr('save');
        if (!save || save === '{}') {
            $('#photo').val('');
        } else {
            $('#photo').val(save);
        }

        // 保存选中的location
        var location = $('.location-ul input[type="checkbox"]:checked');
        if (location.length) {
            var locations = new Array();
            $.each(location, function() {
                var c = $(this).attr('id');
                if (c)
                    locations.push($(this).attr('id'));
            });
            location = locations.join(separator);
        } else {
            location = '';
        }
        $('#location').val(location);
    });
}

function removeObject($this, url, containerSelector, valSelector) {
    if (!confirm('确认删除该对象？')) {
        return;
    }
    if(!containerSelector) {
        containerSelector = '.itm';
    }
    if(!valSelector) {
        valSelector = '.tmp';
    }
    var container = $this.closest(containerSelector);
    var id = container.find(valSelector).val();
    var data = {id: id};
    $.ajax({
        url: url,
        dataType: 'json',
        data: data,
        type: 'POST',
        success: function(response) {
            if (response) {
                container.fadeOut(400, function() {
                    container.remove();
                });
            } else {
                alert('删除失败');
            }
        }
    });
}