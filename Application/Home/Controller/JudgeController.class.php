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

		if ($current < $contest['judge_first_from']) {
			$this->first = -1;
		}
		elseif ($contest['judge_first_from'] <= $current && $current <= $contest['judge_first_to']) {
			$this->first = 0;
		}
		else {
			$this->first = 1;
		}
		if ($current < $contest['judge_second_from']) {
			$this->second = -1;
		}
		if ($contest['judge_second_from'] <= $current && $current <= $contest['judge_second_to']) {
			$this->second = 0;
		}
		else {
			$this->second = 1;
		}

		$this->display();
	}

	public function first() {
		$submissions = M('judge_first')->where(array('contest_id'=>C('CONTESTID'),'user_id'=>$_SESSION['uid']))->select();
		$count = 0;
		$start = -1;
		$data = [];
		for ($i = 0; $i < count($submissions); $i++) { 
			if ($submissions[$i]['yes_or_no'] != '') {
				$count += 1;
			}
			if ($submissions[$i]['yes_or_no'] == '' && $start == -1) {
				$start = $i;
			}
			$tmp = M('submission')->where(array('contest_id'=>C('CONTESTID'), 'id'=>$submissions[$i]['submission_id']))->find();
			$tmp['yes_or_no'] = $submissions[$i]['yes_or_no'];
			$tmp['offset'] = $i;
			$data[] = $tmp;
		}
		if ($start == -1) {
			$start = 0;
		}
		$this->left = intval(100 * $count / floatval(count($submissions)));
		$this->right = 100 - $this->left;
		$this->data = $data;
		$this->start = $start;
		$this->display();
	}

	public function first_detail() {

	}
}