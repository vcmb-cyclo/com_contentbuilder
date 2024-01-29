/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

-- Obsolete tables
DROP TABLE IF EXISTS "#__admintools_profiles";
DROP TABLE IF EXISTS "#__admintools_acl";
DROP TABLE IF EXISTS "#__admintools_waftemplates";

-- Changed column names
ALTER TABLE "#__admintools_filescache"
    RENAME COLUMN "admintools_filescache_id" TO "id";

ALTER TABLE "#__admintools_scanalerts"
    RENAME COLUMN "admintools_scanalert_id" TO "id";

-- New columns

ALTER TABLE "#__admintools_redirects"
    ADD COLUMN
        "created" TIMESTAMP without time zone NULL DEFAULT NULL;

ALTER TABLE "#__admintools_redirects"
    ADD COLUMN
        "created_by" bigint NOT NULL DEFAULT '0';

ALTER TABLE "#__admintools_redirects"
    ADD COLUMN
        "modified" TIMESTAMP without time zone NULL DEFAULT NULL;

ALTER TABLE "#__admintools_redirects"
    ADD COLUMN
        "modified_by" bigint NOT NULL DEFAULT '0';

ALTER TABLE "#__admintools_redirects"
    ADD COLUMN
        "checked_out" bigint NOT NULL DEFAULT '0';

ALTER TABLE "#__admintools_redirects"
    ADD COLUMN
        "checked_out_time" TIMESTAMP without time zone NULL DEFAULT NULL;
