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
App::uses('UploadComponent', 'Controller/Component');
/**
 * The component class is used to handle image attachment 
 * upload/delete progress.
 *
 * @package       app.Controller.Component
 */
class FigureAttachmentComponent extends UploadComponent {
    
/**
 * (non-PHPdoc)
 * @see UploadComponent::post()
 */
    public function post($print_response = false) {
        $result = parent::post($print_response);
        $this->generate_response($result, true);
    }
    
    /**
     * echo "{'url':'" . $info["url"] . "','title':'" . $title . "','original':'" . $info["originalName"] . "','state':'" . $info["state"] . "'}";
     * (non-PHPdoc)
     * @see UploadComponent::generate_response()
     */
    protected function generate_response($content, $print_response = true) {
        if ($print_response) {
            $resp = array();
            foreach($content['files'] as $i => $item) {
                $resp[$i]['url'] = $item->url;
                $resp[$i]['title'] = $item->name;
                $resp[$i]['original'] = $item->name;
                $resp[$i]['state'] = 'SUCCESS';
            }
            $json = json_encode(array_shift($resp));
            $this->body($json);
        }
        return $content;
    }
}