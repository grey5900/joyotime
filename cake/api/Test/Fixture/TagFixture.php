<?php
class TagFixture extends CakeTestFixture {
	public $import = array('model' => 'Tag');
	
    public function init() {
        $this->records = array(
            array(
                '_id' => new MongoId('52302e996f159a11294df447'),
                'title' => 'mock_tag',
                'counter' => 1,
                'voices' => array(
                    'mock_voice_1'
                ),
                'visble' => 1,
                'created' => new MongoDate(),
                'modified' => new MongoDate(),
            )
        );
        parent::init();
    } 
}