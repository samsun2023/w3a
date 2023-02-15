<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:24:"tpl/appapi/shareurl.html";i:1567072326;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="msapplication-task" content="name=<?php echo $sys['site_title']; ?>;icon-uri=<?php echo $sys['site_favicon']; ?>" /> 
    <link rel="Shortcut Icon" href="<?php echo $sys['site_favicon']; ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>会员宣传海报</title>
    <meta name="description" content="<?php echo $sys['site_title']; ?>" /> 
    <meta name="keywords" content="<?php echo $sys['site_title']; ?>" />
    <link rel="stylesheet" type="text/css" href="__ROOT__/tpl/appapi/css/g.css">
    <link rel="stylesheet" type="text/css" href="__ROOT__/tpl/appapi/css/download.css?v=2.1">
    <link rel="stylesheet" href="__ROOT__/tpl/appapi/css/common.css?v=1">
</head>
<style type="text/css">
    .but{
        width:80%;
        height:45px;
        line-height:45px;
        text-align:center;
        background:#393D49;
        border-radius:5px;
        margin:0 auto 40px;
        font-size:15px;
        display: block;
        color:#fff;
    }
    .bg{
        width:100%;
        background:#34394f;
        padding:20px;
        color:#fff;
        font-size:15px;
    }
    .title{
        margin:10px 0;
    }
    .content{
        border-radius:5px;
        border:1px solid #fff;
        padding:10px;
    }
    /* 兼容IOS长按保存图片 CSS3 */
    #qr_png{
        -webkit-touch-callout: default; /* displays the callout */
        -webkit-touch-callout: initial;
        -webkit-touch-callout: inherit;
        -webkit-touch-callout: unset;
    }
</style>
<body style="background:#f65249">
    <?php echo create_share_qr($uid); ?>
    <div id="wrapper">            
        <div id="content">
            <div class="bg">
                <div class="title">宣传规则：</div>
                <div class="content">
                    <p>1、用户通过你分享的二维码下载安装APP并成功运行，奖获得金币<?php echo $sys['app_sharepoint']; ?>个；</p>
                    <p>2、用户可无限次数获取分享金币；</p>
                    <p>3、每个用户终身只能被分享1次，重复安装无效；</p>
                    <p>4、更多奖励等你来拿；</p>
                </div>
            </div>
            <img id="qr_png" src="__ROOT__/qr/uis_<?php echo $uid; ?>.jpg" style="width:100%;">
            <!-- <div class="but">长按保存宣传海报</div>  -->
        </div>
    </div>
    
    <script type="text/javascript">

    </script>
</body>
</html>
