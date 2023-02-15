<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:27:"./tpl/mscms/novel/show.html";i:1675873234;s:30:"./tpl/mscms/common/header.html";i:1571122115;s:30:"./tpl/mscms/common/footer.html";i:1675869491;}*/ ?>
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
<link href="__ROOT__/tpl/mscms/msvodx/css/novel.css" rel="stylesheet">
<style>
.novel-main .novel-left{box-shadow:none;}
</style>
<style> 
.aparagraph img{max-width:98%;} 
</style>
<script>
<?php if($permit == 0): ?>
novelpermit(<?php echo $info['gold']; ?>,<?php echo $info['id']; ?>);
<?php endif; ?>
var page = 0;
var addLiIndex=1;
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
var  resourceId = " <?php echo $info['id']; ?>";
var data = {
"page":page,
"resourceId":resourceId,
"resourceType":3,
};
var html = '';
$.ajax({
type: 'POST',
url: url,
data: data,
dataType: 'json',
success: function(data){
console.log(data);
$('#comment_num').html("("+data.data.count+")");
var datas = data.data.list;
if(datas){
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
//$('#liAdd_'+curIndex).show('SLOW');
}
}
});
}
$(function () {
getCommentList();
//收藏
<?php if(!$isCollected): ?>
$(".fn-shoucang1").on("click",function () {
var collectData={type:'3',id:'<?php echo $info["id"]; ?>'};
$.post('<?php echo url("api/colletion"); ?>',collectData,function (data) {
if(data.resultCode==0){
$('.fn-shoucang1').addClass("isSelected");
layer.msg('收藏成功',{time:1000,icon:1});
$('#collectText').html('已收藏');
}else{
layer.msg('收藏失败，原因：'+data.error,{time:1000,icon:2});
}
},'json');
});
<?php endif; ?>
//点赞
<?php if(!$isGooded): ?>
$("#giveGoodBtn").on('click',function(){
var reqData={resourceType:'novel',resourceId:<?php echo $info['id']; ?>};
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
$("#comment_content").on("keyup",function () {
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
$("#submit_comment").on("click",function () {
var content =  $.trim($("#comment_content").val());
var to_user = $("#to_user").val();
var to_id = $("#to_id").val();
if((content == "" || content == undefined || content == null || content == $(".comment-atuser").html())) {
layer.msg('请输入评论的内容!');
return false;
}
var reqData={resourceType:'3',resourceId:<?php echo $info['id']; ?>,content:content,to_user:to_user,to_id:to_id};
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
</script>
<div class="s-body">
<div class="content">
    <!--
  <div class="stui-pannel stui-pannel-bg clearfix" style="margin-top:55px;">
    <div class="stui-pannel-box">
      <div class="col-pd"> <a class="hidden-xs" target="_blank" href="http://www.msvod.cc/"> <img class="img-responsive" src="/XResource/20190919/izDst2ihXD6E6xEmBMFikXAz8d3rFNpf.png"  style="margin:0 auto; width:100%;border-radius:10px;"/> </a> </div>
    </div>
  </div>
  -->
  <div class="spv-comment comment">
    <div class="comment-left">
      <div class="novel-main">
        <div class="novel-left">
          <h1 style="font-size:20px;"><?php echo $info['title']; ?></h1>
          <div class="source">
            <div style="float:left;margin-right:30px;margin-top:7px;color:#999;">发布时间：<?php echo date("y-m-d H:i:s",$info['update_time']); ?></div>
            <div style="float:left;margin-right:20px;margin-top:7px;color:#999;">作者：<?php if($info['user_id']>0): ?><a title="进入ta的主页" href="<?php echo url('homepage/index',array('uid'=>$info['user_id'])); ?>"><var><?php echo $info['username']; ?></var></a><?php else: ?>管理员<?php endif; ?></div>
            <span class="see" style=" border: 1px solid #eee;padding:7px 15px 2px;" id="giveGoodBtn"> <i class="fa fa-thumbs-up" style="<?php if($isGooded): ?>margin-bottom:6px;<?php else: ?>color:#ff7c00;margin-bottom:6px;<?php endif; ?>"></i> <var id="goodNum"><?php echo $info['good']; ?></var></span> <span class="see fn-shoucang1" style="margin-bootom:20px;border: 1px solid #eee;padding:7px 15px 7px;"> <i class="fa fa-star fn-shoucang1" style="<?php if($collect_info=="已收藏"): else: ?>color:#ff7c00;<?php endif; ?>"></i> <?php if($collect_info=="已收藏"): ?><var id="collectText">已收藏</var><?php else: ?><var id="collectText">收藏</var><?php endif; ?></span> </div>
          <div class="source"> <?php if(is_array($tag_list) || $tag_list instanceof \think\Collection || $tag_list instanceof \think\Paginator): $i = 0; $__LIST__ = $tag_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?> <span><?php echo $vo['name']; ?></span> <?php endforeach; endif; else: echo "" ;endif; ?> </div>
          <div class="aparagraph" style="line-height:30px;font-size:16px;padding:12px;text-indent:1em;border-top: 1px solid #eee;"> <?php echo htmlspecialchars_decode($info['content']); ?> </div>
          <div class="cut"> <?php if(!empty($previous_info)): ?>
          <!-- <a class="prev"><i class="btn fn-prev"></i>上一篇</a>
           <a class="next">下一篇<i class="btn fn-next"></i></a>-->
            <a class="prev" title="<?php echo $previous_info['title']; ?>" href="<?php echo url('novel/show',array('id'=>$previous_info['id'])); ?>">
            <div class="prev-btn" style="margin-left: -7px;"><i class="fa fa-angle-left"></i></div>
            <div class="prev-box">
              <div class="img-bg"> <img src="<?php echo $previous_info['thumbnail']; ?>" /> </div>
              <p><?php echo $previous_info['title']; ?></p>
            </div>
            </a> <?php endif; if(!empty($next_info)): ?> <a class="next" title="<?php echo $next_info['title']; ?>"  href="<?php echo url('novel/show',array('id'=>$next_info['id'])); ?>">
            <div class="prev-box">
              <div class="img-bg"> <img src="<?php echo $next_info['thumbnail']; ?>" /> </div>
              <p><?php echo $next_info['title']; ?></p>
            </div>
            <div class="prev-btn" style="margin-right: -7px;"><i class="fa fa-angle-right"></i></div>
            </a> <?php endif; ?> </div>
        </div>
      </div>
      <div class="area-form">
        <div class="comment-form">
          <div class="form-cell">
            <div class="form-wordlimit"> <span class="form-wordlimit-input input-count" id="length">0</span> <span class="form-wordlimit-upper">/300</span> </div>
            <div class="form-textarea form-textarea-picdom">
              <textarea data-maxlength="300" name="content"
placeholder="看点槽点，不吐不快！别憋着，马上大声说出来吧！"  id="comment_content" ></textarea>
              <div class="comment-atuser" style="position:absolute;left:4px;top:37px;font-size:12px;background: #ebebeb"></div>
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
        <div class="comment-tab"> <span class="comment-tab-left">全部评论<em class="num" id="comment_num">(0)</em></span>
          <!--<span class="comment-tab-right">我的评论消息</span>-->
        </div>
        <div class="comment-list" >
          <ul id="comment-list">
            <li class='not-comment' id='not-comment'>暂没评论 ~</li>
          </ul>
          <div id="more-comment" align="center" style="display: none"><span onclick="getCommentList()" style="height:70px;line-height:70px;border: 1px solid #eee;padding: 0 15px 0px;">加载更多 <i class="btn fn-more"></i></span></div>
        </div>
      </div>
    </div>
    <div class="comment-right">
      <div class="sub-tab">
        <p class="title">相关推荐</p>
        <div class="select" style="display: block;">
          <ul>
            <?php if(!(empty($recom_list) || (($recom_list instanceof \think\Collection || $recom_list instanceof \think\Paginator ) && $recom_list->isEmpty()))): if(is_array($recom_list) || $recom_list instanceof \think\Collection || $recom_list instanceof \think\Paginator): $i = 0; $__LIST__ = $recom_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
            <li> <a href="<?php echo url('novel/show',array('id'=>$vo['id'])); ?>">
              <div class="pic"> <img src="<?php echo $vo['thumbnail']; ?>" > </div>
              <p><?php echo $vo['title']; ?></p>
              <p class="content-like"> <span class="mod-like"><i class="fa fa-eye"></i> <?php echo $vo['click']; ?></span> <span class="mod-like" style="float: right;"><i class="fa fa-thumbs-o-up"></i> <?php echo $vo['good']; ?></span> </p>
              </a> </li>
            <?php endforeach; endif; else: echo "" ;endif; else: ?>
            <div class="not-comment not">暂时没有数据 ~</div>
            <?php endif; ?>
          </ul>
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