<?php
/**
 * Api接口层
 * LastDate:    2017/11/27
 */

namespace app\controller;

use app\model\Order;
use app\model\RechargePackage;
use think\Controller;
use think\Request;
use think\Db;
use UploadUtils\Uploader as UploadUtil;
use aop\AopClient;
use aop\AlipayTradeAppPayRequest;
class Appapi extends Controller
{
    private $allowFileType = ['video', 'image', 'ico'];
    private $uper = null;
    public function __construct(Request $request)
    {
        //$origin=$request->header('origin'); //"http://sp.msvodx.com"
        //$allowDomain=['msvodx.com','meisicms.com'];
        parent::__construct($request);
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With');

        $AuthAct = [];

        if (in_array(strtolower($request->action()), $AuthAct)) {
            /*
             if ($request->isPost() && $request->isAjax()) {

             } else {
                 $returnData = ['statusCode' => '4001', 'error' => '请求方式错误'];
                 die(json_encode($returnData, JSON_UNESCAPED_UNICODE));
             }*/
        }
    }

    public function _empty()
    {
        $returnData = ['Code' => '201', 'Msg' => '请求接口不存在'];
        die(json_encode($returnData, JSON_UNESCAPED_UNICODE));
    }




    /**
     * 注册接口
     * @param account  用户名or账号
     * @param pwd 密码
     * @param pid 分享代理ID
     * @param did 用户手机唯一ID
     * @date    2019/5/8
     */
    public function register(Request $request){
        if ($request->isPost()) {
            $userdb = Db::name('member');
            $data['username'] = $data['nickname'] = $request->post('account/s', '', '');
            $data['password'] = $request->post('pwd/s', '', '');

            $data['pid'] = $request->post('pid/d', '');
            $data['did'] = $request->post('did/s', '');

            if (empty($data['username'])) die(json_encode(['Code' => 201, 'Msg' => '用户名不能为空'], JSON_UNESCAPED_UNICODE));
            if (empty($data['password'])) die(json_encode(['Code' => 201, 'Msg' => '密码不能为空'], JSON_UNESCAPED_UNICODE));
            $info = $userdb->where(array('username'=>$data['username']))->find();
            if(!empty($info)) die(json_encode(['Code' => 201, 'Msg' => '该用户名已经存在'], JSON_UNESCAPED_UNICODE));

            $data['headimgurl'] = '/static/images/user_dafault_headimg.jpg';
            $data['password']   = enCode_member_password($data['password']);
            $data['last_time']  = $data['add_time'] = time();
            // 推荐人ID是否为空
            if(!empty($data['pid'])){
                // 判断上级代理是否存在
                if(!$userdb->field('id')->where(array('id'=>$data['pid'], 'is_agent'=>1))->find()){
                    // 不存在
                    $data['pid'] = '';
                }
            }
            // 验证用户手机唯一标识
            if(!empty($data['did']) && $userdb->field('did')->where(array('did'=>$data['did']))->find()){
                //die(json_encode(['Code' => 201, 'Msg' => '该手机已经注册过会员'], JSON_UNESCAPED_UNICODE));
                $data['did'] = '';
            }
            $member_id = $userdb->insertGetId($data);
            // 奖励业务处理 第四个参数1为注册其它为分享
            $reg_award = get_config('app_reg_award');
            // 该手机为第一次注册并且注册奖励大于0
            if(!empty($data['did']) && !empty($reg_award)) $this->__awardUser($userdb, $member_id, $reg_award, 1);
            return json_encode(['Code' => 200, 'Msg' => '注册成功', 'Data' => array('member_id'=>$member_id)], JSON_UNESCAPED_UNICODE);
        } else {
            die(json_encode(['Code' => 201, 'Msg' => '请以POST方式提交'], JSON_UNESCAPED_UNICODE));
        }
    }


    /**
     * 登陆接口
     * @param account  用户名or账号
     * @param pwd 密码
     * @date    2019/5/8
     */
    public function login(Request $request){
        $username = $request->post('account/s', '', '');
        $password = $request->post('pwd/s', '', '');
        if (empty($username)) die(json_encode(['Code' => 201, 'Msg' => '用户名不能为空'], JSON_UNESCAPED_UNICODE));
        if (empty($password)) die(json_encode(['Code' => 201, 'Msg' => '密码不能为空'], JSON_UNESCAPED_UNICODE));
        $where['username'] = $username;
        $where['password'] = enCode_member_password($password);
        $memberInfo = Db::name('member')->where($where)->find();
        if (!$memberInfo) die(json_encode(['Code' => 201, 'Msg' => '帐号密码错误'], JSON_UNESCAPED_UNICODE));
        if ($memberInfo['status'] == 0)    die(json_encode(['Code' => 201, 'Msg' => '您的帐户已被禁用'], JSON_UNESCAPED_UNICODE));
        return json_encode(['Code' => 200, 'Msg' => '登录成功', 'Data' => array('member_id'=>$memberInfo['id'])], JSON_UNESCAPED_UNICODE);
    }

  
  	 /**
     * 微信登陆接口
     * @param account  用户名or账号
     * @param pwd 密码
     * @date    2019/28/8
     */
    public function wechatLogin(Request $request){
        $unionid =  $request->post('unionid/s','');
        $openid =   $request->post('openid/s','');
        $nickname = $request->post('nickname/s','');
        $headimgurl = $request->post('headimgurl/s','');
        $sex = $request->post('sex','');

        $userdb = Db::name('member');
        //先判断用户是否存在
        $memberinfo = $userdb->where(['unionid'=>$unionid])->find();
        if(empty($openid))  die(json_encode(['Code' => 201, 'Msg' => '登入失败，没获取到openid！'], JSON_UNESCAPED_UNICODE));
        if(empty($memberinfo)){
            $memberinfo = $userdb->where(['openid'=>$openid])->find();
            if($memberinfo){
                $userdb->where(['openid'=>$openid])->update(['unionid'=>$unionid]);
            }
        }
      
        //用户信息入库
        if(empty($memberinfo)){
            $userdata['pid'] = $request->post('pid/d', '');
            $userdata['did'] = $request->post('did/s', '');
            // 推荐人ID是否为空
            if(!empty($userdata['pid'])){
                // 判断上级代理是否存在
                if(!$userdb->field('id')->where(array('id'=>$userdata['pid'], 'is_agent'=>1))->find()){
                    // 不存在
                    $userdata['pid'] = '';
                }
            }
            // 验证用户手机唯一标识
            if(!empty($userdata['did']) && $userdb->field('did')->where(array('did'=>$userdata['did']))->find()){
                $userdata['did'] = '';
            }
            $userdata['username']='wx_'.date('ymdHis').rand(000,999);
            $userdata['nickname']=$nickname;
            $userdata['headimgurl']=$headimgurl;
            $userdata['add_time']=time();
            $userdata['sex']= $sex;
            $userdata['last_ip']=$request->ip();
            $userdata['pid']=0;
            $userdata['openid']=$openid;
            $userdata['unionid']=$unionid;
            $info=['id'=>1,'username'=>$nickname,'password'=>''];
            $userdata['access_token']=get_token($info);

            $uid = $userdb->insertGetId($userdata);
          	// 奖励业务处理 第四个参数1为注册其它为分享
            $reg_award = get_config('app_reg_award');
            // 该手机为第一次注册并且注册奖励大于0
            if(!empty($userdata['did']) && !empty($reg_award)) $this->__awardUser($userdb, $uid, $reg_award, 1);
            return json_encode(['Code' => 200, 'Msg' => '登录成功', 'Data' => array('member_id'=>$uid)], JSON_UNESCAPED_UNICODE);
        }else if($memberinfo){
            $userdata = [
                'nickname' =>$nickname,
                'sex' => $sex,
                'headimgurl' =>$headimgurl,
            ];
            $userdb->where(['openid'=>$openid])->update($userdata);
            return json_encode(['Code' => 200, 'Msg' => '登录成功', 'Data' => array('member_id'=>$memberinfo['id'])], JSON_UNESCAPED_UNICODE);
        }else{
            return json_encode(['Code' => 201, 'Msg' => '登录失败,获取不到微信信息', 'Data' => 'null'], JSON_UNESCAPED_UNICODE);
        }
    }
  
