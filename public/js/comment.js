/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function TsComment(){
    if(this instanceof TsComment){
		this.init();
	}else{
		return new TsComment();
	}
}

TsComment.prototype = {
    init:function(){
        var context = this;
        context._video_key();
        
        var _config = context._config('danmu');
        //console.log(ts_user_id+'--');
        var ts_user_id = window.ts_user_id||'';
        if(_config){
            if(_config.split('_')[0] === ts_user_id && _config.split('_')[1] === 'no'){
                $('#tv-danmuopened').hide();
                $('#tv-senddanmu').hide();
                $('#tv-danmu-all').hide();
                $('#tv-danmuclosed').show();
                $('#tv-fullscreendanmu,#tv-verticaldanmu').hide();
                $('#danmakuwrap').show();
                $('#img_danmu_box').hide();
            }
            
        }
        
        this._request('api/comments/hot/get/'+context._pid(),null,function(data){
            context._parse(data);
        });
        
        $('#tv-fullscreendanmu,#tv-verticaldanmu').click(function(){
            $('#tv-fullscreendanmu,#tv-verticaldanmu').removeClass('selected');
            $(this).addClass('selected');
            
            if($(this).attr('id') == 'tv-fullscreendanmu'){
                $('#tv-danmubar-auto-box').hide();
                context._clear();
                
                $('#danmakuwrap').show();
            }else{
                $('#tv-danmubar-auto-box').show();
                $('#danmakuwrap').hide();
            }
        });
        
        if($('#ts_comment_tmp').length === 0){
            var $dom = $(this._tmp);
            $dom.attr('id','ts_comment_tmp');
            $('body').append($dom);
        }
        
        $('#tv-danmuopened').hover(function(){
            $('#tv-danmuclosed').find('.text').text('开弹幕');
            $(this).find('.text').text('关弹幕');
            $(this).find('.ico').addClass('close_ico');
            $('#tv-danmuclosed').find('.ico').addClass('open_ico');
        },function(){
            $(this).find('.text').text('弹幕开');
            $(this).find('.ico').removeClass('close_ico');
        });
        
        $('#tv-danmuclosed').hover(function(){
            $('#tv-danmuopened').find('.text').text('关弹幕');
            $(this).find('.text').text('开弹幕');
            $(this).find('.ico').addClass('open_ico');
            $('#tv-danmuopened').find('.ico').addClass('close_ico');
        },function(){
            $(this).find('.text').text('弹幕关');
            $(this).find('.ico').removeClass('open_ico');
        });
        
        $('#tv-senddanmu').click(function(e){
            if(!ts_user_id){loginTipFunc();e.preventDefault();return false;}
        });
        
        $('#tv-danmubar-auto-box,#tv-danmubar-history').on('click','.opera:not(.selected)',function(){
            if(!ts_user_id){loginTipFunc();return;}
            if($(this).hasClass('is_me'))return;
            //console.log('ccc'+$(this).closest('.tv-danmubar-single-itm').attr('action_id'));
            var val = parseInt($(this).text());
            val+=1;
            $(this).text(val);
            $(this).addClass('selected');
            context._up_arr[$(this).closest('.tv-danmubar-single-itm').attr('action_id')] = val;
            //context._up_arr.push($(this).closest('.tv-danmubar-single-itm').attr('action_id'));
            context._request('api/comments/up',{id:$(this).closest('.tv-danmubar-single-itm').attr('action_id')})
        });
        
        $('#tv-danmuopened').click(function(){
            $(this).hide();
            $('#tv-senddanmu').hide();
            $('#tv-danmu-all').hide();
            $('#img_danmu_box').hide();
            $('#tv-danmuclosed').show();
            context.stop();
            if(ts_user_id){
                context._config('danmu',ts_user_id+'_'+'no');
            }
            context._config('danmu');
            
            $('#tv-fullscreendanmu,#tv-verticaldanmu').hide();
            $('#danmakuwrap').hide();
            
        });
        
        $('#tv-danmuclosed').click(function(){
            $(this).hide();
            $('#tv-danmuopened').show();
            $('#tv-danmu-all').show();
            $('#img_danmu_box').show();
            $('#tv-senddanmu').show();
            if(ts_user_id){
                context._config('danmu','');
            }
            context.start();
            $('#tv-fullscreendanmu,#tv-verticaldanmu').show();
            
            if($('#tv-fullscreendanmu').hasClass('selected')){
                context._clear();
                $('#danmakuwrap').show();
            }
        });
        
        $('#tv-danmu-all').click(function(){
            $('#tv-danmubar-history').toggle();
            context._history(context._data);
        });
        
        $('#tv-danmubar-history .close').click(function(){
            $('#tv-danmubar-history').hide();
        });
        
        
        $('.submit_comment').click(function(){
            var text = $(this).closest('div').find('.value_comment').val();
            var time = context._time();
            //alert('1');
            if($('#P_popup .img_upload_form .danmu_img').length >0 && $('#P_popup .img_upload_form .danmu_img').val() != ""){
                
                
                //$(this).closest('div').find('.value_comment').val();
                context._time_arr.push(time+2);
                context._data.push({"id":"5448a3f4b7c58e100f8b4568","image":$('#P_popup .imgHeadPhoto').attr('src'),"text":text,"time_at":time+2,"pid":"53","up":0,"hot":0,"type":"image","username":"我",is_me:true});
                
                /*
                
                var $img_box = $('<div style="position:absolute;left:400px;z-index:100;top:50px;"><img style="max-width:300px;"/><div class="content" style="background:#000000;color:#ffffff;font-size:18px;padding:10px;max-width:300px;"></div></div>');
               
               $img_box.find('img').on('load',function(){
                   var w_height = $(window).height();
                   var b_height = $img_box.height();
                   
                   //$img_box.css('top',(w_height-b_height)/2+'px');
                   $img_box.appendTo('#img_danmu_box');
                   
                   function fade(){
                       if($('#tv-video-player')[0].seeking || $('#tv-video-player')[0].paused){
                            //setTimeout(fade,30000);
                            //return;
                        }
                       
                        $img_box.fadeOut(300,function(){
                           $(this).remove();
                       });
                   }
                   
                   setTimeout(fade,30000);
                   
               });
               $img_box.find('img').attr('src',$('#P_popup .imgHeadPhoto').attr('src'));
                $img_box.find('.content').text(text);
                */
                
                
                
                
                $('#P_popup .img_upload_form .danmu_time').val(context._time());
                $('#P_popup .img_upload_form .danmu_pid').val(context._pid());
                $('#P_popup .img_upload_form').submit();
                $('.P_closebtn').click();
                return;
            }
            
            //alert(2)
            //var text = $(this).closest('div').find('.value_comment').val();
            if(text === '')return;
            
            var data = {
                type:'text',
                time_at:time,
                text:text,
                pid:context._pid()
            };
            context._time_arr.push(time+1);
            console.log(time);
            context._data.push({"id":"5448a3f4b7c58e100f8b4568","text":text,"time_at":time+1,"pid":"53","up":0,"hot":0,"type":"text","username":"我",is_me:true});
            context._request('api/comments/add',data);
            $('.P_closebtn').click();
            
        });
        
        $('.value_comment').on('keyup',function(e){
            
            if($('#P_popup .img_upload_form .danmu_img').length >0)return;
            if(e.which === 13){
                var text = $(this).val();
                if(text === '')return;
                var time = context._time();
                var data = {
                    type:'text',
                    time_at:time,
                    text:text,
                    pid:context._pid()
                };
                context._time_arr.push(time+1);
                //console.log(time);
                context._data.push({"id":"5448a3f4b7c58e100f8b4568","text":text,"time_at":time+1,"pid":"53","up":0,"hot":0,"type":"text","username":"我",is_me:true});
                context._request('api/comments/add',data);
                $('.P_closebtn').click();
            }
        });
    },
    _up_arr:{},
    _clear:function(){
                //$('#danmakuwrap .danmaku').remove();
                //danmakucache.scroll[0].line = 0;
                //danmakucache.scroll[0].count = 0;
                //danmakucache.fixed[0].count = 0;
                //danmakucache.fixed[0].count = 0;
                
                $('#danmakuwrap .danmaku').each(function(){
                    danmakucache.scroll[$(this)[0].getAttribute('line') - 1].count--;
                    $(this).remove();
                });
                
    },
    _text_count:function(){
        var count = 0;
        
        for(var i = 0;i<this._data.length;i++){
            if(this._data[i].type == 'text'){
                count ++;
            }
        }
        
        return count;
    }
    ,
    _tmp:['<div class="tv-danmubar-single-itm" style="display:none;">',
                    '<div class="li simple">',
                        '<span class="danmu-type"></span>',
                        '<span class="creator">night knight</span>',
                        '<div class="clear"></div>',
                    '</div>',
                    '<div class="li txt-content">',
                        '这几节目不错， 为随便说说为的看法，首先。为随便说说为的看法。',
                    '</div>',
                    '<div class="li opera">',
                        '43',
                    '</div>',
                    '<div class="clear"></div>',
                '</div>'].join(''),
    _data:null,
    _loop_id:null,
    _time_arr:[],
    _pid:function(){
        return $('.list-item.selected').attr('id');
        var path = window.location.pathname;
        var start = path.lastIndexOf('/')+1;
        var end = path.length;
        return path.substr(start);
    },
    _request:function(action,data,callback){
        var url =  window.location.protocol + "//" + window.location.host+'/'+action;
        var context = this;
        
        
        
        
        
        $.ajax({
            url:url,
            type:'post',
            dataType:'json',
            data:data,
            success:function(data){
                
                if(callback){
                    callback(data);
                }
                
                //context._parse();
            }
        });
    },
    _video_key:function(){
        $(document).on('keyup',function(e){
            var video = $('#tv-video-player')[0];
            
            if($(':focus').is('input')){
               
                return true;
            }
            switch(e.which){
                case 32:
                    if(video.paused === true){
                        video.play();
                    }else{
                        video.pause();
                    }
                    break;
            }
           // console.log(e.which);
        });
    },
    _parse:function(data){
        //console.log(data);
        if(data.code !== 200){
            
        }else{
            if(data.data.length > 0){
                this._data = data.data;
                
                this._time_arr = [];
                for(var i = 0;i<data.data.length;i++){
                    this._time_arr.push(data.data[i].time_at);
                }
                
                
            }else{
                this._data = [];
            }
            
        }
    },
    _history:function(item){
        var context = this;
        
        $('.tv-danmubar-history-head span').text('所有弹幕 ('+context._text_count()+')');
        $('.tv-danmubar-history-body').html('');
        for(var i = 0;i<item.length;i++){
            
            if(item[i].type === 'image'){
                
            }else{
                var $tmp = $('#ts_comment_tmp').clone(true).removeAttr('id').show();
                $tmp.find('.creator').text(item[i].username);
                $tmp.find('.txt-content').text(item[i].text);
                $tmp.attr('action_id',item[i].id);
                $tmp.find('.opera').text(item[i].up);
                console.log(item[i]);
                 if(item[i].is_me){
                    $tmp.find('.opera').addClass('is_me').css('cursor','default');
                }
                $tmp.css('top',top);

                if(item[i].id in context._up_arr){
                    $tmp.find('.opera').addClass('selected');
                    $tmp.find('.opera').text(context._up_arr[item[i].id]);
                }

                $('.tv-danmubar-history-body').append($tmp);
            }
            
        }
        
        
    },
    _item_in:function(item){
        var context = this;
        
        if($('#tv-video-player')[0].seeking || $('#tv-video-player')[0].paused)return;
        //if(ccc){return};
        //console.log(item);
        var s='';
        var dom = '<div class="comment_box" style="position:absolute;top:400px;width:100%"></div>';
        var tmp = '<div style="width:200px;height:100px;background:#cccccc;"></div>';
        var $dom = $(dom);
        
        for(var i = 0;i<item.length;i++){
            if(item[i].type === 'image'){
               var $img_box = $('<div style="position:absolute;left:200px;z-index:100;top:50px;"><img style="max-width:300px;"/><div class="content" style="background:#000000;color:#ffffff;font-size:18px;padding:10px;max-width:300px;"></div></div>');
               
               $img_box.find('img').on('load',function(){
                   var w_height = $(window).height();
                   var b_height = $img_box.height();
                   
                   //$img_box.css('top',(w_height-b_height)/2+'px');
                   $img_box.appendTo('#img_danmu_box');
                   
                   function fade(){
                       if($('#tv-video-player')[0].seeking || $('#tv-video-player')[0].paused){
                            setTimeout(fade,30000);
                            return;
                        }
                       
                        $img_box.fadeOut(300,function(){
                           $(this).remove();
                       });
                   }
                   
                   setTimeout(fade,30000);
                   
               });
               $img_box.find('img').attr('src',item[i].image);
                $img_box.find('.content').text(item[i].text);
                
            }else{
                
                var $tmp = $('#ts_comment_tmp').clone(true).removeAttr('id').show();
                $tmp.find('.creator').text(item[i].username);
                $tmp.find('.txt-content').text(item[i].text);
                $tmp.attr('action_id',item[i].id);
                $tmp.find('.opera').text(item[i].up);
                if(item[i].is_me){
                    $tmp.find('.opera').addClass('is_me').css('cursor','default');
                }
                $tmp.css('top',top);

                if(item[i].id in context._up_arr){
                    $tmp.find('.opera').addClass('selected');
                    $tmp.find('.opera').text(context._up_arr[item[i].id]);
                }

                top += $tmp.height();
                $dom.append($tmp);
                s+=this._tmp;
            }
            
            
        }
        
        var _height = $('.comment_box').height();
        var _top = -50;
        if(_height){
            _top -=_height;
        }
       // console.log(_height+'------');
         $('.comment_box').animate({
            top:_top
        },500,function(){
            $(this).remove();
        });
        $('#tv-danmubar-auto-box').append($dom);
        var top = 0;
        $dom.find('.tv-danmubar-single-itm').each(function(){
            
            //console.log(top);
            top+= 30;
            $(this).css('top',top);
            top+=$(this).height();
        });
        //$('#tv-danmubar-auto-box').append($('#ts_comment_tmp').clone(true).removeAttr('id').show());
        //$('.tv-danmubar-single-itm').show();
        
       
        _height = top;
        _top = -50;
        if(_height){
            _top -=_height;
        }
        
        
        //console.log(_top +'--------'+_height);
        
            function out(){
                if($('#tv-video-player')[0].seeking || $('#tv-video-player')[0].paused){
                    setTimeout(out,5000);
                    return;
                }
                $dom.animate({
                top:_top
                },500,function(){
                    $dom.remove();
                });
            }
        
        $dom.animate({
            top:'150px'
        },500,function(){
            setTimeout(out,5000);
        });
        
        
    },
    _time:function(){
      return Math.round($('#tv-video-player')[0].currentTime);  
    },
    _config:function(key,value){
        if(window.localStorage){
            if(value === ''){
                window.localStorage.removeItem(key);
            }
            if(value){
                window.localStorage.setItem(key,value);
            }else{
                return window.localStorage.getItem(key);
            }
        }
    },
    start:function(){
        var context = this;
        
        
        
        function loop(){
            var data = context._data;
            if(!data || data.length === 0){
                $('#tv-danmu-all').hide();
                return;
            }else{
                $('#tv-danmu-all').show();
            }
            
            //$('.tv-danmubar-single-itm').css('background','#ffffff');
            
            var c_time = context._time();
            var c_index = $.inArray(c_time, context._time_arr);
            if(c_index > -1){
                var item = [];
                for(var i=0;i<context._data.length;i++){
                    if(context._data[i].time_at === c_time){
                        item.push(context._data[i]);
                    }
                }
                context._item_in(item);
            }
        }
        
        loop();
        this._loop_id = setInterval(loop,1000);
    },
    stop:function(){
        clearInterval(this._loop_id);
        $('.comment_box').hide(100,function(){
            $(this).remove();
        });
    },
    mycheck:function(time,data){
        //var context = TsComment.prototype;
        
        
        var c_time = Math.round($('#tv-video-player')[0].currentTime);
        var c_index = $.inArray(c_time,time);
        
        //console.log(c_time+'check---'+c_index+'---' +time)
            if(c_index > -1){
                var item = [];
                for(var i=0;i<data.length;i++){
                    if(data[i].time_at === c_time && data[i].type === 'text'){
                        //item.push(context._data[i]);
                        gendanmaku(data[i].text);
                    }
                }
                //context._item_in(item);
            }
        
        
        /*
        if( context._time_arr[danmakucursor] <= Math.round($(".player")[0].currentTime * 10)/10 ) {
        

            for(var i=0;i<context._data.length;i++){
                    if(context._data[i].time_at === context._time_arr[danmakucursor]){
                        //item.push(context._data[i]);
                        gendanmaku(context._data[i].text);
                    }
                }

            //gendanmaku(danmakutext);
            danmakucursor++;
            context.mycheck();
        }else{
            return true;
        }*/
    }
};




