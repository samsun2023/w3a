<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:35:"./tpl/mscms/mobile/video/lists.html";i:1675867255;s:37:"./tpl/mscms/mobile/common/header.html";i:1675867255;s:37:"./tpl/mscms/mobile/common/footer.html";i:1675867255;}*/ ?>
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
<link rel="stylesheet" href="__ROOT__/tpl/mscms/mobile/static/css/v-list.css">
    <div class="vault-box">
        <div class="vault-top">
            <div class="tab">
                <a href="<?php echo url('video/lists'); ?>" class="cur">??????</a>
                <a href="<?php echo url('images/lists'); ?>">??????</a>
                <a href="<?php echo url('novel/lists'); ?>">??????</a>
            </div>
            <div class="item" >
                <label>?????????</label>
                <ul id="class_box">
                    <li <?php if(empty($cid) || (($cid instanceof \think\Collection || $cid instanceof \think\Paginator ) && $cid->isEmpty())): ?>class="current" <?php endif; ?> data="0"><a href="#">??????</a></li>
                    <?php if(is_array($class_list) || $class_list instanceof \think\Collection || $class_list instanceof \think\Paginator): $i = 0; $__LIST__ = $class_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <li id="lc-<?php echo $vo['id']; ?>" data="<?php echo $vo['id']; ?>"  <?php if($cid == $vo['id']): ?>class="current"<?php endif; ?>>
                        <a href="#lc-<?php echo $vo['id']; ?>"> <?php echo $vo['name']; ?></a>
                    </li>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </ul>
            </div>
            <?php if(!(empty($class_sublist) || (($class_sublist instanceof \think\Collection || $class_sublist instanceof \think\Paginator ) && $class_sublist->isEmpty()))): ?>
            <div class="item" style="display:block;"><label>????????????</label>
                <ul id="sub_box">
                    <li <?php if(empty($sub_cid) || (($sub_cid instanceof \think\Collection || $sub_cid instanceof \think\Paginator ) && $sub_cid->isEmpty())): ?>class="current" <?php endif; ?> data="0"><a href="#">??????</a></li>
                    <?php if(is_array($class_sublist) || $class_sublist instanceof \think\Collection || $class_sublist instanceof \think\Paginator): $i = 0; $__LIST__ = $class_sublist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <li id="ls-<?php echo $vo['id']; ?>"  data="<?php echo $vo['id']; ?>"  <?php if($sub_cid == $vo['id']): ?>class="current"<?php endif; ?>>
                    <a href="#ls-<?php echo $vo['id']; ?>"  >
                        <?php echo $vo['name']; ?>
                    </a>
                    </li>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </ul>
            </div>
            <?php endif; ?>
            <div class="item">
                <label>?????????</label>
                <ul id="tag_box">
                    <li <?php if(empty($tag_id) || (($tag_id instanceof \think\Collection || $tag_id instanceof \think\Paginator ) && $tag_id->isEmpty())): ?>class="current" <?php endif; ?> data="0"><a href="#">??????</a></li>
                    <?php if(is_array($tag_list) || $tag_list instanceof \think\Collection || $tag_list instanceof \think\Paginator): $i = 0; $__LIST__ = $tag_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <li id="lt-<?php echo $vo['id']; ?>" data="<?php echo $vo['id']; ?>"  <?php if($tag_id == $vo['id']): ?>class="current"<?php endif; ?>>
                        <a href="#"><?php echo $vo['name']; ?></a>
                    </li>
                    <?php endforeach; endif; else: echo "" ;endif; ?>


                </ul>
            </div>
            <div class="item">
                <label>?????????</label>
                <ul id="orderCode">
                    <li id="lastTime" <?php if($orderCode == 'lastTime'): ?>class="current"<?php endif; ?>><a href="#">??????</a></li>
                    <li id="hot" <?php if($orderCode == 'hot'): ?>class="current"<?php endif; ?>><a href="#">??????</a></li>
                    <li id="reco" <?php if($orderCode == 'reco'): ?>class="current"<?php endif; ?>><a href="#">??????</a></li>
                </ul>
            </div>
        </div>
        <span class="select">??????</span>
    </div>
        <div class="panel">
            <ul class="content-list">
                <?php if(!(empty($video_list) || (($video_list instanceof \think\Collection || $video_list instanceof \think\Paginator ) && $video_list->isEmpty()))): if(is_array($video_list) || $video_list instanceof \think\Collection || $video_list instanceof \think\Paginator): $i = 0; $__LIST__ = $video_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                <li>
                    <a class="v" href="<?php echo url('video/play',array('id'=>$vo['id'])); ?>">
                        <div class="v-thumb">
                            <div class="v-pic-real" style="background-image:url('<?php echo $vo['thumbnail']; ?>');"></div>
                            <div class="v-tagrb"><span class="vv-time"><?php echo $vo['play_time']; ?></span></div>
                            <?php if($vo['reco'] > '0'): ?>
                            <div class="v-straw">??????</div>
                            <?php endif; if($vo['type'] == 1): ?><span class="btn fn-duoji"></span><?php endif; ?>
                        </div>
                        <div class="v-metadata">
                            <div class="v-title"><?php echo $vo['title']; ?></div>
                            <div class="v-desc">
                                <i class="btn fn-bofang" title="??????"></i>
                                <span class="v-num"><?php echo $vo['click']; ?></span>&nbsp;
                                <i class="btn fn-jinbi1" title="??????"></i>
                                <span class="v-num"><?php echo $vo['gold']; ?></span>
                                <var style="float: right;">
                                    <i class="btn fn-time" title="????????????"></i>
                                    <span class="v-num"><?php echo date('m-d',$vo['update_time']); ?></span>
                                </var>
                            </div>
                        </div>
                    </a>
                </li>
                <?php endforeach; endif; else: echo "" ;endif; else: ?>
                <div class="not-comment not">?????????????????? ~</div>
                <?php endif; ?>
            </ul>
        </div>
        <form action="" method="get" id="forms">
            <input type="hidden" id="current_orderCodes"  name="orderCode" value="<?php echo (isset($orderCode) && ($orderCode !== '')?$orderCode:'lastTime'); ?>" >
            <input type="hidden" id="current_tag_id" name="tag_id"  value="<?php echo (isset($tag_id) && ($tag_id !== '')?$tag_id:'0'); ?>" >
            <input type="hidden" id="current_sub_cid" name="sub_cid"  value="<?php echo (isset($sub_cid) && ($sub_cid !== '')?$sub_cid:'0'); ?>" >
            <input type="hidden" id="current_cid" name="cid"  value="<?php echo (isset($cid) && ($cid !== '')?$cid:'0'); ?>" >
        </form>
