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
 * WeixinApi Controller
 *
 * Handle events posted from Weixin server.
 *
 * @package		app.Controller
 */
class LocationsController extends AppController
{
    public $name = 'Locations';
	
	public $layout = 'mobile';
	
	public $components = array('InchengduApi');
	
	public $helpers = array('Location', 'Tip');
        
/**
 * The wap page for a detail location.
 * 
 * @param number $id The place id in api of inchengdu.
 */
    public function index($id = 0) {
        if(FALSE == ($id = intval($id))) {
            throw new NotFoundException();
        } 
        
        $place = $this->InchengduApi->place();
        $this->set('location', $place->getDetail($id));
        $tips = $place->listTip($id);
        $this->set('tip', $tips);
        $this->set('id', $id);
        $this->set('title_for_layout', 'IN成都');
    }
}