/*
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

-- Obsolete tables
DROP TABLE IF EXISTS `#__admintools_profiles`;
DROP TABLE IF EXISTS `#__admintools_acl`;
DROP TABLE IF EXISTS `#__admintools_waftemplates`;

-- Change column names
ALTER TABLE `#__admintools_filescache` CHANGE `admintools_filescache_id` `id` bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#__admintools_scanalerts` CHANGE `admintools_scanalert_id` `id` bigint(20) NOT NULL AUTO_INCREMENT;

-- New columns

ALTER TABLE `#__admintools_redirects` ADD COLUMN `created` datetime NULL DEFAULT NULL AFTER `ordering`;

ALTER TABLE `#__admintools_redirects` ADD COLUMN `created_by` int(11) NOT NULL DEFAULT '0' AFTER `created`;

ALTER TABLE `#__admintools_redirects` ADD COLUMN `modified` datetime NULL DEFAULT NULL AFTER `created_by`;

ALTER TABLE `#__admintools_redirects` ADD COLUMN `modified_by` int(11) NOT NULL DEFAULT '0' AFTER `modified`;

ALTER TABLE `#__admintools_redirects` ADD COLUMN `checked_out` int(11) NOT NULL DEFAULT '0' AFTER `modified_by`;

ALTER TABLE `#__admintools_redirects` ADD COLUMN `checked_out_time` datetime NULL DEFAULT NULL AFTER `checked_out`;

-- Change column types

ALTER TABLE `#__admintools_wafblacklists` CHANGE `application` `application` enum ('site','admin','api','both') NOT NULL DEFAULT 'site';