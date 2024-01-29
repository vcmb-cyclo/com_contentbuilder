<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseModel;
use RuntimeException;

#[\AllowDynamicProperties]
class ChecktempandlogdirectoriesModel extends BaseModel
{
	/**
	 * Performs the folders checks and returns an array with the writebility status
	 *
	 * @return  array
	 */
	public function checkFolders(): array
	{
		$tmpDir = $this->checkTmpFolder();
		$logDir = $this->checkLogFolder();

		return [
			'tmp' => $tmpDir,
			'log' => $logDir,
		];
	}

	/**
	 * Check if the tmp folder is writeable. If not, create a new one.
	 *
	 * @return  mixed|string
	 */
	private function checkTmpFolder()
	{
		$tmpDir = Factory::getApplication()->get('tmp_path');

		// If the folder is ok, let's stop here
		if ($this->checkFolder($tmpDir))
		{
			return $tmpDir;
		}

		// Folder is NOT ok? Let's try with "tmp"
		$tmpDir = JPATH_ROOT . '/tmp';

		if (!Folder::exists($tmpDir))
		{
			Folder::create($tmpDir);
		}

		if (Folder::exists($tmpDir) && !is_writable($tmpDir))
		{
			// If it's writable, let's save the path inside the configuration file
			$this->saveConfigurationValue('tmp_path', $tmpDir);

			return $tmpDir;
		}

		// Still no luck? Let's try with "temp"
		$tmpDir = JPATH_ROOT . '/temp';

		if (!Folder::exists($tmpDir))
		{
			Folder::create($tmpDir);
		}

		if (Folder::exists($tmpDir))
		{
			// If it's writable, let's save the path inside the configuration file
			if (is_writable($tmpDir))
			{
				$this->saveConfigurationValue('tmp_path', $tmpDir);

				return $tmpDir;
			}

			// Still not writable? Time for a Hail Mary approach: chmod it and put a .htaccess file in it
			if (!$this->chmod($tmpDir) || !is_writable($tmpDir))
			{
				throw new RuntimeException(Text::_('COM_ADMINTOOLS_CHECKTEMPANDLOGDIRECTORIES_ERR_CHMOD_TMPFOLDER'));
			}

			$contents = "<IfModule !mod_authz_core.c>
Order deny,allow
Deny from all
</IfModule>
<IfModule mod_authz_core.c>
  <RequireAll>
	Require all denied
  </RequireAll>
</IfModule>
";

			File::write($tmpDir . '/.htaccess', $contents);

			$this->saveConfigurationValue('tmp_path', $tmpDir);

			return Text::sprintf('COM_ADMINTOOLS_CHECKTEMPANDLOGDIRECTORIES_LBL_TMPDIR_WORKAROUND', $tmpDir);
		}

		throw new RuntimeException(Text::_('COM_ADMINTOOLS_CHECKTEMPANDLOGDIRECTORIES_ERR_TMPDIR_CREATION'));
	}

	private function checkLogFolder()
	{
		$logDir = Factory::getApplication()->get('log_path');

		// If the folder is ok, let's stop here
		if ($this->checkFolder($logDir))
		{
			return $logDir;
		}

		// We only try administrator/logs, the default path in Joomla 4.
		$logDir = JPATH_ROOT . '/logs';

		if (!Folder::exists($logDir))
		{
			Folder::create($logDir);
		}

		// If it's writable, let's save the path inside the configuration file
		if (Folder::exists($logDir) && is_writable($logDir))
		{
			$this->saveConfigurationValue('log_path', $logDir);

			return $logDir;
		}

		// The folder exists BUT it's not writeable. We'll chmod it and put a .htaccess file in it.
		if (Folder::exists($logDir))
		{
			// Still not writable? Let's try a nasty hack: chmod it to 0777 and put a .htaccess file in it
			if (!$this->chmod($logDir) || !is_writable($logDir))
			{
				throw new RuntimeException(Text::_('COM_ADMINTOOLS_CHECKTEMPANDLOGDIRECTORIES_ERR_CHMOD_LOGFOLDER'));
			}

			$contents = "<IfModule !mod_authz_core.c>
Order deny,allow
Deny from all
</IfModule>
<IfModule mod_authz_core.c>
  <RequireAll>
	Require all denied
  </RequireAll>
</IfModule>
";

			File::write($logDir . '/.htaccess', $contents);

			$this->saveConfigurationValue('log_path', $logDir);

			return Text::sprintf('COM_ADMINTOOLS_CHECKTEMPANDLOGDIRECTORIES_LBL_LOGDIR_WORKAROUND', $logDir);
		}

		throw new RuntimeException(Text::_('COM_ADMINTOOLS_CHECKTEMPANDLOGDIRECTORIES_ERR_LOGDIR_CREATION'));
	}

