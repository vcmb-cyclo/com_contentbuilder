/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

CREATE TABLE IF NOT EXISTS "#__admintools_adminiplist"
(
    "id"          serial NOT NULL,
    "ip"          character varying(255) DEFAULT NULL,
    "description" character varying(255) DEFAULT NULL,
    PRIMARY KEY ("id"),
    CONSTRAINT "#__admintools_adminiplist_ip" UNIQUE ("ip")
);

CREATE TABLE IF NOT EXISTS "#__admintools_badwords"
(
    "id"   serial NOT NULL,
    "word" character varying(255) DEFAULT NULL,
    PRIMARY KEY ("id"),
    CONSTRAINT "#__admintools_badwords_word" UNIQUE ("word")
);

CREATE TABLE IF NOT EXISTS "#__admintools_customperms"
(
    "id"    serial                 NOT NULL,
    "path"  character varying(255) NOT NULL,
    "perms" character varying(4) DEFAULT '0644',
    PRIMARY KEY ("id")
);

CREATE INDEX "#__admintools_customperms_path" ON "#__admintools_customperms" ("path");

CREATE TABLE IF NOT EXISTS "#__admintools_filescache"
(
    "id"       serial                  NOT NULL,
    "path"     character varying(2048) NOT NULL,
    "filedate" int                     NOT NULL DEFAULT '0',
    "filesize" int                     NOT NULL DEFAULT '0',
    "data"     bytea,
    "checksum" character varying(32)   NOT NULL DEFAULT '',
    PRIMARY KEY ("id")
);

CREATE TABLE IF NOT EXISTS "#__admintools_ipautoban"
(
    "ip"     character varying(255) NOT NULL,
    "reason" character varying(255)      DEFAULT 'other',
    "until"  timestamp without time zone DEFAULT NULL,
    PRIMARY KEY ("ip")
);

CREATE TABLE IF NOT EXISTS "#__admintools_ipblock"
(
    "id"          serial NOT NULL,
    "ip"          character varying(255) DEFAULT NULL,
    "description" character varying(255) DEFAULT NULL,
    PRIMARY KEY ("id"),
    CONSTRAINT "#__admintools_ipblock_ip" UNIQUE ("ip")
);

CREATE TABLE IF NOT EXISTS "#__admintools_ipallow"
(
    "id"          serial NOT NULL,
    "ip"          character varying(255) DEFAULT NULL,
    "description" character varying(255) DEFAULT NULL,
    PRIMARY KEY ("id"),
    CONSTRAINT "#__admintools_ipallow_ip" UNIQUE ("ip")
);

CREATE TABLE IF NOT EXISTS "#__admintools_log"
(
    "id"        serial                      NOT NULL,
    "logdate"   timestamp without time zone NOT NULL,
    "ip"        character varying(40)    DEFAULT NULL,
    "url"       character varying(10240) DEFAULT NULL,
    "reason"    character varying(255)   DEFAULT 'other',
    "extradata" text,
    PRIMARY KEY ("id")
);

CREATE INDEX "#__admintools_log_logdate_reason" ON "#__admintools_log" ("logdate", "reason");

CREATE TABLE IF NOT EXISTS "#__admintools_redirects"
(
    "id"             serial                      NOT NULL,
    "source"         character varying(255)               DEFAULT NULL,
    "dest"           character varying(255)               DEFAULT NULL,
    "ordering"       bigint                      NOT NULL DEFAULT '0',
    "published"      smallint                    NOT NULL DEFAULT '1',
    "created"        TIMESTAMP without time zone NULL     DEFAULT NULL,
    "created_by"     bigint                      NOT NULL DEFAULT '0',
    "modified"       TIMESTAMP without time zone NULL     DEFAULT NULL,
    "modified_by"    bigint                      NOT NULL DEFAULT '0',
    "checked_out"    bigint                      NOT NULL DEFAULT '0',
    "checked_out_by" TIMESTAMP without time zone NULL     DEFAULT NULL,
    "keepurlparams"  smallint                    NOT NULL DEFAULT '1',
    PRIMARY KEY ("id")
);