    /**
     * 修改密码
     * @param userId  用户UID
     * @param oldPwd 旧密码
     * @param newPwd 新密码
     * @param confirm 确认密码
     * @date  6/10/2019
     */
    public function changePwd(Request $request)
    {
        if ($request->isPost()) {
            $userid   = $request->post('userId/d', '');
            $password = $request->post('oldPwd/s', '');
            $new = $request->post('newPwd/s', '');
            $confirm = $request->post('confirm/s', '');
            if(empty($userid)) die(json_encode(['Code' => 201, 'Msg' => '用户不存在或登录超时'], JSON_UNESCAPED_UNICODE));
            if(empty($password)) die(json_encode(['Code' => 201, 'Msg' => '原始密码不能为空'], JSON_UNESCAPED_UNICODE));
            $userdb = Db::name('member');
            $user = $userdb->field('password')->where(array('id' => $userid))->find();
            if(!$user) die(json_encode(['Code' => 201, 'Msg' => '用户不存在或登录超时'], JSON_UNESCAPED_UNICODE));
            if(strlen($new) < 6 || strlen($new) > 20) die(json_encode(['Code' => 201, 'Msg' => '新密码只能是6~20位字母或数字'], JSON_UNESCAPED_UNICODE));
            if($new == $password) die(json_encode(['Code' => 201, 'Msg' => '新密码不能跟原始密码一致'], JSON_UNESCAPED_UNICODE));
            if($new != $confirm) die(json_encode(['Code' => 201, 'Msg' => '两次密码输入不一致'], JSON_UNESCAPED_UNICODE));
            $md5pwd = encode_member_password($password);
            if($md5pwd != $user['password']) die(json_encode(['Code' => 201, 'Msg' => '原始密码错误'], JSON_UNESCAPED_UNICODE));
            $row = $userdb->where(array('id' => $userid))->update(array('password' => encode_member_password($new)));
            //member_logout();
            if($row){
                die(json_encode(['Code' => 200, 'Msg' => '密码修改成功'], JSON_UNESCAPED_UNICODE));
            }else{
                die(json_encode(['Code' => 201, 'Msg' => '密码修改失败'], JSON_UNESCAPED_UNICODE));
            } 
        } else {
            die(json_encode(['Code' => 201, 'Msg' => '请以POST方式提交'], JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * 首页顶部分栏接口
     * @date    2019/5/8
     */
    public function homeTab(Request $request){
        $where = array(
            'status' =>1,
            'type' =>1,
            'pid' =>0,
        );
        $list = Db::name('class')->where($where)->field('id as tabId,name as tabName')->order('sort ASC')->select();
        return  json_encode(['Code' => 200, 'Msg' => '', 'Data' => array('homeTab'=>$list)]);
    }

    /**
     * 首页数据
     * @date    2019/5/8
     */
    public function homeMain(Request $request){
      	// 页码
        $page   = $request->param('page/d', 1);
        if(empty($page)) $page = 1;
        $wheres = "name in ('app_logo','web_server_url','homepage_resource_num')";
        $config = Db::name('admin_config')->where($wheres)->column('name,value');
        // APP Logo
        $data['app_logo'] = $config['app_logo'];
        // 首页加载课程数
        //$limit  = $config['homepage_resource_num'] ? (int)$config['homepage_resource_num'] : 10;
        $limit  = 20;
        // 加载更多课程数
        $limits = $limit * $page;
        $banners= Db::name('app_ad')
                    ->where(array('status'=>1,'type'=>2))
                    ->field('url,img images_url,status target,title info')
                    ->order('sort asc')
                    ->select();
        // 默认轮播图
        $default= [array(
                'url'=>'#',
                'images_url'=>$config['web_server_url'].'/tpl/appapi/img/banner.jpg',
                'target'=>1,
                'info'=>'示例'
            )];
        // 首页轮播图
        $data['banner'] = empty($banners) ? $default : $banners;
        $videoDb= Db::name('video');
      	// 课程状态，审核状态，非剧集
        //$wheres = ['status'=>1,'is_check'=>1,'pid'=>0,'type'=>0];
      	// 课程状态，审核状态，含剧集
      	$wheres = ['status'=>1,'is_check'=>1,'pid'=>0];
        $fields = "id,title,click,good,thumbnail,play_time,gold,update_time,type,pid";
        // 最新
        $newVideos =  $videoDb->where($wheres)
                      ->order('update_time desc')
                      ->field($fields)
                      ->limit($limits)
                      ->select();
        // 热门
        $hotVideos =  $videoDb->where($wheres)
                      ->order('click desc')
                      ->field($fields)
                      ->limit($limit)
                      ->select(); 
        // 推荐
        $recomVideos= $videoDb->where(['reco'=>array('neq','0')])
                      ->where($wheres)
                      ->order('reco desc,update_time desc')
                      ->field($fields)
                      ->limit($limit)
                      ->select(); 
        $videoArray = [$newVideos, $hotVideos, $recomVideos];
        $videoTitle = ['最新课程', '热门课程', '推荐课程'];
        for ($i = 0; $i < count($videoTitle); $i++) { 
            $arrList = $videoArray[$i];
            foreach ($arrList as $k => $v) {
                // 课程集
                if ($v['type']) {
                    // 集数
                    $totals = $videoDb->field('id')->where(array('status'=>1,'is_check'=>1, 'pid' => $v['id']))->count();
                    $arrList[$k]['play_time'] = '共'.$totals.'集'; 
                    // 如果课程集数为0则去掉它
                    if(empty($totals)) unset($arrList[$k]);$arrList=array_values($arrList);
                }else{
                    // 课程时长为空则默认图标
                    if(empty($v['play_time'])) $arrList[$k]['play_time'] = '未知';
                }
            }
            $data['Videos'][] = ['title'=>$videoTitle[$i],'list'=>$arrList];
        }
        return json_encode(['Code' => 200, 'Msg' => '', 'Data' => $data], JSON_UNESCAPED_UNICODE);
    }


    /**
     * 课程详情
     * @param videoId  课程ID
     * @param userId 用户ID
     * @requestType 请求方式get
     * @date    2019/5/9
     */
    public function detail(Request $request){
        $videoId = $request->param('videoId/d', '', '');
        $userId = $request->param('userId/d', '', '');
        if (empty($videoId)) die(json_encode(['Code' => 201, 'Msg' => '非法操作'], JSON_UNESCAPED_UNICODE));
        $member = get_member_info($userId);
        //$video = Db::name('video')->where(array('id'=>$videoId))->field('title,url,thumbnail,info,short_info,gold,click,class,img')->find();
      	$videoDb = Db::name('video');
        $video = $videoDb->where(array('status'=>1, 'is_check'=>1, 'id'=>$videoId))->find();
      	if(!$video) die(json_encode(['Code' => 201, 'Msg' => '该课程不存在或已删除'], JSON_UNESCAPED_UNICODE));
        // 增加课程或课程集点击量
        $videoDb->where(['id'=>$video['id']])->setInc('click');
        // 课程集 start
        if($video['type']){
            $typeWhere = ['status'=>1,'is_check'=>1, 'pid' => $video['id']];
            if($videoDb->where($typeWhere)->count()){
                $video = $videoDb->where($typeWhere)->order('sort ASC')->find(); 
                // 增加课程点击量
                $videoDb->where(['id'=>$video['id']])->setInc('click');
            }else{
                die(json_encode(['Code' => 201, 'Msg' => '课程集正在更新中，请稍后再访问'], JSON_UNESCAPED_UNICODE));
            }
        }else{
            if(!empty($video['pid'])){
                $typeWhere = ['status'=>1,'is_check'=>1, 'pid' => $video['pid']];
                // 增加课程集点击量
                $videoDb->where(['id'=>$video['pid']])->setInc('click');
            }else{
                $typeWhere = ['id' => '0'];
            }
        }
      	$collectedWorks = $videoDb->field("id,title,thumbnail,good,add_time,gold,sort")->where($typeWhere)->order('sort ASC')->select();
        if(count($collectedWorks) > 0){
            foreach ($collectedWorks as $k => $v) {
                // 去掉JAVA分割符
                $sort = str_replace('@', '', $v['sort']);
              	// 设置剧集按钮颜色
                if(empty($sort)){
                    $sort  = "片头";
                    $color = "#8b55f6";
                }else{
                    $sort  = intval($sort);
                    $color = "#666666";
                }
                if($v['id'] == $video['id']){
                    // 当前选中课程
                    $set = $v['id']."@".""."@".$color."@".$video['id']."@".get_config('web_server_url')."/tpl/appapi/img/set_yes.gif";
                }else{
                    // 其它
                    $set = $v['id']."@".$sort."@".$color."@".$video['id']."@".get_config('web_server_url')."/tpl/appapi/img/set.png";
                }
                // 剧集列表ID@剧集排序@剧集排序颜色(标准编码)@当前选中课程ID@剧集按钮图片
                $data['collectedWorks'][] = $set;
            }
        }else{
            $data['collectedWorks'] = null;
        } // 课程集 end
      	// 课程密钥
        $video['url'] .= "?sign=" . create_yzm_play_sign();
        // 课程介绍内容输出反转义
        $video['info'] = html_entity_decode($video['info']);
      	// HTML标签列表
        $htmlTag = ['<p>','</p>','<a>','</a>','<div>','</div>'];
        // 去掉HTML标签
        foreach ($htmlTag as $k => $v) {
            $video['info'] = str_replace($v, '', $video['info']);
        }
        $data['videoCut'] = !empty($video['img']) ? json_decode($video['img'],true) : null;
        unset($video['img']);
        $data['isVip'] = $member['isVip'];
        $adSetting = get_config_by_group('video');
        //var_dump($adSetting);
        if($adSetting['ad_on'] == 1){
            if(empty($adSetting['web_pre_ad'])){
                $before = null;
                $data['adTime'] = 0;
            }else{
                $before['img'] = $adSetting['web_pre_ad'];
                $before['url'] = $adSetting['pre_ad_url'];
                $data['adTime'] = empty($adSetting['play_video_ad_time']) ? '0': $adSetting['play_video_ad_time'];
            }
            if(empty($adSetting['suspend_ad'])){
                $pause = null;
            }else{
                $pause['img'] = $adSetting['suspend_ad'];
                $pause['url'] = $adSetting['suspend_ad_url'];
            }
        }else{
            $before = $pause = null;
            $data['adTime'] = 0;
        }
        $data['ad'] = array(
            'before'=>$before,
            'pause'=>$pause,
        );
        //$data['adTime'] = empty($adSetting['play_video_ad_time']) ? '30': $adSetting['play_video_ad_time'];
        //$data['feeLook'] = ($adSetting['look_at_measurement'] == 2) ? $adSetting['look_at_num'] : '30'; //免费查看时长
        $data['feeLook'] = intval(get_config('free_time')); //免费查看时长
        $data['videoInfo'] = $video;
        $data['isShowComments'] = get_config('comment_on');
        if(!empty($userId)){
            $goodHistory = Db::name("video_good_log")->where(["video_id" => $videoId, 'user_id' => $userId])->find();
            $collectionHistory = Db::name("video_collection")->where(["video_id" => $videoId, 'user_id' => $userId])->find();
            $data['isLike'] = empty($goodHistory) ? 0 : 1;
            $data['isCollection'] = empty($collectionHistory) ? 0 : 1;
        }else{
            $data['isLike'] = 0;
            $data['isCollection'] = 0;
        }

        $params = array(
            'type' => 'video',
            'cid' => $video['class'],
          	'limit' => 6,
        );
        $data['guess'] = get_recom_data($params);


        if($data['isVip'] != 1){
            $buyTimeExists = get_config('message_validity');
            $buyTimeExists = 60 * 60 * $buyTimeExists;
            $watchHistory = Db::name('video_watch_log')
                ->where(['user_id' => $userId, 'video_id' => $videoId, 'is_try_see' => 0])
                ->order('id desc')
                ->find();

            if ($watchHistory && $watchHistory['view_time'] > (time() - $buyTimeExists)) {
                //消费周期内，免费看
                $data['alreadyBuy'] = 1;
            }else{
                $data['alreadyBuy'] = 0;
            }
        }else{
            $data['alreadyBuy'] = 1;
        }
        //var_dump($member);
        return  json_encode(['Code' => 200, 'Msg' => '', 'Data' =>$data], JSON_UNESCAPED_UNICODE);

    }

    /**
     * 课程点赞
     * @param videoId  课程ID
     * @param userId 用户ID
     * @requestType 请求方式post
     * @date    2019/5/10
     */
    public function like(Request $request){
        $videoId = $request->post('videoId/d', '', '');
        $userId = $request->post('userId/d', '', '');
        if (empty($videoId)) die(json_encode(['Code' => 201, 'Msg' => '课程id不能为空'], JSON_UNESCAPED_UNICODE));
        if (empty($userId)) die(json_encode(['Code' => 201, 'Msg' => '用户id不能为空'], JSON_UNESCAPED_UNICODE));
        $goodHistory = Db::name("video_good_log")->where(["video_id" => $videoId, 'user_id' => $userId])->find();
        if ($goodHistory) die(json_encode(['Code' => 201, 'Msg' => '您已点过赞了，无需再次点赞'], JSON_UNESCAPED_UNICODE));


        $resource = model('video');
        $dataObj = $resource::get($videoId);
        if (!$dataObj) die(json_encode(['Code' => 201, 'Msg' => '数据验证失败:资源不存在.'], JSON_UNESCAPED_UNICODE));
        $dataObj->good += 1;
        $dataObj->save();


        //写入点赞日志表
        $goodLogData = [
            'add_time' => time(),
            'video_id' => $videoId,
            'user_id' => $userId,
        ];

        Db::name("video_good_log")->data($goodLogData)->insert();
        die(json_encode(['Code' => 200, 'Msg' => '点赞成功'], JSON_UNESCAPED_UNICODE));
    }

    /**
     * 课程点赞
     * @param videoId  课程ID
     * @param userId 用户ID
     * @requestType 请求方式post
     * @date    2019/5/10
     */
    public function comment(Request $request){
        $videoId = $request->post('videoId/d', '', '');
        $userId = $request->post('userId/d', '', '');
        $content = $request->post('content/s', '', '');


        $wheres = "name in ('comment_on','comment_examine_on')";
        $config = Db::name('admin_config')->where($wheres)->column('name,value');

        if ($config['comment_on'] != 1) die(json_encode(['Code' => 201, 'Msg' => '当前暂未支持评论'], JSON_UNESCAPED_UNICODE));
        if (empty($videoId)) die(json_encode(['Code' => 201, 'Msg' => '非法操作'], JSON_UNESCAPED_UNICODE));
        if (empty($userId)) die(json_encode(['Code' => 201, 'Msg' => '请登录后再评论'], JSON_UNESCAPED_UNICODE));
        if (empty($content)) die(json_encode(['Code' => 201, 'Msg' => '内容不能为空'], JSON_UNESCAPED_UNICODE));

        $resourceType = 1;
        $resourceId = $videoId;
        $content = htmlspecialchars(trim($content), ENT_QUOTES);
        $to_user = 0;
        $to_id = 0;
        $insertData = [
            'add_time' => time(),
            'last_time' => time(),
            'resources_type' => $resourceType,
            'resources_id' => $resourceId,
            'content' => $content,
            'send_user' => $userId,
            'to_user' => $to_user,
            'to_id' => $to_id,
        ];
        if (empty($config['comment_examine_on'])) {
            $data['to_id'] = $to_id;
            $data['comment_examine_on'] = 0;
            $insertData['status'] = 1;
            $message = '评论成功';
        } else {
            $data['comment_examine_on'] = 1;
            $insertData['status'] = 0;
            $message = '评论成功,待审核后才显示';
        }
        $insertData['status'] = empty($config['comment_examine_on']) ? 1 : 0;
        $insert_result = Db::name("comment")->insertGetId($insertData);

        die(json_encode(['Code' => 200, 'Msg' => $message], JSON_UNESCAPED_UNICODE));

    }
    /**
     * 购买课程
     * @param videoId  课程ID
     * @param userId 用户ID
     * @requestType 请求方式post
     * @date    2019/5/10
     */
    public function buy(Request $request){
        $videoId = $request->post('videoId/d', '', '');
        $userId = $request->post('userId/d', '', '');
        if (empty($videoId)) die(json_encode(['Code' => 201, 'Msg' => '非法操作'], JSON_UNESCAPED_UNICODE));
        if (empty($userId)) die(json_encode(['Code' => 201, 'Msg' => '请登录后再购买'], JSON_UNESCAPED_UNICODE));


        $correlation = array(
            'user_id' => $userId,
            'is_try_see'=>0,
            'video_id'=>$videoId,
        );
        $buyTimeExists = get_config('message_validity');
        $buyTimeExists = 60 * 60 * $buyTimeExists;
        $correlation['view_time'] = ['>', time() - $buyTimeExists];
        //$watch_log  = insert_watch_log('video', $videoId, $videoInfo['gold'], false, $userId);

        $watch_log = Db::name("video_watch_log")->where($correlation)->find();
        if(empty($watch_log)){

            $videoInfo = Db::name("video")->where(array('id'=>$videoId))->find();
            $memberModel = model('member')->get($userId);
            $memberInfo = $memberModel->toArray();

            //$memberInfo = Db::name("member")->where(array('id'=>$userId))->find();
            if(empty($videoInfo))  die(json_encode(['Code' => 201, 'Msg' => '非法操作'], JSON_UNESCAPED_UNICODE));
            if(empty($memberInfo))  die(json_encode(['Code' => 201, 'Msg' => '非法操作'], JSON_UNESCAPED_UNICODE));

            if($videoInfo['gold'] > $memberInfo['money']) die(json_encode(['Code' => 201, 'Msg' => '金币不足，请先充值'], JSON_UNESCAPED_UNICODE));

            $gold = $videoInfo['gold'];

            $memberModel->money -= $gold;
            $decMoneyRs = $memberModel->save();
            //作者分成
            //author_divide('video', $videoId);
            //消费记录金币变动记录
            Db::name('gold_log')->data(['user_id'=>$userId,'gold'=>"-$gold",'add_time'=>time(),'module'=>'video','explain'=>$videoId])->insert();

            if (isset($_SERVER['HTTP_ALI_CDN_REAL_IP'])) {
                $ip = $_SERVER['HTTP_ALI_CDN_REAL_IP'];
            } else {
                $ip = \think\Request::instance()->ip();
            }
            $insertData = ["video_id" => $videoId, 'user_id' => $userId, 'user_ip' => $ip, 'view_time' => time(), 'gold' => $gold, 'is_try_see' => 0];
            Db::name("video_watch_log")->data($insertData)->insert();

            die(json_encode(['Code' => 200, 'Msg' => '购买成功'], JSON_UNESCAPED_UNICODE));
        }else{
            die(json_encode(['Code' => 201, 'Msg' => '您已经购买过了，无需重新购买'], JSON_UNESCAPED_UNICODE));
        }

    }

    public function vip(Request $request){
        $data['vip'] = get_config('vip');
        die(json_encode(['Code' => 200, 'Msg' => '', 'Data' =>$data], JSON_UNESCAPED_UNICODE));
    }

    public function introduce(Request $request){
      	$userId = $request->param('pid/d', '');
        if(empty($userId)) die(json_encode(['Code' => 201, 'Msg' => '请传参数pid：用户id'], JSON_UNESCAPED_UNICODE));
        $data['url'] = get_config('web_server_url').'/appapi/shareUrl/pid/'.$userId;
        die(json_encode(['Code' => 200, 'Msg' => '', 'Data' =>$data], JSON_UNESCAPED_UNICODE));
    }
    public function delegate(Request $request){
        $data['url'] = get_config('delegate');
        die(json_encode(['Code' => 200, 'Msg' => '', 'Data' =>$data], JSON_UNESCAPED_UNICODE));
    }

    public function videoClass(Request $request){
        $classdb = Db::name('class');
        $class_list = $classdb->where(array('status' => 1, 'type' => 1, 'pid' => 0))->field('id,name')->order("sort ASC")->select();
        $tag_list   = Db::name('tag')->where(array('status' => 1, 'type' => 1))->order("sort ASC")->select();
        $area_list  = custom_class('1');
        foreach ($class_list as $k=>$v){
            $subItem =  $all = array(
                'id'=> $v['id'],
                'name'=> '全部',
            );
            $where = array('status' => 1, 'type' => 1, 'pid' => $v['id']);
            if($class_list[$k]['subItem'] = $classdb->where($where)->field('id,name')->order("sort ASC")->select()){
                array_unshift($class_list[$k]['subItem'],$subItem);
            }else{
                $class_list[$k]['subItem'] = null; 
            } 
        }
        $all = array(
            'id'=> 0,
            'name'=> '全部',
        );
        array_unshift($class_list,$all);
        array_unshift($tag_list,$all);
        array_unshift($area_list,$all);
        $class = array(
            'name' => '分类',
            'items' => $class_list
        );
        $tag = array(
            'name' => '类型',
            'items' => $tag_list
        );
        $area = array(
            'name' => '区域',
            'items' => $area_list
        );
        $data[] = $class;
        $data[] = $tag;
        $data[] = $area;
        die(json_encode(['Code' => 200, 'Msg' => '', 'Data' =>$data], JSON_UNESCAPED_UNICODE));
    }

	/* 获取评论 */
    public function commentList(Request $request)
    {
        // 课程id
        $videoId = $request->param('videoId/d', '0', '');
        // 评论最后一条ID
        $lastId = $request->param('lastId/d', '0', '');

        $limit = $request->post('limit/d', 10);

        $commentdb = Db::name("comment");
        $where = "status = 1 and  resources_type = 1 and resources_id = $videoId and to_id = 0";
        $count = $commentdb->where($where)->count();
        $order = 'last_time desc';
        $last_info = $commentdb->where(array('id'=>$lastId))->find();
        if(!empty($last_info)){
            $where .= " and comment.last_time < ".$last_info['last_time'];
        }
        //echo  $where;exit;
        $field = 'id,send_user,content,last_time';
        $list = Db::view('comment', $field)
            ->view('member', 'username,headimgurl,nickname', 'comment.send_user=member.id', 'LEFT')
            ->where('comment.' . $where)
            ->limit($limit)
            ->order($order)
            ->select();

        //$list = Db::name("comment")->where($where)->order($order)->limit($start,$limit)->field($field)->select();
        //
        /* foreach ($list as $k => $v){
             $where1 = "to_id = ".$v['id'];
             $list[$k]['list'] =  Db::view('comment', $field)
                 ->view('member', 'username,headimgurl,nickname', 'comment.send_user=member.id', 'LEFT')
                 ->where('comment.' . $where1)
                 ->order('last_time asc')
                 ->select();
         }*/
        $data = array(
            'count'=>$count,
            'list'=>$list
        );
        if (!empty($list)) {
            die(json_encode(['Code' => 200, 'Msg' => '加载完成', 'Data' =>$data], JSON_UNESCAPED_UNICODE));
        } else {
            die(json_encode(['Code' => 200, 'Msg' => '没用更多数据了', 'Data' =>null], JSON_UNESCAPED_UNICODE));
        }
    }


    public function videoList(Request $request){
        $pre = config('database.prefix');
        $sub_cid = empty($request->param('sub_cid')) ? 0 : $request->param('sub_cid');
        $page = $request->param('page/d', 1);
      	if($page < 1) $page = 1;
        $limit = 20;
        $start = $limit * ($page-1);
        $cid = empty($request->param('cid')) ? 0 : $request->param('cid');
        if (!empty($cid)) {
            $menu = db::name('class')->where(array('id' => $cid))->find();
            if (!empty($menu['pid'])) {
                $sub_cid = $cid;
                $cid = $menu['pid'];
            }
        }
        $tag_id = empty($request->param('tag_id')) ? 0 : $request->param('tag_id');
        $area_id = empty($request->param('area_id')) ? 0 : $request->param('area_id'); //new add

		// 不含课程集
        //$where = "{$pre}video.status = 1 and {$pre}video.is_check=1 and {$pre}video.pid = 0 and {$pre}video.type = 0";
        // 含课程集
        $where = "{$pre}video.status = 1 and {$pre}video.is_check=1 and {$pre}video.pid = 0";
      	
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
        $orderCode = empty($request->param('orderCode')) ? 'lastTime' : $request->param('orderCode');
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
             case 'good':
                $order = "good desc";
                break;
            default:
                $order = "update_time desc";
                break;
        }
        $videoDb = Db::name('video');
        if (in_array($orderCode, ['pay'])) {
            $video_list = Db::view("{$pre}video", 'id,title,click,good,thumbnail,play_time,reco,update_time,gold,type')
                ->view('video_watch_log', ['video_id',"count(video_watch_log.id)"=>'num'], "{$pre}video.id=video_watch_log.video_id and video_watch_log.gold > 0", 'LEFT')
                ->group("{$pre}video.id")->order('num', 'DESC')
                ->limit($start, $limit)
                ->where($where)
                ->select();
        } elseif (in_array($orderCode, ['discuss'])) {//评论数
            //$where .= " and comment.resources_type = 1";
            $video_list = Db::view("{$pre}video", 'id,title,click,good,thumbnail,play_time,reco,update_time,gold,type')
                ->view('comment', ['resources_id',"count(comment.id)"=>'num'], "{$pre}video.id=comment.resources_id and comment.resources_type = 1", 'LEFT')
                ->group("{$pre}video.id")->order('num', 'DESC')
                ->limit($start, $limit)
                ->where($where)
                ->select();
        } else {
            $order = empty($order) ? 'id desc' : $order;
            $video_list = Db::name('video')->where($where)->field('id,title,click,good,thumbnail,play_time,reco,update_time,gold,type')->order($order)
                ->limit($start, $limit)->select();
        }
        $count = $videoDb->where($where)->count();
      	// 课程集处理
        foreach ($video_list as $k => $v) {
            // 课程集
            if ($v['type']) {
                // 集数
                $totals = $videoDb->field('id')->where(array('status'=>1,'is_check'=>1, 'pid' => $v['id']))->count();
                $video_list[$k]['play_time'] = '共'.$totals.'集'; 
                // 如果课程集数为0则去掉它
                if(empty($totals)) unset($video_list[$k]);
                // 重新排序
                $video_list= array_values($video_list);
            }else{
                // 课程时长为空则默认图标
                if(empty($v['play_time'])) $video_list[$k]['play_time'] = '未知';
            }
        }
        $data =  array(
            'count'=>$count,
            'videolist'=>$video_list,
        );
        die(json_encode(['Code' => 200, 'Msg' => '', 'Data' =>$data], JSON_UNESCAPED_UNICODE));
    }


    /**
     * 收银台
     * @param Request $request
     */
    public function pay(Request $request)
    {
        $orderSn = $request->param('orderSn');
        $order = Order::get($orderSn);
        
        if (!$order) {
            die(json_encode(['Code' => 201, 'Msg' => '订单不存在'], JSON_UNESCAPED_UNICODE));
        }
      
        if ($order->status == 1)  die(json_encode(['Code' => 201, 'Msg' => '此订单已支付，无需再支付！'], JSON_UNESCAPED_UNICODE));
        $payParams = ['orderSn' => $order->order_sn, 'price' => $order->price, 'payChannel' => $order->pay_channel,'qrcode'=>'','payName'=>''];
        
        if (strtolower($order->payment_code) == 'codepay') {
            //not native payment
            $payClass = '\\systemPay\\' . $order->payment_code;
            try {
                $payer = new $payClass();
                $payerPayRs = $payer->createPayQrcode($payParams);
            } catch (\Exception $exception) {
                $this->error($exception->getMessage());
            }
            if (isset($payerPayRs['result']) && $payerPayRs['result'] == 0) {
                $order->save(['third_id' => $payerPayRs['thirdOrderId'], 'real_pay_price' => $payerPayRs['realPayPrice']]);
            } else {
                $this->error($payerPayRs['msg'], null, '', 5);
            }
        }elseif (strtolower($order->payment_code) == 'glpay') {
            //not native payment

            $payClass = '\\systemPay\\' . $order->payment_code;
            try {
                $payer = new $payClass();
                $payerPayRs = $payer->createPayQrcode($payParams);
            } catch (\Exception $exception) {
                $this->error($exception->getMessage());
            }
            if (isset($payerPayRs['result']) && $payerPayRs['result'] == 0) {
                $order->save(['third_id' => $payerPayRs['thirdOrderId'], 'real_pay_price' => $payerPayRs['realPayPrice']]);
            } else {
                $this->error($payerPayRs['msg'], null, '', 5);
            }
            if ($payerPayRs['isJump']) {
                echo $payerPayRs['payHtml'];
                exit;
            }
        }else {
            //native payment
            $payClass = 'systemPay\\' . $order->pay_channel;
            if (file_exists(ROOT_PATH . $payClass . '.php')) return $this->error('发生错误：支付插件不存在！');
            $payClass = "\\" . $payClass;
            try {
                $payer = new $payClass();
                $payerPayRs = $payer->createPayCode($payParams);
            } catch (\Exception $exception) {
                die(json_encode(['Code' => 201, 'Msg' => $exception->getMessage()], JSON_UNESCAPED_UNICODE));
            }
            if ($payerPayRs['result'] != 0) {
                die(json_encode(['Code' => 201, 'Msg' => $payerPayRs['msg']], JSON_UNESCAPED_UNICODE));
            }

            if ($payerPayRs['isJump']) {
                echo $payerPayRs['payHtml'];
                exit;
            }
        }



        $this->assign('payerInfo', $payerPayRs);
        $this->assign('navTopTitle', '支付-收银台');
        $this->assign('payImgs', $payImgs);

        return $this->fetch('system_pay/pay');

    }


    /**
     * 订单创建
     * @param Request $request
     */
    public function createOrder(Request $request)
    {
        $buyType = $request->post('buyType/d', '0');
        $userId = $request->post('userId/d', '', '');
        $buyType = in_array($buyType, [1, 2]) ? $buyType : 1;
        if (empty($userId)) die(json_encode(['Code' => 201, 'Msg' => '参数错误用户信息获取不到！'], JSON_UNESCAPED_UNICODE));
        // 验证用户ID是否存在
        $userInfo = Db::name('member')->field('is_permanent')->where(['id' => $userId])->find();
        if (!$userInfo) die(json_encode(['Code' => 201, 'Msg' => '参数错误用户信息获取不到！'], JSON_UNESCAPED_UNICODE));

        $payCode = $request->post('payCode/s', '');
        $payCodeArr = explode('|', $payCode);
        if (count($payCodeArr) < 2) die(json_encode(['Code' => 201, 'Msg' => '支付方式参数错误，请重试！'], JSON_UNESCAPED_UNICODE));
        $price = $request->post('price/f', 0);
        if ($price <= 0) die(json_encode(['Code' => 201, 'Msg' => '支付金额必须大于0元'], JSON_UNESCAPED_UNICODE));
        $orderInfo = [
            'payment_code' => $payCodeArr[0],                   //支付方式的code
            'pay_channel' => $payCodeArr[1],                    //支付渠道：alipay qqpay wxpay
            'price' => $price,                                  //金额
            'buy_type' => $buyType,                             //购买类型，1:金币，2:vip
            'user_id' => $userId,                  //会员Id
            'from_agent_id' => 0,           	   //当前代理商id
            'from_domain' => '',                   //请求的来源网址
            'is_app' => 1,
        ];
        switch ($buyType) {
            case 1: //gold
                $gold = $request->post('gold/d', 0);
                $rate = get_config('gold_exchange_rate'); //金币兑换比例
                $orderInfo['buy_glod_num'] = !empty($gold) ? $gold : (int)$orderInfo['price'] * $rate;  //购买的金币数
                break;
            case 2: //vip
                //如果已是永久会员，则无需再充值
                if ($userInfo['is_permanent']) die(json_encode(['Code' => 201, 'Msg' => '您已是我站永久VIP,无需充值VIP!'], JSON_UNESCAPED_UNICODE));
                $packageId = $request->post('packageId/d', 0);
                $packageInfo = RechargePackage::get($packageId);
                if (!$packageInfo) {
                    die(json_encode(['Code' => 201, 'Msg' => '您要购买的套餐不存在或已关闭'], JSON_UNESCAPED_UNICODE));
                }
                //如果是购买vip套餐，那么金额以套餐金额为准
                if ($packageInfo->price != $orderInfo['price']) $orderInfo['price'] = $packageInfo->price;
                $orderInfo['buy_vip_info'] = $packageInfo->hidden(['status', 'sort'])->toJson();   //购买的vip信息
                break;
        }

        $orderInfo['order_sn'] = create_order_sn();
        $order = new Order();
        $order->save($orderInfo);
        $orderSn = $order->order_sn;
        if ($orderSn) {
            die(json_encode(['Code' => 200, 'Msg' => '下单成功', 'Data' =>$orderInfo], JSON_UNESCAPED_UNICODE));
        } else {
            die(json_encode(['Code' => 201, 'Msg' => '创建订单失败，请重试！'], JSON_UNESCAPED_UNICODE));
        }

    }

    /**
     * 搜索
     * @param Request $request
     */
    public function search(Request $request)
    {
        $key_word = htmlspecialchars(trim($request->post('key_word/s','')),ENT_QUOTES);
        $correlation = "status = 1 and is_check=1 and title like '%$key_word%'";
        $order="update_time desc";
        $count =  Db::name('video')->where($correlation)->count();
        $list = Db::name('video')->where($correlation)->order($order)->select();
        $data = array(
            'count'=>$count,
            'list'=>$list
        );
        die(json_encode(['Code' => 200, 'Msg' => '', 'Data' =>$data], JSON_UNESCAPED_UNICODE));
    }

    public function apppay(Request $requests)
    {
       	$orderSn = $requests->post('orderSn');
        $order = Order::get($orderSn);
        if (!$order) {
            die(json_encode(['Code' => 201, 'Msg' => '订单不存在'], JSON_UNESCAPED_UNICODE));
        }
        $aop = new AopClient;
 
        $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
      	$configs = db::name('app_nativepay')->where(array('pay_code'=>'aliAppPay'))->find();
      	$configs = json_decode($configs['config'],true);
      	 foreach ($configs as  $v){
           	 $config[$v['name']] = $v['value'];
        }
        $aop->appId = $config['appid'];
      	$aop->rsaPrivateKey = $config['user_private_key'];
        //$aop->rsaPrivateKey ='MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQC8B7kLu8LaSBaWdYYla+ULzhjnXdtU3if1f6K/SRppeT3u39GET/XB0eTZaNAaWCLae50WLJW+q66ml4/zLJwNUjjHrSxo1Bt4wOM4hymqoZ+1FP+0BAKxHSS7nefJf5cgu+OIrEWoAk2unmEfMZ5KysiM7yLjR0I6DF+RzRT4PQuweyFZ8lHE9nN3m82wpLWnPlL7cGXAUoqWkv2xuYcC/hnzL8LlEMubiwl3CAEEhpEskj4v6Y9ui2Oz/ht9vuEkwq5mnS7LLVKAJjPespcI9vUbBpmS+1zK937suQmpnrHHtCC5yptpKRVT3WtfjQop/TQ3B/C7c+TCQ5Ss/oFdAgMBAAECggEAb+tk4LANJmJcV4hypZmYRzOQrT63i8eEH2Oumo9H0lohXCsLCeeST6DAyAvUOqoVbNjBQu6c31ZPacezHjqCVIeJVokkcu01wOWHGiGR0ofLiTyLizoL5CKFt8sgqVZxvUE5CSQJfhyI4pRRGQEuyDP1tbWtnXzP1b+Br0ZT4gFkmO37MaNOYlT7s+kLjag3A+xM7f4jZIbshZpCMRh74slNHMGYIM6M4hqzJ6NhCyoYdV/QWBbyxVMoqx+ZMFH5Q43XlYx6NkBWkFKIY/gZMbhggZzqmH88TzrPR49zACOFc2eVwzxoumPXn6zjjC9lvEZGQioj+s1TGAx+irCfnQKBgQD6Y465gho4Jzo0gsiEav+rnlL0FrFcCk+QlwynqYAnOgcpHcfsDl7E5qlfGluWYsMOk64DaAju2bkU22PkXC7rRI6cT/4eSWSOm9fexpefEtd2jo19/NSQ5Gz2UVRddd9ohe+rpDFWcL4JeKIi2vlirbJDO+uPnCVBthLBTGjoSwKBgQDAPmxDg+ENIUKkFHjHTUxVuGNIg2Bt27faspzXLMsBMlhtvS2wWl6J+vP3gnNoz3v+WEmhSE/bhKUrqYb3GbgPgyo2dlRo+PGw0rsR3sqCnEOqc9dF4TPRY/A0V0gsmhSuZmZIesk/rPo6kMM0xUO6n8oqrs/ES+ePG6emjbiD9wKBgQC71qZCuryKtbrvm9FrrY2CJPMcVE3Xu5B26yo+OWV/iAPJL6NEjn8//b7ALtjYl9y2+ckImypgbQtw/ykQLquwKa1GzKfZ9rsuVPF8GcWzO4JSWZ0CEAMzc3ney6KbvorMMfZb8IBm1YtrNYmE1ntSMPZThAcypDQ6+KlXQ8hLwwKBgBn4FL3mEb+pT4xEq7AYlAg8WUURYjRU6vgjCqbSiTXHLETeuk5JVt4CxXQY7igpZxGLsZ6U2xzHRVypkO7OTZvi1w/2Wh64CkYdoWGfm/Ga1FUxQtJQwqQ1gNxBMiG8SrDoaka+N3NflYnVxljZkPWj4jZz2OMmcchWLwaDvHovAoGBAOighn6BKJuwcqFkWwnQQQKAErYIIAU93YQ+qh7VJmATdxqEehU7ZhOaeXI0e+48m/Vr+MpM5LeuuEOtVdbBc8qhuODHDQcJQBus9ja8BiwepTcYahKfbCR+ZmqFtvnqtlK7snNDcfztiotydzSra3wDWg1OsM9UyRlBmr7EamqK';
        $aop->charset = "UTF-8";
        $aop->signType = "RSA2";
      	$aop->alipayrsaPublicKey = $config['ali_public_key'];
        $body = $requests->post('body/s', $orderSn);
        $subject = $requests->post('subject/s', '购买金币/VIP('.$orderSn.')');
        $data = array(
            'body'=>$body,
            'subject'=>$subject,
            'out_trade_no'=>$orderSn,
            'timeout_express'=>'30m',
            'total_amount'=>$order['price'],
            'product_code'=>'QUICK_MSECURITY_PAY'
        );
        $bizcontent = json_encode($data);
		//
        $request = new AlipayTradeAppPayRequest();
        $request->setNotifyUrl("http://v.msvodx.com/pay_notify/aliapppayNotify");
        $request->setBizContent($bizcontent);
		//这里和普通的接口调用不同，使用的是sdkExecute
        $response = $aop->sdkExecute($request);
		//htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
        $data =  htmlspecialchars($response);//就是orderString 可以直接给客户端请求，无需再做处理。
        die(json_encode(['Code' => 200, 'Msg' => '获取成功','Data' => $response], JSON_UNESCAPED_UNICODE));
    }
  
  	public function wxAppPay(Request $requests){
        $orderSn = $requests->post('orderSn');
        $order = Order::get($orderSn);
        if (!$order) {
            die(json_encode(['Code' => 201, 'Msg' => '订单不存在'], JSON_UNESCAPED_UNICODE));
        }
		if($order['status'] == 1){
          	die(json_encode(['Code' => 201, 'Msg' => '订单已支付，无需再次支付'], JSON_UNESCAPED_UNICODE));
        }
        $payClass = '\\systemPay\\wxAppPay';
        $payParams['orderSn'] = $orderSn;
        $payParams['price'] = $order['price'];
        try {
            $payer = new $payClass();
            $payerPayRs = $payer->createPayCode($payParams);
            die(json_encode(['Code' => 200, 'Msg' => '下单成功','Data' => $payerPayRs], JSON_UNESCAPED_UNICODE));
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

  	public function buyCardPasswordUrl(Request $request)
    {
        $data = get_config('buy_cardpassword_uri');
        die(json_encode(['Code' => 200, 'Msg' => '获取成功','Data' => $data], JSON_UNESCAPED_UNICODE));
    }
  
 	 public function appStart(Request $request)
    {
        $wheres = "name in ('app_start_screen','app_start_time','app_start_url')";
        $config = Db::name('admin_config')->where($wheres)->column('name,value');
        die(json_encode(['Code' => 200, 'Msg' => '获取成功','Data' => $config], JSON_UNESCAPED_UNICODE));
    }
  
  	public function notice(Request $request)
    {
        $app_notice = db::name('app_notice')->where(array('status'=>1))->order('add_time desc')->field('title,content')->find();
        if(empty($app_notice)){
            $Msg = '当前没有新公告';
        }else{
            $Msg = '获取成功';
        }
        die(json_encode(['Code' => 200, 'Msg' => $Msg,'Data' => $app_notice], JSON_UNESCAPED_UNICODE));
    }
  
  	public function thirdPartyLogin(Request $request)
    {
        $logininfo = Db::name('app_login_setting')->where(['status' => 1])->field('login_code,login_name,config')->select();
        if(empty($logininfo)){
            $data = array(
                'status' => 0,
                'list' => 'null'
            );
        }else{
            $data = array(
                'status' => 1,
                'list' => $logininfo
            );
        }
        die(json_encode(['Code' => 200, 'Msg' => '获取成功','Data' => $data], JSON_UNESCAPED_UNICODE));
    }
  	
    //为防止混淆 立此  分界线------------------------------------------何-----------------------------------------------------------
	
  	
    public function giftlist()//打赏课程列表
    {
        $data = array();
        $where = empty($data['where']) ? "status = 1" : $data['where'];
        $orders = empty($data['orders']) ? 'sort DESC' : $data['orders'];
        $field = empty($data['field']) ? 'id,name,images,price,info' : $data['field'];
        $list = Db::name('gift')->where($where)->order($orders)->field($field)->select();
        die(json_encode(['Code' => 200, 'Msg' => '获取成功','Data' => $list], JSON_UNESCAPED_UNICODE));
    }
  
	/* 课程打赏 */
    public function gift(Request $request)
    {
        //判断用户参数是否合法
        $itemid = $request->post('giftId/d');
        $projectid = $request->post('videoId/d');
        $user_id = $userId = $request->post('userId/d');
        $type=1;

        if (empty($itemid)) die(json_encode(['Code' => 201, 'Msg' => 'giftId不能为空'], JSON_UNESCAPED_UNICODE));
        if (empty($projectid)) die(json_encode(['Code' => 201, 'Msg' => 'videoId不能为空'], JSON_UNESCAPED_UNICODE));
        if (empty($user_id)) die(json_encode(['Code' => 201, 'Msg' => 'userId不能为空'], JSON_UNESCAPED_UNICODE));
        //判断礼物是否存在
        $gift_info = Db::name('gift')->where(['id' => $itemid,  'status' => 1])->field('id,name,images,price,info')->find();
        if (empty($gift_info)) die(json_encode(['Code' => 201, 'Msg' => "打赏的礼物不存在！"]));
        //判断用户金币是否足够
        $user_info = model('member')->get($user_id);
        if (intval($user_info->money) < intval($gift_info['price'])) die(json_encode(['Code' => 201, 'Msg' => '你的金币不足'], JSON_UNESCAPED_UNICODE));
        //打赏记录入库
        $gift_data = [
            'user_id' => $user_id,
            'gratuity_time' => time(),
            'content_type' => $type,
            'content_id' => $projectid,
            'gift_info' => json_encode($gift_info),
            'price' => intval($gift_info['price'])
        ];
        $result = Db::name('gratuity_record')->insert($gift_data);
        if ($result) {
            $user_info->money = $user_info->money - $gift_info['price'];
            $user_info->save();
        }
        //统计该课程的打赏
        $gratuity = Db::name('gratuity_record')->where(['content_type' => $type, 'content_id' => $projectid])->select();
        $count = Db::name('gratuity_record')->where(['content_type' => $type, 'content_id' => $projectid])->field(" count(distinct user_id) as count ")->find();
        $gold_log_data = array(
            'user_id' => $user_id,
            'gold' => '-' . intval($gift_info['price']),
            'module' => 'reward',
            'explain' => $projectid
        );
        write_gold_log($gold_log_data);
        $count_price = 0;
        foreach ($gratuity as $k => $v) {
            $json_relust = json_decode($v['gift_info']);
            $count_price = $json_relust->price + $count_price;
        }
        $returndata = ['countprice' => $count_price, 'counts' => $count['count']];
        die(json_encode(['Code' => 200, 'Data' => $returndata, 'Msg' => "谢谢你,打赏成功"], JSON_UNESCAPED_UNICODE));
    }

    public function personal($userId=0)
    {
        if ($userId == 0) die(json_encode(['Code' => 201, 'Msg' => '用户未登陆'], JSON_UNESCAPED_UNICODE));
        $user_info = db::name('member')->where(array('id' => $userId))->find();
        $member = get_member_info($userId);
        if($user_info)
        {
            $user_infox['avatar']=	$user_info['headimgurl'];
            $user_infox['username']=	$user_info['username'];
            $user_infox['nickname']=	$user_info['nickname'];
            $user_infox['isVip']=	$member['isVip'];
            $user_infox['vipEndDate']=	$user_info['out_time'];
            $user_infox['money']=	$user_info['k_money'];
            $user_infox['corn']=$user_info['money'];
            if($user_info['is_permanent'] == 1){
               $user_infox['vipEndDate'] = 0;
            }
          	$user_infox['is_permanent']=$user_info['is_permanent'];
            die(json_encode(['Code' => 200, 'Msg' => '获取成功','Data' => $user_infox], JSON_UNESCAPED_UNICODE));
        }else
        {
            die(json_encode(['Code' => 201, 'Msg' => '用户不存在'], JSON_UNESCAPED_UNICODE));
        }
    }



    public function upload(Request $request){
        //var_dump($request);exit;
        $this->uper = UploadUtil::instance();
        $fileType = $request->post('fileType', '');
        $fileName = $request->post('fileName', '');
        if (empty($fileType)) die(json_encode(['Code' => 201, 'Msg' => '缺少参数：fileType .'], JSON_UNESCAPED_UNICODE));
        if (!in_array($fileType, $this->allowFileType))  die(json_encode(['statusCode' => 201, 'Msg' => '参数格式不正确：fileType ,允许的fileType只能是' . implode(',', $this->allowFileType)], JSON_UNESCAPED_UNICODE));
		
      	$web_server_url = get_config('web_server_url');
        $uploadpath = "XResource/".date("Ymd");
        //var_dump($_FILES);exit;
        $uploadname = time().rand(99999999,999999999).".".$this->__getExtension($_FILES["fileName"]["name"]);

        if (!is_dir($uploadpath)){
            $res=mkdir($uploadpath,0777,true);
        }
        move_uploaded_file($_FILES["fileName"]["tmp_name"], $uploadpath ."/".$uploadname);

        die(json_encode(['Code' => 200,'Msg'=>'上传成功', 'Data' =>$web_server_url.'/'.$uploadpath ."/".$uploadname], JSON_UNESCAPED_UNICODE));
    }

    public function __getExtension($str) {
        $i = strrpos($str,".");
        if (!$i) {
            return "";
        }
        $l = strlen($str) - $i;
        $ext = substr($str,$i+1,$l);
        return $ext;
    }



    public function updateUserInfo(Request $request)
    {
        if($this->request->isPost()) {
            $data = $request->post();
            if (empty($data['userId'])) die(json_encode(['Code' => 201, 'Msg' => 'userid不能为空'], JSON_UNESCAPED_UNICODE));
            if (empty($data['content'])) die(json_encode(['Code' => 201, 'Msg' => 'content不能为空'], JSON_UNESCAPED_UNICODE));
            $memberId=$data['userId'];

            $type =$data['type']?$data['type']:0;
            $updatefile = $data['type']==0?"headimgurl":"nickname";
            $updata[$updatefile]=$data['content'];//headimgurl  nickname
        }else
        {
            die(json_encode(['Code' => 201, 'Msg' => '非法的操作'], JSON_UNESCAPED_UNICODE));
        }
        $result = db::name('member')->where(" id=". $memberId)->update($updata);

        if ($result){
            die(json_encode(['Code' => 200, 'Msg' => '修改成功'], JSON_UNESCAPED_UNICODE));
        }
        else{
            die(json_encode(['Code' => 201, 'Msg' => '修改失败'.json_encode($where)], JSON_UNESCAPED_UNICODE));
        }

    }

    public function bankLists()
    {
        //$list = array("","北京银行","福建兴业银行","广东发展银行","广州市农村信用合作社","广州市商业银行","华夏银行","交通银行","上海浦东发展银行","深圳发展银行","兴业银行","招商银行","中国工商银行","中国建设银行","中国民生银行","中国民生银行","中国农业发展银行","中国人民银行","中国银行","中国邮政","中信实业银行");
        $list['ABC']="农业银行";
        $list['BJBANK']="北京银行";
        $list['GDB']="广东发展银行";
        //$list[]="广州市农村信用合作社";
        //$list[]="广州市商业银行";
        $list['HXBANK']="华夏银行";
        $list['COMM']="交通银行";
        $list['SPDB']="上海浦东发展银行";
        $list["SDB"]="深圳发展银行";
        $list['CIB']="兴业银行";
        $list['CMB']="招商银行";
        $list['ICBC']="中国工商银行";
        $list['CCB']="中国建设银行";
        $list['CMBC']="中国民生银行";
        //$list['']="中国农业发展银行";
        $list['PBOC']="中国人民银行";
        $list['BOC']="中国银行";
        $list['PSBC']="中国邮政";
        $list['CITIC']="中信实业银行";
        $listarr = array();
        $i=0;
        foreach($list as $k =>$v){
            $listarr[$i]['title']=$k;
            $listarr[$i]['name']=$v;
            $listarr[$i]['img']=url('/')."static/bankimgfpng/".$k.".png";
            $i++;
        }
        die(json_encode(['Code' => 200, 'Msg' => '获取成功','Data'=>$listarr], JSON_UNESCAPED_UNICODE));	
    }

    public function bankList($type=1,$userId=0)//收款方式（1.支付宝2银行卡 3微信 ）
    {
        if(intval($userId)==0){die(json_encode(['Code' => 201, 'Msg' => '参数不齐'], JSON_UNESCAPED_UNICODE));}
        //获取用户提现方式
        $momey_account=Db::name('draw_money_account')->where(['user_id'=>$userId,'type'=>$type])->order('id','desc')->field('id,title,account,img,account_name,bank')->select();
        $accountArr=array();
        foreach($momey_account as $k =>$v)
        {

            if($type==1)
            {
                $accountArr[$k]['id']=$v['id'];
                $accountArr[$k]['alipayAccount']=$v['account'];
            }

            if($type==2)
            {   
                $accountArr[$k]['carId']= $v['id'];
                $accountArr[$k]['bankName']=$v['bank'];
                $accountArr[$k]['cardNum']=substr($v['account'],-4);//$v['account'];
            }

            if($type==3)
            {
                $accountArr[$k] = $v;
            }
            $bankimg = empty($v['img']) ? 'BANK.png' : $v['img'];
            $accountArr[$k]['icon'] = url('/').'static/bankimgfpng/'.$bankimg;

        }
      	die(json_encode(['Code' => 200, 'Msg' => '成功','Data' => $accountArr], JSON_UNESCAPED_UNICODE));
    }


    public function addbank(Request $request)
    {

        if($this->request->isPost()) {
            $data = $request->post();
            if (empty($data['userId'])) die(json_encode(['Code' => 201, 'Msg' => 'userid不能为空'], JSON_UNESCAPED_UNICODE));
            if (empty($data['type'])) die(json_encode(['Code' => 201, 'Msg' => 'type不能为空'], JSON_UNESCAPED_UNICODE));
            $memberId=$data['userId'];
        }else
        {
            die(json_encode(['Code' => 201, 'Msg' => '非法的操作1'], JSON_UNESCAPED_UNICODE));
        }

        if($data['type'] == 1 || $data['type'] == 2)// 处理银行卡
        {
            if (empty($data['cardUser'])) die(json_encode(['Code' => 201, 'Msg' => 'cardUser不能为空'], JSON_UNESCAPED_UNICODE));
            if (empty($data['bankName'])) die(json_encode(['Code' => 201, 'Msg' => 'bankName不能为空'], JSON_UNESCAPED_UNICODE));
            if (empty($data['cardNum'])) die(json_encode(['Code' => 201, 'Msg' => 'cardNum不能为空'], JSON_UNESCAPED_UNICODE));
            $bank = $data['bankName'];
            if(strstr($bank, '中国银行')){
                $bankimg = "BOC.png";
            }elseif(strstr($bank, '工商')){
                $bankimg = "ICBC.png";
            }elseif(strstr($bank, '建设')){
                $bankimg = "CCB.png";
            }elseif(strstr($bank, '农业')){
                $bankimg = "ABC.png";
            }elseif(strstr($bank, '光大')){
                $bankimg = "CEB.png";
            }elseif(strstr($bank, '民生')){
                $bankimg = "CMBC.png";
            }elseif(strstr($bank, '中信')){
                $bankimg = "CITIC.png";
            }elseif(strstr($bank, '交通')){
                $bankimg = "COMM.png";
            }elseif(strstr($bank, '华夏')){
                $bankimg = "HXBANK.png";
            }elseif(strstr($bank, '招商')){
                $bankimg = "CMB.png";
            }elseif(strstr($bank, '兴业')){
                $bankimg = "CIB.png";
            }elseif(strstr($bank, '广发')){
                $bankimg = "CGB.png";
            }elseif(strstr($bank, '上海浦东')){
                $bankimg = "SPDB.png";
            }elseif(strstr($bank, '邮政')){
                $bankimg = "PSBC.png";
            }elseif(strstr($bank, '中国人民')){
                $bankimg = "PBOC.png";
            }elseif(strstr($bank, '北京银行')){
                $bankimg = "BJBANK.png";
            }elseif(strstr($bank, '广东发展')){
                $bankimg = "GDB.png";
            }elseif(strstr($bank, '深圳发展')){
                $bankimg = "SDB.png";
            }else{
                $bankimg = "BANK.png";
            }

            $account_name=$data['cardUser'];
            $bankaccount=$data['cardNum'];
            $indata['user_id'] = $memberId;
            $indata['title'] = '银行卡' . substr($bankaccount, 0, 4) . '****' . substr((int)$bankaccount, -4);
            $indata['type'] = 2;
            $indata['account'] = $bankaccount;
            $indata['account_name'] = $account_name;
            $indata['img'] = $bankimg;
            $indata['bank'] = $bank;
        }

        if($data['type'] == 3 || $data['type'] == 4)// 处理支付宝
        {
            if (empty($data['alipayAccount'])) die(json_encode(['Code' => 201, 'Msg' => 'alipayAccount不能为空'], JSON_UNESCAPED_UNICODE));
            $alipayaccount=$data['alipayAccount'];
            $account_name=$data['alipayAccount'];
            //if (empty($alipayaccount)) {die(json_encode(['Code' => 201, 'Msg' => '请填写必要的选项后再试'], JSON_UNESCAPED_UNICODE));}
            $indata['account'] = $alipayaccount;
            $indata['account_name'] = $account_name;
            $indata['img'] = "alipay.png";
            $indata['type'] = 1;
            $indata['user_id'] = $memberId;
            $indata['title'] = '支付宝' . $alipayaccount;
        }

        if($data['type'] == 1 || $data['type'] == 3)// 添加处理 1为银行卡  3 为支付宝
        {
            $result = Db::name('draw_money_account')->insert($indata);
            if ($result){
                die(json_encode(['Code' => 200, 'Msg' => '添加成功'], JSON_UNESCAPED_UNICODE));
            }
            else{
                die(json_encode(['Code' => 201, 'Msg' => '添加失败'], JSON_UNESCAPED_UNICODE));
            }
        }


        if($data['type'] == 2 || $data['type'] == 4)// 编辑处理
        {
            $idFile = $data['type'] == 2?"cardId":"alipayId";

            if (empty($data[$idFile])) die(json_encode(['Code' => 201, 'Msg' => $idFile.'不能为空'], JSON_UNESCAPED_UNICODE));

            $id = $data[$idFile];
            $where['id'] = $id;
            $where['user_id'] = $memberId;
            $where['type'] = $data['type'] == 2?2:1;// 2为银行卡   4 为支付宝  收款方式（1.支付宝2银行卡）
            $card_info = Db::name('draw_money_account')->where($where)->field("id")->find();
            if (empty($card_info)) die(json_encode(['Code' => 201, 'Msg' => '无此数据'], JSON_UNESCAPED_UNICODE));
            $result = db::name('draw_money_account')->where($where)->update($indata);

            if ($result){
                die(json_encode(['Code' => 200, 'Msg' => '修改成功'], JSON_UNESCAPED_UNICODE));
            }
            else{
                die(json_encode(['Code' => 201, 'Msg' => '修改失败'.json_encode($where)], JSON_UNESCAPED_UNICODE));
            }


        }

    }


    public function delbank(Request $request){// type 1 为支付宝  2 为银行卡

        if($this->request->isPost()) {
            $data = $request->post();
            if (empty($data['userId'])) die(json_encode(['Code' => 201, 'Msg' => 'userid不能为空'], JSON_UNESCAPED_UNICODE));
            if (empty($data['type'])) die(json_encode(['Code' => 201, 'Msg' => 'type不能为空'], JSON_UNESCAPED_UNICODE));
            $memberId=$data['userId'];
            $idFile = $data['type'] == 2?"cardId":"alipayId";
            if (empty($data[$idFile])) die(json_encode(['Code' => 201, 'Msg' => $idFile.'不能为空'], JSON_UNESCAPED_UNICODE));

        }else
        {
            die(json_encode(['Code' => 201, 'Msg' => '非法的操作1'], JSON_UNESCAPED_UNICODE));
        }

        $id = $data[$idFile];
        $where['id'] = $id;
        $where['user_id'] = $memberId;
        $where['type'] = $data['type'] == 2?2:1;// 2为银行卡   4 为支付宝  收款方式（1.支付宝2银行卡）
        $card_info = Db::name('draw_money_account')->where($where)->field("id")->find();
        if (empty($card_info)) die(json_encode(['Code' => 201, 'Msg' => '无此数据'], JSON_UNESCAPED_UNICODE));



        $result = Db::name("draw_money_account")->where($where)->delete();
        if ($result) {
            die(json_encode(['Code' => 200, 'Msg' => '删除成功'], JSON_UNESCAPED_UNICODE));
        } else {
            die(json_encode(['Code' => 201, 'Msg' => '删除失败'], JSON_UNESCAPED_UNICODE));
        }


    }


//public function alipayList(){}
//public function addalipay(){}
//public function delalipay(){}


    public function collectionList($userId=0){ //获取我收藏的课程

        if(intval($userId)==0){die(json_encode(['Code' => 201, 'Msg' => '参数不齐'], JSON_UNESCAPED_UNICODE));}
        $video_info=Db::view('video_collection','id,video_id')
            ->view('video','good,click,title,add_time,class,play_time,thumbnail,reco','video_collection.video_id=video.id')
          	->view('class','name as videoKind','video.class=class.id')
            ->where('video.status=1 and video.is_check=1 and video_collection.user_id='.$userId)
            ->paginate(10);
        $dataArr = array();
        if($video_info){
            foreach($video_info as $k =>$v){
                $dataArr[$k]['videoId']=$v['video_id'];
                $dataArr[$k]['videoTitle']=$v['title'];
                $dataArr[$k]['videoImgUrl']=$v['thumbnail'];
                $dataArr[$k]['videoSendDate']=$v['add_time'];
                $dataArr[$k]['videoKind']=$v['videoKind'];
            }
            $Code = 200;
            $Msg = "成功";
        }else
        {
            $Code = 201;
            $Msg = "您还没收藏课程哦";
        }
        die(json_encode(['Code' => $Code, 'Msg' => $Msg,'Data' => $dataArr], JSON_UNESCAPED_UNICODE));
    }

    public function addCollection($userId,$videoId)
    {
        $id=$videoId;
        $member_id=$userId;

        $db = Db::name('video');
        $collect_db = Db::name('video_collection');
        //判断存如课程id是否存在
        $result_video = $db->where(['status' => 1, 'id' => $id])->find();
        if (empty($result_video)) {
            die(json_encode(['Code' => 201, 'Msg' => '当前课程不存在'], JSON_UNESCAPED_UNICODE));
        }
        //判断课程是否已经收藏
        $result_collect = $collect_db->where(['user_id' => $member_id, 'video_id' => $id])->find();
        if ($result_collect) {
            die(json_encode(['Code' => 201, 'Msg' => '该课程已经收藏过了'], JSON_UNESCAPED_UNICODE));
        }
        //插入用户收藏日志
        $collect_data = [
            'user_id' => $member_id,
            'video_id' => $id,
            'collection_time' => time()
        ];
        $insert_result = $collect_db->data($collect_data)->insert();
        if ($insert_result) {
            die(json_encode(['Code' => 200, 'Msg' => '收藏成功'], JSON_UNESCAPED_UNICODE));
        } else {
            die(json_encode(['Code' => 201, 'Msg' => '收藏失败'], JSON_UNESCAPED_UNICODE));
        }

    }

    public function deleteCollection(Request $request){//($userId=0,$IDvideoId=0){//userId用户IDvideoId课程ID\

        if($this->request->isPost()) {
            $data = $request->post();
            if (empty($data['userId'])) die(json_encode(['Code' => 201, 'Msg' => 'userid不能为空'], JSON_UNESCAPED_UNICODE));
            if (empty($data['videoId'])) die(json_encode(['Code' => 201, 'Msg' => 'videoId不能为空'], JSON_UNESCAPED_UNICODE));
            $userId = $data['userId'];
            $IDvideoId = $data['videoId'];


        }else
        {
            die(json_encode(['Code' => 201, 'Msg' => '非法的操作1'], JSON_UNESCAPED_UNICODE));
        }


        $result = Db::name('video_collection')->where(["video_id" => $IDvideoId,"user_id" => $userId])->find();// 先看这条记录是不是它收藏的
        if(empty($result)){ die(json_encode(['Code' => 201, 'Msg' => '非法的操作2'], JSON_UNESCAPED_UNICODE)); }

        $results = Db::name("video_collection")->where(["video_id" => $IDvideoId,"user_id" => $userId])->delete();
        if ($results) {
            die(json_encode(['Code' => 200, 'Msg' => '删除成功'], JSON_UNESCAPED_UNICODE));
        } else {
            die(json_encode(['Code' => 201, 'Msg' => '删除失败'], JSON_UNESCAPED_UNICODE));
        }

    }


    public function balance($userId=0)//money余额  corn金币
    {
        if ($userId == 0) die(json_encode(['Code' => 201, 'Msg' => '用户未登陆'], JSON_UNESCAPED_UNICODE));
        $user_info = db::name('member')->where(array('id' => $userId))->find();
        if(intval($user_info['id'])==0){die(json_encode(['Code' => 201, 'Msg' => '用户不存在'], JSON_UNESCAPED_UNICODE));}
        $balanceArr['money'] =$user_info['k_money'];
        $balanceArr['corn'] =$user_info['money'];
        die(json_encode(['Code' => 200, 'Msg' => "成功",'Data' => $balanceArr], JSON_UNESCAPED_UNICODE));
    }



    public function getmoney(Request $request){//userId用户ID    cardId银行卡ID   money提现金额

        if($this->request->isPost()) {
            $data = $request->post();
            if (empty($data['userId'])) die(json_encode(['Code' => 201, 'Msg' => 'userid不能为空'], JSON_UNESCAPED_UNICODE));
            if (empty($data['cardId'])) die(json_encode(['Code' => 201, 'Msg' => 'cardId不能为空'], JSON_UNESCAPED_UNICODE));
            if (empty($data['money'])) die(json_encode(['Code' => 201, 'Msg' => 'money不能为空'], JSON_UNESCAPED_UNICODE));

            $userId = $data['userId'];
            $cardId = $data['cardId'];
            $money = $data['money'];
        }else
        {
            die(json_encode(['Code' => 201, 'Msg' => '非法的操作'], JSON_UNESCAPED_UNICODE));
        }


        if ($userId == 0) die(json_encode(['Code' => 201, 'Msg' => '用户未登陆'], JSON_UNESCAPED_UNICODE));

        $is_withdrawals = get_config('is_withdrawals');
        if (!$is_withdrawals) {
            die(json_encode(['Code' => 4005, 'Msg' => "管理员没有开启用户提现功能！"], JSON_UNESCAPED_UNICODE));
        }
        //判断用户提现频率
        $log = Db::name('draw_money_log')->where(['user_id' => $userId])->order('add_time', 'desc')->find();
        $withdrawals_frequency = get_config('withdrawals_frequency');
        $ltime = (intval($withdrawals_frequency) * 3600);
        $newtime = intval(time() - $log['add_time']);
        if ($ltime > $newtime) {
            //die(json_encode(['Code' => 201, 'Msg' => "你的提现频率过快，请隔一段时间后再试"], JSON_UNESCAPED_UNICODE));
        }
        //判断提现最低限额
        $menber_info = get_member_info($userId);
        $min_withdrawals = get_config('min_withdrawals');
        if (intval($money) < intval($min_withdrawals)) {
            die(json_encode(['Code' => 201, 'Msg' => "提现失败，最低提现数为：".intval($min_withdrawals)], JSON_UNESCAPED_UNICODE));
        }
        //判断用户提现帐户是否存在
        $money_account = Db::name('draw_money_account')->where(['id' => $cardId, 'user_id' => $userId])->find();
        if (empty($money_account)) {
            die(json_encode(['Code' => 201, 'Msg' => "你的收款帐户不存在！"], JSON_UNESCAPED_UNICODE));
        }

        if (intval($menber_info['k_money']) < intval($money)) {
            die(json_encode(['Code' => 201, 'Msg' => "提现失败：你的账户K币不足"], JSON_UNESCAPED_UNICODE));
        }
        $result = Db::name('member')->where(['id' => $userId])->setDec('k_money',$money);
        if ($result) {

            $gold_log_data = array(
                'user_id' => $userId,
                'gold' => '-' . $money,
                'module' => 'draw_money',
                'explain' => '金币提现',
                'type' => 1
            );
            write_gold_log($gold_log_data,'gold_log_k');


            $data = array(
                'user_id' => $userId,
                'gold' => $money,
                'money' => $money,
                'add_time' => time(),
                'update_time' => time(),
                'status' => 0,
                'info' => json_encode($money_account)
            );

            $insert_info = Db::name('draw_money_log')->insert($data);
            if ($insert_info) die(json_encode(['Code' => 200, 'Msg' => "你的提现申请已提交成功"], JSON_UNESCAPED_UNICODE));

        }

        die(json_encode(['Code' => 201, 'Msg' => "未知错误"], JSON_UNESCAPED_UNICODE));


    }

    /* 交易明细 */
    public function record($userId=0, $IDtype=0, $lastId=0){ //userId用户IDtype类型  0消费明细  1充值明细   2提现  3推广金币   4提成收入  lastId最后一条记录ID
        if ($userId == 0) die(json_encode(['Code' => 201, 'Msg' => '用户未登陆'], JSON_UNESCAPED_UNICODE));
      	$dbs = array('gold_log','order','draw_money_log','gold_log','gold_log_k');
        $titles = array('消费明细','充值明细','提现明细','推广金币','提成收入');
        $whereArr = "user_id = ".$userId;//array('user_id'=>$userId);
        $orderstr = "add_time";
        if(empty($IDtype)){
            //$orderstr = "view_time";
            $whereArr .= " and gold < 0";
        }
        $sid = $IDtype== 1 ? "order_sn" : "id";
        if($IDtype ==3){
            $whereArr .= " and gold > 0";
        }
       	if($IDtype == 4){
            $whereArr .= " and `explain` in ('分销提成','代理分成')";
        }
        if($lastId != 0){
            $whereArr .= " and {$sid} < {$lastId}";
        }
        $list = Db::name($dbs[$IDtype])->where($whereArr)->order($orderstr,'desc')->limit(10)->select();
        $listArr = [];
        if($list){
            foreach($list  as $k =>$v)
            {
                $listArr[$k]['recordId'] = $listArr[$k]['orderID'] = $v[$sid];
                $listArr[$k]['date'] = $v[$orderstr];
                $result = $this->__record_remark($IDtype, $v, $orderstr);
                $listArr[$k]['title']  = $result['title'];
                $listArr[$k]['remark'] = $result['remark'];
                $listArr[$k]['content']= $result['content'];
                if($IDtype==1)
                {
                    $statusArr=array('未支付','已支付');//0：未支付，1：已支付  订单金额 price
                    $listArr[$k]['status'] = $statusArr[$v['status']];
                }

                if($IDtype==2)
                {
                    $statusArr=array('未处理','已完成','已拒绝');
                    $listArr[$k]['status'] = $statusArr[$v['status']];
                }

            }
        }
        die(json_encode(['Code' => 200, 'Msg' => "成功",'Data' => $listArr], JSON_UNESCAPED_UNICODE));
    }

    public function  __record_remark($idtyp=0, $row=array(), $orderstr)
    {
        $result = [];
        if($idtyp==0)
        {
            switch ($row['module']) {
                // 观看课程
                case 'video':
                    $videoinfo = Db::name('video')->field('title')->where(array('id'=>$row['explain']))->find();
                    $result['title']  = '观看课程';
                    $result['remark'] = $videoinfo['title'];
                    break;
                // 提现
                case 'draw_money':
                    $result['title']  = '余额提现';
                    $result['remark'] = '扣除'.ltrim($row['gold'], '-').'金币';
                    break;
                default: // reward 打赏
                    $videoinfo = Db::name('video')->field('title')->where(array('id'=>$row['explain']))->find();
                    $result['title']  = '课程打赏';
                    $result['remark'] = $videoinfo['title'];
                    break;
            }
            $result['content'] = $row['gold']." 金币";
        }

        if($idtyp==1)
        {
            $buyArr = array('','充值金币','购买vip');//购买类型，1:金币，2:vip
            $statusArr=array('未支付','已支付');//0：未支付，1：已支付  订单金额 price
          	if($row['buy_type'] == 1){
                $str = $row['buy_glod_num'].'金币';
            }else{
                $buy_vip_info = \Qiniu\json_decode($row['buy_vip_info'],true);
                $str = $buy_vip_info['name'];
            }
            $remark_str = $str;//"观看课程ID ".$row['video_id']." 扣除金币".$row['video_id'];
            $result['title']  = $buyArr[$row['buy_type']];
            $result['remark'] = $remark_str;
            $result['content'] = $row['price']."元";
        }

        if($idtyp==2)
        {
            $result['remark'] = $result['title'] = "金币提现";
            $result['content'] = '-'.$row['gold']." 元";
        }

        if($idtyp==3)
        {
            $remark_str = $row['explain'];
            $result['remark'] = $result['title'] = $remark_str;
            $result['content'] = '+'.$row['gold']." 金币";
        }

        if($idtyp==4)
        {
           	$remark_str = $row['explain'];
            $result['title'] = '代理提成';
            $result['remark'] = $remark_str;
            $result['content'] = '+'.$row['gold']." 金币";
        }
        return $result;
    }

    public function td(Request $request){
        $userId = $request->param('userId/d', '', '');
        if (empty($userId)) die(json_encode(['Code' => 201, 'Msg' => '非法操作'], JSON_UNESCAPED_UNICODE));
        $wheres = "name in ('td_text','td_bgimg')";
        $config = Db::name('admin_config')->where($wheres)->column('name,value');
        $config['td_code'] = get_config('web_server_url').'/appapi/download/pid/'.$userId;
        die(json_encode(['Code' => 200, 'Msg' => '', 'Data' =>$config], JSON_UNESCAPED_UNICODE));
    }

    public function tdRecord($userId=0){//推广记录  我的下级代理
        global $tdidlist,$idsk,$todaytal;
        $userid = (int)$userId;
        if(empty($userid)) die(json_encode(['Code' => 201, 'Msg' => '请传参数：用户userId'], JSON_UNESCAPED_UNICODE));
        $this->__tdIds($userid);
        die(json_encode(['Code' => 200, 'Msg' => "成功",'Data' => array('total'=>$idsk,'today'=>$todaytal,'list'=>$tdidlist)], JSON_UNESCAPED_UNICODE));
    }


    public function __tdIds($ids)
    {
        global $tdidlist,$idsk,$todaytal;
        $shareDb = Db::name('app_share_log');
        $tdidlist = $td_list = $shareDb->field('id,gold,share_time as date,share_type')->where("user_id='$ids'")->order('id desc')->select();
        $idsk = count($td_list);
        $todaytal = $shareDb->where("user_id='$ids'")->whereTime('share_time', 'today')->count();
        foreach($td_list as $k => $v)
        {
            if($v['share_type']==1){
                $tdidlist[$k]['name'] = '增加['.$v['gold'].']个金币';
            }else{
                $tdidlist[$k]['name'] = '获得['.$v['gold'].']天Vip服务';
            }
        }
    }

    public function chargeType(){//充值方式
        $curSysPayCode = get_config('system_payment_code');
      	//echo $curSysPayCode;
        $themeBasename = "default";
        //支付方式列表

        //echo url('/');
        $paymentList = [];
        switch ($curSysPayCode) {
            case 'codePay':
                $codePayInfo = Db::name('payment')->where("pay_code='{$curSysPayCode}'")->find();
                if (!$codePayInfo || $codePayInfo['status'] != 1) {
                    $this->error('当前支付不可用，请联络管理员解决！', null, '', 10);
                }

                $paymentList[] = ['payName' => '支付宝支付', 'payCode' => 'codePay|aliPay', 'payIcon' => url('/').'/tpl/'.$themeBasename.'/static/images/Alipay.png'];
                $paymentList[] = ['payName' => '微信支付', 'payCode' => 'codePay|wxPay', 'payIcon' => url('/').'/tpl/'.$themeBasename.'/static/images/WeChat.png'];
                $paymentList[] = ['payName' => 'QQ钱包', 'payCode' => 'codePay|qqPay', 'payIcon' => url('/').'/tpl/'.$themeBasename.'/static/images/QQ.png'];
                break;
            case 'glpay':
                $codePayInfo = Db::name('payment')->where("pay_code='{$curSysPayCode}'")->find();
                if (!$codePayInfo || $codePayInfo['status'] != 1) {
                    $this->error('当前支付不可用，请联络管理员解决！', null, '', 10);
                }
                $paymentList[] = ['payName' => '支付宝支付', 'payCode' => 'glpay|aliPay', 'payIcon' => url('/').'tpl/'.$themeBasename.'/static/images/Alipay.png'];
                //$paymentList[] = ['payName' => '微信支付', 'payCode' => 'glpay|wxPay', 'payIcon' => url('/').'tpl/'.$themeBasename.'/static/images/WeChat.png'];
                break;
            case 'haoPay':
                $PayInfo = Db::name('payment')->where("pay_code='{$curSysPayCode}'")->find();
                if (!$PayInfo || $PayInfo['status'] != 1) {
                    $this->error('当前支付不可用，请联络管理员解决！', null, '', 10);
                }
                $paymentList[] = ['payName' => '支付宝支付', 'payCode' => 'haoaPay|aliPay', 'payIcon' => url('/').'/tpl/'.$themeBasename.'/static/images/Alipay.png'];
                $paymentList[] = ['payName' => '微信支付', 'payCode' => 'haoaPay|wxPay', 'payIcon' => url('/').'/tpl/'.$themeBasename.'/static/images/WeChat.png'];
                $paymentList[] = ['payName' => 'QQ钱包', 'payCode' => 'haoaPay|qqPay', 'payIcon' => url('/').'/tpl/'.$themeBasename.'/static/images/QQ.png'];
                break;

        }

		$where = ['status' => 1];
        $nativeList = Db::name('app_nativepay')->where($where)->select();
        foreach ($nativeList as $item) {
          switch ($item['pay_code']) {
            case 'wxAppPay':
              $paymentList[] = ['payName' => '微信支付', 'payCode' => 'nativePay|wxAppPay', 'payIcon' => url('/').'/tpl/'.$themeBasename.'/static/images/WeChat.png'];
              break;
            case 'aliAppPay':
              $paymentList[] = ['payName' => '支付宝支付', 'payCode' => 'nativePay|aliAppPay', 'payIcon' => url('/').'/tpl/'.$themeBasename.'/static/images/Alipay.png'];
              break;
            case 'paypal':
              $paymentList[] = ['payName' => 'paypal', 'payCode' => 'nativePay|paypal', 'payIcon' =>  url('/').'/tpl/'.$themeBasename.'/static/images/Paypal.png'];
              break;
          }
        }


        die(json_encode(['Code' => 200, 'Msg' => "成功",'Data' => $paymentList], JSON_UNESCAPED_UNICODE));
    }


    public function chargeData(){//充值数据

        $gold_exchange_rate=get_config('gold_exchange_rate');
        //整理套餐数据
        $rechargeList = Db::name('recharge_package')->where('status=1')->order('sort asc')->select();
        $rechargeArr = array();
        foreach($rechargeList as $k =>$v)
        {
            $rechargeArr[$k]['vipId']=$v['id'];
            $rechargeArr[$k]['title']=$v['name'];
            $rechargeArr[$k]['money']=$v['price'];
            $rechargeArr[$k]['time']=$v['days'];
            $rechargeArr[$k]['tips']="";
        }
        //金币套餐数据
        $goldPackageList = Db::name('gold_package')->select();
        $goldArr = array();
        foreach($goldPackageList as $k =>$v)
        {
            $goldArr[$k]['cornId']=$v['id'];
            $goldArr[$k]['title']=$v['name'];
            $goldArr[$k]['money']=$v['price'];
            $goldArr[$k]['corn']=$v['gold'];
            $goldArr[$k]['tips']="";
            //$goldArr[$k]['cornCal']=$gold_exchange_rate;
        }
        die(json_encode(['Code' => 200, 'Msg' => "成功",'Data' => array('vip'=>$rechargeArr,'corn'=>$goldArr,'cornCal'=>$gold_exchange_rate)], JSON_UNESCAPED_UNICODE));

    }



    public function chargeSearch(Request $request){
        $card_number = $request->post('cardNum/s');//cardNum
        //$verifyCode = $request->post('verifyCode/s', '', '');
        if (empty($card_number)) die(json_encode(['Code' => 201, 'Msg' => '数据验证失败:卡号不能为空'], JSON_UNESCAPED_UNICODE));
        $field = 'id,card_number,card_type,out_time,status,price,gold,vip_time';
        $where['card_number'] = $card_number;
        $info = Db::name('card_password')->where($where)->field($field)->find();
        if (empty($info)) die(json_encode(['Code' => 201, 'Msg' => '数据验证失败:该卡不存在'], JSON_UNESCAPED_UNICODE));
        $info['out_times'] = ($info['out_time'] < time()) ? '已过期' : date('Y-m-d H:i', $info['out_time']);

        $statusArr = array('未使用','已使用');//状态，0未使用，1已使用
        $typeArr=array('','VIP卡','金币卡');//卡类型1为vip卡，2为金币卡
        $infox['cardType'] = $typeArr[$info['card_type']];
        $infox['vipTime'] = $info['vip_time'];
        $infox['chargeCorn'] = $info['price'];
        $infox['endTime'] = $info['out_time'];
        $infox['cardStatus'] = $statusArr[$info['status']];
        die(json_encode(['Code' => 200, 'Msg' => '查询成功', 'Data' => $infox], JSON_UNESCAPED_UNICODE));

    }



    public function charge(Request $request){//userId用户IDcardNum卡号
        if($this->request->isPost()) {
            $data = $request->post();
            if (empty($data['userId'])) die(json_encode(['Code' => 201, 'Msg' => 'userId不能为空'], JSON_UNESCAPED_UNICODE));
            if (empty($data['IDcardNum'])) die(json_encode(['Code' => 201, 'Msg' => 'IDcardNum不能为空'], JSON_UNESCAPED_UNICODE));
            $memberId = $data['userId'];
            $card_number = $data['IDcardNum'];
        }else
        {
            die(json_encode(['Code' => 201, 'Msg' => '非法的操作'], JSON_UNESCAPED_UNICODE));
        }
        // 处理充值

        $field = 'id,card_number,card_type,out_time,status,price,gold,vip_time';
        $where['card_number'] = $card_number;
        //  $where['id'] = $id;
        $info = Db::name('card_password')->where($where)->field($field)->find();
        if (empty($info)) die(json_encode(['Code' => 201, 'Msg' => '数据验证失败:信息不匹配'], JSON_UNESCAPED_UNICODE));
        if ($info['status'] == 1) die(json_encode(['Code' => 201, 'Msg' => '该卡密已经使用过了'], JSON_UNESCAPED_UNICODE));
        if ($info['out_time'] < time()) die(json_encode(['Code' => 201, 'Msg' => '该卡密已过期了'], JSON_UNESCAPED_UNICODE));
        if ($info['card_type'] == 1) {
            $userinfo = db::name('member')->where(array('id' => $memberId))->field('out_time,is_permanent')->find();
            if ($userinfo['is_permanent'] == 1) die(json_encode(['Code' => 201, 'Msg' => '您无需再充值vip'], JSON_UNESCAPED_UNICODE));
            if ($info['vip_time'] == 999999999) {
                db::name('member')->where(array('id' => $memberId))->update(array('is_permanent' => 1));
            } else {
                if ($userinfo['out_time'] < time()) {
                    $out_time = time() + $info['vip_time'] * 3600 * 24;
                } else {
                    $out_time = $userinfo['out_time'] + $info['vip_time'] * 3600 * 24;
                }
                db::name('member')->where(array('id' => $memberId))->update(array('out_time' => $out_time));
            }
            db::name('card_password')->where($where)->update(array('status' => 1, 'use_time' => time()));
        } else {
            // 增加卡金额
            db::name('member')->where(array('id' => $memberId))->setInc('money', $info['gold']);
            db::name('card_password')->where($where)->update(array('status' => 1, 'use_time' => time()));
            $gold_log_data = array(
                'user_id' => $memberId,
                'gold' => intval($info['gold']),
                'module' => 'card_password',
                'explain' => '卡密充值'
            );
            write_gold_log($gold_log_data);
        }
        // 分销商分成
        //distributor_divide(array('id'=>$memberId,'gold'=>$info['price']));
        // 代理商提成
        //cur_agent_divide($memberId,$info['price']);//消费者ID，消费金额
        die(json_encode(['Code' => 200, 'Msg' => '充值成功'], JSON_UNESCAPED_UNICODE));

    }

	/* 获取APP最新版号，APP下载地址 */
    public function getVersion(Request $request)
    {
        // 分享者ID
        $pid = $request->param('pid/d', '');
        // 用户手机唯一标识
        $did = $request->param('did/s', '');
        // 分享业务处理
        if(!empty($pid) && !empty($did)) $this->__shareUser($pid, $did);
        // 安卓版本号
        $data['apk_version'] = get_config('apk_version');
        // 安卓APP更新地址
        $data['android'] = get_config('app_android');
        // 安卓更新内容
        //$data['apk_update'] =  '用户ID：'.$pid.'设备ID：'.$did;
        $data['apk_update'] = get_config('app_update_apk');
        // 苹果版本号
        $data['ios_version'] = get_config('ios_version');
        // 苹果APP更新地址
        $data['apple']   = get_config('app_apple');
        // 苹果更新内容
        $data['ios_update'] = get_config('app_update_ios');
        //$data['ios_update'] = '用户ID：'.$pid.'设备ID：'.$did;
        // 返回数据
        die(json_encode(['Code' => 200, 'Msg' => '获取成功', 'Data' => $data], JSON_UNESCAPED_UNICODE));
    }

	/* 获取APP广告 id => 1为首页2为分类页3为播放页 */
    public function getAd(Request $request)
    {
        $adid = $request->param('id/d', 1);
        if($adid > 3 || $adid < 1) die(json_encode(['Code' => 201, 'Msg' => '非法广告id，1为首页2为分类页3为播放页'], JSON_UNESCAPED_UNICODE));
        // 查询广告
        $adinfo = Db::name('app_ad')->where("id='$adid' and type=1 and status=1")->find();
      	if(!$adinfo) $adinfo = null;
        // 返回数据
        die(json_encode(['Code' => 200, 'Msg' => '获取成功', 'Data' => $adinfo], JSON_UNESCAPED_UNICODE));
    }

	/* 使用系统代理模板内容接口 */
    public function htmlContent(Request $request)
    {
        $typeid = $request->param('type/d', 1);
        switch ($typeid) {
            // 代理内容模板
            case '1':
                $html['title']   = '成为代理';
                $html['content'] = get_config('app_delegate_view');
                $html['color']   = get_config('app_color_a');
                break;
            // 会员内容模板
            default:
                $html['title']   = '会员制度';
                $html['content'] = get_config('app_vip_view');
                $html['color']   = get_config('app_color_c');
                break;
        }
        $html['content'] = html_entity_decode($html['content']);
        $this->assign('html',$html);
        return $this->fetch('tpl/appapi/index.html');
    }
  
  	/* 会员分享业务处理 */ 
    public function __shareUser($pid, $did)
    {
        // 分享奖励
        $sharePoint = (int)get_config('app_sharepoint');
        if($sharePoint > 0){
            $userDb = Db::name('member');
            // 验证分享者ID是否存在并且不能是代理
            if($userDb->field('id')->where(array('id' => $pid, 'is_agent' => 0))->find()){
                $shareDb = Db::name('app_share_log');
                // 验证用户手机唯一标识是否已分享过，防重复分享
                $shareLog = $shareDb->field('id')->where(array('did' => $did))->find();
                // 新用户
                if(!$shareLog){
                    // 验证手机唯一标识是否被注册
                    $regDid = $userDb->field('id')->where(array('did' => $did))->find();
                    // 未被注册
                    if(!$regDid){
                        // 奖励业务处理
                        $this->__awardUser($userDb, $pid, $sharePoint);
                        // 金币/VIP天数
                        $type = get_config('app_reg_type');
                        // 拼接数据
                        $data = ['user_id'=>$pid,'share_time'=>time(),'to_ip'=>request()->ip(),'did'=>$did,'gold'=>$sharePoint,'share_type'=>$type];
                        // 写入分享记录
                        $shareDb->insert($data);
                    }
                }
            }
        }
    }

    /* 会员奖励 $type => 路由，注册/分享 */ 
    public function __awardUser($userDb, $userId, $awar, $type = '')
    {
        // 奖励类型
        switch (get_config('app_reg_type')) {
            // 奖励金币
            case '1':
                // 注册
                if($type == 1) {
                    $typeModule = 'appRegister';
                    $typeName   = '注册奖励';
                // 分享    
                }else{
                    $typeModule = 'appShare';
                    $typeName   = '分享奖励';
                }
                // 增加会员金币
                $userDb->where(array('id' => $userId))->setInc('money', $awar);
                // 写入金币记录
                write_gold_log(array('user_id'=>$userId, 'gold'=>$awar, 'module'=>$typeModule, 'explain'=>$typeName));
                break;
            // 奖励VIP天数
            case '2':
                // 会员信息
                $userinfo = $userDb->field('out_time')->where(array('id' => $userId))->find();
                // 会员到期时间
                $out_time = $userinfo['out_time'];
                // 该会员是否到期
                if ($out_time > time()) {
                    // 未到期，则从到期时间计算
                    $user['out_time'] = strtotime("+{$awar} days", $out_time);
                } else {
                    // 已到期，则从当前时间开始计算
                    $user['out_time'] = strtotime("+{$awar} days");
                }
                // 更新会员状态
                $userDb->where(array('id' => $userId))->update($user);
                break;
            default:
                # code...
                break;
        }  
    }
  
  	/* 宣传模板 */
    public function shareUrl(Request $request)
    {
        $userId = $request->param('pid/d', '');
        $wheres = "name in ('site_title','site_favicon','app_sharepoint','web_server_url','vip')";
        $config = Db::name('admin_config')->where($wheres)->column('name,value');
        //$this->assign('url', $config['web_server_url'].'/appapi/shareUrl/pid/'.$userId);
        $this->assign('sys', $config);
        $this->assign('uid', $userId);
        return $this->fetch('tpl/appapi/shareurl.html');
    }

    /* 路由判断，安卓与苹果手机访问 */
    public function download(Request $request)
    {
        $pid = $request->param('pid/d', '');
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']); 
        $android = (strpos($agent, 'android')) ? true : false; 
        // 分享路由
        $route = $_SERVER['HTTP_USER_AGENT'];
        // 是否为微信或QQ内打开
        if(strpos($route, 'MicroMessenger') == false && strpos($route, 'QQ/') == false) {
            // 安卓
            if($android){
                echo "<script>window.location.href='".get_config('app_apk_download').$pid."'</script>";
            // 否则都跳转至苹果页面
            }else{
                echo "<script>window.location.href='".get_config('app_ios_download').$pid."'</script>";
            }
        }else{
            $img = empty(get_config('vip')) ? '/tpl/appapi/img/ms.jpg' : get_config('vip');
            // 微信或QQ内打开
echo"
<!DOCTYPE html>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<meta name='viewport' content='maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width,initial-scale=1.0,user-scalable=no'>
<meta name='renderer' content='webkit'>
<meta http-equiv='X-UA-Compatible' content='IE=Edge,chrome=1'>
<title data='1'>APP下载</title>
</head>
<body style='background-color:#333333;'><br>
<div style='background-color:#333333;width:100%;'><img src='".$img."' width='100%'></div>
</body>
</html>
";
        }
    }
	
  	/* 用于后台远程调用分成 */
    public function divideIntoUrl(Request $request)
    {
        $userId = $request->param('userId/d', 0);
        $price  = $request->param('price/f', 0);
        $orderSn= $request->param('orderSn/s', '');
        if(empty($userId) || empty($price) || empty($orderSn) || $price < 1) die('非法操作，请刷新后再试01！');
        // 验证数据的真伪
        $orWhere = "order_sn='$orderSn' and user_id='$userId' and is_app=1 and status=1";
        $isOrder = Db::name('order')
                 ->field("price")
                 ->where($orWhere)
                 ->find();
        if(empty($isOrder['price']) || $isOrder['price']<>$price){
            die('非法操作，请刷新后再试02！');
        }else{
          	$logDb = Db::name('app_divide_log');
            // 该订单是否已分成
            if($logDb->field('order_sn')->where("order_sn='$orderSn'")->find()) die('非法操作，请刷新后再试03！');
            // 分成函数
            app_divide_into($userId, $price, $orderSn);
            echo 'success';
        }
    }

    /* APP更新下载页 */
    public function downloadUpdates(Request $request)
    {
        $userId = $request->param('uid/d', ''); 
        // 用户点击下载后的业务逻辑
        if ($request->isPost()) {
            // 业务处理
            
        // 载入下载页面    
        }else{
            // 获取网站配置信息
            $wheres = "name in ('site_title','site_logo','site_favicon','web_server_url','app_android','app_apple','apk_version','ios_version','app_update_apk','app_update_ios')";
            $config = Db::name('admin_config')->where($wheres)->column('name,value');
            $this->assign('uid', $userId);
            $agent = strtolower($_SERVER['HTTP_USER_AGENT']); 
            $android = (strpos($agent, 'android')) ? true : false; 
            // 安卓
            if($android){
                $config['versions'] = $config['apk_version'];
                $config['download'] = $config['app_android'];
                $config['updates']  = str_replace('；','</br>',$config['app_update_apk']);
                $config['type_img'] = 'android';
                $config['typename'] = 'Android';
            }else{
                $config['versions'] = $config['ios_version'];
                $config['download'] = $config['app_android'];
                $config['updates']  = str_replace('；','</br>',$config['app_update_ios']);
                $config['type_img'] = 'ios';
                $config['typename'] = 'iPhone';
            }
            $this->assign('c', $config);
            return $this->fetch('tpl/appapi/updates.html');
        }
    }
  
  	 //软件采集入库
    public function appsyncAddVideo(Request $request)
    {

        if ($request->isPost()) {
            $data = $request->post();
          	$datas = http_build_query($data);
            $logDir = dirname(__FILE__) . "../../../appsyncAddVideo.log";
          	file_put_contents($logDir, "\r\n" . str_repeat('-', 50) . 'hello', FILE_APPEND);
          	file_put_contents($logDir, "\r\n" . str_repeat('-', 50) . $datas, FILE_APPEND);
            $list = json_decode($data['data'],true);
            $conditions = 'status = 1 and type = 1';
            $class_list = db::name('class')->where($conditions)->column('*','name');
            if(empty($list)){
                file_put_contents($logDir, "\r\n" . '非JSON!', FILE_APPEND);
                die(json_encode(['Code' => 201, 'Msg' => '非JSON'], JSON_UNESCAPED_UNICODE));
            }
            foreach ($list as $k=>$v){
                $class_id = isset($class_list[$v['typeText']]) ? $class_list[$v['typeText']]['id'] : 0;
                $videoData = [
                    'title' => $v['title'],
                    'url' => $v['vUrl'],
                    'class' => $class_id,
                    'thumbnail' => $v['cover'],
                    'add_time' => time(),
                    'update_time' => time(),
                    'status' => 1
                ];
                $videoData['is_check'] = 1;
              	$info = Db::name('video')->where(array('url'=>$v['vUrl']))->find();
              	if(empty($info)){
                  Db::name('video')->insert($videoData);
                }
            }
            file_put_contents($logDir, "\r\n" . 'Success!', FILE_APPEND);
            die('Success!');
        }else{
            die(json_encode(['Code' => 201, 'Msg' => '请以POST方式提交'], JSON_UNESCAPED_UNICODE));
        }
    }
  
}

