<?php
namespace Home\Controller;
use Think\Controller;
class BackController extends Controller{
	public function _initialize(){
		if(isset($_SESSION['urole']) || $_SESSION['urole'] > 0){
			// $url = strstr($_SERVER["REQUEST_URI"], 'Home');
			// session('url', $url);
			// $this->redirect('Index/login');
		}
	}
}
?>