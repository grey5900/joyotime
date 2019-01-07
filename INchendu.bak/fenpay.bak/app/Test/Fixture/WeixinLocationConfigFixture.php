<?php
class WeixinLocationConfigFixture extends CakeTestFixture {
	public $import = array('model' => 'WeixinLocationConfig');

    public function init() {
        $this->records = array(
            array(
                'id' => 1,
                'weixin_config_id' => 1,
                'image_attachment_id' => 1,
                'type' => 'multiply',
                'title' => 'test_title_location_config',
            ),
        );
        parent::init();
    }
}