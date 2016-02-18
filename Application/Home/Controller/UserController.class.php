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
					$filename = genRandStr();
					$new_file = "./Public/upload/portrait/".date('Ymd_', time()).$filename.'.'.$filetype;
					if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $_POST['portrait'])))){
						$user['portrait'] = substr($new_file, 8);
					}
				}
			}
			$user['nickname'] = $_POST['nickname'];
			$user['gender'] = intval($_POST['gender']);
			$user['cellphone'] = $_POST['cellphone'];
			$user['companyC'] = $_POST['companyC'];
			$user['companyE'] = $_POST['companyE'];
			$user['companyP'] = $_POST['companyP'];
			$user['position'] = $_POST['position'];
			$user['contacterC'] = $_POST['contacterC'];
			$user['contacterE'] = $_POST['contacterE'];
			$user['phone'] = $_POST['phone'];
			$user['fax'] = $_POST['fax'];
			$user['zipcode'] = $_POST['zipcode'];
			$user['addressC'] = $_POST['addressC'];
			$user['addressE'] = $_POST['addressE'];
			$user['introduction'] = $_POST['introduction'];
			M('user')->where(array('id'=>$_SESSION['uid'], 'email'=>$_SESSION['uemail']))->save($user);
			$this->redirect('User/home');
		}
		else {
			$this->redirect('User/home');
		}
	}
}