function PreviewImage(fileObj,imgPreviewId,divPreviewId){  
    //alert('c');
    var allowExtention=".jpg,.gif,.png";//允许上传文件的后缀名document.getElementById("hfAllowPicSuffix").value;  
    var extention=fileObj.value.substring(fileObj.value.lastIndexOf(".")+1).toLowerCase();              
    var browserVersion= window.navigator.userAgent.toUpperCase();  
    if(allowExtention.indexOf(extention)>-1){   
        if(fileObj.files){//HTML5实现预览，兼容chrome、火狐7+等  
            if(window.FileReader){  
                var reader = new FileReader();   
                reader.onload = function(e){  
                    //alert($('.'+imgPreviewId).length);
                    //document.getElementById(imgPreviewId).setAttribute("src",e.target.result);  
                    
                    var content = $('.send-danmu-board.P_bg')[0];
                    
                    var mt = "-" + $(content).height() / 2 + "px";
                    $(content).css('margin-top', mt);
                    
                    
                    $('.'+imgPreviewId).on('load',function(){
                        var content = $('.send-danmu-board.P_bg')[0];
                    
                        var mt = "-" + $(content).height() / 2 + "px";
                        $(content).css('margin-top', mt);
                    });
                    
                    $('.'+imgPreviewId)[1].setAttribute("src",e.target.result);  
                    
                    
                }    
                reader.readAsDataURL(fileObj.files[0]);  
            }else if(browserVersion.indexOf("SAFARI")>-1){  
                alert("不支持Safari6.0以下浏览器的图片预览!");  
            }  
        }else if (browserVersion.indexOf("MSIE")>-1){  
            if(browserVersion.indexOf("MSIE 6")>-1){//ie6  
                document.getElementById(imgPreviewId).setAttribute("src",fileObj.value);  
                $('.'+imgPreviewId).setAttribute("src",fileObj.value);  
            }else{//ie[7-9]  
                fileObj.select();  
                if(browserVersion.indexOf("MSIE 9")>-1)  
                    fileObj.blur();//不加上document.selection.createRange().text在ie9会拒绝访问  
                var newPreview =document.getElementById(divPreviewId+"New");  
                if(newPreview==null){  
                    newPreview =document.createElement("div");  
                    newPreview.setAttribute("id",divPreviewId+"New");  
                    newPreview.style.width = document.getElementById(imgPreviewId).width+"px";  
                    newPreview.style.height = document.getElementById(imgPreviewId).height+"px";  
                    newPreview.style.border="solid 1px #d2e2e2";  
                }  
                newPreview.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod='scale',src='" + document.selection.createRange().text + "')";                              
                var tempDivPreview=document.getElementById(divPreviewId);  
                tempDivPreview.parentNode.insertBefore(newPreview,tempDivPreview);  
                tempDivPreview.style.display="none";                      
            }  
        }else if(browserVersion.indexOf("FIREFOX")>-1){//firefox  
            var firefoxVersion= parseFloat(browserVersion.toLowerCase().match(/firefox\/([\d.]+)/)[1]);  
            if(firefoxVersion<7){//firefox7以下版本  
                document.getElementById(imgPreviewId).setAttribute("src",fileObj.files[0].getAsDataURL());  
            }else{//firefox7.0+                      
                document.getElementById(imgPreviewId).setAttribute("src",window.URL.createObjectURL(fileObj.files[0]));  
            }  
        }else{  
            document.getElementById(imgPreviewId).setAttribute("src",fileObj.value);  
        }           
    }else{  
        alert("仅支持"+allowExtention+"为后缀名的文件!");  
        fileObj.value="";//清空选中文件  
        if(browserVersion.indexOf("MSIE")>-1){                          
            fileObj.select();  
            document.selection.clear();  
        }                  
        fileObj.outerHTML=fileObj.outerHTML;  
    }  
}