	/**
	 * Checks if the directory has a correct value: not empty, not the site root and it's writable
	 *
	 * @param   string  $dir  Absolute path to the folder
	 *
	 * @return  bool    Is the folder path ok?
	 */
	private function checkFolder($dir)
	{
		$forbiddenFolders = [
			JPATH_ROOT,
			JPATH_ADMINISTRATOR,
		];

		$forbiddenContainers = [
			JPATH_ADMINISTRATOR . '/cache',
			JPATH_ADMINISTRATOR . '/components',
			JPATH_ADMINISTRATOR . '/help',
			JPATH_ADMINISTRATOR . '/includes',
			JPATH_ADMINISTRATOR . '/language',
			JPATH_ADMINISTRATOR . '/manifests',
			JPATH_ADMINISTRATOR . '/modules',
			JPATH_ADMINISTRATOR . '/templates',
			JPATH_API,
			JPATH_ROOT . '/api',
			JPATH_CACHE,
			JPATH_ROOT . '/cache',
			JPATH_ADMINISTRATOR . '/cache',
			JPATH_CLI,
			JPATH_ROOT . '/cli',
			JPATH_ROOT . '/components',
			JPATH_ROOT . '/images',
			JPATH_ROOT . '/includes',
			JPATH_ROOT . '/language',
			JPATH_ROOT . '/layouts',
			JPATH_LIBRARIES,
			JPATH_ROOT . '/libraries',
			JPATH_ROOT . '/media',
			JPATH_ROOT . '/modules',
			JPATH_ROOT . '/plugins',
			JPATH_PLUGINS,
			JPATH_THEMES,
			JPATH_ROOT . '/templates',
		];

		$dir = rtrim($dir, '/\\');

		// Empty directory?
		if (!$dir)
		{
			return false;
		}

		// Is it a forbidden folder?
		foreach ($forbiddenFolders as $checkAgainst)
		{
			if (strtolower($checkAgainst) === strtolower($dir))
			{
				return false;
			}
		}

		// Is it inside a forbidden container folder?
		foreach ($forbiddenContainers as $checkAgainst)
		{
			if (strpos(strtolower($dir), strtolower($checkAgainst)) === 0)
			{
				return false;
			}
		}

		// Is this a directory we cannot write to?
		if (!is_writable($dir))
		{
			return false;
		}

		return true;
	}

	private function saveConfigurationValue($key, $value)
	{
		/** @var AdministratorApplication $app */
		$app = Factory::getApplication();

		$app->set($key, $value);

		// Attempt to write the configuration file as a PHP class named JConfig.
		$configuration = $app->getConfig()->toString('PHP', ['class' => 'JConfig', 'closingtag' => false]);

		$configurationFilePath = JPATH_CONFIGURATION . '/configuration.php';
		$result                = File::write($configurationFilePath, $configuration);

		if (!$result)
		{
			throw new RuntimeException(Text::_('COM_ADMINTOOLS_CHECKTEMPANDLOGDIRECTORIES_ERR_SAVING_JCONFIG'));
		}

		// Clear opcode caches
		if (function_exists('apc_delete_file'))
		{
			apc_delete_file($configurationFilePath);
		}

		if (function_exists('opcache_invalidate'))
		{
			opcache_invalidate($configurationFilePath, true);
		}
	}

	/**
	 * CHMODs the directory to world writeable.
	 *
	 * Note that this is not as terribly insecure as it sounds. The files are still owned by the correct user (broken
	 * server configuration notwithstanding!) which means they can't be written to by random users. This applies to the
	 * .htaccess file we created in there, making the folder inaccessible to the world over the web.
	 *
	 * Sure, some broken server setups will allow the .htaccess to be overwritten or you may have a server which does
	 * not support .htaccess files. In this case, brother, you REALLY need to fix your server configuration. Fixing the
	 * temp and logs folders on a broken server setup isn't possible without the kind of access you haven't and probably
	 * can't give to a PHP application...
	 *
	 * @param $dir
	 *
	 * @return bool
	 *
	 * @throws RuntimeException
	 */
	private function chmod($dir)
	{
		if (!Folder::exists($dir))
		{
			throw new RuntimeException('Can not chmod directory ' . $dir . ' because it doesn\'t exist');
		}

		$dir = Path::clean($dir);

		// We can't type the oh triple seven octal because some scanners will kill it. So, we have to be creative...
		$ohTripleSeven = 600 - 45 * 2 + 1;

		return chmod($dir, $ohTripleSeven);
	}
}