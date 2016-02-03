<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
    	$this->display();
    }

    public function contest(){
    	$contestId = I('contestId');
    	$contest = M('contest')->where(array('id'=>$contestId))->find();
    	$this->display();
    }
}