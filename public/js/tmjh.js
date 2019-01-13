/*
* @Author: Tumujinhua
* @Date:   2016-11-04 09:28:53
* @Last Modified time: 2016-11-11 11:53:42
* @Third-party plug-in : layer.js  jquery.form.js
*/

/*ajax post 普通表单提交*/
$(function(){
    $('#ajax-post').click(function(){
        var target,post_data;
        var obj = $('form');
        var that = this;
        target = obj.attr('action');
        post_data = obj.serialize();
        $.ajax({type: 'POST',url: target ,data:post_data,dataType:'json',
            success:function(data){
                if (data.status == 1)
                {
                    if (data.url) {
                        window.location.href=data.url;
                    }else{
                        window.location.reload();
                    }
                }else{
                    layer.msg(data.tips);
                }
            }
        });
    return false;
    });
});

/*表单+文件上传 ajax post提交*/
$(function(){
    $("#sync-btn").click(function(){
        var target = $('form').attr('action');
        $("#with-img-upload").ajaxSubmit({dataType:'json',type:'post',url:target,
            success:function(data){
                if (data.status == 1)
                {
                    if (data.url) {
                        window.location.href=data.url;
                    }else{
                        window.location.reload();
                    }
                }else{
                    layer.msg(data.tips);
                }
            }
        });
    return false;
    });
});

/*ajax get 删除表格记录*/
$(function(){
    $('.ajax-get').click(function(){
        var target;
        var that = this;
        if ( $(this).hasClass('confirm') )
        {
            if(!confirm('确认要执行该操作吗?'))
            {
                return false;
            }
        }
        if ( (target = $(this).attr('href')) || (target = $(this).attr('url')) )
        {
            $.ajax({type: 'GET',url: target ,dataType:'json',
                success: function(data){
                    if (data.status == 1)
                    {
                        $(that).parent().parent().remove();
                    }else{
                        layer.msg('删除失败');
                    }
                }
            });
        }
    return false;
    });
});


/**
 * 导航高亮
 * @param {[string]} actuelurl [Index/index]
 */
function NavigateLight(needUrl)
{
  var trueUrl;
  var currentUrl = window.location.href;
  if (needUrl)
  {
    //var url =  currentUrl.replace(/(.*)\/{1}.*/, '$1');
    var url =  currentUrl.replace( /([^\/]*\/\/[^\/]*\/).*/g , '$1' );
    trueUrl = url+needUrl;
  }else{
    trueUrl = currentUrl;
  }
  $("a[href='"+trueUrl+"']").parent().addClass('active');
  $("a[href='"+trueUrl+"']").parent().parent().parent().addClass('active');
}

/*点击返回*/
$(function(){
    $('#return_back').click(function(){
        window.history.go(-1);
        return false;
    })
})
