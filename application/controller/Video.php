<?php
/**
 * 课程控制器类
 */

namespace app\controller;

use think\Db;
use think\Request;
use think\Session;


class Video extends BaseController
{
    protected function _initialize()
    {
        $this->assign('page_title', lang('Video'));
        $this->assign('navTopTitle', lang('Video'));
        $this->assign('curFooterNavIndex', 2);   //底部导航选中的序号
    }

    //兼容index直入列表页
    public function index(Request $request)
    {
        $this->redirect('lists');
    }

    //课程列表页
    public function lists(Request $request)
    {
        $pre = config('database.prefix');
        $class_sublist = array();
        $sub_cid = empty($request->param('sub_cid')) ? 0 : $request->param('sub_cid');
        $cid = $request->get('cid');
        if (!isset($cid)) {
            $cid = empty($request->param('cid')) ? 0 : $request->param('cid');
            if (!empty($cid)) {
                $menu = db::name('class')->where(array('id' => $cid))->find();
                if (!empty($menu['pid'])) {
                    $sub_cid = $cid;
                    $cid = $menu['pid'];
                }
            }
        }
        $tag_id = $request->get('tag_id');
        if (!isset($tag_id)) {
            $tag_id = empty($request->param('tag_id')) ? 0 : $request->param('tag_id');
        }
        $area_id = $request->get('area_id');
        if (!isset($area_id)) {
            $area_id = empty($request->param('area_id')) ? 0 : $request->param('area_id'); //new add
        }
        $class_list = Db::name('class')->where(array('status' => 1, 'type' => 1, 'pid' => 0))->field('id,name')->select();
        $tag_list   = Db::name('tag')->where(array('status' => 1, 'type' => 1))->select();
        $area_list  = custom_class('1'); //2019/4/22 区域 thirteen 1课程 2为图片 3为资讯
        $where = "{$pre}video.status = 1 and {$pre}video.is_check=1 and {$pre}video.pid = 0 ";
        if (!empty($cid)) {
            $class_sublist = Db::name('class')->where(array('status' => 1, 'type' => 1, 'pid' => $cid))->field('id,name')->select();
            $this->assign('sub_cid', $sub_cid);

            if (empty($sub_cid)) {
                if (empty($class_sublist)) {
                    $where .= " and {$pre}video.class = $cid";
                } else {
                    $param = array(
                        'db' => 'class',
                        'where' => array('status' => 1, 'type' => 1, 'pid' => $cid),
                        'field' => 'id',
                    );
                    $sub_array = get_field_values($param);
                    $where .= " and ({$pre}video.class = $cid or {$pre}video.class in (" . implode(',', $sub_array) . "))";
                }
            } else {
                $where .= " and {$pre}video.class = $sub_cid";
            }

        }
        if (!empty($tag_id)) {
//            $where .= " and tag = $tag_id";
            $where .= " and FIND_IN_SET($tag_id, {$pre}video.tag)";
        }
        if (!empty($area_id)) {//区域 new add
            $where .= " and FIND_IN_SET($area_id, {$pre}video.area_id)";
        }
        $orderCode = $request->get('orderCode');
        if (!isset($orderCode)) $orderCode = empty($request->param('orderCode')) ? 'lastTime' : $request->param('orderCode');
        switch ($orderCode) {
            case 'lastTime':
                $order = "update_time desc";
                break;
            case 'lastTimeASC':
                $order = "update_time asc";
                break;
            case 'hot':
                $order = "click desc";
                break;
            case 'hotASC':
                $order = "click asc";
                break;
            case 'reco':
                $order = "reco desc";
                break;
            case 'recoASC':
                $order = "reco asc";
                break;
            default:
                $order = "update_time desc";
                break;
        }

        if (in_array($orderCode, ['pay'])) {
            $video_list = Db::view("{$pre}video", 'id,title,click,good,thumbnail,play_time,reco,update_time,gold,type')
                ->view('video_watch_log', ['video_id',"count(video_watch_log.id)"=>'num'], "{$pre}video.id=video_watch_log.video_id and video_watch_log.gold > 0", 'LEFT')
                ->group("{$pre}video.id")->order('num', 'DESC')
                ->where($where)
                ->paginate(20, false, array('query' => $request->get()));
            $pages = $video_list->render();
            $count = $video_list->toArray()['total'];
        } elseif (in_array($orderCode, ['discuss'])) {//评论数
            //$where .= " and comment.resources_type = 1";
            $video_list = Db::view("{$pre}video", 'id,title,click,good,thumbnail,play_time,reco,update_time,gold,type')
                ->view('comment', ['resources_id',"count(comment.id)"=>'num'], "{$pre}video.id=comment.resources_id and comment.resources_type = 1", 'LEFT')
                ->group("{$pre}video.id")->order('num', 'DESC')
                ->where($where)
                ->paginate(20, false, array('query' => $request->get()));
            $pages = $video_list->render();
            $count = $video_list->toArray()['total'];
        } else {
            $order = empty($order) ? 'id desc' : $order;
            $count = Db::name('video')->where($where)->count();
            $video_list = Db::name('video')->where($where)->field('id,title,click,good,thumbnail,play_time,reco,update_time,gold,type')->order($order)->paginate(20, false, array('query' => $request->get()));
            $pages = $video_list->render();
        }

        $this->assign('cid', $cid);
        $this->assign('tag_id', $tag_id);
        $this->assign('class_list', $class_list);
        $this->assign('class_sublist', $class_sublist);
        $this->assign('orderCode', $orderCode);
        $this->assign('count', $count);
        $this->assign('video_list', $video_list);
        $this->assign('pages', $pages);
        $this->assign('tag_list', $tag_list);
        //new start 2019/4/22
        $this->assign('area_id', $area_id);     
        $this->assign('area_list', $area_list); 
        $this->assign('classname',custom_another('1'));
        //new end
        $this->assign('page_title', lang('VideoList'));
        $this->assign('navTopTitle', lang('VideoList'));
        return view();
    }

