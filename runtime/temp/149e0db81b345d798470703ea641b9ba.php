<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:28:"./tpl/mscms/index/login.html";i:1569567832;s:30:"./tpl/mscms/common/header.html";i:1571122115;s:30:"./tpl/mscms/common/footer.html";i:1675869491;}*/ ?>
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
<style>
.eg-register-1 {margin: 100px auto 50px auto;padding-bottom: 50px;width: 1200px;background: #fff;border-radius:20px;}
.pos-r {position: relative;}
.mar-center {margin-right: auto;margin-left: auto; }
.register-content {padding: 65px 0 70px 0;width: 680px;}
.eg-border-b {border-bottom: 1px solid #ddd; }
.tc {text-align: center;}
.row-one {padding: 5px 0;line-height: 24px;}
.eg-font-size-22 {font-size: 22px !important;}
.font { font-weight: bold;}
.mar-bot-10 { margin-bottom: 10px;}
.form01 { margin-left: 55px;padding-top: 55px;width: 900px;}
.row-onef { padding: 22px 0; line-height: 24px;}
.label-st02 {display: inline-block; margin-right: 10px;width: 130px; text-align: right;font-size: 18px; }
.xing {padding: 5px; color: #ad0711;}
.collection {display: inline-block; vertical-align: middle; margin-left: 10px;width: 305px;}
.input-txt01 {width: 305px;height: 35px; line-height: 35px;background: #F1F1F1;padding: 0 5px; border: 0;border-bottom: 1px solid #ddd;outline: none;color: #666;}
.btn-wst01 { margin-top: 20px;margin-left: 84px;width: 374px; height: 52px;outline: none;border: 0;-webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;background: #ff7c00;color: #fff;font-weight: bold;font-size: 14px;cursor: pointer;}
.pad-left-132 {padding-left: 83px;}
.row-onev { padding: 0;line-height: 24px;}
.fr {float: right; }
.rc-st01 {font-size: 14px; color: #666;}
</style>
<div id="eg-register-1" class="eg-register-1 pos-r ">
  <div class="register-content mar-center">
    <div class="eg-border-b tc">
      <div class="row-one">
        <!--<img src="__ROOT__/tpl/zixun/static/images/find_password.png" alt="" class="mar-left-267 mar-bom-25">-->
        <p class="eg-font-size-22 font mar-bot-10" style="color:#e23535;">用户登录</p>
        <p class="eg-font-size-16">为了您更好的体验，请登录</p>
      </div>
    </div>
    <div class="form01">
      <div class="row-onef">
        <label class="label-st02"><span class="xing">*</span>帐号</label>
        <div class="collection error500">
          <input type="text" name="mobile" id="userName" class="input-txt01" placeholder="账号">
        </div>
      </div>
      <div class="row-onef">
        <label class="label-st02"><span class="xing">*</span>密码</label>
        <div class="collection error500">
          <input type="password" name="password" id="password" class="input-txt01" placeholder="密码">
        </div>
      </div>
      <?php if(get_config('verification_code_on')): ?>
      <div class="row-onef">
        <label class="label-st02"><span class="xing">*</span>验证码</label>
        <div class="collection error500 pos-r">
          <input type="password" name="password" id="verifyCode" class="input-txt01" placeholder="验证码">
          <img src="<?php echo url('api/getCaptcha'); ?>" class="check-btn pos-a" style="border: 0;" onclick="this.src='<?php echo url('api/getCaptcha'); ?>?'+Math.random()" id="verifyCodeImg" /> </div>
      </div>
      <?php endif; ?>
      <div class="row-onet pad-left-132  pos-r" style="width: 455px;"> <span class="video_check_num video_check_num3 pos-a" style="display: none;left:25px;right: auto;"> <img src="__ROOT__/tpl/mscms/msvodx/images/danger.png" alt=""> <span class="error_info">号码格式有误，请重新输入</span> </span> </div>
      <div class="row-onef">
        <button class="btn-wst01 btn-wst01-creator mar-left-80 submit-btn" id="btn-wst01" onclick="return login()">登 录</button>
      </div>
      <div class="row-onev pad-left-132 " style="width: 560px;margin-left: 87px;float:right;"> <span class="fl "><a href="<?php echo url('index/register'); ?>" class="rc-st01">没有帐号？去注册</a></span> <?php if($register_validate != 0): ?> <span class="fr mar-right-58"> <a href="<?php echo url('member/seek_pwd'); ?>" title="" class="rc-st01">找回密码</a> </span> <?php endif; ?> </div>
      <div class="row-onef" align="center" style="margin-left:220px;margin-top:80px;"> <?php  $longwait=get_sanfanlogin(); if(is_array($longwait) || $longwait instanceof \think\Collection || $longwait instanceof \think\Paginator): if( count($longwait)==0 ) : echo "" ;else: foreach($longwait as $key=>$vo): ?>
        <ul class="">
          <?php if($vo['login_code'] == 'qq'): ?>
          <li class="qweibo" style="float:left;"><a href="<?php echo url('open/login',['code'=>'qq']); ?>"><em>&nbsp;</em><img src="/tpl/mscms/msvodx/images/qq.png" width="50"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>
          <?php endif; if($vo['login_code'] == 'wechat'): ?>
          <li class="douban" style="float:left;"> <a href="<?php echo url('open/login',['code'=>'wechat']); ?>"><em>&nbsp;</em><img src="/tpl/mscms/msvodx/images/wx.png" width="50"></a> </li>
          <?php endif; ?>
        </ul>
        <?php endforeach; endif; else: echo "" ;endif; ?> </div>
      <br>
      <br>
      <div align="center" style="margin-right:335px;margin-top:10px;">使用第三方登录</div>
    </div>
  </div>
</div>
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
<script type="text/javascript">
//console.log("当前用户Id:<?php echo session('member_id'); ?>");
var disabled = 0;
function login() {
var user = $('#userName').val();
var password = $('#password').val();
var verifyCode=$('#verifyCode');
if (user == '' || password == '') {
$(".row-onet .pos-a").show();
$(".error_info").html("用户名或密码不能为空！");
return  false;
}
if(verifyCode.val()==''){
$(".row-onet .pos-a").show();
$(".error_info").html("验证码不能为空!");
verifyCode.focus();
return false;
}
var url = "<?php echo url('api/login'); ?>";
$.post(url, {userName: user, password: password,verifyCode:verifyCode.val()}, function (data) {
if (data.statusCode == 0) {
/*layer.msg('登陆成功', {time: 1000, icon: 1}, function() {
location.reload();
});*/
$(".row-onet .pos-a").show();
$(".error_info").html("登陆成功");
location.href="/";
} else {
$(".row-onet .pos-a").show();
$(".error_info").html(data.error);
//                layer.msg(data.error, {icon: 2, anim: 6, time: 1000});
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
</script>
</body></html>