<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:38:"./tpl/mscms/mobile/index/register.html";i:1675867255;s:35:"./tpl/mscms/mobile/common/head.html";i:1569483096;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo (isset($page_title) && ($page_title !== '')?$page_title:""); ?>_<?php echo $seo['site_title']; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<link rel="stylesheet" href="__ROOT__/tpl/mscms/mobile/static/css/common.css" />
<link rel="stylesheet" href="__ROOT__/tpl/mscms/mobile/static/css/member.css">
<link rel="stylesheet" href="__ROOT__/tpl/mscms/mobile/static/css/style.css" />
<link rel="stylesheet" href="__ROOT__/tpl/mscms/static/js/layui/css/layui.css">
<script src="__ROOT__/tpl/mscms/mobile/static/js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="__ROOT__/tpl/mscms/mobile/static/js/layer_mobile/layer.js"></script>
<script type="text/javascript" charset="utf-8" src="__ROOT__/tpl/mscms/static/js/layui/layui.js"></script>
<script type="text/javascript" src="__ROOT__/tpl/mscms/mobile/static/js/common.js"></script>
<link href="__ROOT__/tpl/mscms/msvodx/awesome/css/font-awesome.css" rel="stylesheet">
<link href="__ROOT__/tpl/mscms/msvodx/css/font.css" rel="stylesheet">
<!--
<script>
layui.use(['form', 'layedit', 'laydate'], function(){ });
</script>
-->
<?php $menu = getMenu();?>
</head>
<body>
<header class="js-header">
<div  class="head">
<a id="navBackBtn" href="javascript:history.go(-1);" class="go-back"><i></i></a>
<span id="navTopTitle"></span>
<div class="menu"><i class="btn fn-sort"></i></div>
</div>
<nav class="js-nav">
<ul>
<?php if(is_array($menu) || $menu instanceof \think\Collection || $menu instanceof \think\Paginator): $i = 0; $__LIST__ = $menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
<li <?php if($vo['current'] == 1): ?>class="cur"<?php endif; ?> >
<?php echo $vo['name']; if(!(empty($vo['sublist']) || (($vo['sublist'] instanceof \think\Collection || $vo['sublist'] instanceof \think\Paginator ) && $vo['sublist']->isEmpty()))): ?>
<div class="menu-two">
<?php if(is_array($vo['sublist']) || $vo['sublist'] instanceof \think\Collection || $vo['sublist'] instanceof \think\Paginator): $i = 0; $__LIST__ = $vo['sublist'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
<a target="_self" <?php if($v['current'] == 1): ?>class="cur"<?php endif; ?> href="<?php echo $v['url']; ?>"><?php echo $v['name']; ?></a>
<?php endforeach; endif; else: echo "" ;endif; ?>
</div>
<?php endif; ?>
</a>
</li>
<?php endforeach; endif; else: echo "" ;endif; ?>
</ul>
</nav>
<div class="nav-masklayer"></div>
</header>
<div class="box">
<link rel="stylesheet" href="__ROOT__/tpl/mscms/mobile/static/css/login.css" >
<script src="__ROOT__/tpl/mscms/mobile/static/js/jquery.validate.min.js" type="text/javascript" ></script>
<div>
    <?php  $register_validate = empty(get_config('register_validate')) ? 0 : get_config('register_validate');?>
    <div class="login-box">
        <form id="regForm" action="">
            <p>
                <i class="btn fn-zhanghao"></i>
                <input type="text"  <?php if($register_validate == 1): ?>placeholder="????????????"<?php else: if($register_validate == 2): ?>placeholder="????????????"<?php else: ?>placeholder="?????????"<?php endif; endif; ?> class="name" name="username" id="username">
            </p>
            <p>
                <i class="btn fn-nicheng" style="font-size: 20px;margin-left: -3px;"></i>
                <input type="text" placeholder="????????????" class="nickname" name="nickname" id="nickname">
            </p>
            <p>
                <i class="btn fn-201"></i>
                <input type="password" placeholder="??????" class="pwd" name="password" id="password">
            </p>
            <p>
                <i class="btn fn-201"></i>
                <input type="password" placeholder="????????????" class="rePwd" name="confirm_password" id="confirm_password">
            </p>
            <?php if($register_validate != 0): ?>
            <p>
                <i class="btn fn-verification-code"></i>
                <input type="text" name="verifyCode" class="code" id="verifyCode" lay-verify="required" placeholder="?????????">
                <span id="register_code">???????????????</span>
            </p>
            <?php else: if(get_config('verification_code_on')): ?>
            <p>
                <i class="btn fn-verification-code"></i>
                <input type="text" name="verifyCode" id="verifyCode"  placeholder="?????????">
                <span id="register_code">
                      <img src="<?php echo url('api/getCaptcha'); ?>" onclick="this.src='<?php echo url('api/getCaptcha'); ?>?'+Math.random()" id="verifyCodeImg"/>
                     </span>
            </p>
            <?php endif; endif; ?>
            <button class="submit" type="submit" onclick="verifyForm()">??? ???</button>
            <div class="forget"><?php if($register_validate != 0): ?><a href="<?php echo url('member/seek_pwd'); ?>">???????????????</a><?php endif; ?><a class="register-btn" href="<?php echo url('index/login'); ?>">?????????????????????</a></div>
        </form>
    </div>
</div>

<script>
    var register_validate = "<?php echo $register_validate; ?>";
    if(register_validate == 1){
        var reg_userName = '????????????';
    }else if(register_validate == 2){
        var reg_userName = '????????????';
    }else{
        var reg_userName = '?????????';
    }
    $(function () {
        $("input").focus(function () {
            $(this).parent().css("border-bottom", "1px solid #c1dffd");
        });
        $("input").blur(function () {
            $(this).parent().css("border-bottom", "1px solid #eee");
        });

        $("#register_code").on('click', getCode);

    });

    var codeTime = 30;

    function getCodeTimer() {
        codeTime--;
        $("#register_code").unbind("click");
        $("#register_code").html('???????????????(' + codeTime + ")");
        if (codeTime <= 0) {
            $('#register_code').click(getCode).html('???????????????');
        } else {
            setTimeout(getCodeTimer, 1000);
        }
    }

    function getCode() {
        var username = $("#username").val();
        if (username == '') {
            var msgs = reg_userName+'????????????.';
            layer.open({skin: 'msg', content: msgs, time: 3});
            return false;
        }
        $("#register_code").unbind("click");
        $.post("<?php echo url('api/getRegisterCode'); ?>", {username: username,reg_username:reg_userName}, function (resp) {
            if (resp.statusCode == 0) {
                codeTime = 30;
                getCodeTimer();
//                    $("#verifyCode").focus();
                layer.open({skin: 'msg', content: resp.error, time: 3});
            } else {
//                    $("#username").focus();
                $('#register_code').click(getCode)
                layer.open({skin: 'msg', content: resp.error, time: 2});
            }
        }, 'JSON');

    }

    $.validator.addMethod('email', function (val, element) {
        var reg = /^[A-Za-zd]+([-_.][A-Za-zd]+)*@([A-Za-zd]+[-.])+[A-Za-zd]{2,5}$/;
        return this.optional(element) || isEmail(val);
    }, '??????????????????????????????');


    function verifyForm() {
        if(register_validate == 1){
            var msgs  = '????????????????????????';
        }else if(register_validate == 2){
            var msgs  = '????????????????????????';
        }else{
            var msgs  = '?????????????????????';
        }
        $('#regForm').validate({
            rules: {
                username: {
                    required: true,
                },
                nickname: {required: true},
                password: "required",
                confirm_password: {required: true, equalTo: "#password"},
            },
            messages: {
                username: {required:msgs},
                nickname: {required:"?????????????????????"},
                password: {required:"???????????????"},
                confirm_password: {
                    required: "?????????????????????",
                    equalTo: "???????????????????????????"
                },
                verifyCode: "??????????????????"
            },
            showErrors: function (errorMap, errorList) {
                var msg = "";
                $.each(errorList, function (i, v) {
                    layer.open({skin: 'msg', content: v.message, time: 2});
                    return false;
                });
            },
            onfocusout: false,
            onkeyup: false,
            debug: true,
            submitHandler: function () {
                $.post("<?php echo url('api/register'); ?>", {
                    username: $("#username").val(),
                    nickname: $("#nickname").val(),
                    password: $('#password').val(),
                    confirm_password: $("#confirm_password").val(),
                    verifyCode: $('#verifyCode').val(),
                }, function (resp) {
                    if(resp.statusCode==0){
                        layer.open({skin:'msg',content:"???????????????????????????",time:1,end:function(){
                            location.href='<?php echo url("member/member"); ?>';
                        }});
                    }else{
                        layer.open({skin:'msg',content:resp.error,time:2});
                    }
                }, 'json');
                return false;
            }
        });
    }

    $(verifyForm());

    $(function () {
        //to and footer nav setting
        var navTopTitle = "<?php echo (isset($navTopTitle) && ($navTopTitle !== '')?$navTopTitle:'?????????'); ?>";
        $('#navTopTitle').html(navTopTitle);
        $('.navFooter').removeClass('active');
        $('.navFooter:nth-child(<?php echo (isset($curFooterNavIndex) && ($curFooterNavIndex !== '')?$curFooterNavIndex:2); ?>)').addClass('active');
    });

</script>
</body>
</html>