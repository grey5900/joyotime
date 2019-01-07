<?php
class AutoReplyLocationFixture extends CakeTestFixture {
	public $import = array('model' => 'AutoReplyLocation');

    public function init() {
        $this->records = array(
            array(
                'id' => 1,
                'user_id' => 1,
                'title' => 'The first location',
                'image_attachment_id' => 1,
                'longitude' => 104.04648211586915,
                'latitude' => 30.6438007,
                'map_url' => 'http://map.soso.com',
                'address' => 'test address',
                'request_total' => 1,
                'description' => 'test description for first location',
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s') 
            ), 
            array(
                'id' => 2,
                'user_id' => 1,
                'title' => 'The second location',
                'image_attachment_id' => 1,
                'longitude' => 104.14648211586915,
                'latitude' => 30.7438007,
                'map_url' => 'http://map.soso.com/2',
                'address' => 'test address for second location',
                'request_total' => 0,
                'description' => 'test description for second location',
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s') 
            ), 
        );
        parent::init();
    }
}