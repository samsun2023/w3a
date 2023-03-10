<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:37:"./tpl/mscms/mobile/member/member.html";i:1675867255;s:37:"./tpl/mscms/mobile/common/header.html";i:1675867255;s:37:"./tpl/mscms/mobile/common/footer.html";i:1675867255;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<?php $menu = getMenu();?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta content="telephone=no" name="format-detection">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title><?php echo (isset($page_title) && ($page_title !== '')?$page_title:""); ?>_<?php echo $seo['site_title']; ?></title>
<meta name="keywords" lang="zh-CN" content="<?php echo $seo['site_keywords']; ?>"/>
<meta name="description" lang="zh-CN" content="<?php echo $seo['site_description']; ?>" />
<link rel="stylesheet" href="__ROOT__/tpl/mscms/mobile/static/css/swiper.min.css">
<link rel="stylesheet" href="__ROOT__/tpl/mscms/mobile/static/css/common.css" />
<script src="__ROOT__/tpl/mscms/mobile/static/js/jquery-3.2.1.min.js"></script>
<script src="__ROOT__/tpl/mscms/mobile/static/js/swiper.min.js"></script>
<script type="text/javascript" src="__ROOT__/tpl/mscms/mobile/static/js/layer_mobile/layer.js"></script>
<script type="text/javascript" src="__ROOT__/tpl/mscms/mobile/static/js/common.js"></script>
<link href="__ROOT__/tpl/mscms/msvodx/awesome/css/font-awesome.css" rel="stylesheet">
<link href="__ROOT__/tpl/mscms/msvodx/css/font.css" rel="stylesheet">
</head>
<body>
<div>
<header class="js-header">
<div class="head">
<a href="/" class="logo"><img src="<?php echo $seo['site_logo_mobile']; ?>"/></a>
<?php $controller =  lcfirst(request()->controller());?>
<form
<?php switch($controller): case "images": ?> action="<?php echo url('search/index',array('type'=>'atlas')); ?>"<?php break; case "atlas": ?> action="<?php echo url('search/index',array('type'=>'atlas')); ?>"<?php break; case "novel": ?>action="<?php echo url('search/index',array('type'=>'novel')); ?>"<?php break; case "search": ?>action="<?php echo url('search/index',array('type'=>$type)); ?>"<?php break; default: ?>
action="<?php echo url('search/index',array('type'=>'video')); ?>"
<?php endswitch; ?>
method="get" id="myform">
    <div class="search">
        <div style="display: inline-block;float: left;">
            <?php if($controller=="atlas" || isset($type)&& $type=='atlas'): ?> <span class="choice-box1">??????<i class="btn fn-triangle"></i></span>
            <?php elseif($controller=="images" || isset($type)&& $type=='images'): ?> <span class="choice-box1">??????<i class="btn fn-triangle"></i></span>
            <?php elseif($controller=="novel" || isset($type)&& $type=='novel'): ?> <span class="choice-box1">??????<i class="btn fn-triangle"></i></span>
            <?php elseif($controller=="member" || isset($type)&& $type=='member'): ?> <span class="choice-box1">??????<i class="btn fn-triangle"></i></span>
            <?php elseif($controller=="video" || isset($type)&& $type=='video'): ?> <span class="choice-box1">??????<i class="btn fn-triangle"></i></span>
            <?php else: ?>
            <span class="choice-box1">??????<i class="btn fn-triangle"></i></span>
            <?php endif; ?>

            <div class="choice-btn">
                <div class="choice-shadow">
                    <p onclick="$('#myform').attr('action','<?php echo url('search/index',array('type'=>'video')); ?>')">??????</p>
                    <p onclick="$('#myform').attr('action','<?php echo url('search/index',array('type'=>'atlas')); ?>')">??????</p>
                    <p onclick="$('#myform').attr('action','<?php echo url('search/index',array('type'=>'novel')); ?>')">??????</p>
                    <p onclick="$('#myform').attr('action','<?php echo url('search/index',array('type'=>'member')); ?>')">??????</p>
                    <?php if(isset($type)): ?><p onclick="$('#myform').attr('action','<?php echo url('search/index',array('type'=>$type)); ?>')">??????</p><?php endif; ?>
                </div>
            </div>
        </div>
        <input class="js-placeholder" placeholder="?????????" type="search" value='<?php if(isset($key_word)): ?><?php echo $key_word; endif; ?>' name="key_word"><i class="btn fn-sousuo" onclick="$('#myform').submit();" style="float: right;width: 23px;"></i>
    </div>
