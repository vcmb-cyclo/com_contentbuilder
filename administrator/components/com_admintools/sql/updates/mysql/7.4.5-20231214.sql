/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

-- Change column types

ALTER TABLE `#__admintools_log` CHANGE `reason` `reason` enum ('other','admindir','awayschedule','adminpw','ipwl','ipbl','sqlishield','antispam','tpone','tmpl','wafblacklist','template','muashield','csrfshield','badbehaviour','geoblocking','rfishield','dfishield','uploadshield','xssshield','httpbl','loginfailure','securitycode','sessionshield','external','nonewadmins','nonewfrontendadmins','configmonitor','phpshield','404shield','itemidshield','susparam') DEFAULT 'other';