var getline = function(type, foo) {
	if (type == 's') {
		reverse = foo||'yes';
		//console.log(reverse);
		line = danmakucache.scroll[0].line;
		min = danmakucache.scroll[0].count;
		if (reverse == 'yes') {
			$.each(danmakucache.scroll.slice().reverse(), function() {
				if (this.count <= min) {
					min = this.count;
					line = this.line;
				}
				//console.log(min);
			});
		}
		else {
			$.each(danmakucache.scroll.slice(), function() {
				if (this.count <= min) {
					min = this.count;
					line = this.line;
				}
				//console.log(min);
			});
		}
		return line;
	}
	else {
		reverse = foo||'yes';
		//console.log(reverse);
		line = danmakucache.fixed[0].line;
		min = danmakucache.fixed[0].count;
		if (reverse == 'yes') {
			$.each(danmakucache.fixed.slice().reverse(), function() {
				if (this.count <= min) {
					min = this.count;
					line = this.line;
				}
				//console.log(min);
			});
		}
		else {
			$.each(danmakucache.fixed.slice(), function() {
				if (this.count <= min) {
					min = this.count;
					line = this.line;
				}
				//console.log(min);
			});
		}
		return line;
	}
};

var classNameGen = function(charsLength,chars) {
    var length = charsLength; 
 
    if (!chars) 
        var chars = "abcdefghijkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ1234567890"; 
 
    var randomChars = ""; 
 
    for(x=0; x<length; x++) { 
        var i = Math.floor(Math.random() * chars.length); 
        randomChars += chars.charAt(i); 
    } 
    return 'h' + randomChars;
};

