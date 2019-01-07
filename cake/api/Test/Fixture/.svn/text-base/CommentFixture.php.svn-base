<?php
class CommentFixture extends CakeTestFixture {
	public $import = array('model' => 'Comment');
	
    public function init() {
        $this->records = array(
            array(
                '_id' => new MongoId('71f0c30f6f159aec6fad8ce3'),
                'voice_id' => '51f225e26f159afa43e76aff',            
                'user_id' => '51f0c30f6f159aec6fad8ce3',
                'voice_title' => 'voice title first',
                'voice_user_id' => '51f0c30f6f159aec6fad8ce3',
                'score' => 5,
                'content' => 'The first comment content',
                'hide' => false,    
                'created' => new MongoDate(),
                'modified' => new MongoDate(),
            ),
        );
        parent::init();
    } 
}