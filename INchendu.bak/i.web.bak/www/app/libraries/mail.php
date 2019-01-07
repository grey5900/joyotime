<?php
/**
 * 发送邮件
 * Create by 2012-5-15
 * @author liuw
 * @copyright Copyright(c) 2012-2014 Liuw
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   

 // Code   
class Mail {

	var $conf;
	var $Mailer;
	
	public function __construct(){
		global $CI;
		require APPPATH . './libraries/phpmailer/class.phpmailer.php';
		$this->Mailer = new PHPMailer();
		$this->Mailer->IsSMTP();
		//设置参数
		$this->conf = $CI->config->item('email_serv');
		$smtp = $this->conf['smtp_server'];
		$this->Mailer->SMTPAuth = $smtp['auth'];
		$this->Mailer->Host = $smtp['host'];
		$this->Mailer->Port = $smtp['port'];
		$this->Mailer->SMTPSecure = $smtp['secure'];
		$this->Mailer->CharSet = $this->conf['charset'];
		$this->Mailer->Encoding = $this->conf['encoding'];
		$this->Mailer->Username = $this->conf['post_email'];
		$this->Mailer->Password = $this->conf['post_pwd'];
		$this->Mailer->From = $this->conf['post_email'];
		$this->Mailer->FromName = $this->conf['from_name'];
		$this->Mailer->WordWrap = $this->conf['word_wrap'];
	}
	
	public function send($toEmails, $subject=null, $body=null, $attachment=null){
		if($toEmails==null || empty($toEmails))
			die('收件人列表不能为空！');
		$this->Mailer->Subject = $subject != null && !empty($subject) ? $subject : $this->conf['default_subject'];
		$this->Mailer->Body = $body != null && !empty($body) ? $body : $this->conf['default_body'];
		$this->Mailer->IsHTML(true);
		if(!empty($attachment) && file_exists($attachment)){
			//有附件
			$filename = pathinfo($attachment, PATHINFO_BASENAME);
			$this->Mailer->AddAttachment($attachment, $filename);
		}
		foreach($toEmails as $key=>$toEmail){
			$this->Mailer->AddAddress($toEmail['mail'], isset($toEmail['name']) && !empty($toEmail['name']) ? $toEmail['name'] : $toEmail['mail']);
		}
		if(!$this->Mailer->Send()){
			return array('code'=>0,'data'=>'邮件发送失败了->'.$this->Mailer->ErrorInfo);
		}
		@unlink($attachment);
		return array('code'=>1,'data'=>'邮件已成功发送');
	}

}   
   
 // File end