<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:28:"./tpl/mscms/video/lists.html";i:1675872999;s:30:"./tpl/mscms/common/header.html";i:1571122115;s:30:"./tpl/mscms/common/footer.html";i:1675869491;}*/ ?>
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
           <li style="color:#fff;"><a class="mac_user" href="/index/login.html"><i class="icon iconfont icon-account"></i> 登录 / 注册</a></li>
		   <?php else: ?>
		   <li style="color:#fff;"><a class="mac_user" style="color:#19d8a5;" href="<?php echo url('member/member'); ?>"><img width="30" style="border-radius:10px;"class="logged" src="<?php if(session('member_info')['headimgurl'] != ''): ?><?php echo session('member_info')['headimgurl']; else: ?>/static/images/user_dafault_headimg.jpg<?php endif; ?>" title="已登录"> <?php echo session('member_info')['nickname']; ?> &nbsp;</a><a href="javascript:void(0);" class="user-out" onclick="logout()" title="退出">退出</a></li>
		   <?php endif; ?>
          </ul>
          <div class="stui-header__search">
            <form id="search" name="search" method="POST" action="/search/index/type/video.html" onSubmit="return qrsearch();">
              <input type="text" name="key_word" class="mac_wd form-control" value="" placeholder="请输入关键词..."/>
              <button class="submit" type="submit" name="submit"><i class="icon iconfont icon-search"></i></button>
            </form>
          </div>
        </div>
        <ul class="stui-header__menu type-slide">
          <?php if(is_array($menu) || $menu instanceof \think\Collection || $menu instanceof \think\Paginator): $i = 0; $__LIST__ = $menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
          <li <?php if($vo['current'] == 1): ?>class="active" <?php else: endif; ?>><a href="<?php echo $vo['url']; ?>"><?php echo $vo['name']; ?></a></li>
          <?php endforeach; endif; else: echo "" ;endif; ?>

          <li ><a href="/system_pay/recharge.html" target="_blank">加入VIP</a></li>
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
//console.log("当前用户Id:<?php echo session('member_id'); ?>");
var disabled = 0;
var register_validate = "<?php echo $register_validate; ?>";
if(register_validate == 1){
var reg_userName = '邮箱地址';
}else if(register_validate == 2){
var reg_userName = '手机号码';
}else{
var reg_userName = '用户名';
}
function login() {
var user = $('#userName').val();
var password = $('#password').val();
var verifyCode=$('#verifyCode');
if (user == '' || password == '') {
layer.msg(reg_userName+'或密码不能为空.', {icon: 2, anim: 6, time: 1000});
return false;
}
if(verifyCode.val()==''){
layer.msg('验证码不能为空.', {icon: 2, anim: 6, time: 1000});
verifyCode.focus();
return false;
}
var url = "<?php echo url('api/login'); ?>";
$.post(url, {userName: user, password: password,verifyCode:verifyCode.val()}, function (data) {

if (data.statusCode == 0) {
layer.msg('登陆成功', {time: 1000, icon: 1}, function() {
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
$('#register_code').html('获取验证码');
disabled = 0;
}
}
function getCode(){
var user = $('#reg_userName').val();
if(disabled)  return false;
if (user == '') {
$('#reg_userName').focus();
layer.msg(reg_userName+'不能为空.', {icon: 2, anim: 6, time: 1000});
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
layer.msg(reg_userName+'不能为空.', {icon: 2, anim: 6, time: 1000});
return false;
}
if (nickname == '') {
layer.msg('用户昵称不能为空.', {icon: 2, anim: 6, time: 1000});
return false;
}
if (password == '') {
layer.msg('密码不能为空.', {icon: 2, anim: 6, time: 1000});
return false;
}
if (password != confirm_password) {
layer.msg('两次密码不一致.', {icon: 2, anim: 6, time: 1000});
return false;
}
if(verifyCode==''){
layer.msg('验证码不能为空.', {icon: 2, anim: 6, time: 1000});
$('#codes').focus();
return false;
}
var url = "<?php echo url('api/register'); ?>";
$.post(url, {username: user,nickname:nickname, password: password,confirm_password:confirm_password,verifyCode:verifyCode,cpa_uid:cpa_uid}, function (data) {
if (data.statusCode == 0) {
console.log(data);
layer.msg('注册成功', {time: 1000, icon: 1}, function() {
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
layer.msg('签到成功',  {icon: 1, anim: 6, time: 2000},function () {
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
layer.msg('退出成功', {time: 1000, icon: 1}, function() {
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
<br>
<br>
<br>
<br>
<br>
<div class="container">
<div class="row">
<div class="stui-pannel stui-pannel-bg clearfix">
  <div class="stui-pannel_hd">
    <ul class="stui-screen__list type-slide bottom-line-dot clearfix" id="class_box">
      <li> <span class="text-muted">按分类</span> </li>
      <li class="<?php if(empty($cid) || (($cid instanceof \think\Collection || $cid instanceof \think\Paginator ) && $cid->isEmpty())): ?>swiper-slide active<?php endif; ?>"><a href="#" data="0">全部</a></li>
      <?php if(is_array($class_list) || $class_list instanceof \think\Collection || $class_list instanceof \think\Paginator): $i = 0; $__LIST__ = $class_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
      <li class="<?php if($cid == $vo['id']): ?>swiper-slide active<?php endif; ?>"><a data="<?php echo $vo['id']; ?>" href="#"><?php echo $vo['name']; ?></a></li>
      <?php endforeach; endif; else: echo "" ;endif; ?>
    </ul>
    <?php if(!(empty($class_sublist) || (($class_sublist instanceof \think\Collection || $class_sublist instanceof \think\Paginator ) && $class_sublist->isEmpty()))): ?>
    <ul class="stui-screen__list type-slide bottom-line-dot clearfix">
      <li> <span class="text-muted">子分类</span> </li>
      <li class="<?php if(empty($cid) || (($cid instanceof \think\Collection || $cid instanceof \think\Paginator ) && $cid->isEmpty())): ?>swiper-slide active<?php endif; ?>"><a href="#" data="0">全部</a></li>
      <?php if(is_array($class_sublist) || $class_sublist instanceof \think\Collection || $class_sublist instanceof \think\Paginator): $i = 0; $__LIST__ = $class_sublist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
      <li class="<?php if($cid == $vo['id']): ?>swiper-slide active<?php endif; ?>"><a data="<?php echo $vo['id']; ?>" href="#"><?php echo $vo['name']; ?></a></li>
      <?php endforeach; endif; else: echo "" ;endif; ?>
    </ul>
    <?php endif; ?>
    <ul class="stui-screen__list type-slide bottom-line-dot clearfix" id="tag_box">
      <li> <span class="text-muted">按标签</span> </li>
      <li class="<?php if(empty($tag_id) || (($tag_id instanceof \think\Collection || $tag_id instanceof \think\Paginator ) && $tag_id->isEmpty())): ?>swiper-slide active<?php endif; ?>"><a href="#" data="0">全部</a></li>
      <?php if(is_array($tag_list) || $tag_list instanceof \think\Collection || $tag_list instanceof \think\Paginator): $i = 0; $__LIST__ = $tag_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
      <li <?php if($tag_id == $vo['id']): ?>class="swiper-slide active"<?php endif; ?>><a data="<?php echo $vo['id']; ?>" href="#"><?php echo $vo['name']; ?></a></li>
      <?php endforeach; endif; else: echo "" ;endif; ?>
    </ul>
    <?php if($area_list): ?>
    <ul class="stui-screen__list type-slide bottom-line-dot clearfix" id="area_box">
      <li> <span class="text-muted">按地区</span> </li>
      <li class="<?php if(empty($area_id) || (($area_id instanceof \think\Collection || $area_id instanceof \think\Paginator ) && $area_id->isEmpty())): ?>swiper-slide active<?php endif; ?>"><a href="#" data="0">全部</a></li>
      <?php if(is_array($area_list) || $area_list instanceof \think\Collection || $area_list instanceof \think\Paginator): $i = 0; $__LIST__ = $area_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
      <li <?php if($area_id == $vo['id']): ?>class="swiper-slide active"<?php endif; ?>><a data="<?php echo $vo['id']; ?>" href="javascript:;"><?php echo $vo['name']; ?></a></li>
      <?php endforeach; endif; else: echo "" ;endif; ?>
    </ul>
    <?php endif; ?> 
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
          <div class="stui-pannel__head clearfix"> <a class="more text-muted pull-right" href="/video/lists.html?orderCode=lastTime&tag_id=0&sub_cid=0&cid=0&area_id=0"  style="margin-left:10px;">推荐</a> <a class="more text-muted pull-right" href="/video/lists.html?orderCode=hot&tag_id=0&sub_cid=0&cid=0&area_id=0" style="margin-left:10px;">热门</a> <a class="more text-muted pull-right" href="/video/lists.html?orderCode=reco&tag_id=9&sub_cid=0&cid=2&area_id=0" style="margin-left:10px;">最新</a> <a class="more text-muted pull-right" href="/video/lists.html?orderCode=lastTime&tag_id=0&sub_cid=0&cid=0&area_id=0"  style="margin-left:10px;">全部</a>
            <h3 class="title"> <img src="__ROOT__/tpl/mscms/msvodx/picture/icon_1.png"/> <a href="#">课程列表</a> </h3>
          </div>
        </div>
        <div class="stui-pannel_bd clearfix"> 
		 <?php if(!(empty($video_list) || (($video_list instanceof \think\Collection || $video_list instanceof \think\Paginator ) && $video_list->isEmpty()))): ?>
          <ul class="stui-vodlist clearfix">
            <?php if(is_array($video_list) || $video_list instanceof \think\Collection || $video_list instanceof \think\Paginator): $i = 0; $__LIST__ = $video_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
            <li class="col-md-5 col-sm-4 col-xs-3 ">
              <div class="stui-vodlist__box"> <a class="stui-vodlist__thumb lazyload" href="<?php echo url('video/play',array('id'=>$vo['id'])); ?>" title="<?php echo $vo['title']; ?>" data-original="<?php echo $vo['thumbnail']; ?>" style="border-radius:15px;background: url(<?php echo $vo['thumbnail']; ?>) no-repeat; background-position:50% 50%; background-size: cover;"> <span class="play hidden-xs"></span> <span class="pic-text text-right"><?php echo $vo['play_time']; ?></span> </a>
                <div class="stui-vodlist__detail">
                  <h4 class="title text-overflow"><a href="<?php echo url('video/play',array('id'=>$vo['id'])); ?>"  title="<?php echo $vo['title']; ?>"><?php echo $vo['title']; ?></a> </h4>
                  <p class="text text-overflow text-muted hidden-xs">20<?php echo date("y-m-d",$vo['update_time']); ?> <span style="margin-left:25px;"> <i class="fa fa-dot-circle-o fa-lg"></i> <?php echo $vo['gold']; ?></span> <span style="float:right;"><i class="fa fa-eye fa-lg"></i> <?php echo $vo['click']; ?></span></p>
                </div>
              </div>
            </li>
            <?php endforeach; endif; else: echo "" ;endif; else: ?>
            <div align="center" class="not-data" style="height:50px;color:red;">暂时没有数据 ~</div>
          </ul>
          <?php endif; ?> </div>
      </div>
    </div>
  </div>
</div>
<form action="" method="get" id="forms">
<input type="hidden" id="current_orderCodes"  name="orderCode" value="<?php echo (isset($orderCode) && ($orderCode !== '')?$orderCode:'0'); ?>" >
<input type="hidden" id="current_tag_id" name="tag_id"  value="<?php echo (isset($tag_id) && ($tag_id !== '')?$tag_id:'0'); ?>" >
<input type="hidden" id="current_sub_cid" name="sub_cid"  value="<?php echo (isset($sub_cid) && ($sub_cid !== '')?$sub_cid:'0'); ?>" >
<input type="hidden" id="current_cid" name="cid"  value="<?php echo (isset($cid) && ($cid !== '')?$cid:'0'); ?>" >
<input type="hidden" id="current_area_id" name="area_id" value="<?php echo (isset($area_id) && ($area_id !== '')?$area_id:'0'); ?>" >
</form>
<ul class="stui-page text-center cleafix">
<?php echo $pages; ?>
</ul>
<script type="text/javascript">
$(function () {
$('#orderCode').change(function(){
$('#current_orderCodes').val($(this).val());
$('#forms').submit();
})
$('#sub_box').find('a').click(function(){
var sub = $(this).attr('data');
$('#current_sub_cid').val(sub);
$('#forms').submit();
})
$('#class_box').find('a').click(function(){
var cid = $(this).attr('data');
$('#current_cid').val(cid);
$('#current_sub_cid').val(0);
$('#forms').submit();
})
$('#tag_box').find('a').click(function(){
var tag_id = $(this).attr('data');
$('#current_tag_id').val(tag_id);
$('#forms').submit();
})
$('#area_box').find('a').click(function(){
var area_id = $(this).attr('data');
$('#current_area_id').val(area_id);
$('#forms').submit();
})
$(".sort-pack").hover(function(){
$(this).find(".play-bg").show();
$(this).find("span").addClass("cur");
},function(){
$(this).find(".play-bg").hide();
$(this).find("span").removeClass("cur");
});
});
</script>
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
      <div class="col-pd text-center hidden-xs" style="line-height:25px;"> 网站合作联系QQ：xxxxxxx 联系邮箱：<a href="mailto:{maccms:email}">admin@w3a.one</a><br>
        Copyright <span class="fontArial"> </span> 2021-2023 www.w3a.one. All Rights Reserved. <a href="https://www.w3a.one" target="_blank">[w3a.one]</a><br>
        网站所有内容均来源于互联网相关站点自动搜索采集信息，相关链接已经注明来源，如有侵权请留言！ </div>
    </div>
  </div>
</div>
</body>
</html>