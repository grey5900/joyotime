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
 * Salers Controller
 *
 * @package		app.Controller
 */
class SalersController extends AppController
{
    public $name = 'Salers';
    
    public $components = array();
    
    public $uses = array('Saler', 'Shop');
    
    public $layout = 'fenpay';

    /**
     * The hybird page means maybe including image
     * and text at the same time.
     *
     * @param array $filter            
     */
    public function index($filter = array()) {
        $this->paginate = array(
            'limit' => 20,
            'order' => array(
                "Saler.id" => "desc" 
            ),
            'paramType' => "querystring",
        );
        $salers = $this->paginate('Saler');
        $this->set('salers', $salers);
        $this->render('index');
    }

    public function add() {
        if($this->request->is('post')) {
            if($this->Saler->save($this->data)) {
                $this->Shop->increaseSalerTotal($this->data['Saler']['shop_id']);
                $this->Session->setFlash('保存成功', 'flash');
                return $this->redirect('/salers/index');
            }
            $this->Session->setFlash($this->errorMsg($this->Saler), 'flash', array('class' => 'alert-error'));
        }
        $this->set('shops', $this->Shop->find('list', array(
            'fields' => array('id', 'name'),
            'order' => array('id' => 'desc')
        )));
        return $this->render('add');
    }
    
    public function edit($id = 0) {
        $this->request->data = $this->Saler->read(null, $id);
        $this->set('shops', $this->Shop->find('list', array(
    		'fields' => array('id', 'name'),
    		'order' => array('id' => 'desc')
        )));
        return $this->render('add');
    }
    
    public function delete($id = 0) {
        $this->autoLayout = false;
        $this->autoRender = false;
        $response = array();
        
        if(!$id) {
        	$response['result'] = false;
        	$response['message'] = '无法删除';
        } else {
        	$exist = $this->Saler->read(null, $id);
        	if(!$exist) {
        		$response['result'] = false;
        		$response['message'] = '你没有权限删除';
        	} else {
        		if($this->Saler->delete($id, TRUE)) {
        		    $this->Shop->decreaseSalerTotal($exist['Saler']['shop_id']);
        			$response['result'] = true;
        			$response['message'] = '删除成功。';
        		} else {
        			$response['result'] = false;
        			$response['message'] = '删除失败';
        		}
        	}
        }
        return json_encode($response);
    }
}