<script type="text/javascript">
    $(function () {
        $('#orderCode').find('li').click(function(){
            $('#current_orderCodes').val($(this).attr('id'));
            $('#forms').submit();
        })

        $('#sub_box').find('li').click(function(){
            var sub = $(this).attr('data');
            $('#current_sub_cid').val(sub);
            //$('#forms').attr('action',"#"+$(this).attr('id'));
            $('#forms').submit();
        })

        $('#class_box').find('li').click(function(){
            var cid = $(this).attr('data');
            $('#current_cid').val(cid);
            $('#current_sub_cid').val(0);
            //$('#forms').attr('action',"#"+$(this).attr('id'));
            $('#forms').submit();
        })

        $('#tag_box').find('li').click(function(){
            var tag_id = $(this).attr('data');
            $('#current_tag_id').val(tag_id);
            //$('#forms').attr('action',"#"+$(this).attr('id'));

            $('#forms').submit();
        })


    });
    var status = 1;
    var page  = 0;
    $(window).scroll(function(){
        var srollPos = $(window).scrollTop();
        var orderCode = $('#current_orderCodes').val();
        var tag_id =  $('#current_tag_id').val();
        var sub_id = $('#current_sub_cid').val();
        var cid = $('#current_cid').val();
        var data = {
            "page" : page ,
            "orderCode" : orderCode,
            "tag_id" : tag_id,
            "sub_id" : sub_id,
            "cid" : cid,
            "offset" : 20,
        };
        if(srollPos+$(window).height() > $(document).height() - 80){
            if(status == 1) {
                status = 0;
                $.ajax({
                        type: "POST",
                        data: data,
                        dataType: "JSON",
                        url: "<?php echo url('Api/moreData'); ?>",
                        success: function (data) {
                        if(data.resultCode == 0){
                            var datas = data.data.list;
                            if(datas){
                                var html = '';
                                datas.forEach(function(item) {
                                    var  url =  '/video/play/id/'+item['id'];
                                    var update_time = item['update_time'];
                                    var newDate = new Date(update_time * 1000);
                                    html += '<li>';
                                    html += '<a class="v" href="'+url+'">';
                                    html += '    <div class="v-thumb">';
                                    html += '   <img src="'+item['thumbnail']+'">';
                                    html += '    <div class="v-tagrb"><span class="v-time">'+item['play_time']+'</span></div>';
                                    if(item['reco'] > 0){
                                        html += '     <div class="v-straw">??????</div>';
                                    }
                                    html += '</div>';
                                    html += '<div class="v-metadata">';
                                    html += '   <div class="v-title">'+item['title']+'</div>';
                                    html += '    <div class="v-desc">';
                                    html += '   <i class="btn fn-bofang" title="??????"></i>';
                                    html += '   <span class="v-num">'+item['click']+'</span>&nbsp;';
                                    html += ' <i class="btn fn-jinbi1" title="??????"></i>';
                                    html += '   <span class="v-num">'+item['gold']+'</span>';
                                    html += '     <var style="float: right;">';
                                    html += '     <i class="btn fn-time" title="????????????"></i>';
                                    html += '     <span class="v-num">'+(newDate.getMonth() + 1)+'/'+newDate.getDate()+'</span>';
                                    html += ' </var>';
                                    html += ' </div>';
                                    html += ' </div>';
                                    html += ' </a>';
                                    html += ' </li>';
                                });
                                $('.not-comment').remove();
                                $('.content-list').append(html);
                                page++;
                                status = 1;
                            }else{
                                console.log('????????????????????????');
                            }
                        }else{
                            console.log('????????????????????????');
                        }
                    }
                });
            }
        }
    })
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