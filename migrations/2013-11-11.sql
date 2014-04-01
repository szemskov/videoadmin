ALTER TABLE `translations`
  ADD `ova_url_sochi` VARCHAR(2000) NULL DEFAULT NULL AFTER `ova_url_post`,
  ADD `ova_url_post_sochi` VARCHAR(2000) NULL DEFAULT NULL AFTER `ova_url_sochi`;
