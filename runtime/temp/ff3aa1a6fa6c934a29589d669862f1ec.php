<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:35:"./tpl/mscms/mobile/index/index.html";i:1675867255;s:37:"./tpl/mscms/mobile/common/header.html";i:1675867255;s:37:"./tpl/mscms/mobile/common/footer.html";i:1675867255;}*/ ?>
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
<style>
    .panel{margin-top: 15px;}
</style>
        <!--?????????-->
        <div class="swiper-container">
            <div class="swiper-wrapper">
                <?php if(is_array($seo['banner']) || $seo['banner'] instanceof \think\Collection || $seo['banner'] instanceof \think\Paginator): $i = 0; $__LIST__ = $seo['banner'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                <div class="swiper-slide" style="background: url(<?php echo $v['images_url']; ?>) no-repeat;background-size: cover;background-position: center;">
                    <!--<img src="<?php echo $v['images_url']; ?>" />-->
                </div>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
        </div>
        <!---->
        <div class="panel">
            <div class="title">
                <div class="name"><i class="fa fa-thumbs-o-up" style="color:#FF6666;"></i> ????????????</div>
                <div class="column-tip"><a href="<?php echo url('video/lists',array('orderCode'=>'hot')); ?>"><i class="fa fa-angle-double-right" style="font-size:1.5em;"></i></a></div>
            </div>
            <ul class="content-list">
                <?php if(!(empty($recom_list) || (($recom_list instanceof \think\Collection || $recom_list instanceof \think\Paginator ) && $recom_list->isEmpty()))): if(is_array($recom_list) || $recom_list instanceof \think\Collection || $recom_list instanceof \think\Paginator): $i = 0; $__LIST__ = $recom_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                <li>
                    <div class="v">
                        <div class="v-thumb">
                            <!--<div class="v-pic-default">
                                <img src="<?php echo $vo['thumbnail']; ?>">
                            </div>-->
                            <a class="v-link" href="<?php echo url('video/play',array('id'=>$vo['id'])); ?>">
                                <div class="v-pic-real" style="background-image:url('<?php echo $vo['thumbnail']; ?>');"></div>
                            </a>
                            <div class="v-tagrb"><span class="vv-time"><?php echo $vo['play_time']; ?></span></div>
                        </div>
                        <div class="v-metadata">
                            <div class="v-title"><?php echo $vo['title']; ?>
                            </div>
                            <div class="v-desc">
                                <i class="btn fn-bofang" title="??????"></i>
                                <span class="v-num"><?php echo $vo['click']; ?></span>
                                <span>
                                    <i class="btn fn-jinbi1" style="margin-left: 5px;"></i>
                                    <span class="v-num"><?php echo $vo['gold']; ?></span>
                                </span>&nbsp;
                                <span style="float: right;">
                                    <i class="btn fn-zan2" title="??????"></i>
                                    <span class="v-num"><?php echo $vo['good']; ?></span>
                                 </span>

                            </div>

                        </div>
                    </div>
                </li>
                <?php endforeach; endif; else: echo "" ;endif; else: ?>
                <div class="not-comment not">?????????????????? ~</div>
                <?php endif; ?>
            </ul>
        </div>
 

        <?php if(is_array($video_list) || $video_list instanceof \think\Collection || $video_list instanceof \think\Paginator): $i = 0; $__LIST__ = $video_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if(!(empty($vo['list']) || (($vo['list'] instanceof \think\Collection || $vo['list'] instanceof \think\Paginator ) && $vo['list']->isEmpty()))): ?>
        <div class="panel">
            <div class="title" style="margin-bottom: 5px;">
                <div class="name"><i class="fa fa-bars" style="color:#FF6666;"></i> <?php echo $vo['name']; ?></div>
                <div class="column-tip"><a href="<?php echo url('video/lists',array('orderCode'=>'hot')); ?>"><i class="fa fa-angle-double-right" style="font-size:1.5em;"></i></a></div>
            </div>
            <ul class="content-list">
                <?php if(is_array($vo['list']) || $vo['list'] instanceof \think\Collection || $vo['list'] instanceof \think\Paginator): $i = 0;$__LIST__ = is_array($vo['list']) ? array_slice($vo['list'],0,10, true) : $vo['list']->slice(0,10, true); if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                <li>
                    <div class="v">
                        <div class="v-thumb">
                            <a class="v-link" href="<?php echo url('video/play',array('id'=>$v['id'])); ?>">
                                <div class="v-pic-real" style="background-image:url('<?php echo $v['thumbnail']; ?>');"></div>
                            </a>
                            <div class="v-tagrb"><span class="v-time"><?php echo $v['play_time']; ?></span></div>
                        </div>
                        <div class="v-metadata">
                            <div class="v-title">
                                <a href="#"><?php echo $v['title']; ?></a>
                            </div>
                            <div class="v-desc">
                                <i class="btn fn-bofang" title="??????"></i>
                                <span class="v-num"><?php echo $v['click']; ?></span>
                                <span style="display: inline-block;">
                                    <i class="btn fn-jinbi1" style="margin-left: 5px;"></i>
                                    <span class="v-time"><?php echo $v['gold']; ?></span>
                                </span>&nbsp;&nbsp;
                                <span style="float: right;">
                                    <i class="btn fn-zan2" title="??????"></i>
                                    <span class="v-num"><?php echo $v['good']; ?></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </li>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </div>
        <?php endif; endforeach; endif; else: echo "" ;endif; ?>






<script>
    var swiper = new Swiper('.swiper-container', {
        autoplay:true,
        pagination: {
            el: '.swiper-pagination',
        },
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