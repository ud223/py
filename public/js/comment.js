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
            }
            
        }
        
        this._request('api/comments/hot/get/'+context._pid(),null,function(data){
            context._parse(data);
        });
        if($('#ts_comment_tmp').length === 0){
            var $dom = $(this._tmp);
            $dom.attr('id','ts_comment_tmp');
            $('body').append($dom);
        }
        
        $('#tv-senddanmu').click(function(e){
            if(!ts_user_id){loginTipFunc();e.preventDefault();return false;}
        });
        
        $('#tv-danmubar-auto-box,#tv-danmubar-history').on('click','.opera:not(.selected)',function(){
            if(!ts_user_id){loginTipFunc();}
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
            $('#tv-danmuclosed').show();
            context.stop();
            if(ts_user_id){
                context._config('danmu',ts_user_id+'_'+'no');
            }
            context._config('danmu')
        });
        
        $('#tv-danmuclosed').click(function(){
            $(this).hide();
            $('#tv-danmuopened').show();
            $('#tv-danmu-all').show();
            
            $('#tv-senddanmu').show();
            if(ts_user_id){
                context._config('danmu','');
            }
            context.start();
        });
        
        $('#tv-danmu-all').click(function(){
            $('#tv-danmubar-history').toggle();
            context._history(context._data);
        });
        
        $('#tv-danmubar-history .close').click(function(){
            $('#tv-danmubar-history').hide();
        });
        
        
        $('.submit_comment').click(function(){
            var text = $(this).closest('div').find('input').val();
            if(text === '')return;
            var time = context._time();
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
    _up_arr:{}
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
        
        $('.tv-danmubar-history-body').html('');
        for(var i = 0;i<item.length;i++){
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
    }
};



$(function(){
    var ts_comment = new TsComment();
    ts_comment.start();
});