<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:32:"./tpl/mscms/member/seek_pwd.html";i:1569406096;s:27:"./tpl/mscms/common/top.html";i:1571122134;}*/ ?>
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
<link rel="stylesheet" href="__ROOT__/tpl/mscms/msvodx/css/member.css">


<div id="block-B" class="pp_con_wrap">
    <div class="pp_main fc-main clearfix" id="J_verify-phone">
        <!--???????????????-->
        <div class="top_tips">
            <i class="pp_icon diamond_gray"></i>
            ????????????????????????????????????????????????????????????????????????????????????
        </div>
        <form action="" method="post" id="myform">
        <!--????????????-->

        <div id="step1" class="pp_pw_retakeStep">
            <div class="retakeStep_wrap">
                <div class="pp_icon step_bg stepOne">
                    <div class="step_list">
                        <ul>
                            <li class="step_pass">1.??????????????????</li>
                            <li>2.???????????????</li>
                            <li>3.????????????</li>
                        </ul>
                    </div>
                </div>
                <div class="step_con">
                        <div class="pp_account_form_item">
                            <span class="vl-inline item_title_big">
                                <?php if($register_validate == 1): ?>
                                    <label>?????????????????????</label>
                                <?php else: ?>
                                    <label>?????????????????????</label>
                                <?php endif; ?>
                            </span>
                            <span class="vl-inline item_input">
                                <input id="email" name="email" class="input-common input-common-ph" type="text"
                                       <?php if($register_validate == 1): ?>placeholder="???????????????????????????????????????"<?php else: ?>placeholder="?????????????????????????????????"<?php endif; ?> >

                            </span>
                            <a class="set-warnning" id="getCodeBtn" onclick="sendCode()">???????????????</a><!--??????????????????class="sent"-->
                            <!--<span class="error-warnning">???????????????</span>-->
                        </div>
                        <div class="pp_account_form_item">
                            <span class="vl-inline item_title_big">
                                <label>?????????????????????</label>
                            </span>
                            <span class="vl-inline item_input" style="width: 170px;">
                                <input id="mailVerifyCode" class="input-common input-common-ph" style="width: 150px;"
                                       type="text" placeholder="??????????????????">
                            </span>
                        </div>
                        <!--????????????????????????-->
                        <div class="pp_account_form_item">
                            <span class="vl-inline item_title_big">
                                <label></label>
                            </span>
                            <span class="vl-inline">
                             <a id="nextStepBtn" class="pc-btn pc-btn-green" onclick="checkCode()" href="javascript:void(0)"
                                style="width: 150px;margin-top: 25px;">?????????</a>
                            </span>
                        </div>
                        <!--??????????????????????????????-->

                </div>
            </div>
        </div>
        <!--???????????????-->
        <div id="step2" style="display: none;" class="pp_pw_retakeStep">
            <div class="retakeStep_wrap">
                <div class="pp_icon step_bg stepTwo">
                    <div class="step_list">
                        <ul>
                            <li class="step_pass">1.??????????????????</li>
                            <li class="step_pass">2.???????????????</li>
                            <li>3.????????????</li>
                        </ul>
                    </div>
                </div>
                <div class="step_con">
                    <form action="">
                        <div class="pp_account_form_item">
                                <span class="item_title_big">
                                    <label for="">???????????????</label>
                                </span>
                            <span class="item_input">
                                    <input class="input-common" id='new' name="new"  type="password" placeholder="6~20??????????????????">
                                </span>
                            <!--<span class="error_warnning"></span>-->
                        </div>
                        <div class="pp_account_form_item">
                                <span class="item_title_big">
                                    <label for="">???????????????</label>
                                </span>
                            <span class="item_input">
                                    <input class="input-common" id='confirm' name="confirm" type="password" placeholder="?????????????????????">
                                </span>
                            <!--<span class="error_warnning"></span>-->
                        </div>
                        <!--????????????????????????-->
                        <div class="pp_account_form_item">
                             <span class="vl-inline item_title_big">
                                <label></label>
                            </span>
                            <span class="vl-inline">
                             <a id="saveBindBtn" class="pc-btn pc-btn-green" onclick="setPassWord()" href="javascript:void(0)"
                                style="width: 150px;margin-top: 25px;">????????????</a>
                            </span>
                        </div>
                        <!--??????????????????????????????-->
                    </form>
                </div>
            </div>
        </div>
        <!--????????????-->
        <div id="step3" style="display: none;" class="pp_pw_retakeStep">
            <div class="retakeStep_wrap">
                <div class="pp_icon step_bg stepThree">
                    <div class="step_list">
                        <ul>
                            <li class="step_pass">1.??????????????????</li>
                            <li class="step_pass">2.???????????????</li>
                            <li class="step_pass">3.????????????</li>
                        </ul>
                    </div>
                </div>
                <div class="step_con">
                    <div class="success_note">
                        <i class="success_hint_bd btn fn-baocunchenggong"></i>
                        <span>?????????????????????????????????</span><br>
                        <a href="<?php echo url('member/member'); ?>">????????????</a>
                        <a href="/">??????</a>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
