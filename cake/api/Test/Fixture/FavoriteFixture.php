<?php
class FavoriteFixture extends CakeTestFixture {
	public $import = array('model' => 'Favorite');
	
    public function init() {
        $this->records = array(
            array(
                '_id' => new MongoId('55f0c30f6f159aec6fad8ce3'),
                'title' => 'The first favorite',
                'size' => 0,     // The current size of voices.
                'user_id' => '51f0c30f6f159aec6fad8ce3',
                'isdefault' => 1, 
                'thumbnail' => array(),
                'voices' => array(
                ),    // [1, 2, 3, 4] voice_id
                'created'=>new MongoDate(),
                'modified'=>new MongoDate(),
            ),
        );
        parent::init();
    } 
}