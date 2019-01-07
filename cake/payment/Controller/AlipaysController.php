<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class AlipaysController extends AppController {
	
	public $name = 'Alipays';

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();
	
	public $components = array('ReceiptApi' => array(
		'className' => 'FishSayingApi.Receipt'
	));
	
	public $autoLayout = false;
	
	public $autoRender = false;
	
	public function manual($receiptId, $price) {
		$resp = $this->ReceiptApi->edit(array(
			'receipt_id' => $receiptId,
			'trade_no' => 'manual_processed',
			'price' => $price
		));
		
		if($resp->isFail()) {
			var_dump("[$receiptId]".' fails to pay, because '.$resp->getMessage());
		} else {
			var_dump("[$receiptId]".' processed in successfully');
		}
		
		echo 'OK';
	}
	
	public function callback() {
		require_once(VENDORS."alipay/alipay.config.php");
		require_once(VENDORS."alipay/lib/alipay_notify.class.php");
		
		//计算得出通知验证结果
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyNotify();
		
		if($verify_result) {//验证成功
			
			//商户订单号
			$out_trade_no = $_POST['out_trade_no'];
		
			//支付宝交易号
			$trade_no = $_POST['trade_no'];
		
			//交易状态
			$trade_status = $_POST['trade_status'];
		
		
			if($_POST['trade_status'] == 'TRADE_FINISHED') {
				$resp = $this->ReceiptApi->edit(array(
					'receipt_id' => $out_trade_no,
					'trade_no' => $trade_no,
					'price' => $_POST['price']
				));
				
				if($resp->isFail()) {
					$this->log("[$out_trade_no]".' fails to pay, because '.$resp->getMessage(), 'alipay_processed');
				} else {
					$this->log("[$out_trade_no]".' processed in successfully', 'alipay_processed');
				}
			}
			else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
				$this->log($out_trade_no, 'alipay');
				$this->log($trade_no, 'alipay');
				$this->log($trade_status, 'alipay');
			}
		
			echo "success";		//请不要修改或删除
		}
		else {
			//验证失败
			echo "fail";
		
			$this->log($_POST, 'alipay_exceptions');
		}
	}
}
