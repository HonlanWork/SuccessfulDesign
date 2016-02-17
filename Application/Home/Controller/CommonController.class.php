<?php
namespace Home\Controller;
use Think\Controller;
class CommonController extends Controller{
	public function _initialize(){
		if(!isset($_SESSION['uid']) || !isset($_SESSION['uemail'])){
			$url = strstr($_SERVER["REQUEST_URI"], 'Home');
			session('url', $url);
			$this->redirect('Index/login');
		}
	}
}
?>