CREATE TABLE IF NOT EXISTS "#__admintools_scanalerts"
(
    "id"           serial                  NOT NULL,
    "path"         character varying(2048) NOT NULL,
    "scan_id"      bigint                  NOT NULL DEFAULT '0',
    "diff"         text,
    "threat_score" int                     NOT NULL DEFAULT '0',
    "acknowledged" smallint                NOT NULL DEFAULT '0',
    PRIMARY KEY ("id")
);

CREATE TABLE IF NOT EXISTS "#__admintools_scans"
(
    "id"         serial                      NOT NULL,
    "comment"    text,
    "scanstart"  TIMESTAMP without time zone NULL     DEFAULT NULL,
    "scanend"    TIMESTAMP without time zone NULL     DEFAULT NULL,
    "status"     character varying(10)       NOT NULL DEFAULT 'run',
    "origin"     character varying(30)       NOT NULL DEFAULT 'backend',
    "totalfiles" int                         NOT NULL DEFAULT '0',
    PRIMARY KEY ("id")
);

CREATE INDEX "#__admintools_idx_stale" ON "#__admintools_scans" ("status", "origin");

CREATE TABLE IF NOT EXISTS "#__admintools_storage"
(
    "key"   character varying(255) NOT NULL,
    "value" text                   NOT NULL,
    PRIMARY KEY ("key")
);

CREATE TABLE IF NOT EXISTS "#__admintools_wafexceptions"
(
    "id"     serial NOT NULL,
    "option" character varying(255) DEFAULT NULL,
    "view"   character varying(255) DEFAULT NULL,
    "query"  character varying(255) DEFAULT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE IF NOT EXISTS "#__admintools_wafblacklists"
(
    "id"            serial                 NOT NULL,
    "option"        character varying(255) NOT NULL,
    "view"          character varying(255) NOT NULL,
    "task"          character varying(255) NOT NULL,
    "query"         character varying(255) NOT NULL,
    "query_type"    character varying(1)   NOT NULL,
    "query_content" character varying(255) NOT NULL,
    "verb"          character varying(6)   NOT NULL,
    "application"   character varying(5)   NOT NULL DEFAULT 'site',
    "enabled"       smallint               NOT NULL DEFAULT 1,
    PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "#__admintools_waftemplates";

CREATE TABLE IF NOT EXISTS "#__admintools_ipautobanhistory"
(
    "id"     serial                 NOT NULL,
    "ip"     character varying(255) NOT NULL,
    "reason" character varying(255)      DEFAULT 'other',
    "until"  timestamp without time zone DEFAULT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE IF NOT EXISTS "#__admintools_cookies"
(
    "series"      character varying(255)      NOT NULL,
    "client_hash" character varying(255)      NOT NULL,
    "valid_to"    timestamp without time zone NULL DEFAULT NULL,
    PRIMARY KEY ("series")
);

CREATE TABLE IF NOT EXISTS "#__admintools_tempsupers"
(
    "user_id"    bigint                      NOT NULL,
    "expiration" timestamp without time zone NOT NULL,
    PRIMARY KEY ("user_id")
);

INSERT INTO "#__admintools_wafblacklists"
("option", "view", "task", "query", "query_type", "query_content", "verb", "enabled")
VALUES ('', '', '', 'list[select]', 'E', '!#^[\\p{L}\\d,\\s]+$#iu', '', 1);

INSERT INTO "#__admintools_wafblacklists"
("option", "view", "task", "query", "query_type", "query_content", "verb", "enabled")
VALUES ('com_users', '', '', 'user[groups]', 'P', '', '', 1);

INSERT INTO "#__admintools_wafblacklists"
("option", "view", "task", "query", "query_type", "query_content", "verb", "application", "enabled")
VALUES ('com_content', 'category', '', 'type', 'R', '!#^[a-z][a-z\\-_0-9]{2,}$#i', '', 'site', 1);

INSERT INTO "#__admintools_wafblacklists"
("option", "view", "task", "query", "query_type", "query_content", "verb", "application", "enabled")
VALUES ('', '', '', 'public', 'E', '', 'GET', 'api', 1);

--
-- Create the common table for all Akeeba extensions.
--
-- This table is never uninstalled when uninstalling the extensions themselves.
--
CREATE TABLE IF NOT EXISTS "#__akeeba_common"
(
    "key"   character varying(190) NOT NULL,
    "value" text                   NOT NULL,
    PRIMARY KEY ("key")
);