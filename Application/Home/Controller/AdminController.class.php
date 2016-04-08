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
}
?>