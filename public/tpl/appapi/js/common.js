// 封装常用方法

var common = {
    /**
     * [loading加载]
     * @param  {object} par [需要配置的参数，hasClose是否有关闭按钮，默认没有。title文字提示]
     * @param  {Function} callback [点击关闭按钮回调函数]
     */
    loadingShow: function(par, callback) {
        par = par || {};
        par.hasClose = par.hasClose || false;
        par.title = par.title || "";
        callback = callback || function(){};
        var content = '<div class="gu_loading">'+
                        '<div class="loading_main">'+
                            '<div class="circle_bounce">'+
                                '<div class="child circle-1"></div>'+
                                '<div class="child circle-2"></div>'+
                                '<div class="child circle-3"></div>'+
                                '<div class="child circle-4"></div>'+
                                '<div class="child circle-5"></div>'+
                                '<div class="child circle-6"></div>'+
                                '<div class="child circle-7"></div>'+
                                '<div class="child circle-8"></div>'+
                                '<div class="child circle-9"></div>'+
                                '<div class="child circle-10"></div>'+
                                '<div class="child circle-11"></div>'+
                                '<div class="child circle-12"></div>'+
                            '</div>'+
                            '<div class="close">&times;</div>'+
                            '<div class="text">'+par.title+'</div>'+
                        '</div>'+
                    '</div>';
        $("body").append(content);
        if(par.title == ""){
            $(".gu_loading .text").remove();
        }
        if(!par.hasClose){
            $(".gu_loading .close").remove();
        }
        $(".gu_loading .close").click(function(){
            common.loadingHide();
            callback();
        });
    },


    /**
     * [移除loading]
     */
    loadingHide: function(){
        $(".gu_loading").remove();
    },

}