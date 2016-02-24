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
					echo $new_file;
					if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $_POST['portrait'])))){
						$user['portrait'] = substr($new_file, 8);
						session('uportrait', $user['portrait']);
					}
					else {
						echo 'error';
					}
				}
			}
			die;

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
		$this->all = M('submission')->where(array('user_id'=>$_SESSION['uid']))->select();
		$this->notcomplete = M('submission')->where(array('user_id'=>$_SESSION['uid'], 'iscomplete'=>0))->select();
		$this->notpaied = M('submission')->where(array('user_id'=>$_SESSION['uid'], 'ispaied'=>0))->select();
		$this->notsubmitted = M('submission')->where(array('user_id'=>$_SESSION['uid'], 'issubmitted'=>0))->select();
		$this->display();
	}

	public function submission() {
		$this->submission = M('submission')->where(array('id'=>I('id'), 'user_id'=>$_SESSION['uid']))->find();
		$images = array();
		for ($i = 1; $i <= 6; $i++) { 
			if ($this->submission['image'.$i] != '') {
				array_push($images, $this->submission['image'.$i]);
			}
		}
		$this->images = $images;
		$this->display();
	}
}