CREATE TABLE `code` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(16) DEFAULT NULL,
  `code` text NOT NULL,
  `created_at` int(11) NOT NULL,
  `reply_to` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `status` enum('visible','draft','hidden','deleted') NOT NULL DEFAULT 'visible',
  `ip` varchar(39) DEFAULT NULL,
  `language` varchar(16) DEFAULT NULL,
  `views` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_id` (`unique_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;