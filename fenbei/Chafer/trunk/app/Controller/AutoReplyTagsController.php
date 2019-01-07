<?php
APP::uses('AppController', 'Controller');
class AutoReplyTagsController extends AppController {
    
    public $name = 'AutoReplyTags';
    
    public $autoRender = false;
    public $autoLayout = false;
    
    /**
     * Check tag is valid or not.
     * 
     * @param string $tag
     * @return string JSON
     */
    public function check($tag = '') {
        $return = array();
        if(!$tag) {
            $return['result'] = false;
            $return['message'] = '没有找到Tag数据';
        } else {
            $exist = $this->AutoReplyTag->find('first', array(
            	'conditions' => array(
            		'name' => $tag,
            	    'user_id' => $this->Auth->user('id'),
            	),
                'recursive' => -1
            ));
            $return['result'] = true;
            if($exist) {
                $return['id'] = $exist['AutoReplyTag']['id'];
            }
        }
        return json_encode($return);
    }
}