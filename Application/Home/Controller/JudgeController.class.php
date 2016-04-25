<?php
namespace Home\Controller;
use Think\Controller;
class JudgeController extends Controller{
	public function _initialize(){
		if(!isset($_SESSION['urole']) || $_SESSION['urole'] != 1){
			$this->redirect('Index/contest', array('id'=>C('CONTESTID')));
		}
	}

	public function index(){
		$current = intval(time());
		$contest = M('contest')->where(array('id'=>C('CONTESTID')))->find();
		if ($contest['judge_first_from'] <= $current && $current <= $contest['judge_first_to']) {
			$this->first = 1;
		}
		else {
			$this->first = 0;
		}
		if ($contest['judge_second_from'] <= $current && $current <= $contest['judge_second_to']) {
			$this->second = 1;
		}
		else {
			$this->second = 0;
		}

		$this->display();
	}

	public function first(){
		$submissions = M('judge_first')->where(array('contest_id'=>C('CONTESTID'),'user_id'=>$_SESSION['uid']))->select();
		$count = 0;
		for ($i = 0; $i < count($submissions); $i++) { 
			if ($submissions[$i]['yes_or_no'] != '') {
				$count += 1;
			}
		}
		$this->process = $count / floatval(count($submissions));
		$this->display();
	}
}