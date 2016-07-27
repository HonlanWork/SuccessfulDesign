<?php
namespace Home\Controller;
use Think\Controller;
class PayController extends Controller {
    public function promotion_pay_success(){
        M('promotion')->where(array('submission_id'=>I('submission_id'), 'promotion_code'=>I('promotion_code')))->save(array('ispaied'=>1));
        $email = M('email')->where(array('name'=>'购买推广'))->find();
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
        $submission = M('submission')->where(array('id'=>I('submission_id')))->find();
        $tmp = '作品中文名称：'.$submission['titlec'].'<br/>作品英文名称：'.$submission['titlee'].'<br/>作品类别：'.$submission['category'];
        $email_content = explode("^^^", $email_content);
        $email_content = $email_content[0].$tmp.$email_content[1];
        $admins = M('user')->where(array('role'=>2))->select();
        foreach ($admins as $key => $value) {
            SendMail($value['email'], $email['title'], $email_content);
        }
        $this->redirect('User/submissions');
    }

    public function promotion_pay_cancel(){
        $this->redirect('Contest/promotion');
    }

    public function promotion_pay_success_api(){
        M('promotion')->where(array('submission_id'=>I('submission_id'),'promotion_code'=>I('promotion_code')))->save(array('ispaied'=>1));
        $email = M('email')->where(array('name'=>'购买推广'))->find();
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
        $submission = M('submission')->where(array('id'=>I('submission_id')))->find();
        $tmp = '作品中文名称：'.$submission['titlec'].'<br/>作品英文名称：'.$submission['titlee'].'<br/>作品类别：'.$submission['category'];
        $email_content = explode("^^^", $email_content);
        $email_content = $email_content[0].$tmp.$email_content[1];
        $admins = M('user')->where(array('role'=>2))->select();
        foreach ($admins as $key => $value) {
            SendMail($value['email'], $email['title'], $email_content);
        }
        echo json_encode(array('submission_id'=>I('submission_id'), 'promotion_code'=>I('promotion_code')));
    }
}