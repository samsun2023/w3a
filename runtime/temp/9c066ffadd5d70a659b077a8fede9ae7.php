<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:34:"./tpl/mscms/systemMsg/message.html";i:1569479814;s:27:"./tpl/mscms/common/top.html";i:1571122134;s:30:"./tpl/mscms/common/bottom.html";i:1675869473;}*/ ?>
<?php if(request()->param('simple')!=1): ?><!DOCTYPE html>
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
<script type="text/javascript" src="__ROOT__/tpl/mscms/msvodx/js/user.js"></script>
<link href="__ROOT__/tpl/mscms/msvodx/css/user_common.css" rel="stylesheet">
<link rel="stylesheet" href="__ROOT__/tpl/mscms/msvodx/css/iconfont.css">
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
</script><?php endif; ?>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<link href="/tpl/mscms/msvodx/awesome/css/font-awesome.css" rel="stylesheet">
<script src="/tpl/mscms/msvodx/js/msvod.js"></script>
<style>
    html{background: #f1f2f3;}
    .s-body{min-height:auto !important;}
    .error-box{display: block;margin:90px auto 0;background: #fff;padding: 50px 0;width: 1150px;border-radius:20px;margin-bottom:50px;}
    .prompt,.error,.success{text-align: center;}
    .prompt .error-img i,.error .error-img i,.success .error-img i{font-size: 65px;width: auto;}
    .error-box a{width: 80px;height: 35px;line-height: 35px;margin: 10px auto;border-radius: 25px;text-decoration: none;}
    .error-box p{margin: 20px 0 7px;font-size: 18px;}
    .error-box span{color: #afafaf;}
    .error-box var{font-style: normal;font-weight: bold;}
    .error-box .error-img i,.error-box p,.error-box a{display: block;text-align: center;}

    .prompt .error-img i,.prompt p,.prompt a{color:#1296DB;}
    .prompt span{color: #afafaf;}
    .prompt a{border: 1px solid #1296DB;}

    .error .error-img i,.error p,.error a{color:#D81E06;}
    .error a{border: 1px solid #D81E06;}

    .success .error-img i,.success p,.success a{color:#4CBD27;}
    .success a{border: 1px solid #4CBD27;}
    @media screen and (max-width: 1400px) {
        .error-box {width: 980px !important;}
    }
    @media screen and (max-width: 768px) {
        .error-box {width: auto !important;}
    }

</style>
<script>
    $(function(){
        var $height = $(window).height();
        console.info($height);
        $(".error-box").css("padding-top",$height/6);
        $(".error-box").css("height",$height/2);
        $(".error-box").css("padding-bottom",$height/6.4)
    });
</script>
<div class="s-body">
    <div class="error-box">
        <?php switch($code) {
            case 3:
        ?>
        <!--提示-->
        <div class="prompt" style="">
            <div class="error-img"><i class="fa-exclamation-triangle" style="color:#FF6600;"></i></div>
            <div class="error-info">
                <p><?php echo(strip_tags($msg));?></p>
                <span>自动跳转，时间：<var><b id="wait"><?php echo($wait);?></b>s</var></span>
                <a id="href" href="/">返回</a>
            </div>
        </div>
        <?php
            break;
            case 0:
        ?>
        <!--失败-->
        <div class="error">
            <div class="error-img"><i class="fa fa-times-circle" style="color:#red;"></i></div>
            <div class="error-info">
                <p><?php echo(strip_tags($msg));?></p>
                <span>自动跳转，时间：<var><b id="wait"><?php echo($wait);?></b>s</var></span>
                <a id="href" href="/">返回</a>
            </div>
        </div>
        <?php
            break;
            case 1:
        ?>
        <!--成功-->
        <div class="success" style="">
            <div class="error-img"><i class="fa fa-check-circle" style="color:#339900;"></i></div>
            <div class="error-info">
                <p><?php echo(strip_tags($msg));?></p>
                <span>自动跳转，时间：<var><b id="wait"><?php echo($wait);?></b>s</var></span>
                <a id="href" href="/">返回</a>
            </div>
        </div>
        <?php
            break;
            }
        ?>
    </div>
</div>

<script type="text/javascript">
    (function(){
        var wait = document.getElementById('wait'),
            href = document.getElementById('href').href;
        var interval = setInterval(function(){
            var time = --wait.innerHTML;
            if(time <= 0) {
                location.href = href;
                clearInterval(interval);
            };
        }, 1000);
    })();
</script>
<?php if(request()->param('simple')!=1): ?> <div class="container">
  <div class="row" style="font-size:14px;">
    <div class="stui-foot clearfix">
      <div class="col-pd text-center hidden-xs" style="line-height:25px;"> 网站合作联系QQ：xxxxxxx 联系邮箱：<a href="mailto:{maccms:email}">admin@w3a.one</a><br>
        Copyright <span class="fontArial"> </span> 2021-2023 www.w3a.one. All Rights Reserved. <a href="https://www.w3a.one" target="_blank">[w3a.one]</a><br>
        网站所有内容均来源于互联网相关站点自动搜索采集信息，相关链接已经注明来源，如有侵权请留言。 </div>
    </div>
  </div>
</div>
</body>
</html><?php endif; ?>