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

    public function contest_close(){
        M('contest')->where(array('id'=>C('CONTESTID')))->save(array('open'=>0));
        M('submission')->where(array('contest_id'=>C('CONTESTID'),'ispaied'=>1,'issubmitted'=>0))->save(array('ispaied'=>1,'issubmitted'=>1));
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

        $all = M('submission')->where(array('contest_id'=>C('CONTESTID')))->select();
        $tmp = array();
        for ($i = 0; $i < count($all); $i++) { 
            $cate = $all[$i]['category'];
            if (array_key_exists($cate, $tmp)) {
                $tmp[$cate] += 1;
            }
            else {
                $tmp[$cate] = 1;
            }
        }
        $category = array();
        $category[0] = array();
        $category[1] = array();
        foreach ($tmp as $key => $value) {
            $category[0][] = $key;
            $category[1][] = $value;
        }
        layout('admin');
        $this->category = json_encode($category);
        $this->display();
	}

	public function mark_as_paid() {
		M('submission')->where(array('id'=>I('id')))->save(array('ispaied'=>1, 'pay_confirm'=>0, 'issubmitted'=>0));
		$this->redirect('Admin/submissions');
	}

	public function mark_as_notpaid() {
		M('submission')->where(array('id'=>I('id')))->save(array('ispaied'=>0, 'issubmitted'=>0));
		$this->redirect('Admin/submissions');
	}

	public function mark_as_submitted() {
		M('submission')->where(array('id'=>I('id')))->save(array('ispaied'=>1, 'pay_confirm'=>0, 'issubmitted'=>1));
		$this->redirect('Admin/submissions');
	}

	public function mark_as_notsubmitted() {
		M('submission')->where(array('id'=>I('id')))->save(array('ispaied'=>1, 'pay_confirm'=>0, 'issubmitted'=>0));
		$this->redirect('Admin/submissions');
	}

    public function delete_submission(){
        M('submission')->where(array('id'=>I('id')))->delete();
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
            array('submitts','提交时间'),
            array('invitation','合伙伙伴'),
            ); 
        $xlsData = M('submission')->where(array('contest_id'=>C('CONTESTID')))->field('id,titlec,titlee,category,companyc,companye,position,email,companyp,cellphone,addts,introductionc,introductione,demandc,demande,challengec,challengee,costc,coste,solutionc,solutione,conclusionc,conclusione,ispaied,issubmitted,submitts,invitation')->select();

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

    public function user() {
        $user = M('user')->where(array('id'=>I('id')))->find();
        $categories = M()->query("select distinct(category) from submission where contest_id=".C('CONTESTID'));
        $this->category = $user['category'];
        $category = split(',', $user['category']);
        $tmp = [];
        for ($i = 0; $i < count($categories); $i++) {
            $flag = false;
            for ($j = 0; $j < count($category); $j++) { 
                if ($categories[$i]['category'] == $category[$j]) {
                    $flag = true;
                    break;
                }
            }
            if ($flag) {
                $tmp[] = [$categories[$i]['category'], 1];
             } 
             else {
                $tmp[] = [$categories[$i]['category'], 0];
             }
        }
        $this->categories = $tmp;
        $this->user = $user;
        layout('admin');
        $this->display();
    }

    public function user_add(){
        layout('admin');
        $this->display();
    }

    public function user_add_handle(){
        $salt = genRandStr();

        M('user')->data(array(
            'email' => I('email'),
            'password' => sha1($salt.I('password')),
            'salt' => $salt,
            'activate' => 1,
            'role' => I('role'),
            "nickname" => I('email'),
            "gender" => 1,
            "portrait" => "/img/user.png",
            "introduction" => "暂无",
            'kol' => ''
            ))->add();

        $this->redirect('Admin/users');
    }

    public function user_edit() {
        M('user')->where(array('id'=>I('id')))->save(array('category'=>I('categories'),'note'=>I('note')));
        $this->redirect('Admin/users');
    }

	public function reset_password() {
		$user = M('user')->where(array('id'=>I('id')))->find();
		M('user')->where(array('id'=>I('id')))->save(array('password'=>sha1($user['salt'].'123456')));
		$this->redirect('Admin/users');
	}

    public function set_as_common(){
        M('user')->where(array('id'=>I('id')))->save(array('role'=>0,'judge_code'=>''));
        $this->redirect('Admin/users');
    }

    public function set_as_judge(){
        M('user')->where(array('id'=>I('id')))->save(array('role'=>1,'judge_code'=>sha1(genRandStr())));
        $this->redirect('Admin/users');
    }

    public function activate(){
        M('user')->where(array('id'=>I('id')))->save(array('activate'=>1,'activate_key'=>''));
        $this->redirect('Admin/users');
    }

    // 编辑翻译
	public function translations() {
        layout('admin');
		$this->display();
	}

	public function translations_search() {
        $map = array();
        $map['ch'] = array('like', '%'.I('keyword').'%');
		$translations = M('translation')->where($map)->select();
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
    			// 总数量，已完成，通过率
    			$judges[$assignments[$i]['user_id']] = [0,0,0];
    		}
    		$judges[$assignments[$i]['user_id']][0] += 1;
    		if ($assignments[$i]['yes_or_no'] != '') {
    			$judges[$assignments[$i]['user_id']][1] += 1;
    		}
            if ($assignments[$i]['yes_or_no'] == 'yes') {
                $judges[$assignments[$i]['user_id']][2] += 1;
            }
    	}
    	$tmp = array();
    	foreach ($judges as $key => $value) {
    		$t = M('user')->where(array('id'=>$key))->find();
    		$tmp[] = array('email'=>$t['email'], 'all'=>$value[0], 'finished'=>$value[1], 'inrate'=>round(floatval($value[2]) / floatval($value[0]), 4));
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

        $begin = 0;
    	for ($i = 0; $i < count($submissions); $i++) {
            $sum = 0;

    		for ($j = $begin; ; $j++) {
                if ($j == count($users)) {
                    $j = 0;
                }
                $flag = false;
                $tmp = split(',', $users[$j]['category']);
                for ($k = 0; $k < count($tmp); $k++) {
                    if ($tmp[$k] == $submissions[$i]['category']) {
                        $flag = true;
                        break;
                    }
                }
                if ($flag) {
                    M('judge_first')->add(array('contest_id'=>C('CONTESTID'),'submission_id'=>$submissions[$i]['id'],'user_id'=>$users[$j]['id'],'yes_or_no'=>'','timestamp'=>''));
                    $sum += 1;
                    $begin = $j + 1;

                    if ($begin == count($users)) {
                        $begin = 0;
                    }

                    if ($sum == 3) {
                        break;
                    }
                }
    		}
    	}
    	$this->redirect('Admin/judge_first');
    }

    public function judge_first_delete(){
        M('judge_first')->where(array('contest_id'=>C('CONTESTID')))->delete();
        $this->redirect('Admin/judge_first');
    }

    public function judge_first_export(){
        $xlsName = "submission";
        $xlsCell = array(
            array('id','序号'),
            array('titlec','中文名'),
            array('titlee','英文名'),
            array('judge_first_extra','是否人工入围'),
            array('judge_result','评审结果'),
            array('judge1','评委1'),
            array('judge2','评委2'),
            array('judge3','评委3'),
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
            array('invitation','合伙伙伴'),
            ); 
        $xlsData = M('submission')->where(array('contest_id'=>C('CONTESTID'), 'issubmitted'=>1))->select();

        $judges = M('user')->where(array('role'=>1))->select();
        $tmp = array();
        for ($i = 0; $i < count($judges); $i++) { 
            $tmp[$judges[$i]['id']] = $judges[$i]['email'];
        }
        $judges = $tmp;
        $judgements = M('judge_first')->where(array('contest_id'=>C('CONTESTID')))->select();

        $xlsTitle = iconv('utf-8', 'gb2312', $xlsName);//文件名称
        $fileName = '第一轮评审结果_'.date('YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($xlsCell);
        $dataNum = count($xlsData);
        vendor("PHPExcel.PHPExcel");

        for ($i = 0; $i < count($xlsData); $i++) {
            $judge1 = '';
            $judge2 = '';
            $judge3 = '';
            $judge_result = '';
            $judge_count = 0;
            $yes_count = 0;
            $no_count = 0; 
            for ($j = 0; $j < count($judgements); $j++) { 
                if ($judgements[$j]['submission_id'] == $xlsData[$i]['id']) {
                    if ($judgements[$j]['yes_or_no'] == 'yes') {
                        $yes_count += 1;
                    }
                    elseif ($judgements[$j]['yes_or_no'] == 'no') {
                        $no_count += 1;
                    }
                    $judge_count += 1;
                    if ($judge_count == 1) {
                        $judge1 = $judges[$judgements[$j]['user_id']].' '.$judgements[$j]['yes_or_no'];
                    }
                    elseif ($judge_count == 2) {
                        $judge2 = $judges[$judgements[$j]['user_id']].' '.$judgements[$j]['yes_or_no'];
                    }
                    if ($judge_count == 3) {
                        $judge3 = $judges[$judgements[$j]['user_id']].' '.$judgements[$j]['yes_or_no'];
                        break;
                    }
                }
            }
            $xlsData[$i]['judge1'] = $judge1;
            $xlsData[$i]['judge2'] = $judge2;
            $xlsData[$i]['judge3'] = $judge3;
            if ($yes_count + $no_count == 3) {
                $judge_result = '已完成 ';
            }
            else {
                $judge_result = '未完成 ';
            }
            $judge_result .= $yes_count.'入围 '.$no_count.'淘汰';
            $xlsData[$i]['judge_result'] = $judge_result;
        }
       
        $objPHPExcel = new \PHPExcel();
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
        
        for($i = 0; $i < $cellNum; $i++){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'1', $xlsCell[$i][1]); 
        } 
          // Miscellaneous glyphs, UTF-8   
        for($i = 0; $i < $dataNum; $i++){
            for($j = 0; $j < $cellNum; $j++){
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
        $stat = array('-1' => 0, '0~30' => 0, '30~60' => 0, '60~90' => 0);
        foreach ($data as $key => $value) {
            if ($value[0] != $value[1]) {
                $stat['-1'] += 1;
            }
            else {
                $tmp = floatval($value[2]) / $value[1];
                if ($tmp <= 30) {
                    $stat['0~30'] += 1;
                }
                elseif ($tmp <= 60) {
                    $stat['30~60'] += 1;
                }
                else {
                    $stat['60~90'] += 1;
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

        $begin = 0;
        for ($i = 0; $i < count($submissions); $i++) {
            $sum = 0;

            for ($j = $begin; ; $j++) {
                if ($j == count($judges)) {
                    $j = 0;
                }
                $flag = false;
                $tmp = split(',', $judges[$j]['category']);
                for ($k = 0; $k < count($tmp); $k++) {
                    if ($tmp[$k] == $submissions[$i]['category']) {
                        $flag = true;
                        break;
                    }
                }
                if ($flag) {
                    M('judge_second')->add(array('contest_id'=>C('CONTESTID'), 'submission_id'=>$submissions[$i]['id'], 'user_id'=>$judges[$j]['id'], 'strategy'=>0, 'process'=>0, 'result'=>0, 'total'=>0, 'comment'=>'', 'timestamp'=>'', 'is_judged'=>0));

                    $sum += 1;
                    $begin = $j + 1;

                    if ($begin == count($judges)) {
                        $begin = 0;
                    }

                    if ($sum == 3) {
                        break;
                    }
                }
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