var getWidth = function(clientWidth) {
	return clientWidth;
	return ( 2 * clientWidth ) -  ( ( 3 / 14 ) * clientWidth );
};

var releasedanmaku = function(selectedline, foo) {
	initfoo = false||foo;
	//console.log(initfoo);
	if ($(".player")[0].paused || initfoo == true) {
		setTimeout(function() {
			releasedanmaku(selectedline);
		}, 2000);
		//console.log('Waiting Line ' + selectedline);
 	}
 	else {
		danmakucache[selectedline - 1].count--;
		//console.log('Released Line ' + selectedline);
 	}
};

var getdanmakuType = function(type) {
	type = parseInt(type);
	switch (type) {
		case 0:
			return 'special2';
			break;

		case 1:
			return 'general';
			break;

		case 2:
			return 'general';
			break;

		case 3:
			return 'general';
			break;

		case 4:
			return 'special2';
			break;

		case 5:
			return 'special1';
			break;

		case 6:
			return 'special3';
			break;

		default:
			return 'general';
			break;
	}
};

var gendanmaku = function(text, type, size, color) {
        function getOs()  
        {  
            var OsObject = "";  
           if(navigator.userAgent.indexOf("MSIE")>0) {  
                return "ie";  
           }  
           if(navigator.userAgent.indexOf("Firefox")>0){  
                return "firefox";  
           }  
           if(navigator.userAgent.indexOf("Safari")>0) {  
                return "safari";  
           }   
           if(navigator.userAgent.indexOf("Camino")>0){  
                return "camino";  
           }  
           if(navigator.userAgent.indexOf("Gecko/")>0){  
                return "gecko";  
           }  

        }
    
    
    
	text = text.replace(/ /g, '&nbsp;');
	text = text.replace(/\/n/g, '<br>');
	//console.log(typefoo);
	
	type = type||'general';
	size = size||25;
	size = size + 'px';

	color = color||'#ffffff';

	/*
	type = $('#selecteddanmakumode')[0].value;
	size = $('#selecteddanmakufontsize')[0].value + 'px';
	color = $('#hexVal')[0].value;
	*/

	//console.log(type);

	dmkid = classNameGen(16);

	jQuery('<style/>', {
		animation: dmkid,
	}).appendTo('head')[0];

	jQuery('<div/>', {
		class: 'danmaku',
		id: dmkid,
	}).appendTo('.danmakuwrap')[0];

	element = $("#" + dmkid)[0];

	element.innerHTML = text;

	if (type == 'general') {
		destroytype = 's';
		selectedline = getline('s');
		//console.log(selectedline);
		topoffset = ( selectedline - 1 ) * 35;
		danmakucache.scroll[selectedline - 1].count++;

		element.style['font-size'] = size;

	 	startpoint = videowidth + getWidth(element.clientWidth) + 10;
	 	endpoint = - element.clientWidth - 10;
	 	//element.style.display = 'none';
	 	console.log(startpoint+endpoint+'------');
	 	/*
	 	topoffset = 35 * Math.floor(Math.random() * 20);
	 	while (topoffset > videoheight) {
	 		topoffset = 35 * Math.floor(Math.random() * 20);
	 	}
	 	*/
	 	offsetflow = endpoint - startpoint;
                //var ie_dmkstyle ='-moz-animation-name: myfirst;-moz-animation-duration: 5s;-moz-animation-timing-function: linear;-moz-animation-delay: 2s;-moz-animation-iteration-count: infinite;-moz-animation-direction: alternate;-moz-animation-play-state: running;';
                //ie_dmkstyle += ';-moz-animation:' + dmkid + ' linear 8s;'+'animation:' + dmkid + ' linear 8s;';
                
                if(getOs() === 'firefox'){
                dmkstyle = '.' + dmkid + '{font-size:'+size+';text-shadow: 1px 1px 1px #000000;left:' + startpoint + 'px;top: ' + topoffset + 'px;color:' + color + ';-moz-animation:' + dmkid + ' linear 8s;-moz-animation-iteration-count:1;-moz-transform-origin:50% 0%;-moz-backface-visibility:hidden;-moz-perspective:1000;}@-moz-keyframes ' + dmkid + '{from{-moz-transform: translate3d(1px, 0, 0);}to{-moz-transform: translate3d(' + offsetflow + 'px, 0, 0);}} ';
	 	
                 }else if(getOs() === 'ie'){
                     dmkstyle = '.' + dmkid + '{text-shadow: 1px 1px 1px #000000;left:' + startpoint + 'px;top: ' + topoffset + 'px;color:' + color + ';-animation:' + dmkid + ' linear 8s;-animation-iteration-count:1;-transform-origin:50% 0%;-backface-visibility:hidden;-perspective:1000;}@-keyframes ' + dmkid + '{from{-transform: translate3d(1px, 0, 0);}to{-transform: translate3d(' + offsetflow + 'px, 0, 0);}} ';
	 	
                 }else{
                     dmkstyle = '.' + dmkid + '{text-shadow: 1px 1px 1px #000000;left:' + startpoint + 'px;top: ' + topoffset + 'px;color:' + color + ';-webkit-animation:' + dmkid + ' linear 8s;-webkit-animation-iteration-count:1;-webkit-transform-origin:50% 0%;-webkit-backface-visibility:hidden;-webkit-perspective:1000;}@-webkit-keyframes ' + dmkid + '{from{-webkit-transform: translate3d(1px, 0, 0);}to{-webkit-transform: translate3d(' + offsetflow + 'px, 0, 0);}} ';
	 	
                 }
                
                //var ie_hack = '@-moz-keyframes ' + dmkid + '{from{-transform: translate3d(1px, 0, 0);}to{-transform: translate3d(' + offsetflow + 'px, 0, 0);}}';
	 	//dmkstyle = '.' + dmkid + '{text-shadow: 1px 1px 1px #000000;left:' + startpoint + 'px;top: ' + topoffset + 'px;color:' + color + ';-webkit-animation:' + dmkid + ' linear 8s;-webkit-animation-iteration-count:1;-webkit-transform-origin:50% 0%;-webkit-backface-visibility:hidden;-webkit-perspective:1000;}@-webkit-keyframes ' + dmkid + '{from{-webkit-transform: translate3d(1px, 0, 0);}to{-webkit-transform: translate3d(' + offsetflow + 'px, 0, 0);}} ';
	 	//console.log(element.clientWidth);
	 	//element.style.left = startpoint + 'px';
	}
	

	


 	$("style[animation='" + dmkid + "']")[0].innerHTML += dmkstyle;
 	
 	//element.style.display = '';
 	element.className += ' ' + dmkid;

 	//console.log($(".player")[0].paused);
 	if ($(".player")[0].paused) {
 		element.style['-webkit-animation-play-state'] = 'paused';
                element.style['-moz-animation-play-state'] = 'paused';
 	}

 	element.setAttribute('line', selectedline);


        
 	element.addEventListener("webkitAnimationEnd", function(e) {
            
 		e.currentTarget.remove();
 		$("style[animation='" + e.currentTarget.id + "']").remove();
 		console.log('Removed: ' + e.currentTarget.id);
 		if (destroytype == 's') {
 			danmakucache.scroll[e.currentTarget.getAttribute('line') - 1].count--;
 		}
 		else if (destroytype == 'f') {
 			danmakucache.fixed[e.currentTarget.getAttribute('line') - 1].count--;
 		}
 		//console.log('Released Line ' + e.currentTarget.getAttribute('line'));
 	});
        
        element.addEventListener("animationend", function(e) {
            //alert('aaaaaend');
 		e.currentTarget.remove();
 		$("style[animation='" + e.currentTarget.id + "']").remove();
 		console.log('Removed: ' + e.currentTarget.id);
 		if (destroytype == 's') {
 			danmakucache.scroll[e.currentTarget.getAttribute('line') - 1].count--;
 		}
 		else if (destroytype == 'f') {
 			danmakucache.fixed[e.currentTarget.getAttribute('line') - 1].count--;
 		}
 		//console.log('Released Line ' + e.currentTarget.getAttribute('line'));
 	});
 	//console.log('Wrote Line ' + selectedline);
 	
 	//releasedanmaku(selectedline, true);
};
/*
$("video")[0].onprogress = function(e) {
	console.log(e.target.currentTime);
	i = 10;
	while (i <= 450) {
		setTimeout(function() {
			console.log(e.target.currentTime);
		}, i);
		//console.log(i);
		i = i + 20;
	}
}
*/
var danmaku;
var danmakucursor;

