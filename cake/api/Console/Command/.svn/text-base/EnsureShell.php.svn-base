<?php
/**
 * The project of FishSaying is a SNS platform which is
 * based on voice sharing for each other with journey.
 *
 * The RESTful style API is used to communicate with each client-side.
 *
 * PHP 5
 *
 * FishSaying(tm) : FishSaying (http://www.fishsaying.com)
 * Copyright (c) fishsaying.com. (http://fishsaying.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) fishsaying.com. (http://www.fishsaying.com)
 * @link          http://fishsaying.com FishSaying(tm) Project
 * @since         FishSaying(tm) v 0.0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * The command of ensureindex for collections of mongo.
 *
 * @package		app.Console.Command
 */
class EnsureShell extends AppShell {

    public $uses = array(
        'User', 'Checkout', 'Comment', 'Favorite', 'Follow',
        'Notification', 'Voice', 'Tag'
    );

    /**
     * An container for output messages.
     * 
     * @var array
     */
    private $desc = array();

    public function main() {
        $this->out("Please execute command line looks like below,
./Console/cake ensure [command_name]
Please try again.");
    }

    public function initial() {
        $source = $this->User->getDataSource();
        
        // users
        $collection = $source->getMongoCollection($this->User);
        $collection->deleteIndexes();
        $collection->ensureIndex(array('email' => 1, 'password' => 1));
        $collection->ensureIndex(array('username' => 1, 'password' => 1));
        $collection->ensureIndex(array('username' => 1), array("unique" => true));
        $collection->ensureIndex(array('is_contributor' => 1));
        $collection->ensureIndex(array('is_verified' => 1));
        $collection->ensureIndex(array('voice_income_total' => 1));
        $collection->ensureIndex(array('recommend' => 1));
        $collection->ensureIndex(array('gift.register.device_code' => 1));
        $collection->ensureIndex(array('delegation.sina_weibo.open_id' => 1));
        $collection->ensureIndex(array('delegation.qzone.open_id' => 1));
        $collection->ensureIndex(array('delegation.twitter.open_id' => 1));
        $collection->ensureIndex(array('delegation.facebook.open_id' => 1));
        
        // checkout
        $collection = $source->getMongoCollection($this->Checkout);
        $collection->deleteIndexes();
        $collection->ensureIndex(array('user_id' => 1, 'type' => 1, 'created' => -1));
        /**
         * CommentsController::isCommentable()
         * Checkout::isBought()
         */
        $collection->ensureIndex(array('type' => 1, 'user_id' => 1, 'voice_id' => 1));
        /**
         * ReceiptComponent::exist()
         */
        $collection->ensureIndex(array('receipt.identify' => 1));
        
        // comment
        $collection = $source->getMongoCollection($this->Comment);
        $collection->deleteIndexes();
        /**
         * CommentsController::index()
         */
        $collection->ensureIndex(array('voice_id' => 1, 'hide' => 1, 'modified' => -1));
        /**
         * CommentsController::isExist()
         */
        $collection->ensureIndex(array('voice_id' => 1, 'user_id' => 1));
        
        // favorite
        $collection = $source->getMongoCollection($this->Favorite);
        $collection->deleteIndexes();
        /**
         * FavoritesController::index()
         */
        $collection->ensureIndex(array('user_id' => 1, 'created' => 1));
//         $collection->ensureIndex(array('user_id' => 1, 'created' => 1));
        
        // follow
        $collection = $source->getMongoCollection($this->Follow);
        $collection->deleteIndexes();
        /**
         * FollowsController::deleteAll()
         * Follow::exist()
         * Follow::resetNewPosts()
         */
        $collection->ensureIndex(array('user_id' => 1, 'follower_id' => 1));
        $collection->ensureIndex(array('follower_id' => 1));
        $collection->ensureIndex(array('user_id' => 1, 'new_posts' => -1, 'modified' => -1));
        
        // notification
        $collection = $source->getMongoCollection($this->Notification);
        $collection->deleteIndexes();
        /**
         * Notification::newComment()
         */
        $collection->ensureIndex(array('user_id' => 1, 'type' => 1, 'voice_id' => 1));
        /**
         * NotificationsController::index()
         */
        $collection->ensureIndex(array('user_id' => 1, 'modified' => -1));
        
        // voice
        $collection = $source->getMongoCollection($this->Voice);
        $collection->deleteIndexes();
        /**
         * VoicesController::index()
         * - index page
         */
        $collection->ensureIndex(array('language' => 1, 'user_id' => 1, 'status' => 1, 'title' => 1, 'modified' => -1));
        /**
         * For my voices page
         */
        $collection->ensureIndex(array('user_id' => 1, 'status' => 1, 'modified' => -1));
        $collection->ensureIndex(array('voice' => 1));
        $collection->ensureIndex(array('location' => '2d'));
        $collection->ensureIndex(array('tags' => 1));
        
        // tag
        $collection = $source->getMongoCollection($this->Tag);
        $collection->deleteIndexes();
        $collection->ensureIndex(array('category' => 1, 'language' => 1));

        // keywords
        $db = $source->getMongoDb();
        $collection = $db->selectCollection('keywords');
        $collection->deleteIndexes();
        $collection->ensureIndex(array('criteria' => 1, 'count' => -1));
        // keyword_details
        $collection = $db->selectCollection('keyword_details');
        $collection->deleteIndexes();
        $collection->ensureIndex(array('criteria' => 1));
        
        $this->out('Created indexes successfully.');
        return true;
    }
}