<?php
class ImageAttachmentFixture extends CakeTestFixture {
	public $import = array('model' => 'ImageAttachment');

    public function init() {
        $this->records = array(
            array(
                'id' => 1,
                'user_id' => 1,
                'title' => 'the first image uploaded',
                'size' => 5000,
                'type' => 'image/jpeg',
                'original_url' => '/files/auto_replies/covers/1/test.jpg',
                'thumbnail_url' => '/files/auto_replies/covers/1/thumbnail/test.jpg',
                'delete_url' => '/upload/cover?file=test.jpg',
                'delete_type' => 'DELETE',
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s') 
            ) 
        );
        parent::init();
    }
}