/**
 * @package   admintools
 * @copyright Copyright (c)2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

CREATE TABLE IF NOT EXISTS "#__admintools_ipallow"
(
    "id"          serial NOT NULL,
    "ip"          character varying(255) DEFAULT NULL,
    "description" character varying(255) DEFAULT NULL,
    PRIMARY KEY ("id"),
    CONSTRAINT "#__admintools_ipallow_ip" UNIQUE ("ip")
);