    //课程播放页
    public function play(Request $request)
    {

        $videoId = $request->param('id/d', 0);
        if (!$videoId) return $this->error(lang('VideoNull1'));

        $collect_info = lang('Collect');
        if (isCollected('video', $videoId)) $collect_info = lang('Collected');
        $this->assign('collect_info', $collect_info);

        //获取当前id课程信息，并浏览量++
        $videoModel = model('video')->get($videoId);
        if (!$videoModel) return $this->error(lang('VideoNull1'));


        Db::name('video')->where("id=$videoId")->setInc('click');

        $videoInfo = $videoModel->toArray();
        if (empty($videoInfo)) {
            return $this->error(lang('VideoNull1'));
        }
        if ($videoInfo['status'] != 1) {
            return $this->error(lang('VideoNull2'));
        }
        if ($videoInfo['is_check'] != 1) {
            return $this->error(lang('VideoNull3'));
        }

        //click add
        $videoModel->click++;
        $videoModel->save();

        //课程集处理逻辑
        if (isset($videoInfo['type']) && $videoInfo['type'] == 1 || $videoInfo['pid'] > 0) {
            $where = ['pid' => $videoInfo['id']];
            if ($videoInfo['pid'] > 0) $where = ['pid' => $videoInfo['pid']];

            $videoSet = Db::name('video')
                ->where($where)
                ->order('sort asc')
                ->select();
            if ($videoSet) {
                $this->assign('videoSet', $videoSet);
                //如果是课程集，那么进入后自动将第一个子课程的信息做为默认课程信息 @dreamer
                if ($videoInfo['type'] == 1) {
                    $videoInfo = $videoSet[0];
                    $this->assign('videoInfo', $videoInfo);
                }
            }
        }

        //获取课程作者
        $member_info = Db::name('member')->where(['id' => $videoInfo['user_id']])->field('nickname,headimgurl')->find();
        empty($member_info) ? $videoInfo['member'] = lang('Admin') : $videoInfo['member'] = $member_info['nickname'];
        empty($member_info) ? $videoInfo['headimgurl'] = '/static/images/user_dafault_headimg.jpg' : $videoInfo['headimgurl'] = $member_info['headimgurl'];


        //获取课程分类
        $class = Db::name('class')->where(['id' => $videoInfo['class']])->field('name,id')->find();
        if (empty($class)) {
            $videoInfo['classname'] = lang('Unknown');
            $videoInfo['classid'] = '0';
        } else {
            $videoInfo['classname'] = $class['name'];
            $videoInfo['classid'] = $class['id'];
        }
        //获取课程标签
        if (empty($videoInfo['tag'])) {
            $videoInfo['tag'] = 0;
        }
        $tag_list = Db::name('tag')->where("id in({$videoInfo['tag']})")->select();
        $videoInfo['taglist'] = $tag_list;
        $this->assign('videoInfo', $videoInfo);
        //统计该课程的打赏
        $gratuity = Db::name('gratuity_record')->where(['content_type' => 1, 'content_id' => $videoId])->select();
        $count = Db::name('gratuity_record')->where(['content_type' => 1, 'content_id' => $videoId])->field(" count(distinct user_id) as count ")->find();
        $count_price = 0;
        foreach ($gratuity as $k => $v) {
            $json_relust = json_decode($v['gift_info']);
            $count_price = $json_relust->price + $count_price;
        }
        $videoInfo['img'] = !empty($videoInfo['img']) ? json_decode($videoInfo['img'],true) : null;

        //获取推荐数据
        $params = array(
            'type' => 'video',
            'cid' => $videoInfo['class'],
        );
        $recom_list = get_recom_data($params);
        $this->assign('recom_list', $recom_list);

        //播放相关配置信息加载
        $adSetting = get_config_by_group('video');
        $this->assign('adSetting', $adSetting);
        $this->assign('web_title', $videoInfo['title'] . lang('VideoNull4'));

        //当前用户是否点赞过
        $this->assign('img', $videoInfo['img']);
        $this->assign('count', $count['count']);
        $this->assign('count_price', $count_price);
        $this->assign('isGooded', isGooded('video', $videoInfo['id']));
        $this->assign('isCollected', isCollected('video', $videoInfo['id']));


        $this->assign('page_title', $videoInfo['title']);
        $this->assign('navTopTitle', lang('VideoNull5'));
        $member_id = session('member_id');
        $isVip = 'false';
        if(!empty($member_id)){
            $member_info = get_member_info($member_id);
            $isVip = $member_info['isVip'];
        }
        //new add 1课程 2为图片 3为资讯
        $this->assign('classname',custom_another('1'));
        $this->assign('classarea',custom_classfind($videoInfo['area_id']));
        $this->assign('isVip', $isVip);
        return $this->fetch();
    }

