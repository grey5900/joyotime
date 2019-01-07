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
 * @package       app.Model
 * @since         FenPay(tm) v 0.0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * The CRUD controller of auto replay category. 
 *
 * @package       app.Controller
 */
class AutoReplyCategoriesController extends AppController {
    public $name = 'AutoReplyCategories';
    
    public $autoRender = false;
    public $autoLayout = false;
    public $layout = 'fenpay';
    
/**
 * (non-PHPdoc)
 * @see Controller::beforeFilter()
 */
	public function beforeFilter() {
		parent::beforeFilter();
	}
	
/**
 * 
 */
    public function index() {
        $cates = $this->AutoReplyCategory->find('all', array(
            'conditions' => array(
                'user_id' => $this->Auth->user('id') 
            ) 
        ));
        $this->set(compact('cates'));
        $this->autoRender = true;
        $this->autoLayout = true;
    }
    
	public function save() {
	    if($this->request->is('post')) {
	        if(TRUE == ($saved = $this->AutoReplyCategory->save($this->data))) {
	            $this->Session->setFlash('创建分类成功。', 'flash');
                return $this->resp(TRUE, array('message' => '创建分类成功。'));
	        }
	        return $this->resp(FALSE, array('message' => $this->errorMsg($this->AutoReplyCategory)));
	    }
	}
	
	public function delete($id = 0) {
	    if(!$id) return $this->resp(FALSE, array('message' => '缺少主键。'));
	    if($this->AutoReplyCategory->delete($id, false)) {
	        return $this->resp(TRUE, array('message' => '删除分类成功。'));
	    }
	    return $this->resp(TRUE, array('message' => '删除分类失败。'));
	}
}