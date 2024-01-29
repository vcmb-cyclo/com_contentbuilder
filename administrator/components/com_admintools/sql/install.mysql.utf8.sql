/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

CREATE TABLE IF NOT EXISTS `#__admintools_adminiplist` (
    `id`          bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `ip`          varchar(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci,
    `description` varchar(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci,
    PRIMARY KEY (`id`)
) ENGINE InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__admintools_badwords` (
    `id`   bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `word` varchar(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci,
    PRIMARY KEY (`id`),
    UNIQUE KEY (`word`(100))
) ENGINE InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__admintools_customperms` (
    `id`    bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `path`  varchar(255)        NOT NULL COLLATE utf8mb4_unicode_ci,
    `perms` varchar(4) DEFAULT '0644',
    PRIMARY KEY (`id`),
    KEY `#__admintools_customperms_path` (`path`(100))
) ENGINE InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__admintools_filescache` (
    `id`       bigint(20)    NOT NULL AUTO_INCREMENT,
    `path`     varchar(2048) NOT NULL COLLATE utf8mb4_unicode_ci,
    `filedate` int(11)       NOT NULL DEFAULT '0',
    `filesize` int(11)       NOT NULL DEFAULT '0',
    `data`     blob,
    `checksum` varchar(32)   NOT NULL DEFAULT '' COLLATE utf8mb4_unicode_ci,
    PRIMARY KEY (`id`)
) ENGINE InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__admintools_ipautoban` (
    `ip`     varchar(255) NOT NULL COLLATE utf8mb4_unicode_ci,
    `reason` varchar(255) DEFAULT 'other' COLLATE utf8mb4_unicode_ci,
    `until`  datetime     DEFAULT NULL,
    PRIMARY KEY (`ip`(100))
) ENGINE InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__admintools_ipblock` (
    `id`          bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `ip`          varchar(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci,
    `description` varchar(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci,
    PRIMARY KEY (`id`)
) ENGINE InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__admintools_ipallow` (
    `id`          bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `ip`          varchar(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci,
    `description` varchar(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci,
    PRIMARY KEY (`id`)
) ENGINE InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__admintools_log` (
    `id`        bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `logdate`   datetime NOT NULL,
    `ip`        varchar(40) DEFAULT NULL COLLATE utf8mb4_unicode_ci,
    `url`       varchar(10240) DEFAULT NULL COLLATE utf8mb4_unicode_ci,
    `reason`    enum ('other','admindir','awayschedule','adminpw','ipwl','ipbl','sqlishield','antispam','tpone','tmpl','wafblacklist','template','muashield','csrfshield','badbehaviour','geoblocking','rfishield','dfishield','uploadshield','xssshield','httpbl','loginfailure','securitycode','sessionshield','external','nonewadmins','nonewfrontendadmins','configmonitor','phpshield','404shield','itemidshield', 'susparam') DEFAULT 'other',
    `extradata` mediumtext,
    PRIMARY KEY (`id`),
    KEY `#__admintools_log_logdate_reason` (`logdate`, `reason`)
) ENGINE InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__admintools_redirects` (
    `id`               bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `source`           varchar(255)                 DEFAULT NULL COLLATE utf8mb4_unicode_ci,
    `dest`             varchar(255)                 DEFAULT NULL COLLATE utf8mb4_unicode_ci,
    `ordering`         bigint(20)          NOT NULL DEFAULT '0',
    `published`        tinyint(1)          NOT NULL DEFAULT '1',
    `created`          datetime            NULL     DEFAULT NULL,
    `created_by`       int(11)             NOT NULL DEFAULT '0',
    `modified`         datetime            NULL     DEFAULT NULL,
    `modified_by`      int(11)             NOT NULL DEFAULT '0',
    `checked_out`      int(11)             NOT NULL DEFAULT '0',
    `checked_out_time` datetime            NULL     DEFAULT NULL,
    `keepurlparams`    tinyint(1)          NOT NULL DEFAULT '1',
    PRIMARY KEY (`id`)
) ENGINE InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__admintools_scanalerts` (
    `id`           bigint(20)    NOT NULL AUTO_INCREMENT,
    `path`         varchar(2048) NOT NULL COLLATE utf8mb4_unicode_ci,
    `scan_id`      bigint(20)    NOT NULL DEFAULT '0',
    `diff`         mediumtext,
    `threat_score` int(11)       NOT NULL DEFAULT '0',
    `acknowledged` tinyint(4)    NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__admintools_scans` (
    `id`         bigint(20) unsigned            NOT NULL AUTO_INCREMENT,
    `comment`    longtext,
    `scanstart`  TIMESTAMP                      NULL     DEFAULT NULL,
    `scanend`    TIMESTAMP                      NULL     DEFAULT NULL,
    `status`     enum ('run','fail','complete') NOT NULL DEFAULT 'run',
    `origin`     varchar(30)                    NOT NULL DEFAULT 'backend',
    `totalfiles` int(11)                        NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `idx_stale` (`status`, `origin`)
) ENGINE InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__admintools_storage` (
    `key`   varchar(255) NOT NULL COLLATE utf8mb4_unicode_ci,
    `value` longtext     NOT NULL,
    PRIMARY KEY (`key`(100))
) ENGINE InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__admintools_wafexceptions` (
    `id`     bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `option` varchar(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci,
    `view`   varchar(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci,
    `query`  varchar(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci,
    PRIMARY KEY (`id`)
) ENGINE InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__admintools_wafblacklists` (
    `id`            bigint(20) unsigned                NOT NULL AUTO_INCREMENT,
    `option`        varchar(255)                       NOT NULL COLLATE utf8mb4_unicode_ci,
    `view`          varchar(255)                       NOT NULL COLLATE utf8mb4_unicode_ci,
    `task`          varchar(255)                       NOT NULL COLLATE utf8mb4_unicode_ci,
    `query`         varchar(255)                       NOT NULL COLLATE utf8mb4_unicode_ci,
    `query_type`    varchar(1)                         NOT NULL COLLATE utf8mb4_unicode_ci,
    `query_content` varchar(255)                       NOT NULL COLLATE utf8mb4_unicode_ci,
    `verb`          varchar(6)                         NOT NULL COLLATE utf8mb4_unicode_ci,
    `application`   enum ('site','admin','api','both') NOT NULL DEFAULT 'site',
    `enabled`       TINYINT(3)                         NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__admintools_waftemplates`;

CREATE TABLE IF NOT EXISTS `#__admintools_ipautobanhistory` (
    `id`     bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `ip`     varchar(255)        NOT NULL COLLATE utf8mb4_unicode_ci,
    `reason` varchar(255) DEFAULT 'other' COLLATE utf8mb4_unicode_ci,
    `until`  datetime     DEFAULT NULL,
    PRIMARY KEY `id` (`id`)
) ENGINE InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__admintools_cookies` (
    `series`      varchar(255) NOT NULL COLLATE utf8mb4_unicode_ci,
    `client_hash` varchar(255) NOT NULL COLLATE utf8mb4_unicode_ci,
    `valid_to`    datetime     NULL DEFAULT NULL,
    PRIMARY KEY (`series`(100))
) ENGINE InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__admintools_tempsupers` (
    `user_id`    bigint(20) NOT NULL,
    `expiration` datetime   NOT NULL,
    PRIMARY KEY (`user_id`)
) ENGINE InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

INSERT IGNORE INTO `#__admintools_wafblacklists`
(`option`, `view`, `task`, `query`, `query_type`, `query_content`, `verb`, `enabled`)
VALUES ('', '', '', 'list[select]', 'E', '!#^[\\p{L}\\d,\\s]+$#iu', '', 1);

INSERT IGNORE INTO `#__admintools_wafblacklists`
(`option`, `view`, `task`, `query`, `query_type`, `query_content`, `verb`, `enabled`)
VALUES ('com_users', '', '', 'user[groups]', 'P', '', '', 1);

INSERT IGNORE INTO `#__admintools_wafblacklists`
(`option`, `view`, `task`, `query`, `query_type`, `query_content`, `verb`, `application`, `enabled`)
VALUES ('com_content', 'category', '', 'type', 'R', '!#^[a-z][a-z\\-_0-9]{2,}$#i', '', 'site', 1);

INSERT IGNORE INTO `#__admintools_wafblacklists`
(`option`, `view`, `task`, `query`, `query_type`, `query_content`, `verb`, `application`, `enabled`)
VALUES ('', '', '', 'public', 'E', '', 'GET', 'api', 1);

--
-- Create the common table for all Akeeba extensions.
--
-- This table is never uninstalled when uninstalling the extensions themselves.
--
CREATE TABLE IF NOT EXISTS `#__akeeba_common` (
    `key`   VARCHAR(190) NOT NULL COLLATE utf8mb4_unicode_ci,
    `value` LONGTEXT     NOT NULL,
    PRIMARY KEY (`key`(100))
) ENGINE InnoDB DEFAULT CHARSET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;