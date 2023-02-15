$(function () {
    /*获取屏幕高度，设置文本最小高度*/
    var $height = $(window).height()-249;

    $(".s-body").css("min-height",$height);
    $("#new-header .nav-list .nav-t").hover(function(){
        $("#new-header .nav-list .nav-t").find(".sj").hide();
        $(this).find(".sj").show();
    },function(){
        $(this).find(".sj").hide();
        $("#new-header .nav-list.current .nav-t .sj").show();
    });

    /*搜索选择*/
    $(".choice-box1").click(function(){
        $(".choice-btn").toggle();
    });
    $(".choice-btn p").click(function () {
        var self = $(this).html();
        $(".choice-btn").hide();
        $(".choice-box1").html(self + '<i class="fa fa-sort-desc"></i>');
    });

    $(".avatar").click(function () {
        layer.closeAll();
        layer.open({
            type: 1,
            title: lang('Login'),
            area: ['350px', '350px'],
            shadeClose: true,
            skin: 'demo-class',
            anim: 0,
            content: $('#login-box'),
            end:function () {
                console.log('i\' close');
                $(".register").hide();
                $(".register").siblings().show();
            }
        });
    });
    /*登录注册的切换*/
    $(".reg-btn").click(function(){
        $(".layui-layer-content").css("height","100%");
        $(this).parent().parent().hide().siblings().show();

        if($(this).text()==lang('Register')){
            $(".layui-layer-title").html(lang('Register'));
            $(".layui-layer").height(450);
        }else{
            $(".layui-layer-title").html(lang('Login'));
            $(".layui-layer").height(350);
        }
    });

    /*鼠标移到图片上出现的旋转效果*/
    $(".mod-col .view-layer").hover(function(){
        $(this).find(".play-bg").show();
        $(this).find("span").addClass("cur");
    },function(){
        $(this).find(".play-bg").hide();
        $(this).find("span").removeClass("cur");
    });
    /*鼠标移到导航上的效果*/
    $(".g-head-center li").hover(function () {
        $(".g-head-center li").find(".menu-level").hide();
        $(this).find(".menu-level").show();
    },function(){
        $(".g-head-center li").find(".menu-level").hide();
    });

    /*注册验证*/
    $("#code").click(function() {
        var phone = $(".phone").val();
        var re = /^1\d{10}$/;
        if(phone == "" || !(re.test(phone))){
            alert(lang('PhoneNull'));
            $(".point-phone").fadeIn();
            setTimeout(function () {
                $(".point-phone").fadeOut();
            }, 800);
        }else{
            var _self = $("#code");
            var countdown = 60;
            var text = _self.text();
            if(countdown == 0){
                $(_self).attr("disabled",false);
                $(_self).html(lang('GetCode')).removeClass("cur");
                countdown = 60;
                return;
            }else{
                $(_self).attr("disabled",true);
                $(_self).html("" + countdown + lang('RetrySend')).addClass("cur");
                countdown--;
            }
        }
    });
    /*鼠标移上登录头像*/
    $(".login-before").hover(function(){
        $(".after-box").show();
    },function(){
        $(".after-box").hide();
    });

    /*头部二维码*/
    $(".u-box.web").hover(function(){
        $(".main-code").show();
    },function(){
        $(".main-code").hide();
    });

    /*点击签到*/
    $(".sign-btn").click(function () {
        $(this).addClass("Completion").css("cursor","not-allowed");
    });
    //用户选择收藏分类
    $('.sele').click(function(){
        $(this).addClass("select");
        $(this).siblings().removeClass('select');
    });
    
    //提交用户收藏的图片和所选择的相册ID
    $('#layui-layer-btn').click(function(){
      var atlas=$(".select").data("atlas");
      var collect_id=$('#collect_id').val();
      var data={atlasid:atlas,collectid:collect_id}
        $.get('/api/collect_atlas.html',data,function(e){
            if(e.resultCode==0){
                layer.msg(e.message,{icon: 1,end:function(){
                    layer.closeAll();
                }});
            }else {
                layer.msg(e.error, {icon: 2});
            }
        },'json');
    });
    /* 我的收藏图片、点击创建相册*/
    /*$(".album-btn a").click(function () {
        layer.prompt({
                title: '添加相册',
            },
            function(val, index){
                if($.trim(val)==null){
                    layer.msg('请输入相册名称！');
                }
                var imgsname={name:val,type:'2'};
                $.get('/api/control_imgs.html',imgsname,function(e){
                    if(e.resultCode==0){
                        layer.msg(e.message, {icon: 1,end:function(){
                            window.location.reload();//刷新当前页面.
                        }});
                    }else {
                        layer.msg(e.error, {icon: 1});
                    }
                    layer.close(index);
                },'json');
            });
    });*/
});

