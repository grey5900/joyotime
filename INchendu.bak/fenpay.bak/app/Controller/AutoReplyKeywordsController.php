<?php
APP::uses('AppController', 'Controller');
class AutoReplyKeywordsController extends AppController {
    
    public $name = 'AutoReplyKeywords';
    
    public $autoRender = false;
    public $autoLayout = false;
    
    /**
     * Check tag is valid or not.
     * 
     * @return string JSON
     */
    public function check() {
        $return = array();
        $tag = $this->request->query('tag');
        if(!$tag) {
            $return['result'] = false;
            $return['data'][] = array(
            	'id' => $tag,
            	'text' => $tag
            );
            $return['message'] = '没有找到Keyword数据';
        } else {
            $exist = $this->AutoReplyKeyword->find('first', array(
            	'conditions' => array(
            		'name' => $tag,
            	    'user_id' => $this->Auth->user('id'),
            	),
                'recursive' => -1
            ));
            
            if($exist) {
                $return['result'] = false;
                $return['data'][] = array(
                    'id' => $exist['AutoReplyKeyword']['id'],
                    'text' => $tag
                );
            } else {
                $return['result'] = true;
                $return['data'][] = array(
                    'id' => $tag,
            		'text' => $tag
                );
            }
        }
        return json_encode($return);
    }
}