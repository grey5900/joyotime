ALTER TABLE  `auto_reply_echos` ADD `enabled_location` INT( 1 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `auto_reply_location_messages` ADD INDEX  `by_location` (  `auto_reply_location_id` ) ;
ALTER TABLE  `auto_reply_locations` ADD INDEX  `by_image` (  `image_attachment_id` ) ;
ALTER TABLE  `auto_reply_locations` ADD INDEX  `by_search` (  `user_id`, `title` ) ;
ALTER TABLE  `auto_reply_locations` ADD INDEX  `by_user` (  `user_id` ) ;
ALTER TABLE  `auto_reply_message_tags` ADD INDEX  `by_message` (  `auto_reply_message_id` ) ;
