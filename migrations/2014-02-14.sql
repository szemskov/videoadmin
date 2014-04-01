ALTER TABLE `translation_channels` MODIFY child_translation_id INT NOT NULL AFTER `channel`;
ALTER TABLE `translation_channels` ADD `quadro` INT NOT NULL DEFAULT '0' AFTER `child_translation_id`;
