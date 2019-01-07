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
 * Shops Controller
 *
 * @package		app.Controller
 */
class ShopsController extends AppController
{
    public $name = 'Shops';
    
    public $components = array();
    
    public $uses = array('Shop');
    
    public $layout = 'fenpay';

    public function index() {
        $this->paginate = array(
            'limit' => 20,
            'order' => array(
                "Shop.id" => "desc" 
            ),
            'paramType' => "querystring",
        );
        $shops = $this->paginate('Shop');
        $this->set('shops', $shops);
        $this->render('index');
    }

    public function add() {
        $this->autoLayout = false;
        $this->autoRender = false;
        if($this->request->is('post')) {
            if($this->Shop->save($this->data)) {
                return json_encode(array('result' => true, 'message' => '保存成功'));
            }
            return json_encode(array('result' => false, 'message' => $this->errorMsg($this->Shop)));
        }
        return json_encode(array('result' => false, 'message' => '保存失败'));
    }
    
/**
 * Remove one shop
 * @param number $id
 * @return json
 */
    public function delete($id = 0) {
        $this->autoLayout = false;
        $this->autoRender = false;
        $response = array();
        
        if(!$id) {
            $response['result'] = false;
            $response['message'] = '无法删除';
        } else {
            $exist = $this->Shop->read(null, $id);
            if(!$exist) {
                $response['result'] = false;
                $response['message'] = '你没有权限删除';
            } else {
                if($this->Shop->delete($id, TRUE)) {
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