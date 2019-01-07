<?php
/**
 * The project of FenPay is a CRM platform based on Weixin MP API.
 *
 * Use it to communicates with Weixin MP.
 *
 * PHP 5
 *
 * FenPay(tm) : FenPay (http://fenpay.com)
 * Copyright (c) in.chengdu.cn. (http://in.chengdu.cn)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) in.chengdu.cn. (http://in.chengdu.cn)
 * @link          http://fenpay.com FenPay(tm) Project
 * @since         FenPay(tm) v 0.0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('AppController', 'Controller');
/**
 * CouponCodes Controller
 *
 * Management coupon codes 
 *
 * @package		app.Controller
 */
class CouponCodesController extends AppController
{
    public $name = 'CouponCodes';
    
    public $components = array();
    
    public $layout = 'fenpay';
    
    public $uses = array('CouponCode', 'Shop');

    /**
     * The hybird page means maybe including image
     * and text at the same time.
     */
    public function index() {
        $this->set('filter', '');
        $this->set('start', '');
        $this->set('end', '');
        $conditions = array();
        
        if(isset($this->request->query['start']) && isset($this->request->query['end'])) {
            $conditions['CouponCode.created >='] = $this->request->query['start']; 
            $conditions['CouponCode.created <='] = $this->request->query['end']; 
            $this->set('start', $this->request->query['start']);
            $this->set('end', $this->request->query['end']);
        }
        
        if(isset($this->request->query['shop'])) {
            $conditions['CouponCode.shop_id'] = $this->request->query['shop'];
            $this->set('filter', $this->request->query['shop']);
        }
        
//         $criteria = array();
//         if($filter == -1) {
//             $criteria = array(
//                 'AutoReplyMessageNews.auto_reply_category_id' => NULL 
//             );
//         } else if(!empty($filter)) {
//             $criteria = array(
//                 'AutoReplyMessageNews.auto_reply_category_id' => intval($filter) 
//             );
//         }
        
        $this->paginate = array(
            'limit' => 20,
            'order' => array(
                "CouponCode.id" => "desc" 
            ),
            'paramType' => "querystring",
            'conditions' => $conditions,
        );
        $messages = $this->paginate('CouponCode');
        $this->set('messages', $messages);
        $this->set('shops', $this->Shop->find('list', array(
            'fields' => array('id', 'name'),
            'order' => array('id' => 'desc')
        )));
        $this->render('index');
    }

/**
 * Reverse invalid status of coupon code.
 * @param number $id
 */
    public function invalid($id = 0) {
        $this->autoLayout = false;
        $this->autoRender = false;
        if(!$id) {
            $this->Session->setFlash('设置失败', 'flash', array('class' => 'alert-error'));
            return $this->redirect($_SERVER['HTTP_REFERER']);
        }
        $code = $this->CouponCode->read(NULL, $id);
        if($code) {
            if($code['CouponCode']['invalid']) {
                $code['CouponCode']['invalid'] = 0;
                $this->Shop->decreaseInvalidCouponTotal($code['CouponCode']['shop_id']);
            } else {
                $code['CouponCode']['invalid'] = 1;
                $this->Shop->increaseInvalidCouponTotal($code['CouponCode']['shop_id']);
            }
            if($this->CouponCode->save($code)) {
                $this->Session->setFlash('设置成功', 'flash');
                return $this->redirect($_SERVER['HTTP_REFERER']);
            }
        }
        $this->Session->setFlash('设置失败', 'flash', array('class' => 'alert-error'));
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }
}