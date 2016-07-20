<?php
namespace Home\Controller;
use Think\Controller;
header('Content-type:text/html;charset=utf-8');
class UserController extends CommonController {
	public function home() {
		$this->user = M('user')->where(array('id'=>$_SESSION['uid'], 'email'=>$_SESSION['uemail']))->find();
		$this->display();
	}

	public function edit() {
		if (IS_POST) {
			$user = array();
			if ($_POST['portrait'] != '') {
				if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $_POST['portrait'], $result)){
					$filetype = $result[2];
					$filename = 'portrait_'.$_SESSION['uid'];
					$new_file = "./Public/upload/portrait/".$filename.'.'.$filetype;
					if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $_POST['portrait'])))){
						$user['portrait'] = substr($new_file, 8);
						session('uportrait', $user['portrait']);
					}
				}
			}
			$user['nickname'] = $_POST['nickname'];
			$user['gender'] = intval($_POST['gender']);
			$user['cellphone'] = $_POST['cellphone'];
			$user['companyc'] = $_POST['companyc'];
			$user['companye'] = $_POST['companye'];
			$user['companyp'] = $_POST['companyp'];
			$user['position'] = $_POST['position'];
			$user['fax'] = $_POST['fax'];
			$user['zipcode'] = $_POST['zipcode'];
			$user['companyac'] = $_POST['companyac'];
			$user['companyae'] = $_POST['companyae'];
			M('user')->where(array('id'=>$_SESSION['uid'], 'email'=>$_SESSION['uemail']))->save($user);
			$this->redirect('User/home');
		}
		else {
			$this->redirect('User/home');
		}
	}

	public function submissions() {
		$this->all = M('submission')->where(array('contest_id'=>C('CONTESTID'),'user_id'=>$_SESSION['uid']))->select();
		$this->notcomplete = M('submission')->where(array('contest_id'=>C('CONTESTID'),'user_id'=>$_SESSION['uid'], 'iscomplete'=>0))->select();
		$this->notpaied = M('submission')->where(array('contest_id'=>C('CONTESTID'),'user_id'=>$_SESSION['uid'], 'ispaied'=>0))->select();
		$this->notsubmitted = M('submission')->where(array('contest_id'=>C('CONTESTID'),'user_id'=>$_SESSION['uid'], 'issubmitted'=>0))->select();
		$promotions = M('promotion')->where(array('contest_id'=>C('CONTESTID'),'user_id'=>$_SESSION['uid'], 'ispaied'=>1))->order('timestamp desc')->select();
		$names = ['双页年鉴展示', '专题报道', '媒体推广', '微信传播', '3D展示', '实物展示', '增订奖杯&奖状', '增订年鉴'];
		for ($i = 0; $i < count($promotions); $i++) {
			$tmp = '';
			$promotions[$i]['promotion'] = explode(',', $promotions[$i]['promotion']);
			if ($promotions[$i]['promotion'][0] == '1a') {
				$tmp = translate_return('A 基础推广服务');
			}
			elseif ($promotions[$i]['promotion'][0] == '1b') {
				$tmp = translate_return('B 展示推广服务');
			}
			elseif ($promotions[$i]['promotion'][0] == '1c') {
				$tmp = translate_return('C 闪耀推广服务');
			}
			elseif ($promotions[$i]['promotion'][0] == '1d') {
				$tmp = translate_return('D 成功推广服务');
			}
			for ($j = 1; $j < count($promotions[$i]['promotion']); $j++) { 
				if ($promotions[$i]['promotion'][$j] != 0) {
					if ($promotions[$i]['promotion'][$j] == 1) {
						$tmp .= '&nbsp;&nbsp;&nbsp;'.translate_return($names[$j - 1]);
					}
					else {
						$tmp .= '&nbsp;&nbsp;&nbsp;'.translate_return($names[$j - 1]).'x'.$promotions[$i]['promotion'][$j];
					}
				}
			}
			$promotions[$i]['promotion'] = $tmp;

			$tmp_sub = M('submission')->where(array('id'=>$promotions[$i]['submission_id']))->find();
			$promotions[$i]['titlec'] = $tmp_sub['titlec'];
			$promotions[$i]['titlee'] = $tmp_sub['titlee'];
		}
		$this->promotions = $promotions;
		$this->display();
	}

	public function submission() {
		if ($_SESSION['urole'] == 2) {
			$this->submission = M('submission')->where(array('contest_id'=>C('CONTESTID'), 'id'=>I('id')))->find();
		}
		else {
			$this->submission = M('submission')->where(array('contest_id'=>C('CONTESTID'), 'id'=>I('id'), 'user_id'=>$_SESSION['uid']))->find();
		}
		$images = array();
		for ($i = 1; $i <= 6; $i++) { 
			if ($this->submission['image'.$i] != '') {
				array_push($images, $this->submission['image'.$i]);
			}
		}
		$this->images = $images;
		$this->display();
	}

	public function past_submissions() {
		$map['contest_id']  = array('neq', C('CONTESTID'));
		$map['user_id'] = array('eq', $_SESSION['uid']);
		$submissions = M('submission')->where($map)->select();
		$contests = M('contest')->select();
		for ($i = 0; $i < count($submissions); $i++) { 
			for ($j = 0; $j < count($contests); $j++) { 
				if ($contests[$j]['id'] == $submissions[$i]['contest_id']) {
					$submissions[$i]['year'] = intval($contests[$j]['year']);
					break;
				}
			}
		}
		function cmp($a, $b) {
			if ($a['year'] == $b['year']) {
				return 0;
			}
			return ($a['year'] < $b['year']) ? 1 : -1;
		}
		usort($submissions, "cmp");
		$this->submissions = $submissions;
		$this->display();
	}

	public function past_submission() {
		if (C('CURRENT_FINISH') == 0) {
            $map['contest_id']  = array('neq', C('CONTESTID'));
        }
		$map['id'] = array('eq', I('id'));
		$this->submission = M('submission')->where($map)->find();
		
		$images = array();
		for ($i = 1; $i <= 6; $i++) { 
			if ($this->submission['image'.$i] != '') {
				array_push($images, $this->submission['image'.$i]);
			}
		}
		$this->images = $images;
		$this->display();
	}

	public function change_password() {
		$salt = genRandStr();
		M('user')->where(array('id'=>$_SESSION['uid']))->save(array('password'=>sha1($salt.I('password')),'salt'=>$salt));
		echo json_encode(array('result'=>'ok'));
	}
}