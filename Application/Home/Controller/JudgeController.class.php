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

		if ($current < intval($contest['judge_first_from'])) {
			$this->first = -1;
		}
		elseif (intval($contest['judge_first_from']) <= $current && $current <= intval($contest['judge_first_to'])) {
			$this->first = 0;
		}
		else {
			$this->first = 1;
		}
		if ($current < intval($contest['judge_second_from'])) {
			$this->second = -1;
		}
		if (intval($contest['judge_second_from']) <= $current && $current <= intval($contest['judge_second_to'])) {
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
		// offset
		$ids = M('judge_first')->where(array('contest_id'=>C('CONTESTID'),'user_id'=>$_SESSION['uid']))->select();
		$total = count($ids);

		$count = 0;
		for ($i = 0; $i < $total; $i++) { 
			if ($ids[$i]['yes_or_no'] != '') {
				$count += 1;
			}
		}
		$this->left = intval(100 * $count / floatval($total));
		$this->right = 100 - $this->left;

		$submission = M('submission')->where(array('contest_id'=>C('CONTESTID'), 'id'=>$ids[intval(I('offset'))]['submission_id']))->find();
		// $submission = M('submission')->where(array('contest_id'=>C('CONTESTID'), 'id'=>230))->find();

		$images = array();
		for ($i = 1; $i <= 6; $i++) { 
			if ($submission['image'.$i] != '') {
				array_push($images, $submission['image'.$i]);
			}
		}
		$this->images = $images;

		$this->total = $total;
		$this->submission = $submission;
		$this->offset = I('offset');
		$this->display();
	}

	public function first_in() {
		// offset id
		$offset = I('offset');
		M('judge_first')-where(array('contest_id'=>C('CONTESTID'),'user_id'=>$_SESSION['uid'],'submission_id'=>I('id')))->save(array('yes_or_no'=>'yes','timestamp'=>time()));
		$total = M('judge_first')->where(array('contest_id'=>C('CONTESTID'),'user_id'=>$_SESSION['uid']))->count();
		$offset += 1;
		if ($offset == $total) {
			$this->redirect('Judge/first');
		}
		else {
			$this->redirect('Judge/first_detail', array('offset'=>$offset));
		}
	}

	public function first_out() {
		// offset id
		$offset = I('offset');
		M('judge_first')-where(array('contest_id'=>C('CONTESTID'),'user_id'=>$_SESSION['uid'],'submission_id'=>I('id')))->save(array('yes_or_no'=>'no','timestamp'=>time()));
		$total = M('judge_first')->where(array('contest_id'=>C('CONTESTID'),'user_id'=>$_SESSION['uid']))->count();
		$offset += 1;
		if ($offset == $total) {
			$this->redirect('Judge/first');
		}
		else {
			$this->redirect('Judge/first_detail', array('offset'=>$offset));
		}
	}
}