danmakucursor = 0;

var loaddanmaku = function(url) {
	
}

//loaddanmaku();

var checkdanmaku = function() {
	if( danmaku.t[danmakucursor] <= Math.round($(".player")[0].currentTime * 10)/10 ) {
		//console.log(danmaku.d[danmakucursor]);
		//console.log('TYPE: ' + danmaku.s[danmakucursor][0]);
		//console.log('TYPE: ' + getdanmakuType(danmaku.s[danmakucursor][0]));
		//console.log('SIZE: ' + danmaku.s[danmakucursor][1] + 'px');
		//console.log('COLOR: #' + parseInt(danmaku.s[danmakucursor][2]).toString(16).toUpperCase());

		danmakutext = danmaku.d[danmakucursor];
		color = '#' + parseInt(danmaku.s[danmakucursor][2]).toString(16).toUpperCase();
		type = getdanmakuType(danmaku.s[danmakucursor][0]);
		size = danmaku.s[danmakucursor][1];

		gendanmaku(danmakutext, type, size, color);
		
		danmakucursor++;
		checkdanmaku();
	}
	else {
		return true;
	}
}


var mycheckdanmaku = function(){
    if( danmaku.t[danmakucursor] <= Math.round($(".player")[0].currentTime * 10)/10 ) {
        
        
        danmakucursor++;
	checkdanmaku();
    }else{
        return true;
    }
}

