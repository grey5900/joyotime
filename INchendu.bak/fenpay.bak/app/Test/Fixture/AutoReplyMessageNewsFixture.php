<?php
class AutoReplyMessageNewsFixture extends CakeTestFixture {
	public $import = array('table' => 'auto_reply_message_news');

    public function init() {
        $this->records = array(
            array(
                'id' => 1,
                'auto_reply_message_id' => 1,
                'image_attachment_id' => 1,
                'auto_reply_category_id' => 1,
                'title' => 'The tested title',
                'view_total' => 1,
                'selected_by_location_extend' => 1,
            ),
            array(
                'id' => 2,
                'auto_reply_message_id' => 4,
                'image_attachment_id' => 1,
                'auto_reply_category_id' => 1,
                'title' => 'The tested title for link',
                'view_total' => 0,
                'selected_by_location_extend' => 0,
            ),
            array(
                'id' => 3,
                'auto_reply_message_id' => 5,
                'image_attachment_id' => 1,
                'auto_reply_category_id' => null,
                'title' => 'The tested title for map',
                'view_total' => 0,
                'selected_by_location_extend' => 0,
            ),
        );
        parent::init();
    }
}