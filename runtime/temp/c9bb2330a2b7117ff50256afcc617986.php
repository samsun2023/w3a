<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:28:"./tpl/mscms/index/index.html";i:1676400139;s:30:"./tpl/mscms/common/header.html";i:1571122115;s:30:"./tpl/mscms/common/footer.html";i:1675869491;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<?php $menu = getMenu();$register_validate = empty(get_config('register_validate')) ? 0 : get_config('register_validate');?>
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<meta name="renderer" content="webkit">
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<meta name="csrf-param" content="_csrf-frontend">
<title><?php echo (isset($page_title) && ($page_title !== '')?$page_title:""); ?>_<?php echo $seo['site_title']; ?></title>
<meta name="keywords" lang="zh-CN" content="<?php echo $seo['site_keywords']; ?>"/>
<meta name="description" lang="zh-CN" content="<?php echo $seo['site_description']; ?>" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE10" />
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<script type="text/javascript" src="__ROOT__/tpl/mscms/msvodx/js/msvodx.js"></script>
<script>
layui.use(['form', 'layedit', 'laydate'], function(){
});
if(!!window.ActiveXObject || "ActiveXObject" in window){
location.href="<?php echo url('index/remind'); ?>"
}
</script>
<!--[if lt IE 9]>
<script src="__ROOT__/tpl/mscms/msvodx/js/html5.min.js"></script>
<script src="__ROOT__/tpl/mscms/msvodx/js/respond.min.js"></script>
<![endif]-->
</head>
<body>
<?php $controller = lcfirst(request()->controller());$memberInfo = get_member_info();$login_status = check_is_login();?>
<header class="stui-header__top clearfix" id="header-top">
  <div class="container">
    <div class="row">
      <div class="stui-header_bd clearfix">
        <div class="stui-header__logo"> <a class="logo" href="/"></a> </div>
        <div class="stui-header__side">
		<?php $memberInfo = get_member_info();$login_status = check_is_login();?>
          <ul class="stui-header__user">
		  <?php if($login_status['resultCode'] != 1): ?>
           <li style="color:#fff;"><a class="mac_user" href="/index/login.html"><i class="icon iconfont icon-account"></i> ?????? / ??????</a></li>
		   <?php else: ?>
		   <li style="color:#fff;"><a class="mac_user" style="color:#19d8a5;" href="<?php echo url('member/member'); ?>"><img width="30" style="border-radius:10px;"class="logged" src="<?php if(session('member_info')['headimgurl'] != ''): ?><?php echo session('member_info')['headimgurl']; else: ?>/static/images/user_dafault_headimg.jpg<?php endif; ?>" title="?????????"> <?php echo session('member_info')['nickname']; ?> &nbsp;</a><a href="javascript:void(0);" class="user-out" onclick="logout()" title="??????">??????</a></li>
		   <?php endif; ?>
          </ul>
          <div class="stui-header__search">
            <form id="search" name="search" method="POST" action="/search/index/type/video.html" onSubmit="return qrsearch();">
              <input type="text" name="key_word" class="mac_wd form-control" value="" placeholder="??????????????????..."/>
              <button class="submit" type="submit" name="submit"><i class="icon iconfont icon-search"></i></button>
            </form>
          </div>
        </div>
        <ul class="stui-header__menu type-slide">
          <?php if(is_array($menu) || $menu instanceof \think\Collection || $menu instanceof \think\Paginator): $i = 0; $__LIST__ = $menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
          <li <?php if($vo['current'] == 1): ?>class="active" <?php else: endif; ?>><a href="<?php echo $vo['url']; ?>"><?php echo $vo['name']; ?></a></li>
          <?php endforeach; endif; else: echo "" ;endif; ?>

          <li ><a href="/system_pay/recharge.html" target="_blank">??????VIP</a></li>
        </ul>
      </div>
    </div>
  </div>
