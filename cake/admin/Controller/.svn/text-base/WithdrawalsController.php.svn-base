<?php
/**
 * The project of FishSaying is a SNS platform which is
 * based on voice sharing for each other with journey.
 *
 * The RESTful style API is used to communicate with each client-side.
 *
 * PHP 5
 *
 * FishSaying(tm) : FishSaying (http://www.fishsaying.com)
 * Copyright (c) fishsaying.com. (http://fishsaying.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) fishsaying.com. (http://www.fishsaying.com)
 * @link          http://fishsaying.com FishSaying(tm) Project
 * @since         FishSaying(tm) v 0.0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('AppController', 'Controller');
App::uses('Withdrawal', 'Model');
/**
 * FishSaying Controller
 *
 * Handle events posted from fishsaying server.
 *
 * @package		app.Controller
 */
class WithdrawalsController extends AppController
{
    public $name = 'Withdrawals';
    
    public $layout = 'fishsaying';
    
    public $components = array(
        'WithdrawalApi' => array(
            'className' => 'FishSayingApi.Withdrawal'
        )
    );
    
    public $uses = array('Withdrawal');
    
/**
 * (non-PHPdoc)
 * @see Controller::beforeFilter()
 */
    public function beforeFilter() {
    	parent::beforeFilter();
    }
    
    public function index() {
        return $this->no_process_yet();
    }

    public function no_process_yet() {
        $limit = 20;
        $items = array();
        $total = 0;
        
        $query = array(
            'processed' => Withdrawal::NOT_PROCESSED_YET,
            'page' => $this->request->query('page'),
            'limit' => $limit
        );
        $this->paginate = array(
    		'limit' => $limit,
    		'paramType' => "querystring",
        );
        $responser = $this->WithdrawalApi->index($query);
        if(!$responser->isFail()) {
            $cos = $responser->getData();
            $this->Withdrawal->results = $cos['items'];
            $this->Withdrawal->count = $cos['total'];
            $items = $this->paginate('Withdrawal');
            $items = is_array($items) ? $items : array();
            $total = $cos['total'];
        } else {
            $this->Session->setFlash($responser->getMessage());
        }
        
        $this->set('items', $items);
        $this->set('total', $total);
        $this->set('active', 'withdrawals');
    }

    public function processed() {
        $limit = 20;
        $page = isset($this->request->query['page']) ? $this->request->query('page') : 1;
        $query = array(
        	'processed' => implode(',', array(Withdrawal::REVERTED, Withdrawal::PROCESSED)),
            'page' => $this->request->query('page'),
            'limit' => $limit
        );
        $this->paginate = array(
    		'limit' => $limit,
    		'paramType' => "querystring",
        );
        $responser = $this->WithdrawalApi->index($query);
        if(!$responser->isFail()) {
            $cos = $responser->getData();
        	$this->Withdrawal->results = $cos['items'];
        	$this->Withdrawal->count = $cos['total'];
        	$items = $this->paginate('Withdrawal');
        	$items = is_array($items) ? $items : array();
        	$total = $cos['total'];
        } else {
            $this->Session->setFlash($responser->getMessage());
        }
        
        $this->set('items', $items);
        $this->set('total', $total);
        $this->set('active', 'withdrawals');
    }
    
    public function process($id = '') {
        $this->autoLayout = false;
        $this->autoRender = false;
        if(!$id) {
            $this->Session->setFlash(__('缺少提现申请ID'));
            $this->redirect('/withdrawals');
            return false;
        } 
        
        $responser = $this->WithdrawalApi->process($id, array('processed' => Withdrawal::PROCESSED));
        
        if(!$responser->isFail()) {
            $this->Session->setFlash(__('处理提现申请成功'));
        }
        $this->redirect('/withdrawals');
    }
    
    public function revert($id = '') {
        $this->autoLayout = false;
        $this->autoRender = false;
        if(!$id) {
            return $this->resp(false, __('缺少提现申请ID'));
        }
        $reason = $this->request->query('reason');
        if(!$reason) {
            return $this->resp(false, __('请填写驳回理由'));
        }
        $responser = $this->WithdrawalApi->revert(array(
            'checkout_id' => $id,
            'reason' => $reason
        ));
        if(!$responser->isFail()) {
            return $this->resp(true, __('驳回成功'));
        } 
        return $this->resp(false, __('驳回失败，').$responser->getMessage());
    }
}