//打赏礼物
//itemid 礼物id ,itemprice 礼物价格，projectid 打赏目标id ,type 类型ID
function reward(itemid,itemprice,projectid,type){
    var requestData={itemid:itemid,projectid:projectid,type:type,itemprice:itemprice};
    layer.confirm(lang('GiftCount3')+itemprice+lang('GiftCount4'), {
        icon: 3,
        btn: [lang('Confirm'),lang('Cancel')] //按钮
    }, function(){
        $.post('/api/reward',requestData,function (e) {
        if(e.resultCode==0){
            $("#count").html(e.data.counts);
            $("#countprice").html(e.data.countprice);
            layer.msg(e.message, {icon: 6});
        }else if(e.resultCode==4005){
            layer.msg(e.error, {icon: 5,end:function(){
                 $("#loginBtn").click();
            }});
        } else {
            layer.msg(e.error, {icon: 5});
        }
        },'json');
    });
}
//资讯扣费
/*function novelpermit(itemprice,id){
    requestData={itemprice:itemprice,id:id,type:3};
    layer.confirm('你需要花费'+itemprice+'金币浏览该文章吗？', {
        icon: 3,
        btn: ['阅读','取消'] //按钮
    }, function(){
        $.post('/api/permit.html',requestData,function (e) {
            if(e.resultCode==0){
                layer.msg(e.message, {icon: 6,end:function(){
                    window.location.reload();//刷新当前页面.
                }});
            }else if(e.resultCode==4005){
                layer.msg(e.error, {icon: 5,end:function(){
                     $("#loginBtn").click();
                }});
            } else {
                layer.msg(e.error, {icon: 5});
            }
        },'json');
    });
}*/
//图片扣费
/*function imagespermit(itemprice,id){
    requestData={itemprice:itemprice,id:id,type:2};
    layer.confirm('你需要花费'+itemprice+'金币阅读该图册吗？', {
        icon: 3,
        btn: ['阅读','取消'] //按钮
    }, function(){
        $.post('/api/permit.html',requestData,function (e) {
            if(e.resultCode==0){
                layer.msg(e.message, {icon: 6,end:function(){
                    window.location.reload();//刷新当前页面.
                }});
            }else if(e.resultCode==4005){
                layer.msg(e.error, {icon: 5,end:function(){
                     $("#loginBtn").click();
                }});
            }else {
                layer.msg(e.error, {icon: 5});
            }
        },'json');
    });
}*/

/*我的收藏、编辑图册*/
/*function atlas_edit(atlasid,imgname){
    layer.prompt({
            title: '编辑图集信息',
            value: imgname
        },
        function(val, index){
            if($.trim(val)==null){
                layer.msg('请输入相册名称！');
            }
            var imgsname={name:val,type:'1',id:atlasid};
            $.get('/api/control_imgs.html',imgsname,function(e){
                if(e.resultCode==0){
                    layer.msg(e.message, {icon: 1,end:function(){
                        window.location.reload();//刷新当前页面.
                    }});
                }else {
                    layer.msg(e.error, {icon: 1});
                }
                layer.close(index);
            },'json');
        });
}*/


//删除收藏 id为project_id,type为删除类型
function delcolect(id,type,colle_id){
    var DelColleData={type:type,id:id,colle_id:colle_id};
    layer.confirm(lang('ConfirmDel'), {
        btn: [lang('Confirm'), lang('Cancel')] //按钮
    }, function () {
        $.get('/api/delcollection.html',DelColleData,function(e){
            if(e.resultCode==4005){
                layer.msg(lang('DelFail')+'，'+e.error,{time:2000,icon:2});
            }else if(e.resultCode==5002){
                layer.msg(lang('DelFail')+'，'+e.error,{time:2000,icon:2});
            }else if(e.resultCode==0){
                layer.msg(e.message,{time:2000,icon:1,end:function(){
                    window.location.reload();//刷新当前页面.
                }})
            }
        },'json');
    });
}

/*收藏*/
function select_atlas(id) {
    $.get('/api/is_login.html',function(e){//检测登录
        if(e.resultCode==0){
            layer.open({
                type: 1,
                title:lang('CollectPto'),
                skin: 'layui-layer-rim', //加上边框
                area: ['420px', 'auto'], //宽高
                content: $('.select-box')
            });
            $(".layui-layer-content").css("height","100%");
            $('#collect_id').val(id);
            $(this).addClass("isSelected");
        }else {
            layer.msg(lang('IsLogin'));
        }
    },'json');
};

/* login function */
function displayLogin(){
    location.href="/index/login.html";
    //layer.closeAll();
    //$('#login-box').removeClass("hide");
}

/* 验证邮箱地址的合法性 */
function isEmail(str){
    var reg=/^[A-Za-z0-9]+([-_.][A-Za-z0-9]+)*@([A-Za-zd]+[-.])+[A-Za-zd]{2,5}$/;
    return reg.test(str);
}

/* PC判断 */
function isPC() {
    var userAgentInfo = navigator.userAgent;
    var Agents = ["Android", "iPhone",
        "SymbianOS", "Windows Phone",
        "iPad", "iPod"];
    var flag = true;
    for (var v = 0; v < Agents.length; v++) {
        if (userAgentInfo.indexOf(Agents[v]) > 0) {
            flag = false;
            break;
        }
    }
    return flag;
}

/* 语言包 */
function lang(lang){
    // 强制转为小写
    var lang = lang.toLowerCase();
    // 读取语言包
    return $('#lang_'+lang).html();
}