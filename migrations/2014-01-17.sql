CREATE TABLE IF NOT EXISTS `translation_channels` (
  `translation_id` int(11) NOT NULL,
  `channel` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `child_translation_id` int(11) NOT NULL,
  PRIMARY KEY (`translation_id`,`channel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `translations` ADD `parent_id` INT NOT NULL DEFAULT '0' AFTER `id`;