</header>
<script type="text/javascript">
$(".stui-header__logo .logo").css("background","url(<?php echo $seo['site_logo']; ?>) no-repeat");
</script>
<script type="text/javascript">
var cpa_uid="<?php echo request()->param('uid/d'); ?>"; //cpa
//console.log("????????????Id:<?php echo session('member_id'); ?>");
var disabled = 0;
var register_validate = "<?php echo $register_validate; ?>";
if(register_validate == 1){
var reg_userName = '????????????';
}else if(register_validate == 2){
var reg_userName = '????????????';
}else{
var reg_userName = '?????????';
}
function login() {
var user = $('#userName').val();
var password = $('#password').val();
var verifyCode=$('#verifyCode');
if (user == '' || password == '') {
layer.msg(reg_userName+'?????????????????????.', {icon: 2, anim: 6, time: 1000});
return false;
}
if(verifyCode.val()==''){
layer.msg('?????????????????????.', {icon: 2, anim: 6, time: 1000});
verifyCode.focus();
return false;
}
var url = "<?php echo url('api/login'); ?>";
$.post(url, {userName: user, password: password,verifyCode:verifyCode.val()}, function (data) {

if (data.statusCode == 0) {
layer.msg('????????????', {time: 1000, icon: 1}, function() {
location.reload();
});
} else {
layer.msg(data.error, {icon: 2, anim: 6, time: 1000});
$("#verifyCodeImg").click();
}
}, 'JSON');
}
$(document).keyup(function(event){
if(event.keyCode ==13){
if($(".login").is(":hidden")){
return null;
}else{
login();
}
}
});
function codetTmes() {
var second = $('#register_code').html();
second--;
if(second > 0){
$('#register_code').html(second);
setTimeout("codetTmes()",1000);
}else{
$('#register_code').html('???????????????');
disabled = 0;
}
}
function getCode(){
var user = $('#reg_userName').val();
if(disabled)  return false;
if (user == '') {
$('#reg_userName').focus();
layer.msg(reg_userName+'????????????.', {icon: 2, anim: 6, time: 1000});
return false;
}
var url = "<?php echo url('api/getRegisterCode'); ?>";
$.post(url, {username: user,reg_username:reg_userName}, function (data) {
if (data.statusCode == 0) {
disabled = 1;
layer.msg(data.error, {icon: 1, anim: 6, time: 3000});
$('#register_code').html('60');
codetTmes();
}else{
layer.msg(data.error, {icon: 2, anim: 6, time: 1000});
}
}, 'JSON');
}
function register(){
var user = $('#reg_userName').val();
var password = $('#reg_pwd').val();
var confirm_password=$('#reg_pwd_re').val();
var verifyCode=$('#codes').val();
var nickname = $('#nickname').val();
if (user == '') {
layer.msg(reg_userName+'????????????.', {icon: 2, anim: 6, time: 1000});
return false;
}
if (nickname == '') {
layer.msg('????????????????????????.', {icon: 2, anim: 6, time: 1000});
return false;
}
if (password == '') {
layer.msg('??????????????????.', {icon: 2, anim: 6, time: 1000});
return false;
}
if (password != confirm_password) {
layer.msg('?????????????????????.', {icon: 2, anim: 6, time: 1000});
return false;
}
if(verifyCode==''){
layer.msg('?????????????????????.', {icon: 2, anim: 6, time: 1000});
$('#codes').focus();
return false;
}
var url = "<?php echo url('api/register'); ?>";
$.post(url, {username: user,nickname:nickname, password: password,confirm_password:confirm_password,verifyCode:verifyCode,cpa_uid:cpa_uid}, function (data) {
if (data.statusCode == 0) {
console.log(data);
layer.msg('????????????', {time: 1000, icon: 1}, function() {
location.reload();
});
}else{
layer.msg(data.error, {icon: 2, anim: 6, time: 1000});
}
}, 'JSON');
}
function sign(){
var url = "<?php echo url('api/sign'); ?>";
$.post(url, {}, function (data) {
if (data.resultCode == 0) {
$('.sign-btn').find('var').html('+'+data.data['value']);
$('.sign-btn').addClass("signs");
$('.sign-btn').addClass("Completion");
layer.msg('????????????',  {icon: 1, anim: 6, time: 2000},function () {
$('.sign-btn').removeClass("signs");
});
}else{
layer.msg(data.error, {icon: 2, anim: 6, time: 2000});
}
}, 'JSON');
}
function logout(){
var url="<?php echo url('api/logout'); ?>";
$.post(url,{},function(){
layer.msg('????????????', {time: 1000, icon: 1}, function() {
location.reload();
});
},'JSON');
}
//$.post("",{userName:})
$(function () {
$(".select-loginType .account-login").click(function(){
$(this).parent().hide().siblings().show();
});
$(".return-qqWechat").click(function () {
$(this).parent().parent().hide().siblings().show();
});
});
</script>
<script>
setInterval(function(){
doItPerSecond();
},4000);

