<?php
namespace Home\Controller;
use Think\Controller;
class ContestController extends CommonController {
	public function apply() {
		$this->display();
	}

	public function apply_handle() {
		if (IS_POST) {
			$user = M('user')->where(array('id'=>$_SESSION['uid'], 'email'=>$_SESSION['uemail']))->find();
			$id = M('submission')->data(array(
				'contest_id' => 1,
				'user_id' => $_SESSION['uid'],
				'titlec' => trim($_POST['titlec']),
				'titlee' => trim($_POST['titlee']),
				'category' => trim($_POST['category']),
				'companyc' => $user['companyc'],
				'companye' => $user['companye'],
				'companyp' => $user['companyp'],
				'position' => $user['position'],
				'email' => $user['email'],
				'cellphone' => $user['cellphone'],
				'image'=>'/img/design.jpg'
				))->add();
			$this->redirect('Contest/pay', array('id'=>$id));
		}
		else {
			$this->redirect('Contest/apply');
		}
	}

	public function pay() {
		$this->contest = M('contest')->where(array('id'=>1))->find();
		$this->submission = M('submission')->field('id,titlee,titlec,category')->where(array('id'=>I('id'), 'user_id'=>$_SESSION['uid']))->find();
		$this->display();
	}

	public function pay_confirm() {
		M('submission')->where(array('id'=>$_POST['id'], 'user_id'=>$_SESSION['uid']))->save(array('pay_confirm'=>1));
    	$email = M('email')->where(array('name'=>'完成支付'))->find();
    	$email_content = $email['content'];
    	if(count(explode("\n", $email_content)) == 1 ){
    		$email_content = explode("\r", $email_content);
    	} else {
    		$email_content = explode("\n", $email_content);
    	}
    	$temp = '';
    	foreach ($email_content as $key => $value) {
    		$temp .= $value."<br/>";
    	}
    	$email_content = $temp;
    	$submission = M('submission')->where(array('id'=>$_POST['id'], 'user_id'=>$_SESSION['uid']))->find();
    	$tmp = '作品中文名称：'.$submission['titlec'].'<br/>作品英文名称：'.$submission['titlee'].'<br/>作品类别：'.$submission['category'];
        $email_content = explode("^^^", $email_content);
    	$email_content = $email_content[0].$tmp.$email_content[1];
    	$admins = M('user')->where(array('role'=>2))->select();
    	foreach ($admins as $key => $value) {
    		SendMail($value['email'], $email['title'], $email_content);
    	}
		echo json_encode(array('result'=>'ok'));
	}

	public function info() {
		$this->submission = M('submission')->where(array('id'=>I('id'), 'user_id'=>$_SESSION['uid']))->find();
		if ($this->submission['ispaied'] == 0) {
			$this->redirect('Contest/pay',array('id'=>I('id')));
		}
		$this->display();
	}

