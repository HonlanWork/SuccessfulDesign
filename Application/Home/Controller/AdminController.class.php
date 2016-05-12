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
        layout('admin');
		$this->display();
	}

    // 大赛信息
    public function contest(){
        $contest = M('contest')->where(array('id'=>C('CONTESTID')))->find();
        $contest['early_bird_time'] = date('Y-m-d', $contest['early_bird_time']);
        $contest['judge_first_from'] = date('Y-m-d', $contest['judge_first_from']);
        $contest['judge_first_to'] = date('Y-m-d', $contest['judge_first_to']);
        $contest['judge_second_from'] = date('Y-m-d', $contest['judge_second_from']);
        $contest['judge_second_to'] = date('Y-m-d', $contest['judge_second_to']);
        $this->contest = $contest;
        layout('admin');
        $this->display();
    }

    public function contest_edit(){
        M('contest')->where(array('id'=>C('CONTESTID')))->save(array(
            'name' => I('name'),
            'year' => I('year'),
            'fee' => I('fee'),
            'early_bird_time' => strtotime(I('early_bird_time')),
            'early_bird_fee' => I('early_bird_fee'),
            'judge_first_from' => strtotime(I('judge_first_from')),
            'judge_first_to' => strtotime(I('judge_first_to')),
            'judge_second_from' => strtotime(I('judge_second_from')),
            'judge_second_to' => strtotime(I('judge_second_to')),
            ));
        $this->redirect('Admin/contest');
    }

    // 审核支付
	public function contest_pay_confirm(){
		$this->submissions = M('submission')->where(array('contest_id'=>C('CONTESTID'), 'ispaied'=>0, 'pay_confirm'=>1))->select();
        layout('admin');
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

    // 作品管理 
	public function submissions() {
		$this->notpaid = M('submission')->where(array('contest_id'=>C('CONTESTID'),'ispaied'=>0))->select();
        $this->count1 = count($this->notpaid);
		$this->paid_but_notsubmitted = M('submission')->where(array('contest_id'=>C('CONTESTID'),'ispaied'=>1,'issubmitted'=>0))->select();
        $this->count2 = count($this->paid_but_notsubmitted);
		$this->submitted = M('submission')->where(array('contest_id'=>C('CONTESTID'),'ispaied'=>1,'issubmitted'=>1))->select();
        $this->count3 = count($this->submitted);
        layout('admin');
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

    public function export_excel(){
        $xlsName = "submission";
        $xlsCell = array(
            array('id','序号'),
            array('titlec','中文名'),
            array('titlee','英文名'),
            array('category','分类'),
            array('companyc','公司中文名'),
            array('companye','公司英文名'),
            array('position','职位'),
            array('email','邮箱'), 
            array('companyp','公司电话'), 
            array('cellphone','手机'), 
            array('addts','发布日期'), 
            array('introductionc','简述中文'), 
            array('introductione','简述英文'), 
            array('demandc','项目需求中文'), 
            array('demande','项目需求英文'), 
            array('challengec','面临挑战中文'), 
            array('challengee','面临挑战英文'), 
            array('costc','预算评估中文'), 
            array('coste','预算评估英文'), 
            array('solutionc','设计解决方案中文'), 
            array('solutione','设计解决方案英文'), 
            array('conclusionc','项目成效总结中文'), 
            array('conclusione','项目成效总结英文'), 
            array('ispaied','是否支付'), 
            array('issubmitted','是否提交'), 
            array('submitts','提交时间')
            ); 
        $xlsData = M('submission')->where(array('contest_id'=>C('CONTESTID')))->field('id,titlec,titlee,category,companyc,companye,position,email,companyp,cellphone,addts,introductionc,introductione,demandc,demande,challengec,challengee,costc,coste,solutionc,solutione,conclusionc,conclusione,ispaied,issubmitted,submitts')->select();

        $xlsTitle = iconv('utf-8', 'gb2312', $xlsName);//文件名称
        $fileName = '作品汇总_'.date('YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($xlsCell);
        $dataNum = count($xlsData);
        vendor("PHPExcel.PHPExcel");
       
        $objPHPExcel = new \PHPExcel();
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
        
        for($i = 0; $i < $cellNum; $i++){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'1', $xlsCell[$i][1]); 
        } 
          // Miscellaneous glyphs, UTF-8   
        for($i = 0; $i < $dataNum; $i++){
            for($j = 0; $j < $cellNum; $j++){
                if ($j == 25 && $xlsData[$i][$xlsCell[$j][0]] != '') {
                    $xlsData[$i][$xlsCell[$j][0]] = date('Y-m-d H:i:s', $xlsData[$i][$xlsCell[$j][0]]);  
                }
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+2), $xlsData[$i][$xlsCell[$j][0]]);
            }             
        }  
        
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  
        $objWriter->save('php://output'); 
        exit; 
    }

    // 用户管理
	public function users() {
		$this->users = M('user')->order('role desc')->select();
        layout('admin');
		$this->display();
	}

	public function reset_password() {
		$user = M('user')->where(array('id'=>I('id')))->find();
		M('user')->where(array('id'=>I('id')))->save(array('password'=>sha1($user['salt'].'123456')));
		$this->redirect('Admin/users');
	}

    public function set_as_common(){
        M('user')->where(array('id'=>I('id')))->save(array('role'=>0));
        $this->redirect('Admin/users');
    }

    public function set_as_judge(){
        M('user')->where(array('id'=>I('id')))->save(array('role'=>1));
        $this->redirect('Admin/users');
    }

    // 编辑翻译
	public function translations() {
        layout('admin');
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

    // 第一轮审核
    public function judge_first(){
    	$assignments = M('judge_first')->where(array('contest_id'=>C('CONTESTID')))->select();
    	$judges = array();
    	for ($i = 0; $i < count($assignments); $i++) {
    		if (!array_key_exists($assignments[$i]['user_id'], $judges)) {
    			// 总数量，已完成
    			$judges[$assignments[$i]['user_id']] = [0,0];
    		}
    		$judges[$assignments[$i]['user_id']][0] += 1;
    		if ($assignments[$i]['yes_or_no'] != '') {
    			$judges[$assignments[$i]['user_id']][1] += 1;
    		}
    	}
    	$tmp = array();
    	foreach ($judges as $key => $value) {
    		$t = M('user')->where(array('id'=>$key))->find();
    		$tmp[] = array('email'=>$t['email'], 'all'=>$value[0], 'finished'=>$value[1]);
    	}
    	$this->judges = $tmp;

        $data = array();
        for ($i = 0; $i < count($assignments); $i++) { 
            if (!array_key_exists($assignments[$i]['submission_id'], $data)) {
                // 已评审数量，yes数量
                $data[$assignments[$i]['submission_id']] = [0, 0];
            }
            if ($assignments[$i]['yes_or_no'] != '') {
                $data[$assignments[$i]['submission_id']][0] += 1;
                if ($assignments[$i]['yes_or_no'] == 'yes') {
                    $data[$assignments[$i]['submission_id']][1] += 1;
                }
            }
        }

        $stat = array('-1' => 0, '0' => 0, '1' => 0, '2' => 0, '3' => 0);
        foreach ($data as $key => $value) {
            if ($value[0] != 3) {
                $stat['-1'] += 1;
            }
            else {
                if ($value[1] == 0) {
                    $stat['0'] += 1;
                }
                if ($value[1] == 1) {
                    $stat['1'] += 1;
                }
                if ($value[1] == 2) {
                    $stat['2'] += 1;
                }
                if ($value[1] == 3) {
                    $stat['3'] += 1;
                }
            }
        }
        $this->stat = $stat;

        layout('admin');
    	$this->display();
    }

    public function judge_first_assign(){
        M('judge_first')->where(array('contest_id'=>C('CONTESTID')))->delete();

    	$submissions = M('submission')->where(array('contest_id'=>C('CONTESTID'),'ispaied'=>1,'issubmitted'=>1))->select();
    	$users = M('user')->where(array('role'=>1))->select();

    	$number = 0;
    	for ($i = 0; $i < count($submissions); $i++) { 
    		for ($j = 0; $j < 3; $j++) { 
    			M('judge_first')->add(array('contest_id'=>C('CONTESTID'),'submission_id'=>$submissions[$i]['id'],'user_id'=>$users[$number]['id'],'yes_or_no'=>'','timestamp'=>''));
    			$number += 1;
    			if ($number >= count($users)) {
    				$number = 0;
    			}
    		}
    	}
    	$this->redirect('Admin/judge_first');
    }

    public function judge_first_delete(){
        M('judge_first')->where(array('contest_id'=>C('CONTESTID')))->delete();
        $this->redirect('Admin/judge_first');
    }

    // 第二轮评审
    public function judge_second() {
        $assignments = M('judge_second')->where(array('contest_id'=>C('CONTESTID')))->select();
        $judges = array();
        for ($i = 0; $i < count($assignments); $i++) { 
            if (!array_key_exists($assignments[$i]['user_id'], $judges)) {
                $judges[$assignments[$i]['user_id']] = [0, 0];
            }
            $judges[$assignments[$i]['user_id']][0] += 1;
            if ($assignments[$i]['is_judged'] == 1) {
                $judges[$assignments[$i]['user_id']][1] += 1;
            }
        }
        $tmp = array();
        foreach ($judges as $key => $value) {
            $t = M('user')->where(array('id'=>$key))->find();
            $tmp[] = array('email'=>$t['email'], 'all'=>$value[0], 'finished'=>$value[1]);
        }
        $this->judges = $tmp;

        $data = array();
        for ($i = 0; $i < count($assignments); $i++) { 
            if (!array_key_exists($assignments[$i]['submission_id'], $data)) {
                $data[$assignments[$i]['submission_id']] = [0, 0, 0];
            }
            $data[$assignments[$i]['submission_id']][0] += 1;
            if ($assignments[$i]['is_judged'] == 1) {
                $data[$assignments[$i]['submission_id']][1] += 1;
                $data[$assignments[$i]['submission_id']][2] += $assignments[$i]['strategy'] + $assignments[$i]['process'] + $assignments[$i]['result'];
            }
        }
        $stat = array('-1' => 0, '0~5' => 0, '5~10' => 0, '10~15' => 0);
        foreach ($data as $key => $value) {
            if ($value[0] != $value[1]) {
                $stat['-1'] += 1;
            }
            else {
                $tmp = floatval($value[2]) / $value[1];
                if ($tmp <= 5) {
                    $stat['0~5'] += 1;
                }
                elseif ($tmp <= 10) {
                    $stat['5~10'] += 1;
                }
                else {
                    $stat['10~15'] += 1;
                }
            }
        }
        $this->stat = $stat;

        layout('admin');
        $this->display();
    }

    public function judge_second_assign(){
        M('judge_second')->where(array('contest_id'=>C('CONTESTID')))->delete();

        M('submission')->where(array('contest_id'=>C('CONTESTID')))->save(array('judge_first'=>''));
        $assignments = M('judge_first')->where(array('contest_id'=>C('CONTESTID')))->select();
        $data = array();
        for ($i = 0; $i < count($assignments); $i++) { 
            if (!array_key_exists($assignments[$i]['submission_id'], $data)) {
                // 已评审数量，yes数量
                $data[$assignments[$i]['submission_id']] = 0;
            }
            if ($assignments[$i]['yes_or_no'] == 'yes') {
                $data[$assignments[$i]['submission_id']] += 1;
            }
        }
        foreach ($data as $key => $value) {
            if ($value >= 2) {
                M('submission')->where(array('contest_id'=>C('CONTESTID'), 'id'=>$key))->save(array('judge_first'=>'yes'));
            }
            else {
                M('submission')->where(array('contest_id'=>C('CONTESTID'), 'id'=>$key))->save(array('judge_first'=>'no'));
            }
        }

        $submissions = M('submission')->where(array('contest_id'=>C('CONTESTID'), 'judge_first'=>'yes'))->select();

        $judges = M('user')->where(array('role'=>1))->select();

        for ($i = 0; $i < count($submissions); $i++) { 
            for ($j = 0; $j < count($judges); $j++) { 
                M('judge_second')->add(array('contest_id'=>C('CONTESTID'), 'submission_id'=>$submissions[$i]['id'], 'user_id'=>$judges[$j]['id'], 'strategy'=>0, 'process'=>0, 'result'=>0, 'comment'=>'', 'timestamp'=>'', 'is_judged'=>0));
            }
        }
        $this->redirect('Admin/judge_second');
    }

    public function judge_second_delete(){
        M('judge_second')->where(array('contest_id'=>C('CONTESTID')))->delete();
        $this->redirect('Admin/judge_second');
    }

    // kol管理
    public function kol(){
        $kol = M('kol')->where(array('contest_id'=>C('CONTESTID')))->select();
        for ($i = 0; $i < count($kol); $i++) { 
            $kol[$i]['pay'] = M('submission')->where(array('contest_id'=>C('CONTESTID'),'kol'=>$kol[$i]['code'],'ispaied'=>1))->count();
            $kol[$i]['transform'] = round(100 * floatval($kol[$i]['pay']) / floatval($kol[$i]['visit'])).'%';
            $kol[$i]['code'] = U('Index/kol',array('code'=>$kol[$i]['code']),false,true);
        }
        $this->kol = $kol;
        layout('admin');
        $this->display();
    }

    public function kol_add(){
        $code = time().sha1(genRandStr());
        M('kol')->add(array('contest_id'=>C('CONTESTID'),'name'=>I('name'),'code'=>$code));
        vendor("phpqrcode.phpqrcode");
        $value = U('Index/contest',array('kol'=>$code),false,true);
        $filename = 'Public/upload/kol/'.$code.'.png'; 
        $errorCorrectionLevel = "L"; 
        $matrixPointSize = "6"; 
        \QRcode::png($value, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
        $this->redirect('Admin/kol');
    }

    public function kol_delete(){
        $kol = M('kol')->where(array('id'=>I('id')))->find();
        M('kol')->where(array('id'=>I('id')))->delete();
        M('user')->where(array('kol'=>$kol['code']))->save(array('kol'=>''));
        M('submission')->where(array('kol'=>$kol['code']))->save(array('kol'=>''));
        $this->redirect('Admin/kol');
    }

    // 邀请码管理
    public function invitation() {
        $this->invitations = M('invitation')->where(array('contest_id'=>C('CONTESTID')))->select();
        layout('admin');
        $this->display();
    }

    public function invitation_add() {
        M('invitation')->data(array(
            'contest_id' => C('CONTESTID'),
            'code' => substr(md5(genRandStr()), 0, 12),
            'count' => I('count'),
            'remain' => I('count'),
            'name' => I('name'),
            'submissions' => '',
            // 'discount' => I('discount')
            'discount' => 0
            ))->add();
        $this->redirect('Admin/invitation');
    }

    public function invitation_delete(){
        M('invitation')->where(array('contest_id'=>C('CONTESTID'), 'id'=>I('id')))->delete();
        $this->redirect('Admin/invitation');
    }
}
?>