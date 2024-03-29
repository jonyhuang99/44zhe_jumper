<?php

class ApiJumpController extends AppController {

	var $name = 'ApiJump';
	var $uses = array('UserFanli', 'UserMizhe', 'UserCandidate', 'StatJump', 'StatRegFailed', 'StatJump', 'OrderFanli', 'UserBind');
	var $layout = 'ajax';

	function demoReg() {

	}

	function demoJump($type='jsfanli') {
		$user = $this->UserBind->getChannelUserM($type)->find(array('status'=>1), '', 'userid DESC');
		clearTableName($user);
		$my_user = $this->UserBind->field('my_user', array('status'=>1, 'jumper_uid'=>$user['userid'], 'jumper_type'=>$type));
		if(!$my_user)die('can not found my_user');
		$this->set('my_user', $my_user);
	}

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
	 * 跳出前进行返利网登陆，作为保底跳转，记录登陆状态
	 * @return [type] [description]
	 */
	function r(){

		$flag = $_GET['flag'];
		$fdetail = $_GET['fdetail'];
		$url = $_GET['u'];

		if($url){
			$this->redirect($url);
		}else{
			$this->redirect(DEFAULT_ERROR_URL);
		}
	}

	/**
	 * 客户端请求返利网接口出错时，强制进行s2s端的跳转
	 *
	 * http://www.jumper.com/apiJump/jumpForce/taobao/bluecone@163.com/18484876328/0.11/0.01
	 * @param type $shop
	 * @param type $my_user
	 * @param type $p_id
	 * @param type $p_price
	 * @param type $p_fanli
	 */
	function jumpForce($shop, $my_user, $p_id='') {

		$data = taobaoItemDetail($p_id);

		if (!$data) {
			alert('jumpForce', 'pid: ' . $p_id . ' get detail error');
			$this->redirect(DEFAULT_ERROR_URL);
		}

		//$this->jump($shop, $my_user, $p_id, $data['p_title'], $data['p_price'], $data['p_seller']);

		$this->redirect('http://item.taobao.com/item.htm?id=' . $p_id);
	}


	/**
	 * 默认使用返利网，记录到跳转日志以便跟单对应，选择跟单用户并跳转出去
	 * @param type $shopid
	 * @param type $my_user
	 * @param type $p_id
	 * @param type $p_title
	 * @param type $p_price
	 * @param type $p_fanli
	 */
	function jump($shop, $my_user, $p_id='', $p_title, $p_price='', $p_seller='') {

		$oc = $_GET['oc'];
		$shop = low($shop);
		$my_user = low(urldecode($my_user));
		$target = $_GET['target'];//商城页面

		//支持商城
		if ($shop != 'taobao') {

			require MYLIBS . 'jumper' . DS . 'rule_51fanli.class.php';

			//选出跳转userid
			$jump_rule = new rule($shop);

			if(C('shop_tpl', $shop)){
				$userid = $this->UserFanli->getShopUser($shop, $my_user);
				if($userid){
					$jump_url = $jump_rule->getUrl($userid, $target);

					$this->_addStatJump($shop, 'fanli', $my_user, $oc, $userid);
					$this->redirect('http://fun.51fanli.com/goshopapi/goout?' . time() . '&id=' . $jump_rule->ts['id'] . '&go=' . urlencode($jump_url) . '&fp=loading');
				}else{
					alert('shop jump', "[$shop][shop user not hit][$target]");
				}
			}else{
				alert('shop jump', "[$shop][$target]");
			}

			$this->redirect($_GET['target']);
		}

		$fl_userid = $_SESSION['fl_userid']?$_SESSION['fl_userid']:$_COOKIE['fl_userid'];

		if(!$fl_userid){
			alert('jump', '[error][51fanli][without session fl_userid]');
			$this->redirect(DEFAULT_ERROR_URL);
		}


		$this->_addStatJump($shop, 'fanli', $my_user, $oc, $fl_userid, $p_id, $p_title, $p_price, 1, $p_seller);

		//封装goshop跳转地址

		$tpl = C('shop_tpl', 'taobao');

		if ($p_id) {
			$jump_url = 'http://fun.51fanli.com/goshop/go?id='.$tpl['id'].'&go=http%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D'.$p_id.'&lc=taobao_search_0';
			$this->redirect($jump_url);
		}
		else {

			//报警 跳转taobao的url遗失
			alert('jump', '[' . getIp() . '][' . getBrowser() . '] p_id miss');
			$this->redirect(DEFAULT_ERROR_URL);
		}
	}

}

?>