	public function info_handle() {
		if (IS_POST) {
			$submission = M('submission')->where(array('id'=>$_POST['id']))->find();
			if ($submission['ispaied'] == 0) {
				$this->redirect('Contest/pay',array('id'=>I('id')));
			}

			$data = array(
				'titlee' => $_POST['titlee'],
				'titlec' => $_POST['titlec'],
				'category' => $_POST['category'],
				'companye' => $_POST['companye'],
				'companyc' => $_POST['companyc'],
				'companyp' => $_POST['companyp'],
				'position' => $_POST['position'],
				'email' => $_POST['email'],
				'cellphone' => $_POST['cellphone'],
				'addts' => $_POST['addts'],
				'introductione' => $_POST['introductione'],
				'introductionc' => $_POST['introductionc'],
				'demande' => $_POST['demande'],
				'demandc' => $_POST['demandc'],
				'challengee' => $_POST['challengee'],
				'challengec' => $_POST['challengec'],
				'coste' => $_POST['coste'],
				'costc' => $_POST['costc'],
				'solutione' => $_POST['solutione'],
				'solutionc' => $_POST['solutionc'],
				'conclusione' => $_POST['conclusione'],
				'conclusionc' => $_POST['conclusionc'],
				'modifyts' => time());

			for ($i = 1; $i <= 6; $i++) { 
				if ($_POST['image'.$i.$i] != '') {
					if ($_POST['image'.$i.$i] == 'remove') {
						$filename = './Public'.$submission['image'.$i];
						unlink($filename);
						unlink(strstr($filename, '_thumb', true).substr(strstr($filename, '_thumb'), 6));
						$data['image'.$i] = ''; 
					}
					else {
						$filename = 'image'.$i.'.'.substr(strstr($_FILES['image'.$i]['type'], '/'), 1);
						if (!file_exists("./Public/upload/contest2016/".$_POST['id'])){ 
							mkdir("./Public/upload/contest2016/".$_POST['id'], 0777, true);
						}
						$filename = "./Public/upload/contest2016/".$_POST['id']."/".$filename;
						move_uploaded_file($_FILES['image'.$i]["tmp_name"], $filename);

						if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $_POST['image'.$i.$i], $result)){
							$filetype = $result[2];
							$filename = 'image'.$i.'_thumb';
							$new_file = "./Public/upload/contest2016/".$_POST['id']."/".$filename.'.'.$filetype;
							if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $_POST['image'.$i.$i])))){
								$data['image'.$i] = substr($new_file, 8); 
							}
						}
					}
				}
			}

			if ($_POST['videostatus'] == 'add') {
				$filename = 'video'.'.'.substr(strstr($_FILES['video']['type'], '/'), 1);
				if (!file_exists("./Public/upload/contest2016/".$_POST['id'])){ 
					mkdir("./Public/upload/contest2016/".$_POST['id'], 0777, true);
				}
				$filename = "./Public/upload/contest2016/".$_POST['id']."/".$filename;
				move_uploaded_file($_FILES['video']["tmp_name"], $filename);
				$data['video'] = substr($filename, 8);
			}
			elseif ($_POST['videostatus'] == 'remove') {
				$filename = './Public'.$submission['video'];
				unlink($filename);
				$data['video'] = ''; 
			}

			if ($_POST['fileinfo'] == 'remove' ) {
				$filename = './Public'.$submission['file'];
				unlink($filename);
				$data['file'] = '';
			}
			elseif ($_POST['fileinfo'] != '') {
				$filename = $_POST['fileinfo'];
				if (!file_exists("./Public/upload/contest2016/".$_POST['id'])){ 
					mkdir("./Public/upload/contest2016/".$_POST['id'], 0777, true);
				}
				$filename = "./Public/upload/contest2016/".$_POST['id']."/".$filename;
				move_uploaded_file($_FILES['file']["tmp_name"], $filename);
				$data['file'] = substr($filename, 8);
			}

			$completeness = 0;
			if ($data['titlec'] != '') {
				$completeness += 1;
			}
			if ($data['titlee'] != '') {
				$completeness += 1;
			}
			if ($data['category'] != '') {
				$completeness += 1;
			}
			if ($data['companyc'] != '') {
				$completeness += 1;
			}
			if ($data['companye'] != '') {
				$completeness += 1;
			}
			if ($data['position'] != '') {
				$completeness += 1;
			}
			if ($data['email'] != '') {
				$completeness += 1;
			}
			if ($data['companyp'] != '') {
				$completeness += 1;
			}
			if ($data['cellphone'] != '') {
				$completeness += 1;
			}
			if ($data['addts'] != '') {
				$completeness += 1;
			}
			if ($data['introductionc'] != '') {
				$completeness += 1;
			}
			if ($data['introductione'] != '') {
				$completeness += 1;
			}
			if ($data['demandc'] != '') {
				$completeness += 1;
			}
			if ($data['demande'] != '') {
				$completeness += 1;
			}
			if ($data['challengec'] != '') {
				$completeness += 1;
			}
			if ($data['challengee'] != '') {
				$completeness += 1;
			}
			if ($data['costc'] != '') {
				$completeness += 1;
			}
			if ($data['coste'] != '') {
				$completeness += 1;
			}
			if ($data['solutionc'] != '') {
				$completeness += 1;
			}
			if ($data['solutione'] != '') {
				$completeness += 1;
			}
			if ($data['conclusionc'] != '') {
				$completeness += 1;
			}
			if ($data['conclusione'] != '') {
				$completeness += 1;
			}
			if ($completeness == 22) {
				$data['iscomplete'] = 1;
			}
			else {
				$data['iscomplete'] = 0;
			}
			$data['completeness'] = round(floatval($completeness * 100) / 22, 1);

			M('submission')->where(array('id'=>$_POST['id'],'contest_id'=>$_POST['contest_id'], 'user_id'=>$_SESSION['uid']))->save($data);
			
			$images = M('submission')->where(array('id'=>$_POST['id'],'contest_id'=>$_POST['contest_id'], 'user_id'=>$_SESSION['uid']))->field('image1,image2,image3,image4,image5,image6')->find();
			$flag = false;
			for ($i = 1; $i <= 6; $i++) { 
				if ($images['image'.$i] != '') {
					M('submission')->where(array('id'=>$_POST['id'],'contest_id'=>$_POST['contest_id'], 'user_id'=>$_SESSION['uid']))->save(array('image'=>$images['image'.$i]));
					$flag = true;
					break;
				}
			}
			if (!$flag) {
				M('submission')->where(array('id'=>$_POST['id'],'contest_id'=>$_POST['contest_id'], 'user_id'=>$_SESSION['uid']))->save(array('image'=>'/img/design.jpg'));
			}

			$this->redirect('Contest/info', array('id'=>$_POST['id']));
		}
		else {
			$this->redirect('Contest/apply');
		}
	}

	public function submit() {
		$this->submission = M('submission')->field('id,titlee,titlec,category,ispaied,issubmitted')->where(array('id'=>I('id'), 'user_id'=>$_SESSION['uid']))->find();
		if ($this->submission['ispaied'] == 0) {
			$this->redirect('Contest/pay',array('id'=>I('id')));
		}
		$this->display();
	}

	public function submit_handle() {
		if (IS_POST) {
			$submission = M('submission')->where(array('id'=>$_POST['id']))->find();
			if ($submission['ispaied'] == 1) {
				M('submission')->where(array('id'=>$_POST['id'], 'user_id'=>$_SESSION['uid']))->save(array('issubmitted'=>1));
				$this->redirect('Contest/submit', array('id'=>$_POST['id']));	
			}
			else {
				$this->redirect('Contest/pay', array('id'=>$_POST['id']));
			}
		}
		else {
			$this->redirect('Contest/apply');
		}
	}
}