<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:27:"./tpl/mscms/video/play.html";i:1675867255;s:30:"./tpl/mscms/common/header.html";i:1571122115;s:30:"./tpl/mscms/common/footer.html";i:1675869491;}*/ ?>
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
<div class="stui-pannel stui-pannel-bg clearfix" style="margin:0 -15px 20px;">
  <div class="stui-pannel-box">
    <div class="stui-pannel-bd">
      <div class="stui-player col-pd">
        <div class="stui-player__video embed-responsive embed-responsive-16by9 clearfix" id="playerBox"> </div>
        <div class="stui-player__detail detail">
          <ul class="more-btn">
            <li> <a href="javascript:;" class="btn btn-default" onClick="window.location.reload()"><i class="fa-history"></i> 刷新</a> </li>
            <li class="btn btn-default fn-shoucang1" style="background-color: #f5f5f5;"> <i class="fa-heart"></i> <span id="shoucang"><?php if($isCollected): ?>已收藏<?php else: ?>收藏<?php endif; ?></span> </li>
            <li class="btn btn-default fn-zan2" id="giveGoodBtn"><i class="fa-thumbs-o-up <?php if($isGooded): ?>isSelected<?php endif; ?>"></i> (<span id="goodNum"><?php echo $videoInfo['good']; ?></span>)</li>
          </ul>
          <h4 class="title"><b style="color:#FF9900;">正在播放：<?php echo $videoInfo['title']; ?></b></h4>
          <p class="data margin-0"> <span class="text-muted">类型：</span><a href="/video/lists.html?cid=<?php echo (isset($videoInfo['classid']) && ($videoInfo['classid'] !== '')?$videoInfo['classid']:''); ?>" target="_blank"><?php echo (isset($videoInfo['classname']) && ($videoInfo['classname'] !== '')?$videoInfo['classname']:''); ?></a>&nbsp; <span class="split-line"></span> <span class="text-muted hidden-xs">标签：</span><?php if(is_array((isset($videoInfo['taglist']) && ($videoInfo['taglist'] !== '')?$videoInfo['taglist']:'')) || (isset($videoInfo['taglist']) && ($videoInfo['taglist'] !== '')?$videoInfo['taglist']:'') instanceof \think\Collection || (isset($videoInfo['taglist']) && ($videoInfo['taglist'] !== '')?$videoInfo['taglist']:'') instanceof \think\Paginator): $i = 0; $__LIST__ = (isset($videoInfo['taglist']) && ($videoInfo['taglist'] !== '')?$videoInfo['taglist']:'');if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><a  href="/video/lists.html?tag_id=<?php echo $v['id']; ?>" target="_blank"><?php echo (isset($v['name']) && ($v['name'] !== '')?$v['name']:''); ?></a>&nbsp;&nbsp;<?php endforeach; endif; else: echo "" ;endif; ?>&nbsp; <span class="split-line"></span> <span class="text-muted hidden-xs">上传作者：</span><?php if($videoInfo['user_id']>0): ?><a href="<?php echo url('homepage/index',array('uid'=>$videoInfo['user_id'])); ?>" target="_blank"><?php echo (isset($videoInfo['member']) && ($videoInfo['member'] !== '')?$videoInfo['member']:''); ?></a><?php else: ?>管理员<?php endif; ?>&nbsp; <span class="split-line"></span><span class="text-muted hidden-xs">上传日期：</span><span id="teacher-follow"><?php echo date('Y-m-d', $videoInfo['add_time']); ?></span></p>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-lg-wide-75 col-xs-1 padding-0">
    <div class="stui-pannel stui-pannel-bg clearfix">
      <div class="stui-pannel-box">
        <div class="stui-pannel_hd">
          <div class="stui-pannel__head active bottom-line clearfix">
            <h3 class="title"><img src="__ROOT__/tpl/mscms/msvodx/images/icon_6.png"/> 课程打赏 </h3>
            <ul class="nav nav-tabs pull-right">
            </ul>
          </div>
        </div>
        <div class="tab-content stui-pannel_bd col-pd">
          <div class="mid-tool-bar" id="gift_box" style="color:#666;text-align: center;">打赏加载....</div>
        </div>
      </div>
    </div>
    <?php if(!(empty($img) || (($img instanceof \think\Collection || $img instanceof \think\Paginator ) && $img->isEmpty()))): ?>
    <div class="stui-pannel stui-pannel-bg clearfix">
      <div class="stui-pannel-box">
        <div class="stui-pannel_hd">
          <div class="stui-pannel__head clearfix">
            <h3 class="title"> <img src="__ROOT__/tpl/mscms/msvodx/images/icon_8.png"/> 课程载图 </h3>
          </div>
        </div>
        <div class="stui-pannel_bd">
          <ul class="stui-vodlist__bd clearfix">
            <li class="col-md-6 col-sm-4 col-xs-3 ">
              <div class="stui-vodlist__box"> <a class="stui-vodlist__thumb lazyload" href="<?php echo $videoInfo['thumbnail']; ?>" target="_blank" data-original="<?php echo $videoInfo['thumbnail']; ?>" style="-moz-box-shadow:5px 5px 6px #333333; -webkit-box-shadow:2px 2px 5px #333333; box-shadow:5px 5px 6px #333333;border-radius:0px;background: url(<?php echo $videoInfo['thumbnail']; ?>) no-repeat; background-position:50% 50%; background-size: cover;"></a> </div>
            </li>
            <?php if(is_array($img) || $img instanceof \think\Collection || $img instanceof \think\Paginator): $i = 0; $__LIST__ = $img;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
            <li class="col-md-6 col-sm-4 col-xs-3 ">
              <div class="stui-vodlist__box" > <a class="stui-vodlist__thumb lazyload smallimg" href="<?php echo $v; ?>" target="_blank" data-original="<?php echo $v; ?>" style="-moz-box-shadow:5px 5px 6px #333333; -webkit-box-shadow:2px 2px 5px #333333; box-shadow:5px 5px 6px #333333;border-radius:0px;background: url(<?php echo $v; ?>) no-repeat; background-position:50% 50%; background-size: cover;"></a> </div>
            </li>
            <?php endforeach; endif; else: echo "" ;endif; ?>
          </ul>
        </div>
      </div>
    </div>
    <?php endif; if(!(empty($videoInfo['info']) || (($videoInfo['info'] instanceof \think\Collection || $videoInfo['info'] instanceof \think\Paginator ) && $videoInfo['info']->isEmpty()))): ?>
    <div class="stui-pannel stui-pannel-bg clearfix">
      <div class="stui-pannel-box">
        <div class="stui-pannel_hd">
          <div class="stui-pannel__head clearfix">
            <h3 class="title"> <img src="__ROOT__/tpl/mscms/msvodx/images/icon_24.png"/> 课程简介 </h3>
          </div>
        </div>
        <div class="stui-pannel_bd">
          <div class="detail col-pd"> <span class="detail-sketch"><?php echo htmlspecialchars_decode($videoInfo['info']); ?></span></div>
        </div>
      </div>
    </div>
    <?php endif; ?>
    <div class="stui-pannel stui-pannel-bg clearfix">
      <div class="stui-pannel-box">
        <div class="stui-pannel_hd">
          <div class="stui-pannel__head clearfix">
            <h3 class="title"> <img src="__ROOT__/tpl/mscms/msvodx/picture/icon_1.png"/> 猜你喜欢 </h3>
          </div>
        </div>
        <div class="stui-pannel_bd">
          <ul class="stui-vodlist__bd clearfix">
            <?php
            $params = array(
            'type' => 'video',
            'limit'=>12,);
             $new_list = get_recom_data($params);
             if(is_array($new_list) || $new_list instanceof \think\Collection || $new_list instanceof \think\Paginator): $i = 0; $__LIST__ = $new_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
            <li class="col-md-6 col-sm-4 col-xs-3 ">
              <div class="stui-vodlist__box"> <a class="stui-vodlist__thumb lazyload" href="<?php echo url('video/play',array('id'=>$vo['id'])); ?>" title="<?php echo $vo['title']; ?>" data-original="<?php echo $vo['thumbnail']; ?>" style="border-radius:15px;background: url(<?php echo $vo['thumbnail']; ?>) no-repeat; background-position:50% 50%; background-size: cover;"> <span class="play hidden-xs"></span> <span class="pic-text text-right"><?php echo $vo['play_time']; ?></span> </a>
                <div class="stui-vodlist__detail">
                  <h4 class="title text-overflow"><a href="<?php echo url('video/play',array('id'=>$vo['id'])); ?>" title="<?php echo $vo['title']; ?>"><?php echo $vo['title']; ?></a></h4>
                  <p class="text text-overflow text-muted hidden-xs">20<?php echo date("y-m-d",$vo['update_time']); ?> <span style="margin-left:15px;"> <i class="fa fa-dot-circle-o fa-lg"></i> <?php echo $vo['gold']; ?></span> <span style="float:right;"><i class="fa fa-eye fa-lg"></i> <?php echo $vo['click']; ?></span></p>
                </div>
              </div>
            </li>
            <?php endforeach; endif; else: echo "" ;endif; ?>
          </ul>
        </div>
      </div>
    </div>
    <div class="stui-pannel stui-pannel-bg clearfix">
      <div class="stui-pannel-box">
        <div class="stui-pannel_hd">
          <div class="stui-pannel__head clearfix">
            <h3 class="title"> <img src="__ROOT__/tpl/mscms/msvodx/images/icon_12.png"/> 网友评论 </h3>
          </div>
        </div>
        <div class="stui-pannel_bd">
          <div class="detail col-pd">
            <div class="area-form">
              <div class="comment-form">
                <div class="form-cell">
                  <div class="form-wordlimit"> 
				  <span class="form-wordlimit-input input-count" id="length">0</span> <span class="form-wordlimit-upper">/300</span> </div>
                  <div class="form-textarea form-textarea-picdom" style="position:relative">
                    <textarea data-maxlength="300" name="content"placeholder="看点槽点，不吐不快！别憋着，马上大声说出来吧！"  id="comment_content" ></textarea>
                    <div class="comment-atuser" style="position:absolute;left:5px;top:22px;background: #ebebeb"></div>
                    <input type="hidden" id="to_user" value="0">
                    <input type="hidden" id="to_id" value="0">
                  </div>
                  <div class="form-toolbar">
                    <div class="form-tool form-action"> <a href="javascript:;" class="form-btn-submit" id="submit_comment">发表评论</a> </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="area-box">
              <div class="comment-tab"> <span class="comment-tab-left">全部评论<em class="num" id="comment_num">(0)</em></span> </div>
              <div class="comment-list" >
                <ul id="comment-list">
                  <li id='not-comment'>暂没评论~</li>
                </ul>
                <div id="more-comment"><span onClick="getCommentList()">更多<i class="btn fn-more"></i></span></div>
              </div>
            </div>
          </div>
        </div>
      </div>
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
<script type="text/javascript" src="/static/ckplayer/ckplayer.js"></script>
<link rel="stylesheet" href="__ROOT__/tpl/mscms/static/css/viewer.min.css">
<script type="text/javascript" src="__ROOT__/tpl/mscms/static/js/viewer.min.js"></script>
<script>
var viewer = new Viewer(document.getElementById('video-img'), {
url: 'data-original'
});
</script>
<style>
.layui-layer-hui{
background-color: #fff!important;
color:#000;
}
.layui-layer-hui .layui-layer-content{
font-size:16px!important;
text-align: left!important;
}
.important{
font-weight: bold;
color:#843534;
}
</style>
<script src="/static/msvodx/js/video.js"></script>
<script type="text/javascript">
var playRequestUrl='<?php echo url("api/payVideo"); ?>';    //*必需配置项
var player,timer;
var page = 0;
var addLiIndex=1;
var trySeeTime=10;
function adjump(){
var canJumpAd="<?php echo $adSetting['skip_ad_on']; ?>";
if(canJumpAd==1){
player.videoPlay();
}else{
layer.msg('您不能跳过广告',{icon:2,time:2000});
}
}
function replyComment(username,id,to_id){
var reply = '回复 @'+username+' : ';
var length = reply.length;
$('#to_user').val(id);
$('#to_id').val(to_id);
//alert(length);
$(' .comment-atuser').html(reply);
$('#comment_content').val(reply);
$('#comment_content').focus();
}
function getCommentList(){
var nowDate = new Date().getTime();
var times = "";
var url = "<?php echo url('api/getCommentList'); ?>";
var  resourceId = " <?php echo $videoInfo['id']; ?>";
var data = {
"page":page,
"resourceId":resourceId,
"resourceType":1,
};
var html = '';
$.ajax({
type: 'POST',
url: url,
data: data,
dataType: 'json',
success: function(data){
$('#comment_num').html("("+data.data.count+")");
var datas = data.data.list;
if(datas==undefined) return false;
page++;
datas.forEach(function(item) {
var headimgurl = '/static/images/user_dafault_headimg.jpg';
if(item.headimgurl){
headimgurl = item.headimgurl;
}
var time = parseInt(item.last_time*1000);
if(parseInt(time+60*30*1000) > nowDate){
times = '刚刚';
}else if(parseInt(time+60*60*1000) > nowDate){
times = '半个小时前';
} else if(parseInt(time+2*60*60*1000) > nowDate){
times = '1小时前';
}else{
var oDate = new Date(time);
var Hours = oDate.getHours()>10 ? oDate.getHours() :  '0'+oDate.getHours();
var Minutes = oDate.getMinutes()>10 ? oDate.getMinutes() :  '0'+oDate.getMinutes();
times = oDate.getFullYear()+'-'+(oDate.getMonth()+1)+'-'+oDate.getDate()+' '+Hours+':'+Minutes;
}
html += '<li id="common_'+item.id+'">';
html += '<div style="overflow: hidden;padding-bottom: 10px;">';
html += '   <div class="user-avatar">';
html += '       <a href="javascript:">';
html += '           <img src="'+headimgurl+'">';
html += '       </a>';
html += '    </div>';
html += '    <div class="comment-section">';
html += '   <div class="user-info">';
html += '       <a href="javascript:" class="user-name">'+item.nickname+'</a>';
html += '       <span class="comment-timestamp">'+times+'</span>';
html += '       <span class="comment-Reply" onclick="replyComment(\''+item.nickname+'\',\''+item.send_user+'\',\''+item.id+'\')" ></span>';
html += '   </div>';
html += '   <div class="comment-text">'+item.content+'</div>';
html += '   </div>';
html += '</div>';
var list = item.list;
if(list!=undefined) {
list.forEach(function(it) {
var headimgurl = '/static/images/user_dafault_headimg.jpg';
if(it.headimgurl){
headimgurl = it.headimgurl;
}
var time = parseInt(it.last_time*1000);
if(parseInt(time+60*30*1000) > nowDate){
times = '刚刚';
}else if(parseInt(time+60*60*1000) > nowDate){
times = '半个小时前';
} else if(parseInt(time+2*60*60*1000) > nowDate){
times = '1小时前';
}else{
var oDate = new Date(time);
var Hours = oDate.getHours()>10 ? oDate.getHours() :  '0'+oDate.getHours();
var Minutes = oDate.getMinutes()>10 ? oDate.getMinutes() :  '0'+oDate.getMinutes();
times = oDate.getFullYear()+'-'+(oDate.getMonth()+1)+'-'+oDate.getDate()+' '+Hours+':'+Minutes;
}
html += '<div style="padding: 15px 20px;overflow: hidden;border-top: 1px solid #fbfbfb;">';
html += '    <div class="user-avatar">';
html += '    <a href="javascript:"><img src="'+headimgurl+'"></a>';
html += '    </div>';
html += '    <div class="comment-section">';
html += '    <div class="user-info">';
html += '    <a href="javascript:" class="user-name">'+it.nickname+'</a>';
html += '    <span class="comment-timestamp">'+times+'</span>';
html += '       <span class="comment-Reply" onclick="replyComment(\''+it.nickname+'\',\''+it.send_user+'\',\''+item.id+'\')" ></span>';
html += '    </div>';
html += '    <div class="comment-text">'+it.content+'</div>';
html += '</div>';
html += '</div>';
})
}
html += ' </li>';
})
if(data.data.isMore == 1){
$('#more-comment').show();
}else{
$('#more-comment').hide();
}
$('#not-comment').remove();
$('#comment-list').append(html);
}
});
}
function loadedHandler(){
if(player.getMetaDate()){
player.addListener('pause', pauseHandler);
player.addListener('play', playHandler);
}
}
function pauseHandler(){
console.log('pause');
clearInterval(timer);
if(trySeeTime>0)layer.msg('试看计时暂停',{icon:6,time:1000});
}
function playHandler(){
layer.msg('试看计时开始……',{icon:6,time:1000});
timer=setInterval(checkTrySeeTime,1000);
}
function checkTrySeeTime(){
if(trySeeTime<=0){
layer.msg('很抱歉，试看时间到.',{icon:2,time:1000});
//setTimeout("videoPlayInit(<?php echo $videoInfo['id']; ?>)",2000);
setTimeout("location.href=\"<?php echo url('index/prompt',['id'=>$videoInfo['id']]); ?>\"",1500);
player.videoPause();
}else{
trySeeTime--;
console.log(trySeeTime);
}
}
function createPlayer(playParams,isTrySee){
//console.log(playParams);
if(isTrySee==undefined) isTrySee=false;
if(layer!=undefined) layer.closeAll();
var vodUrl=(playParams==undefined)?'#':playParams.videoInfo.url;
var params={
container: '#playerBox',
variable: 'player',
poster:'<?php echo $videoInfo["thumbnail"]; ?>',
video: vodUrl,
//loaded:'loadedHandler',
autoplay:false,
flashplayer:false
};
if(playParams!=undefined){
$.ajax({
url:playRequestUrl,
type:'POST',
dataType:'JSON',
data:{id:playParams.videoInfo.id,surePlay:1,isTrySee:isTrySee},
async:false,
success:function(resp){
//console.log(resp);
if(resp.resultCode!=0){
layer.msg('您不能观看此影片！',{time:2000,icon:2});
return false;
}
params.video=resp.data.videoInfo.url;
if(resp.data.freeType==2 && resp.data.videoInfo.gold>0 && isTrySee){
//按时长试看
trySeeTime=resp.data.freeNum;
console.log('begion try see:'+trySeeTime+'s');
params.loaded="loadedHandler";
}
},
error:function(){
layer.msg('影片信息加载失败！',{time:2000,icon:2});
return false;
}
});
}
<?php if($adSetting['ad_on']==1): ?>
params.adfront='<?php echo $adSetting["pre_ad"]; ?>';
params.adfrontlink='<?php echo $adSetting["pre_ad_url"]; ?>';
params.adfronttime='<?php echo $adSetting["play_video_ad_time"]; ?>';
params.adpause='<?php echo $adSetting["suspend_ad"]; ?>';
params.adpauselink='<?php echo $adSetting["suspend_ad_url"]; ?>';
params.adpausetime='<?php echo $adSetting["play_video_ad_time"]; ?>';
<?php endif; ?>
params.skipButtonShow=false;
player = new ckplayer(params);
if(isTrySee) setTimeout("player.changeControlBarShow(false)",1000); //hide control
if(playParams!=undefined){
setTimeout("player.videoPlay()",1000);//play
}
}
$(function(){
getCommentList();
getGift();
createPlayer(null,null);
videoPlayInit(<?php echo $videoInfo['id']; ?>);
//点赞
<?php if($isGooded==false): ?>
$(document).on('click','#giveGoodBtn',function(){
var reqData={resourceType:'video',resourceId:<?php echo $videoInfo['id']; ?>};
$.post('<?php echo url("api/giveGood"); ?>',reqData,function(data){
//console.log(data);
if(data.resultCode==0){
$('#goodNum').html(data.data.good);
$('#giveGoodBtn').addClass("isSelected");
layer.msg('点赞成功',{time:1000,icon:1});
}else{
layer.msg('点赞失败，原因：'+data.error,{time:1000,icon:2});
}
},'JSON');
});
<?php endif; ?>
//收藏
<?php if(!$isCollected): ?>
$(document).on('click','.fn-shoucang1',function(){
var collectData={type:'1',id:'<?php echo $videoInfo["id"]; ?>'};
$.post('<?php echo url("api/colletion"); ?>',collectData,function (data) {
if(data.resultCode==0){
$('#shoucang').html('已收藏');
$('.fn-shoucang1').addClass("isSelected");
layer.msg('收藏成功',{time:1000,icon:1});
}else{
layer.msg('收藏失败，原因：'+data.error,{time:1000,icon:2});
}
},'json');
});
<?php endif; ?>
$(document).on('keyup','#comment_content',function(){
var length = $("#comment_content").val().length;
var atuser_length  = $(".comment-atuser").html().length;
if(length < atuser_length){
$(".comment-atuser").html('');
$("#comment_content").val('');
$("#length").html(0);
$('#to_user').val(0);
$('#to_id').val(0);
}
if(atuser_length > 0){
length = (length - atuser_length) > 0 ?  (length - atuser_length) : 0;
}
$("#length").html(length);
if(length > 300){
var text = $("#comment_content").val().substring(0,300);
$("#comment_content").val(text);
}
});
//评论
$(document).on('click','#submit_comment',function(){
var content =  $.trim($("#comment_content").val());
var to_user = $("#to_user").val();
var to_id = $("#to_id").val();
if((content == "" || content == undefined || content == null || content == $(".comment-atuser").html())) {
layer.msg('请输入评论的内容!');
return false;
}
var reqData={resourceType:'1',resourceId:<?php echo $videoInfo['id']; ?>,content:content,to_user:to_user,to_id:to_id};
$.post('<?php echo url("api/comment"); ?>',reqData,function(data){
if(data.resultCode==0){
layer.msg(data.message,{time:1000,icon:1});
$("#comment_content").val('');
$("#length").html(0);
$(".comment-atuser").html('');
$('#to_user').val(0);
$('#to_id').val(0);
if(data.data.comment_examine_on != 1){
var headimgurl = '/static/images/user_dafault_headimg.jpg';
if(data.data.userinfo.headimgurl){
headimgurl = data.data.userinfo.headimgurl;
}
var html = '';
$('#not-comment').remove();
if(data.data.to_id != 0){
html += '<div style="padding: 15px 20px;overflow: hidden;border-top: 1px solid #fbfbfb;" id="common_'+data.data.id+'">';
html += '    <div class="user-avatar">';
html += '           <img src="'+headimgurl+'">';
html += '    </div>';
html += '    <div class="comment-section">';
html += '    <div class="user-info">';
html += '    <a href="javascript:" class="user-name">'+data.data.userinfo.nickname+'</a>';
html += '    <span class="comment-timestamp">刚刚</span>';
html += '       <span class="comment-Reply" onclick="replyComment(\''+data.data.userinfo.nickname+'\',\''+data.data.userinfo.id+'\',\''+data.data.to_id+'\')" ></span>';
html += '    </div>';
html += '    <div class="comment-text">'+data.data.content+'</div>';
html += '   </div>';
html += '</div>';
$("#common_"+data.data.to_id).append(html);
var go_id = 'common_'+data.data.id;
window.location.href="#"+go_id;
}else{
html += '<li id="common_'+data.data.id+'">';
html += '<div style="overflow: hidden;padding-bottom: 10px;">';
html += '   <div class="user-avatar">';
html += '       <a href="javascript:">';
html += '           <img src="'+headimgurl+'">';
html += '       </a>';
html += '    </div>';
html += '    <div class="comment-section">';
html += '   <div class="user-info">';
html += '       <a href="javascript:" class="user-name">'+data.data.userinfo.nickname+'</a>';
html += '       <span class="comment-timestamp">刚刚</span>';
html += '       <span class="comment-Reply" onclick="replyComment(\''+data.data.userinfo.nickname+'\',\''+data.data.userinfo.id+'\',\''+data.data.id+'\')" ></span>';
html += '   </div>';
html += '   <div class="comment-text">'+data.data.content+'</div>';
html += '   </div>';
html += '</div>';
html += ' </li>';
$("#comment-list ").prepend(html);
window.location.href="#comment_content";
}
$("#common_"+addLiIndex).hide().slideDown('fast');
addLiIndex++;
}
}else{
layer.msg('评论失败，原因：'+data.error,{time:2000,icon:2});
}
},'JSON');
});
});
function getGift(){
var url = "<?php echo url('Api/getGift'); ?>";
var data = {
"func": "getNameAndTime" ,
};
var html = '';
$.ajax({
type: 'POST',
url: url,
data: data,
dataType: 'json',
success: function(data){
data.forEach(function(item) {
html += '<div class="r-cel" title="'+item.name+'" style="display: inline-block;float:left;width:59.8px;height:125px;border:0px solid #252525;margin:11px;text-align: center;cursor: pointer;position: relative;font-size:10px;color: #999;">' ;
html += '<img src="'+item.images+'"/ width="60" style="border-radius:15px;max-height:65px;min-height:45px;">' ;
html += '<span>'+item.name+'</span>' ;
html += '<p style="color:#ffb100;">'+item.price+'金币</p>' ;
html += '<a onclick="reward('+item.id+','+item.price+',<?php echo $videoInfo['id']; ?>,1)" style="display: block;width:55px;height:20px;line-height:20px;background:#35a082;color: #fff;border-radius:10px;font-size:12px;margin: auto;margin-top:10px;">打赏</a>' ;
html += ' </div>' ;
})
$('#gift_box').html(html);
}
});
}
</script>
<script>
$(function(){
$(".list-box .tab span").click(function(){
var $self = $(this);
$self.addClass("cur").siblings().removeClass("cur");
var $attr = $self.attr("data-for");
$(".list-box .sub-tab>div").hide();
$(".list-box .sub-tab").find("."+ $attr).show();
});
$(".select1").on("click","a",function(){
$(".select1 a").removeClass("cur");
$(this).addClass("cur");
});
});
function check_logins(){
$.post('/api/get_login_status','{}',function (e) {
if(e.resultCode == 3){
layer.msg('该账号已在其他地方登陆',
{
icon: 5,
time: 3000,
shadeClose: false,
shade: 0.8,
btn: ['确定'],
yes:function (index) {
layer.close(index);
window.location.reload();
},
success: function (layero) {
var btn = layero.find('.layui-layer-btn');
btn.css('text-align', 'center');
}
},function () {
window.location.reload();
});
}
},'json');
setTimeout('check_logins()', 5000);
}
</script>
<?php if($login_status['resultCode'] == 1): ?>
<script>
check_logins();
</script>
<?php endif; ?> 