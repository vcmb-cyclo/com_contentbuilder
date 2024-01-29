/**
 * @package   admintools
 * @copyright Copyright (c)2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

CREATE TABLE IF NOT EXISTS `#__admintools_ipallow` (
    `id`          bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `ip`          varchar(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci,
    `description` varchar(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci,
    PRIMARY KEY (`id`)
) ENGINE InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;
