function closevip() {

    $('.msvod-popover-mask').fadeOut(10);

    $('div[id=div_vip]').slideUp(20);

}

function reloadplay() {

    var src = $("iframe[id=baiduSpFrame]").attr("src");

    $("iframe[id=baiduSpFrame]").attr("src", src);

}

function clickstatus(id) {

    $("ul[id=vip_line_ul] li").removeClass("active");

    $("#" + id).addClass("active");

}

$(document).ready(function () {



//复制剪贴板

    $('#copy').zclip({

        path: "/statics/ZeroClipboard.swf",

        copy: function () {

            return $('#tg_link').val();

        }

    });


    $("#vip_default").click(function () {

        reloadplay();

        clickstatus("vip_default");

    });


//验证登录态

    $.getJSON("/index.php/api/ulog/logvip?random=" + Math.random() + "&callback=?", function (data) {

        var tg_link = $("#tg_link").val();

        var uid = "";

        if (data.status == "ok") {

            uid = data.user_id;

            if (data.vip == "yes") {

                $("#vip_1,#vip_2").click(function () {

                    reloadplay();

                    var id = $(this).attr("id");

                    clickstatus(id);

                });


            } else {

                $("#vip_1,#vip_2").click(function () {

                    $("div[id=div_vip]").toggle();

                });

            }


        } else {

            $("#vip_1,#vip_2").click(function () {

                $("div[id=div_vip]").toggle();

            });

        }

        tg_link = tg_link.replace("[1769userid]", uid);

        $("#tg_link").val(tg_link);


    });


//加载相关课程

//$.getJSON("/index.php/play/relate?vid="+vid+"&cid="+cid+"&random="+Math.random()+"&callback=?",function(data) {

//$("#related_ul").html(data.html);

//});

    if (typeof(vid) != undefined) {

        $.post("/index.php/play/relate?vid=" + vid + "&cid=" + cid + "&random=" + Math.random() + "&callback=?", function (data) {

            $("#related_ul").html(data);

        });

    }


});