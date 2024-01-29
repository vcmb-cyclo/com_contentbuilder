/**
 * @package   admintools
 * @copyright Copyright (c)2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

-- Joomla 4.0.0 to 4.2.7 API application exploit neutralisation

INSERT IGNORE INTO `#__admintools_wafblacklists`
(`option`, `view`, `task`, `query`, `query_type`, `query_content`, `verb`, `application`, `enabled`)
VALUES ('', '', '', 'public', 'E', '', 'GET', 'api', 1);
