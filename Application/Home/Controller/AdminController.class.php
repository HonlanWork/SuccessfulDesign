<?php
namespace Home\Controller;
use Think\Controller;
class AdminController extends Controller{
	public function _initialize(){
		if(!isset($_SESSION['urole']) || $_SESSION['urole'] < 2){
			$this->redirect('Index/contest', array('id'=>C('CONTESTID')));
		}
	}

	public function index(){
		$this->display();
	}

	public function contest_pay_confirm(){
		$this->submissions = M('submission')->where(array('contest_id'=>C('CONTESTID'), 'ispaied'=>0, 'pay_confirm'=>1))->select();
		$this->display();
	}

	public function contest_pay_yes(){
		M('submission')->where(array('id'=>I('id')))->save(array('ispaied'=>1, 'pay_confirm'=>0));
		$email = M('email')->where(array('name'=>'确认支付'))->find();
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
		$email_content = explode("^^^", $email_content);
		$email_content = $email_content[0].U('Contest/info', array('id'=>I('id')),false,true).$email_content[1];
		$user_email = M('user')->where(array('id'=>I('user_id')))->find();
		$user_email = $user_email['email'];
		SendMail($user_email, $email['title'], $email_content);
		$this->redirect('Admin/contest_pay_confirm');
	}

	public function contest_pay_no() {
		M('submission')->where(array('id'=>I('id')))->save(array('ispaied'=>0, 'pay_confirm'=>0));
		$this->redirect('Admin/contest_pay_confirm');
	}

	public function submissions() {
		$this->notpaid = M('submission')->where(array('ispaied'=>0))->select();
		$this->paid_but_notsubmitted = M('submission')->where(array('ispaied'=>1, 'issubmitted'=>0))->select();
		$this->submitted = M('submission')->where(array('ispaied'=>1, 'issubmitted'=>1))->select();
		$this->display();
	}

	public function mark_as_paid() {
		M('submission')->where(array('id'=>I('id')))->save(array('ispaied'=>1));
		$this->redirect('Admin/submissions');
	}

	public function mark_as_notpaid() {
		M('submission')->where(array('id'=>I('id')))->save(array('ispaied'=>0, 'issubmitted'=>0));
		$this->redirect('Admin/submissions');
	}

	public function mark_as_submitted() {
		M('submission')->where(array('id'=>I('id')))->save(array('ispaied'=>1, 'issubmitted'=>1));
		$this->redirect('Admin/submissions');
	}

	public function mark_as_notsubmitted() {
		M('submission')->where(array('id'=>I('id')))->save(array('issubmitted'=>0));
		$this->redirect('Admin/submissions');
	}

	public function users() {
		$this->users = M('user')->select();
		$this->display();
	}

	public function reset_password() {
		$user = M('user')->where(array('id'=>I('id')))->find();
		M('user')->where(array('id'=>I('id')))->save(array('password'=>sha1($user['salt'].'123456')));
		$this->redirect('Admin/users');
	}

	public function translations() {
		$this->display();
	}

	public function translations_search() {
		$translations = M('translation')->where(array('ch'=>I('keyword')))->select();
        echo json_encode(array('translations'=>$translations));
	}

	public function translations_save() {
		M('translation')->where(array('id'=>I('id')))->save(array('en'=>I('en')));
		echo json_encode(array('en'=>I('en')));
	}
}
?>