<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
    	// $this->display();
        $this->redirect('Index/contest', array('id'=>C('CONTESTID')));
    }

    public function logging(){
        $log = array(
            'part' => I('part'),
            'content' => I('content'),
            'ip' => get_client_ip(),
            'timestamp' => time()
            );
        M('logging')->add($log);
        echo json_encode(array('result'=>'ok'));
    }

    // 比赛
    public function contest(){
    	$contestId = I('id');
        session('kol', I('kol'));
    	$this->contest = M('contest')->where(array('id'=>$contestId))->find();
    	$this->display();
    }

    // 登陆
    public function login(){
    	if (isset($_SESSION['login_error'])) {
    		$this->error = $_SESSION['login_error'];
			session('login_error', null);
    	}
    	else {
    		$this->error = '';
    	}
    	$this->display();
    }

    // 处理登录
    public function login_handle(){
    	if(IS_POST){
			$user = M('user')->where(array('email'=>I('email')))->find();
			if(!$user){
				$error = '用户不存在';
				session('login_error', $error);
				$this->redirect('Index/login');
			}
			else if($user['password'] != sha1($user['salt'].I('password'))){
				$error = '密码错误';
				session('login_error', $error);
				$this->redirect('Index/login');
			}
			else if($user['activate'] == 0){
				$error = '邮箱未激活';
				session('login_error', $error);
				$this->redirect('Index/login');
			}
			else{
				session('uid', $user['id']);
                session('uemail', $user['email']);
                session('uportrait', $user['portrait']);
				session('urole', $user['role']);
                if ($_SESSION['urole'] == 1) {
                    $this->redirect('Judge/index');
                }
				elseif (isset($_SESSION['url'])) {
					$url = $_SESSION['url'];
					session('url', null);
					$this->redirect($url);
				}
				else {
					$this->redirect('Index/contest', array('id'=>C('CONTESTID')));
				}
			}
		}
		else{
			$this->redirect('Index/login');
		}
    }

    // 注册
    public function register(){
    	if (isset($_SESSION['register_error'])) {
    		$this->error = $_SESSION['register_error'];
			session('register_error', null);
    	}
    	else {
    		$this->error = '';
    	}
    	$this->display();
    }

    // 处理注册
    public function register_handle(){
    	if(IS_POST){
			$user = M('user')->where(array('email'=>I('email')))->find();
			if($user){
				$error = '邮箱已注册';
				session('register_error', $error);
				$this->redirect('Index/register');
			}
			else if(I('password') != I('password1')) {
				$error = '两次密码输入不一致';
				session('register_error', $error);
				$this->redirect('Index/register');
			}
			else{
				$activate_key = '';
				for ($i = 0; $i < 30; $i++) { 
					$activate_key .= rand(0, 9);
				}
                $salt = genRandStr();
                if (isset($_SESSION['kol'])) {
                    $kol = $_SESSION['kol'];
                }
                else {
                    $kol = '';
                }
				M('user')->data(array(
					"email" => I('email'),
					"password" => sha1($salt.I('password')),
                    "salt" => $salt,
					"activate" => 0,
					"activate_key" => md5($activate_key),
                    // 默认值
                    "nickname" => I('email'),
                    "gender" => 1,
                    "portrait" => "/img/user.png",
                    "introduction" => "暂无",
                    "kol" => $kol
					))->add();

				$email = M('email')->where(array('name'=>'激活邮箱'))->find();
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
				$email_content = $email_content[0].U('Index/activate', array('email'=>I('email'), 'activate_key'=>md5($activate_key)),false,true).$email_content[1];
				SendMail(I('email'), $email['title'], $email_content);

				session('register_email', I('email'));
				session('register_key', md5($activate_key));
				$this->redirect('Index/register_activate');
			}
		}
		else{
			$this->redirect('Index/register');
		}
    }

    // 等待激活
    public function register_activate(){
    	if (isset($_SESSION['register_email'])) {
    		$this->email = $_SESSION['register_email'];
			$this->display();
    	}
    	else {
    		$this->redirect('Index/register');
    	}
    }    

    // 重发激活邮件
    public function register_resend(){
    	$email = M('email')->where(array('name'=>'激活邮箱'))->find();
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
    	$email_content = $email_content[0].U('Index/activate', array('email'=>I('email'), 'activate_key'=>$_SESSION['register_key']),false,true).$email_content[1];
    	SendMail(I('email'), $email['title'], $email_content);

    	$this->redirect('Index/register_activate');
    }

    // 激活
    public function activate(){
    	M('user')->where(array('email'=>i('email'),'activate_key'=>I('activate_key')))->save(array('activate'=>1));
    	session('login_error', '邮箱已激活，请登录');
    	$this->redirect('Index/login');
    }

    // 退出登录
    public function logout(){
    	session('uid', null);
    	session('uemail', null);
        session('urole', null);
        session('uportrait', null);
    	$this->redirect('Index/login');
    }

    // 忘记密码
    public function forget(){
    	$this->display();
    }

    // 发送重置密码邮件
    public function reset_send(){
    	$reset_key = '';
    	for ($i = 0; $i < 30; $i++) { 
    		$reset_key .= rand(0, 9);
		}

		M('user')->where(array('email'=>I('email')))->save(array('reset_key'=>md5($reset_key)));

    	$email = M('email')->where(array('name'=>'重置密码'))->find();
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
        $email_content = $email_content[0].U('Index/reset', array('email'=>I('email'), 'reset_key'=>md5($reset_key)),false,true).$email_content[1];
    	SendMail(I('email'), $email['title'], $email_content);

    	return json_encode(array('status'=>200));
    }

    // 重置密码
    public function reset(){
    	if (isset($_SESSION['reset_error'])) {
    		$this->error = $_SESSION['reset_error'];
			session('reset_error', null);
    	}
    	else {
    		$this->error = '';
    	}
    	if (isset($_SESSION['reset_email'])) {
    		$this->email = $_SESSION['reset_email'];
    	}
    	else {
    		$this->email = I('email');
    	}
    	if (isset($_SESSION['reset_key'])) {
    		$this->reset_key = $_SESSION['reset_key'];
    	}
    	else {
    		$this->reset_key = I('reset_key');
    	}
    	$this->display();
    }

    // 处理重置密码
    public function reset_handle(){
    	if (I('password') != I('password1')) {
    		session('reset_error', '两次密码输入不一致');
    		session('reset_email', I('email'));
    		session('reset_key', I('reset_key'));
    		$this->redirect('Index/reset');
    	}
    	else {
    		session('reset_email', null);
    		session('reset_key', null);
    		session('login_error', '密码找回完成，请登录');
            $salt = genRandStr();
    		M('user')->where(array('email'=>I('email'), 'reset_key'=>I('reset_key')))->save(array('password'=>sha1($salt.I('password')),'salt'=>$salt));
    		$this->redirect('Index/login');
    	}
    }

    public function language(){
        session('lang', I('language'));
        echo json_encode(array('language'=>I('language')));
    }

    public function kol(){
        $kol = M('kol')->where(array('code'=>I('code')))->find();
        M('kol')->where(array('code'=>I('code')))->save(array('visit'=>($kol['visit'] + 1)));
        $kol['qrcode'] = '/upload/kol/'.$kol['code'].'.png';
        $this->kol = $kol;
        $this->display();
    }

    public function product(){
        $this->display();
    }

    public function cart(){
        $this->display();
    }

    public function awards(){
        $map = array();
        $map['result'] = array('neq', '');
        if (I('category') != 'all') {
            $map['category'] = array('eq', I('category'));
        }
        $this->category = I('category');

        if (I('year') != 'all') {
            $year = intval(I('year'));
            $contests = M('contest')->select();
            for ($i = 0; $i < count($contests); $i++) { 
                if (intval($contests[$i]['year']) == $year) {
                    $map['contest_id'] = array('eq',  $contests[$i]['id']);
                    break;
                }
            }
        }
        else {
            if (C('CURRENT_FINISH') == 0) {
                $map['contest_id'] = array('neq', C('CONTESTID'));
            }
        }
        $this->year = I('year');
        
        $submissions = M('submission')->field('id,contest_id,titlec,titlee,category,result,image')->where($map)->select();
        $contests = M('contest')->select();

        if (I('page') == '') {
            $page = 0;
        }
        else {
            $page = intval(I('page'));
        }
        $total = count($submissions);
        $this->first = 0;
        if ($total == 0) {
            $this->last = 0;
        }
        else if ($total % 10 == 0) {
            $this->last = intval($total) / 10 - 1;
        }
        else {
            $this->last = floor($total / 10);
        }
        if ($page == 0) {
            $this->previous = 0;
        }
        else {
            $this->previous = $page  - 1;
        }
        if ($page == $this->last) {
            $this->next = $this->last;
        }
        else {
            $this->next = $page + 1;
        }

        if ($page < 2) {
            $this->start = 0;
        }
        else {
            $this->start = $page - 2;
        }
        $end = $this->start + 4;
        if ($end > $this->last) {
            $end = $this->last;
        }
        $this->end = $end;
        $index = array();
        for ($i = $this->start; $i <= $this->end; $i++) { 
            array_push($index, $i);
        }
        $this->index = $index;
        $this->page = $page;
        $this->total = $total;

        $submissions = array_slice($submissions, $page * 10, 10);
        
        for ($i = 0; $i < count($submissions); $i++) { 
            for ($j = 0; $j < count($contests); $j++) { 
                if ($contests[$j]['id'] == $submissions[$i]['contest_id']) {
                    $submissions[$i]['year'] = intval($contests[$j]['year']);
                    break;
                }
            }
        }
        
        $this->submissions = $submissions;

        $years = array();
        if (C('CURRENT_FINISH') == 0) {
            $tmp = date("Y") - 1;
        }
        else {
            $tmp = date("Y");
        }
        for ($i = $tmp; $i >= 2006; $i--) {
            array_push($years, $i);
        }
        $this->years = $years;

        $this->categories = M()->query("select distinct(category) from submission where result != ''");
        $this->display();
    }

    public function downloads(){
        $this->downloads = M('download')->order('name desc')->select();
        $this->display();
    }

    public function moments(){
        $pictures = M('video')->where(array('src'=>''))->order('sort asc')->select();
        $map = array();
        $map['src'] = array('neq', '');
        $videos = M('video')->where($map)->order('sort asc')->select();
        $this->pictures = $pictures;
        $this->videos = $videos;
        $this->display();
    }

    public function moment_picture() {
        $pic = M('picture')->where(array('parent_id'=>I('pid')))->order('sort asc')->select();
        $offset = I('offset');
        while ($offset < 0) {
            $offset += count($pic);
        }
        while ($offset >= count($pic)) {
            $offset -= count($pic);
        }
        echo json_encode(array('pic'=>$pic[$offset]));
    }
}