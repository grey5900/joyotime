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
App::uses('HttpSocket', 'Network/Http');
/**
 * Handle events posted from Fenpay server, 
 * then connect to api of inchengdu,
 * and return result-set wrpped in xml as response for Fenpay server.
 *
 * @package		app.Controller
 */
class MiddlewaresController extends AppController
{
    public $name = 'Middlewares';
    
    public $uses = array();
    
    public $components = array('WeixinApi', 'ApiHeader');
    
    public $autoRender = false;
    public $autoLayout = false;
    
/**
 * Handle all events about talking with Fenpay server 
 * used XML format in Weixin.
 * Response wrapped string to Fenpay server directly.
 * 
 * @return void
 */
    public function handle() {
        return $this->WeixinApi->getReceivedMessage()->getMessage()->toXML();
    }
    
    public function test() {
        $HttpSocket = new HttpSocket();
        // array query
//         $return = $HttpSocket->get(Configure::read('Api.baseurl').'place/search', array(
//             'keyword' => 'a',
//             'page_size' => 10
//         ));

//         $return = $HttpSocket->get(Configure::read('Api.baseurl').'place/get_detail', array(
//             'id' => 134855,
//         ));

        $uri = 'place/quick_search';
        $url = Configure::read('Api.baseurl') . $uri;
        
        $request = array(
        	'header' => $this->ApiHeader->toArray($url),
        );

        $return = $HttpSocket->get($url, array(
            'keyword' => '火锅',    
            'lat' => (double) 30.65740439438564,  
            'lng' => (double) 104.06593065261845,  
    		'page_size' => 10,
    		'page_num' => 1,
        ), $request);

        debug(json_decode($return, true));
        return ;

        $uri = 'place/get_detail';
        $url = Configure::read('Api.baseurl') . $uri;
        
        $request = array(
            'header' => $this->ApiHeader->toArray($url),
        );
        
        // place id: 12120, It's in成都工作室
        $return = $HttpSocket->get($url, array('id' => 5436, 'page_size' => 10), $request);
        
        
//         $uri = 'place/search';
//         $url = Configure::read('Api.baseurl') . $uri;
        
//         $request = array(
//             'header' => $this->ApiHeader->toArray($url),
//         );
        
//         $return = $HttpSocket->get($url, array('lat' => 23.134521, 'lng' => 113.358803, 'page_size' => 10), $request);
        
        $return = json_decode($return, true);
        debug($return);
    }
}