var danmakucheck;










var danmakucache = new Object();
danmakucache.scroll = new Array();
danmakucache.fixed = new Array();

var videowidth;
var videoheight;

$(function(){
    
});








$(function(){
    
    var ts_comment = new TsComment();
    ts_comment.start();
    
    url = '/pre/danmaku.php?c=1456367';
loaddanmaku(url);

var videocanplay;
videocanplay = false;

var videoelement = $("video")[0];





$("video").on('canplay',function(){
    videocanplay = true;
});


$("video").on('play',function(){
    if (videocanplay == true) {
    console.log('onplay');
    danmakucheck = setInterval(function(){ts_comment.mycheck(ts_comment._time_arr,ts_comment._data)}, 1000);

    
    setTimeout(function() {
      //$(".playindicator span")[0].style.display = 'none';
    }, 450);
    videoelement.style.WebkitFilter = 'blur(0px)';
    $($(".danmakuwrap")[0]).find("div").each(function() {
      this.style['-webkit-animation-play-state'] = 'running';
      this.style['-moz-animation-play-state'] = 'running';
      $(this).css('-moz-animation-play-state','running');
      $(this).css('-animation-play-state','running');
      if (this.style.display == 'none') {
        this.style.display = '';
      }
    });
  }
});

$("video").on('pause',function(){
    clearInterval(danmakucheck);

    //$(".playindicator span")[0].style.display = '';
    //$(".playindicator span")[0].className = 'glyphicon glyphicon-pause';
    //videoelement.style.WebkitFilter = 'blur(2px)';
    $($(".danmakuwrap")[0]).find("div").each(function() {
        //console.log('444444444444444');
      this.style['-webkit-animation-play-state'] = 'paused';
      this.style['-moz-animation-play-state'] = 'paused';
      $(this).css('-moz-animation-play-state','paused');
      $(this).css('-animation-play-state','paused');
    });
});


/*
$("video")[0].onplay = function() {
  if (videocanplay == true) {
    console.log('onplay');
    danmakucheck = setInterval(function(){ts_comment.mycheck(ts_comment._time_arr,ts_comment._data)}, 1000);

    
    setTimeout(function() {
      //$(".playindicator span")[0].style.display = 'none';
    }, 450);
    videoelement.style.WebkitFilter = 'blur(0px)';
    $($(".danmakuwrap")[0]).find("div").each(function() {
      this.style['-webkit-animation-play-state'] = 'running';
      this.style['-moz-animation-play-state'] = 'running';
      $(this).css('-moz-animation-play-state','running');
      $(this).css('-animation-play-state','running');
      if (this.style.display == 'none') {
        this.style.display = '';
      }
    });
  }
};

$("video")[0].onpause = function() {
  if (videocanplay == true) {
    clearInterval(danmakucheck);

    //$(".playindicator span")[0].style.display = '';
    //$(".playindicator span")[0].className = 'glyphicon glyphicon-pause';
    //videoelement.style.WebkitFilter = 'blur(2px)';
    $($(".danmakuwrap")[0]).find("div").each(function() {
        console.log('444444444444444');
      this.style['-webkit-animation-play-state'] = 'paused';
      this.style['-moz-animation-play-state'] = 'paused';
      $(this).css('-moz-animation-play-state','paused');
      $(this).css('-animation-play-state','paused');
    });
  }
};
*/


videowidth = $("video").width();//$("video")[0].clientWidth;
 videoheight = $("video").height();//$("video")[0].clientHeight;


var init = function() {
	danmakulinecount = videoheight / 35;
	i = 1;
	while (i < danmakulinecount) {
		foo = new Object();
		foo.line = i;
		foo.count = 0;
		danmakucache.scroll.push(foo);
    danmakucache.fixed.push(foo);
		i++;
	}
};
init();

    
});