    //课程搜索页
    public function search(Request $request)
    {

        return $this->fetch();
    }

    //异步通知课程入库
    public function syncAddVideo()
    {
        //是否开启日志输出
        $debug = false;

        $attachmentConfig = get_config_by_group('attachment');
        $videoConfig = get_config_by_group('video');
        $videoConfig = array_merge($attachmentConfig, $videoConfig);

        //参数设置
        $config = array();
        $config['videoweb'] = $videoConfig['yzm_upload_url'];           //post domain
        $config['weburl'] = $videoConfig['yzm_video_play_domain'];     //thumb domain
        $config['videourl'] = $videoConfig['yzm_video_play_domain'];   //play domain
        $config['key'] = $videoConfig['yzm_api_secretkey'];            //API密钥

        $config['istime'] = "1"; //是否开启时间转换 转码后默认时间格式为 秒 是否需要转换为 00:00:00 时间格式入库(0为不转换,1为转换)
        $config['issize'] = "1"; //是否开启文件大小转换 转码后默认时间格式为 byt 是否需要转换为 Gb Mb Kb形式(0为不转换,1为转换)
        $config['isurl'] = "0"; //是否开启url转码 即中文链接会进行转码后入库 (0为不转换,1为转换)
        //参数设置结束
        $task = file_get_contents("php://input");
        $logDir = dirname(__FILE__) . "../../../syncAddVideo.log";
        file_put_contents($logDir, "\r\n" . str_repeat('--', 50) . $task, FILE_APPEND);
        try {
            if ($task) {
                //logError('课程数据:'.$task);
                $arr = json_decode(str_replace('\\', '/', $task), true);
                if ($debug) file_put_contents($logDir, "\r\n" . var_export($arr, 1), FILE_APPEND);

                $taskid = $arr['shareid']; //old: taskid
                if (!$taskid) {
                    file_put_contents($logDir, "\r\n" . str_repeat('--', 50) . "shareid参数错误", FILE_APPEND);
                    die();
                }

                $json = file_get_contents($config['videoweb'] . "/api/gettask?id=" . $taskid . "&key=" . $config['key']);

                if ($debug) file_put_contents($logDir, "\r\n" . $config['videoweb'] . "/api/gettask?id=" . $taskid . "&key=" . $config['key'] . "\r\n" . $json, FILE_APPEND);

                //logError('课程对应数据:'.$json);
                if ($json) {
                    #if ($json == "key error.") {
                    if (!(stripos($json, 'key error') === false)) {
                        file_put_contents($logDir, "\r\nAPI密钥错误\r\n", FILE_APPEND);
                        die;
                    }
                    $varr = json_decode($json, true);

                    $videotime = $config['istime'] ? secondsToHour($varr[0]['metadata']['time']) : $varr[0]['metadata']['time'];
                    $videosize = $config['issize'] ? formatBytes($varr[0]['metadata']['length']) : $varr[0]['metadata']['length'];
                    $videoresolution = $varr[0]['metadata']['resolution']; //课程原始分辨率
                    $videopic = $varr[0]['output']['pic1'];
                    $orgfile = $varr[0]['orgfile'];
                    $rpath = addslashes(trim($varr[0]['rpath']));
                    $videopic = str_replace($varr[0]['outdir'], $config['weburl'], $videopic);
                    $videopic = str_replace('\\', '/', $videopic); //课程图片
                    $videorpath = $varr[0]['rpath']; //播放地址
                    $tarr = $arr = explode('.', $orgfile);
                    $title = addslashes(trim($tarr[0]));
                    if ($config['isurl']) {
                        $videorpath = str_replace('\\', '/', $videorpath);
                        $videorpath = urlencode($videorpath);
                        $videopic = urlencode($videopic);
                        $videopic = str_replace('%2F', '/', $videopic);
                        $videopic = str_replace('%3A', ':', $videopic);
                        $videopic = str_replace('+', ' ', $videopic);
                        $videorpath = str_replace('%2F', '/', $videorpath);
                        $videorpath = str_replace('+', ' ', $videorpath);
                    }

                    $videorpath = str_replace('\\', '/', $videorpath);

                    //获取mp4的路径地址 "rpath":"\20170721\GR1ZtJBl"
                    $rpath_arr = explode("/", $videorpath);
                    $mp4_path = $config['videourl'] . $videorpath . "/mp4/" . end($rpath_arr) . ".mp4";

                    //$videorpath=$config['videourl']."/".$videorpath."/index.m3u8";
                    $videorpath = $config['videourl'] . $videorpath . "/index.m3u8";
                    $share = $config['videourl'] . "/share/" . $taskid;

                    $videoData = [
                        'title' => $title,
                        'url' => $videorpath,
                        'download_url' => $mp4_path,
                        'play_time' => $videotime,
                        'class' => $videoConfig['sync_add_video_classid'],
                        'thumbnail' => $videopic,
                        'add_time' => time(),
                        'update_time' => time(),
                        'status' => 1
                    ];

                    if ($videoConfig['sync_add_video_need_review'] > 0) {
                        $videoData['is_check'] = 0;
                    } else {
                        $videoData['is_check'] = 1;
                    }

                    if ($debug) file_put_contents($logDir, "\r\n" . var_export($videoData, 1), FILE_APPEND);
                    Db::name('video')->insert($videoData);

                } else {
                    file_put_contents($logDir, "\r\n" . '非JSON!', FILE_APPEND);
                }
            } else {
                file_put_contents($logDir, "\r\n" . 'Hello MsvodX!', FILE_APPEND);
                die('Hello MsvodX!');
            }
        } catch (\Exception $exception) {
            file_put_contents($logDir, "\r\n" . '【同步数据发生错误】：' . $exception->getMessage(), FILE_APPEND);
        }

    }

}