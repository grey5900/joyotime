<?php
App::uses('AppHelper', 'View/Helper');

class LinkHelper extends AppHelper {
    
    public $helpers = array('Html');
    
/**
 * <a data-id="<?php echo $item['AutoReplyMessage']['id']?>" data-url="/auto_reply_messages/delete/" 
        data-toggle="modal" role="button" href="#delete_msg" class="delete-link">删除</a>
 */
    public function remove($url, $id) {
        return $this->Html->link('删除', '#delete_msg', array(
            'data-id' => $id,
            'data-url' => $url,
            'data-del' => 'tr',
            'class' => 'delete-link',
            'role' => 'button',
            'data-toggle' => 'modal',
        ));
    }
}