</div>
<script>
    var register_validate = "<?php echo $register_validate; ?>";
    var  curTime  = 0;
    var disabled = 0;
    var curGetCodeBtn = $('#getCodeBtn');
    var curMailObj = $('#email');
    if(register_validate == 1){
        var pregStr = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
        var types = '????????????';
        var type = 'email';
    }else{
        var pregStr = /((13[0-9])|(14[5|7])|(15([0-3]|[5-9]))|(18[0,5-9]))\d{8}$/;
        var types = '????????????';
        var type = 'tel';
    }
    //send code
    function sendCode() {
        if(disabled)  return false;
        var mailUrl= $('#email').val();
        if (pregStr.test(mailUrl)) {
            var url = "<?php echo url('api/getFindPassWordCode'); ?>";
            var postData = {content: mailUrl,type:type};
            $.post(url, postData, function (resp) {
                if (resp.statusCode == 0) {
                    disabled = 1;
                    $('#mailVerifyCode').focus();
                    curTime = 35;
                    layer.msg(resp.error);
                    timerIndex = setInterval("timer()", 1000);
                } else {
                    layer.tips(resp.error, curMailObj);
                }
            }, 'JSON');
        } else {
            layer.tips('??????????????????'+types, curMailObj);
            return false;
        }
    }
    function timer() {
        curTime--;
        if (curTime <= 0) {
            clearInterval(timerIndex);
            disabled = 0;
            curGetCodeBtn.html('???????????????').on('click', function(){
            }).removeClass('sent');
        }else{
            curGetCodeBtn.html('???????????????(' + curTime + '???)').addClass('sent');
        }
    }
    function checkCode() {
        var mailUrl= $('#email').val();
        var code =  $('#mailVerifyCode').val();
        if (pregStr.test(mailUrl)) {
            var url = "<?php echo url('api/checkPassWordCode'); ?>";
            var postData = {content: mailUrl,code:code,type:type};
            $.post(url, postData, function (resp) {
                if (resp.statusCode == 0) {
                    $('#step1').hide('slow');
                    $('#step2').show('slow');
                } else {
                    layer.tips(resp.message, curMailObj);
                }
            }, 'JSON');
        } else {
            layer.tips('??????????????????'+types, curMailObj);
            return false;
        }
    }
    function setPassWord() {
        curMailObj = $('#new');
        var newpass = curMailObj.val();
        var confirm = $('#confirm').val();
        if(newpass.length < 6 || (newpass.length >20)){
            layer.tips('?????????6~20??????????????????', curMailObj);
            return false;
        }
        if(newpass != confirm) {
            layer.tips('?????????????????????', curMailObj);
            return false;
        }
        $('#myform').submit();
    }
</script>
<?php if($status == '1'): ?>
<script>
$('#step1').hide();
$('#step2').hide();
$('#step3').show();
</script>
<?php endif; ?>
</body>
</html>