function doItPerSecond() {
var self = document.getElementsByClassName("fn-huangguan");

}
/*????????????*/
function showNotice(content){
$('.tips-box').html(content);
layer.open({
type: 1,
title: '??????',
area: ['500px', 'auto'],
shadeClose: false,
skin: 'demo-class',
anim: 0,
content: $('.tips-box')
});
}
</script>
<br>
<br>
<br>
<br>
<br>
<div class="container">
<div class="row">
<div class="stui-pannel stui-pannel-bg clearfix" >
  <div class="stui-pannel-box ">
    <div class="stui-pannel_bd">
      <div class="col-pd clearfix"> <?php if(!(empty($notice) || (($notice instanceof \think\Collection || $notice instanceof \think\Paginator ) && $notice->isEmpty()))): ?>
        <div class="copyright-tips">
          <div class="layout-cont" style="font-size:15px"> <span style="color:red;"><i class="fa fa-volume-up"></i>&nbsp;&nbsp;&nbsp;<b>?????????</b></span>
            <ul>
              <?php if(is_array($notice) || $notice instanceof \think\Collection || $notice instanceof \think\Paginator): $i = 0; $__LIST__ = $notice;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
              <li><a <?php if($v['type'] == 1): ?> onClick="showNotice('<?php echo $v['content']; ?>')" <?php else: ?> href="<?php echo $v['url']; ?>" target='_bank' <?php endif; ?> > <?php echo $i; ?>???<?php echo $v['title']; ?></a></li>
              <?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
          </div>
        </div>
        <?php endif; ?>
        <div class="tips-box">
          <div class="tips-bg"></div>
          <div class="tipsBox">
            <p>??????</p>
            <div class="tips"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="stui-pannel stui-pannel-bg clearfix">
  <div class="stui-pannel-box clearfix">
    <div class="stui-pannel_bd clearfix">
      <div class="col-lg-wide-6 col-xs-1 padding-0">
        <div class="carousel carousel_default col-pd"> <?php if(is_array($seo['banner']) || $seo['banner'] instanceof \think\Collection || $seo['banner'] instanceof \think\Paginator): $i = 0; $__LIST__ = $seo['banner'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
          <div class="wide"> <a href="<?php echo $v['url']; ?>"><img src="<?php echo $v['images_url']; ?>" width="1145" height="480"/></a> </div>
          <?php endforeach; endif; else: echo "" ;endif; ?> </div>
      </div>
    </div>
  </div>
</div>
<div class="stui-pannel clearfix">
  <div class="stui-pannel-box padding-0">
    <div class="col-lg-wide-75 col-xs-1 padding-0">
      <div class="col-pd stui-pannel-bg">
        <div class="stui-pannel_hd">
          <div class="stui-pannel__head clearfix"> <a class="more text-muted pull-right" href="<?php echo url('video/lists',array('orderCode'=>'new')); ?>">?????? <i class="icon iconfont icon-more"></i></a>
            <h3 class="title"> <img src="__ROOT__/tpl/mscms/msvodx/picture/icon_1.png"/> <a href="<?php echo url('video/lists',array('orderCode'=>'new')); ?>">????????????</a> </h3>
          </div>
        </div>
        <div class="stui-pannel_bd clearfix">
          <ul class="stui-vodlist clearfix">
            <?php if(is_array($new_list) || $new_list instanceof \think\Collection || $new_list instanceof \think\Paginator): $i = 0; $__LIST__ = $new_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
            <li class="col-md-5 col-sm-4 col-xs-3 ">
              <div class="stui-vodlist__box"> <a class="stui-vodlist__thumb lazyload" href="<?php echo url('video/play',array('id'=>$vo['id'])); ?>" title="<?php echo $vo['title']; ?>" data-original="<?php echo $vo['thumbnail']; ?>" style="border-radius:15px;background: url(<?php echo $vo['thumbnail']; ?>) no-repeat; background-position:50% 50%; background-size: cover;"> <span class="play hidden-xs"></span> <span class="pic-text text-right"><?php echo $vo['play_time']; ?></span> </a>
                <div class="stui-vodlist__detail">
                  <h4 class="title text-overflow"><a href="<?php echo url('video/play',array('id'=>$vo['id'])); ?>"  title="<?php echo $vo['title']; ?>"><?php echo $vo['title']; ?></a> </h4>
                  <p class="text text-overflow text-muted hidden-xs">20<?php echo date("y-m-d",$vo['update_time']); ?> <span style="margin-left:25px;"> <i class="fa fa-dot-circle-o fa-lg"></i> <?php echo $vo['gold']; ?></span> <span style="float:right;"><i class="fa fa-eye fa-lg"></i> <?php echo $vo['click']; ?></span></p>
                </div>
              </div>
            </li>
            <?php endforeach; endif; else: echo "" ;endif; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<!--
<div class="stui-pannel stui-pannel-bg clearfix">
  <div class="stui-pannel-box">
    <div class="col-pd"> <a class="hidden-xs" target="_blank" href="http://www.msvod.cc/"> <img class="img-responsive" src="/XResource/20190919/izDst2ihXD6E6xEmBMFikXAz8d3rFNpf.png"  style="margin:0 auto; width:100%;border-radius:10px;"/> </a> </div>
  </div>
</div>
-->
<div class="stui-pannel clearfix">
  <div class="stui-pannel-box padding-0">
    <div class="col-lg-wide-75 col-xs-1 padding-0">
      <div class="col-pd stui-pannel-bg">
        <div class="stui-pannel_hd">
          <div class="stui-pannel__head clearfix"> <a class="more text-muted pull-right" href="<?php echo url('video/lists',array('orderCode'=>'new')); ?>">?????? <i class="icon iconfont icon-more"></i></a>
            <h3 class="title"> <img src="__ROOT__/tpl/mscms/msvodx/picture/icon_1.png"/> <a href="<?php echo url('video/lists',array('orderCode'=>'new')); ?>">????????????</a> </h3>
          </div>
        </div>
        <div class="stui-pannel_bd clearfix">
          <ul class="stui-vodlist clearfix">
            <?php
           $params = array(
           'type' => 'video',
           'limit'=>10,);
           $new_list = get_recom_data($params);
            if(is_array($new_list) || $new_list instanceof \think\Collection || $new_list instanceof \think\Paginator): $i = 0; $__LIST__ = $new_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
            <li class="col-md-5 col-sm-4 col-xs-3 ">
              <div class="stui-vodlist__box"> <a class="stui-vodlist__thumb lazyload" href="<?php echo url('video/play',array('id'=>$vo['id'])); ?>" title="<?php echo $vo['title']; ?>" data-original="<?php echo $vo['thumbnail']; ?>" style="border-radius:15px;background: url(<?php echo $vo['thumbnail']; ?>) no-repeat; background-position:50% 50%; background-size: cover;"> <span class="play hidden-xs"></span> <span class="pic-text text-right"><?php echo $vo['play_time']; ?></span> </a>
                <div class="stui-vodlist__detail">
                  <h4 class="title text-overflow"><a href="<?php echo url('video/play',array('id'=>$vo['id'])); ?>"  title="<?php echo $vo['title']; ?>"><?php echo $vo['title']; ?></a> </h4>
                  <p class="text text-overflow text-muted hidden-xs">20<?php echo date("y-m-d",$vo['update_time']); ?> <span style="margin-left:25px;"> <i class="fa fa-dot-circle-o fa-lg"></i> <?php echo $vo['gold']; ?></span> <span style="float:right;"><i class="fa fa-eye fa-lg"></i> <?php echo $vo['click']; ?></span></p>
                </div>
              </div>
            </li>
            <?php endforeach; endif; else: echo "" ;endif; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<!--
<div class="stui-pannel stui-pannel-bg clearfix">
  <div class="stui-pannel-box">
    <div class="col-pd"> <a class="hidden-xs" target="_blank" href="http://www.msvod.cc/"> <img class="img-responsive" src="/XResource/20190919/izDst2ihXD6E6xEmBMFikXAz8d3rFNpf.png"  style="margin:0 auto; width:100%;border-radius:10px;"/> </a> </div>
  </div>
</div>
-->
<div class="stui-pannel clearfix">
  <div class="stui-pannel-box padding-0">
    <div class="col-lg-wide-75 col-xs-1 padding-0">
      <div class="col-pd stui-pannel-bg">
        <div class="stui-pannel_hd">
          <div class="stui-pannel__head clearfix"> <a class="more text-muted pull-right" href="<?php echo url('video/lists',array('orderCode'=>'reco')); ?>">?????? <i class="icon iconfont icon-more"></i></a>
            <h3 class="title"> <img src="__ROOT__/tpl/mscms/msvodx/picture/icon_1.png"/> <a href="<?php echo url('video/lists',array('orderCode'=>'reco')); ?>">????????????</a> </h3>
          </div>
        </div>
        <div class="stui-pannel_bd clearfix">
          <ul class="stui-vodlist clearfix">
            <?php if(is_array($recom_list) || $recom_list instanceof \think\Collection || $recom_list instanceof \think\Paginator): $i = 0; $__LIST__ = $recom_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
            <li class="col-md-5 col-sm-4 col-xs-3 ">
              <div class="stui-vodlist__box"> <a class="stui-vodlist__thumb lazyload" href="<?php echo url('video/play',array('id'=>$vo['id'])); ?>" title="<?php echo $vo['title']; ?>" data-original="<?php echo $vo['thumbnail']; ?>" style="border-radius:15px;background: url(<?php echo $vo['thumbnail']; ?>) no-repeat; background-position:50% 50%; background-size: cover;"> <span class="play hidden-xs"></span> <span class="pic-text text-right"><?php echo $vo['play_time']; ?></span> </a>
                <div class="stui-vodlist__detail">
                  <h4 class="title text-overflow"><a href="<?php echo url('video/play',array('id'=>$vo['id'])); ?>"  title="<?php echo $vo['title']; ?>"><?php echo $vo['title']; ?></a> </h4>
                  <p class="text text-overflow text-muted hidden-xs">20<?php echo date("y-m-d",$vo['update_time']); ?> <span style="margin-left:25px;"> <i class="fa fa-dot-circle-o fa-lg"></i> <?php echo $vo['gold']; ?></span> <span style="float:right;"><i class="fa fa-eye fa-lg"></i> <?php echo $vo['click']; ?></span></p>
                </div>
              </div>
            </li>
            <?php endforeach; endif; else: echo "" ;endif; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<!--
<div class="stui-pannel stui-pannel-bg clearfix">
  <div class="stui-pannel-box">
    <div class="col-pd"> <a class="hidden-xs" target="_blank" href="http://www.msvod.cc/"> <img class="img-responsive" src="/XResource/20190919/izDst2ihXD6E6xEmBMFikXAz8d3rFNpf.png"  style="margin:0 auto; width:100%;border-radius:10px;"/> </a> </div>
  </div>
</div>
-->
<div class="stui-pannel clearfix">
  <div class="stui-pannel-box padding-0">
    <div class="col-lg-wide-75 col-xs-1 padding-0">
      <div class="col-pd stui-pannel-bg">
        <div class="stui-pannel_hd">
          <div class="stui-pannel__head clearfix"> <a class="more text-muted pull-right" href="<?php echo url('video/lists',array('orderCode'=>'hot')); ?>">?????? <i class="icon iconfont icon-more"></i></a>
            <h3 class="title"> <img src="__ROOT__/tpl/mscms/msvodx/picture/icon_1.png"/> <a href="<?php echo url('video/lists',array('orderCode'=>'hot')); ?>">????????????</a> </h3>
          </div>
        </div>
        <div class="stui-pannel_bd clearfix">
          <ul class="stui-vodlist clearfix">
            <?php if(is_array($hot_list) || $hot_list instanceof \think\Collection || $hot_list instanceof \think\Paginator): $i = 0; $__LIST__ = $hot_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
            <li class="col-md-5 col-sm-4 col-xs-3 ">
              <div class="stui-vodlist__box"> <a class="stui-vodlist__thumb lazyload" href="<?php echo url('video/play',array('id'=>$vo['id'])); ?>" title="<?php echo $vo['title']; ?>" data-original="<?php echo $vo['thumbnail']; ?>" style="border-radius:15px;background: url(<?php echo $vo['thumbnail']; ?>) no-repeat; background-position:50% 50%; background-size: cover;"> <span class="play hidden-xs"></span> <span class="pic-text text-right"><?php echo $vo['play_time']; ?></span> </a>
                <div class="stui-vodlist__detail">
                  <h4 class="title text-overflow"><a href="<?php echo url('video/play',array('id'=>$vo['id'])); ?>"  title="<?php echo $vo['title']; ?>"><?php echo $vo['title']; ?></a> </h4>
                  <p class="text text-overflow text-muted hidden-xs">20<?php echo date("y-m-d",$vo['update_time']); ?> <span style="margin-left:25px;"> <i class="fa fa-dot-circle-o fa-lg"></i> <?php echo $vo['gold']; ?></span> <span style="float:right;"><i class="fa fa-eye fa-lg"></i> <?php echo $vo['click']; ?></span></p>
                </div>
              </div>
            </li>
            <?php endforeach; endif; else: echo "" ;endif; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<?php 
$baseConfig = get_config_by_group('base');
$baseConfig['friend_link'] =  empty($seo['friend_link']) ? $baseConfig['friend_link'] : $seo['friend_link'];
$baseConfig['site_icp'] = empty($seo['site_icp']) ? $baseConfig['site_icp'] : $seo['site_icp'];
$baseConfig['site_statis'] = empty($seo['site_statis']) ? $baseConfig['site_statis'] : $seo['site_statis'];
$linkList=get_friend_link($baseConfig);
 ?>
<div class="stui-pannel stui-pannel-bg hidden-sm hidden-xs clearfix">
  <div class="stui-pannel-box">
    <div class="stui-pannel_hd">
      <div class="stui-pannel__head clearfix">
        <h3 class="title"> <img src="__ROOT__/tpl/mscms/msvodx/picture/icon_26.png"/> ???????????? </h3>
      </div>
    </div>
    <div class="stui-pannel_bd clearfix">
      <div class="col-xs-1">
        <ul class="stui-link__text clearfix">
          <?php if(is_array($linkList) || $linkList instanceof \think\Collection || $linkList instanceof \think\Paginator): $i = 0; $__LIST__ = $linkList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$link): $mod = ($i % 2 );++$i;?>
          <li><a class="text-muted" href="<?php echo $link['url']; ?>" title="<?php echo $link['name']; ?>" target="_blank"><?php echo $link['name']; ?></a></li>
          <?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
      </div>
    </div>
  </div>
</div>
<!--
<div class="stui-pannel stui-pannel-bg clearfix">
  <div class="stui-pannel-box">
    <div class="col-pd"> <a class="hidden-xs" target="_blank" href="http://www.msvod.cc/"> <img class="img-responsive" src="/XResource/20190919/izDst2ihXD6E6xEmBMFikXAz8d3rFNpf.png"  style="margin:0 auto; width:100%;border-radius:10px;"/> </a> </div>
  </div>
</div>
-->
 <div class="container">
  <div class="row">
    <div class="stui-foot clearfix">
      <div class="col-pd text-center hidden-xs" style="line-height:25px;"> ??????????????????QQ???xxxxxxx ???????????????<a href="mailto:{maccms:email}">admin@w3a.one</a><br>
        Copyright <span class="fontArial"> </span> 2021-2023 www.w3a.one. All Rights Reserved. <a href="https://www.w3a.one" target="_blank">[w3a.one]</a><br>
        ??????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????? </div>
    </div>
  </div>
</div>
</body>
</html>
