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
class ImageAttachmentComponent extends UploadComponent {
    
/**
 * (non-PHPdoc)
 * @see UploadComponent::post()
 */
    public function post($print_response = false) {
        $result = parent::post($print_response);
        if($result[$this->options['param_name']] && is_array($result[$this->options['param_name']])) {
        	$controller = $this->_Collection->getController();
            $controller->loadModel('ImageAttachment');
        	foreach($result[$this->options['param_name']] as &$image) {
        		$data = (array) $image;
        		$data['title'] = $data['name'];
        		$data['original_url'] = $data['url'];
        		if(TRUE == ($saved = $controller->ImageAttachment->save($data))) {
        			$image->image_attachment_id = $saved['ImageAttachment']['id'];
        		}
        	}
        }
        $this->generate_response($result, true);
    }
    
/**
 * (non-PHPdoc)
 * @see UploadComponent::delete()
 */
    public function delete($print_response = false) {
        $result = parent::delete($print_response);
        $file_name = $this->get_file_name_param();
        $controller = $this->_Collection->getController();
        $controller->loadModel('ImageAttachment');
        $attach = $controller->ImageAttachment->find('first', array(
            'conditions' => array(
                'title' => $file_name,
                'user_id' => $controller->Auth->user('id')
            )
        ));
        if(isset($attach['ImageAttachment']['id'])) {
            $controller->ImageAttachment->delete($attach['ImageAttachment']['id']);
        }
        $this->generate_response($result, true);
    }
}