</form>
<div class="menu"><span class="btn fn-sort"></span></div>
</div>
<nav class="js-nav">
<div class="class-page" style="padding:13px;">
<?php if(is_array($menu) || $menu instanceof \think\Collection || $menu instanceof \think\Paginator): $i = 0; $__LIST__ = $menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
<h3 class="navbt"><?php echo $vo['name']; ?></h3>
<?php if(!(empty($vo['sublist']) || (($vo['sublist'] instanceof \think\Collection || $vo['sublist'] instanceof \think\Paginator ) && $vo['sublist']->isEmpty()))): ?>
<div class="page-m clearfix">
<?php if(is_array($vo['sublist']) || $vo['sublist'] instanceof \think\Collection || $vo['sublist'] instanceof \think\Paginator): $i = 0; $__LIST__ = $vo['sublist'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
<a href="<?php echo $v['url']; ?>" class="<?php if($v['current'] == 1): ?>navdh<?php else: ?>daohang<?php endif; ?>"><?php echo $v['name']; ?></a>
<?php endforeach; endif; else: echo "" ;endif; ?>
</div>
<?php endif; endforeach; endif; else: echo "" ;endif; ?>
</div>
</nav>
<div class="nav-masklayer"></div>
</header>
<div class="content">
<link rel="stylesheet" href="__ROOT__/tpl/mscms/mobile/static/css/member.css">
<style>
    .choice a{display: block;margin: 10px 5px;border:1px solid #ddd;padding: 7px 0;border-radius: 32px;width: 80%;margin: 10px auto;}
    .layui-m-layercont{padding: 30px;}
    .layui-m-layer0 .layui-m-layerchild{width: 70%;background: rgba(255,255,255,.9);}
</style>
<script>
    function sign(){
        var url = "<?php echo url('api/sign'); ?>";
        $.post(url, {}, function (data) {
            if (data.resultCode == 0) {
                $('.sign').find('var').html('+'+data.data['value']);
                $('.sign').addClass("signs");
                $('.sign').addClass("Completion");
                layer.open({skin:'msg',content:'????????????',time:2, end:function(){
                    $('.sign').removeClass("signs");
                }});
            }else{
                layer.open({skin:'msg',content:data.error,time:2});
            }
        }, 'JSON');
    }
</script>
    <div>
        <div class="info-box">
            <img class="pic-info" src="<?php echo $info['headimgurl']; ?>" />
            <div class="info-d">
                <span class="name"><?php echo $info['nickname']; if($info['is_permanent'] == 1): ?>
                            <i class="btn fn-vip1 vip"></i> ??????vip
                   <?php else: if($info['out_time'] > time()): ?>
                    <i class="btn fn-vip1 vip"></i> <?php echo safe_date('Y/m/d',$info['out_time']); ?> ??????
                    <?php else: if(empty($info['out_time'])): ?>
                            <i class="btn fn-vip1"></i>????????????VIP
                         <?php else: ?>
                            <i class="btn fn-vip1"></i>?????????
                         <?php endif; endif; endif; ?>
                </span>

                <span>
                    <a href="<?php echo url('member/record_gold'); ?>" style="display: inline-block;color:#fff;margin-right: 10px;">
                      <i class="btn fn-jinbi2 vip"></i><?php echo $info['money']; ?></a>
                    <a href="<?php echo url('member/record_k_gold'); ?>" style="display: inline-block;color:#fff;margin-right: 10px;">
                      <span style="color:#DAA520;font-weight:bold;font-size:14px;">k </span><?php echo $info['k_money']; ?>
                    </a>
                </span>
            </div>
            <div class="foot-bg"></div>
        </div>
        <div class="M-member">
           <!-- <div class="m-box money info-d">
                &lt;!&ndash;<a href="<?php echo url('member/record_gold'); ?>" style="display: inline-block;"><i class="btn fn-jinbi2 vip"></i><?php echo $info['money']; ?></a>&ndash;&gt;
                <div><a>????????????</a></div>
            </div>-->
            <div class="m-box info-d" style="width: 30%;">
                <div><a href="<?php echo url('member/card_pwd'); ?>">????????????</a></div>
            </div>
            <div class="m-box info-d">
                <div>
                    <a href="<?php echo url('systemPay/recharge'); ?>">????????????/VIP</a>
                </div>
            </div>
            <div class="m-box ">
                <a  <?php if(isSign() != '1'): ?>class="sign"   <?php else: ?> class="sign Completion" <?php endif; ?>  onclick="sign()">?????? <var>+2</var></a>
            </div>

        </div>
        <div class="M-left">
            <ul>
                <li><a href="<?php echo url('member/video'); ?>"><i class="btn fn-video"></i><br />????????????</a></li>
                <li><a href="<?php echo url('member/novel'); ?>"><i class="btn fn-novel"></i><br />????????????</a></li>
                <li><a href="<?php echo url('member/img'); ?>"><i class="btn fn-img"></i><br />????????????</a></li>
                <li><a href="<?php echo url('member/collection_video'); ?>"><i class="btn fn-info"></i><br />????????????</a></li>
                <li class="withdrawals"><a><i class="btn fn-tixian"></i><br />????????????</a></li>
                <li><a href="<?php echo url('member/record_pay'); ?>"><i class="btn fn-3"></i><br />????????????</a></li>
                <li><a href="<?php echo url('member/record_video'); ?>"><i class="btn fn-icon12"></i><br />????????????</a></li>
                <li><a href="<?php echo url('member/agent'); ?>"><i class="btn fn-management"></i><br />?????????</a></li>
                <li class="manage"><a><i class="btn fn-info-mage"></i><br />????????????</a></li>
                <!--<li><a href="<?php echo url('member/card_pwd'); ?>"><i class="btn fn-qiaquan"></i><br />????????????</a></li>-->
            </ul>
        </div>
        <div class="logout">
            <i class="btn fn-tuichu"></i>
            <a onclick="logout()" >????????????</a>
        </div>
    </div>
    <?php  $register_validate = empty(get_config('register_validate')) ? 0 : get_config('register_validate');?>
<script>
    function logout(){
        var url="<?php echo url('api/logout'); ?>";
        $.post(url,{},function(){
            layer.open({time: 2,skin:'msg',content: '????????????',end:function(){
                location.href="/";
            }});
        },'JSON');
    }

    $(function () {
      var register_validate =  "<?php echo $register_validate; ?>";
       $(".M-left li.manage").click(function(){
           var info = "<?php echo url('member/info'); ?>";
           var email = "<?php echo url('member/set_email'); ?>";
           var phone = "<?php echo url('member/set_phone'); ?>";
           var pwd = "<?php echo url('member/set_pwd'); ?>";
           var gold = "<?php echo url('member/record_gold'); ?>";
           var k_gold = "<?php echo url('member/record_k_gold'); ?>";
           var content = '';
           content +='<div class="choice"><a href="'+info+'">????????????</a>';
           if(register_validate == 1){
               content +='<a href="'+email+'">????????????</a>';
           }
           if(register_validate == 2){
               content +='<a href="'+phone+'">????????????</a>';
           }
           content +='<a href="'+pwd+'">????????????</a>';
           content +='<a href="'+k_gold+'">K ?????????</a>';
           content +='<a href="'+gold+'">????????????</a></div>';
           layer.open({
               content: content
           });
       });
       /*??????*/
        $(".M-left li.withdrawals").click(function(){
            var add = "<?php echo url('member/add_card'); ?>";
            var operate = "<?php echo url('member/get_out_pay'); ?>";
            var record = "<?php echo url('member/record_out_pay'); ?>";
            layer.open({
                content: '<div class="choice"><a href="'+operate+'">??????</a><a href="'+record+'">????????????</a><a href="'+add+'">????????????</a></div>'
            });
        });


    });
</script>

</div>
<?php echo $wechatCode; ?>
<footer>
    <a class="navFooter" target="_self" href="/"><i class="btn fn-shouye"></i>??????</a>
    <a class="navFooter active" target="_self" href="<?php echo url('video/lists'); ?>"><i class="btn fn-sort"></i>??????</a>
    <a class="navFooter" target="_self" href="<?php echo url('Share/Index'); ?>"><i class="btn fn-xuanchuan"></i>??????</a>
    <a class="navFooter" target="_self" href="<?php echo url('member/member'); ?>"><i class="btn fn-wode"></i>??????</a>
</footer>
<a class="btn-gotop" id="btn-gotop"><i class="btn fn-top"></i></a>
</div>
<script type="text/javascript">
    $(function(){
        //to and footer nav setting
        var navTopTitle="<?php echo (isset($navTopTitle) && ($navTopTitle !== '')?$navTopTitle:'?????????'); ?>";
        $('#navTopTitle').html(navTopTitle);
        $('.navFooter').removeClass('active');
        $('.navFooter:nth-child(<?php echo (isset($curFooterNavIndex) && ($curFooterNavIndex !== '')?$curFooterNavIndex:2); ?>)').addClass('active');

        $(window).scroll(function () {
            if ($(window).scrollTop() > 100) {
                $("#btn-gotop").fadeIn(500);
            }else {
                $("#btn-gotop").fadeOut(500);
            }
        });
        //???????????????????????????????????????????????????
        $("#btn-gotop").click(function () {
            $('body,html').animate({ scrollTop: 0 }, 1000); //1000??????1?????????????????????
            return false;
        });

    });
</script>
<style>
    .btn-gotop{display: none;position: fixed;bottom: 150px;right: 10px;background: rgba(0,0,0,.5);width: 50px;height:50px;border-radius: 5px;z-index: 99;text-align: center;line-height: 50px;color:#fff;}
    .btn-gotop:hover{color:#fff;}
    .btn-gotop i{font-size: 40px;}

</style>
<?php $login_status = check_is_login();if($login_status['resultCode'] == 3): ?>
<script>
    layer.open({content:'????????????????????????????????????',time:3,skin:'msg'});
</script>
<?php endif; 
$baseConfig = get_config_by_group('base');
$baseConfig['friend_link'] =  empty($seo['friend_link']) ? $baseConfig['friend_link'] : $seo['friend_link'];
$baseConfig['site_icp'] = empty($seo['site_icp']) ? $baseConfig['site_icp'] : $seo['site_icp'];
$baseConfig['site_statis'] = empty($seo['site_statis']) ? $baseConfig['site_statis'] : $seo['site_statis'];
$linkList=get_friend_link($baseConfig);
 ?>
<?php echo htmlspecialchars_decode($baseConfig['site_statis']); ?>
</body>
</html>