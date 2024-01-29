<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Model;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Helper\Storage;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

#[\AllowDynamicProperties]
class FixpermissionsModel extends BaseDatabaseModel
{
	/** @var int Total numbers of folders in this site */
	public $totalFolders = 0;

	/** @var int Numbers of folders already processed */
	public $doneFolders = 0;

	/** @var float The time the process started */
	private $startTime = null;

	/** @var array The folders to process */
	private $folderStack = [];

	/** @var array The files to process */
	private $filesStack = [];

	/** @var int Default directory permissions */
	private $dirperms = 0755;

	/** @var int Default file permissions */
	private $fileperms = 0644;

	/** @var array Custom permissions */
	private $customperms = [];

	/** @var array Skip subdirectories and files of these directories */
	private $skipDirs = [];

	/**
	 * Scans $root for directories and updates $folderStack
	 *
	 * @param   string|null  $root  The full path of the directory to scan
	 */
	public function getDirectories(?string $root = null): void
	{
		$root = $root ?: JPATH_ROOT;
		// For some REALLY BROKEN servers...
		$root = $root ?: realpath('..');

		if (in_array(rtrim($root, '/'), $this->skipDirs))
		{
			return;
		}

		$folders            = Folder::folders($root, '.', false, true) ?: [];
		$this->folderStack  = array_merge($this->folderStack, $folders);
		$this->totalFolders += count($folders);
	}

	/**
	 * Scans $root for files and updates $filesStack
	 *
	 * @param   string|null  $root  The full path of the directory to scan
	 */
	public function getFiles(?string $root = null): void
	{
		$root = $root ?: JPATH_ROOT;
		// For some REALLY BROKEN servers...
		$root = $root ?: realpath('..');

		if (in_array(rtrim($root, '/'), $this->skipDirs))
		{
			return;
		}

		$root = rtrim($root, '/') . '/';

		// Should I include dot files, too?
		$params        = Storage::getInstance();
		$excludeFilter = $params->getValue('perms_show_hidden', 0) ? ['.*~'] : ['^\..*', '.*~'];
		$folders       = Folder::files($root, '.', false, true, [
			'.svn', 'CVS', '.DS_Store', '__MACOSX',
		], $excludeFilter) ?: [];

		$this->filesStack   = array_merge($this->filesStack, $folders);
		$this->totalFolders += count($folders);
	}

	public function startScanning(): bool
	{
		$this->initialize();
		$this->resetStack();
		$this->resetTimer();
		$this->getDirectories();
		$this->getFiles();
		$this->saveStack();

		if (!$this->haveEnoughTime())
		{
			return true;
		}

		return $this->run(false);
	}

	/**
	 * Change the permissions of a file / folder.
	 *
	 * @param   string      $path  Path to file/folder to change permissions on
	 * @param   string|int  $mode  File mode, either as an octal string (e.g. '0644') or as an integer.
	 *
	 * @return  bool
	 */
	public function chmod(string $path, $mode): bool
	{
		if (is_string($mode))
		{
			$mode = octdec($mode);

			if (($mode <= 0) || ($mode > 0777))
			{
				$mode = 0755;
			}
		}

		// Check to make sure the path valid and clean
		$path = Path::clean($path);

		$effectivePath = rtrim(JPATH_SITE, DIRECTORY_SEPARATOR . '/') . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR . '/');

		return @chmod($effectivePath, $mode);
	}

	public function run(bool $resetTimer = true): bool
	{
		if ($resetTimer)
		{
			$this->resetTimer();
		}

		$this->loadStack();

		$result = true;

		while ($result && $this->haveEnoughTime())
		{
			$result = $this->RealRun();
		}

		$this->saveStack();

		return $result;
	}

	public function getRelativePath(string $somePath): string
	{
		$path = Path::clean($somePath, '/');
		$root = Path::clean(JPATH_ROOT, '/');

		return ltrim(substr($path, strlen($root)), '/');
	}

	private function initialize(): void
	{
		$params = Storage::getInstance();

		$dirperms  = '0' . ltrim(trim($params->getValue('dirperms', '0755')), '0');
		$fileperms = '0' . ltrim(trim($params->getValue('fileperms', '0644')), '0');

		$dirperms = octdec($dirperms);

		if (($dirperms < 0400) || ($dirperms > 0777))
		{
			$dirperms = 0755;
		}

		$this->dirperms = $dirperms;

		$fileperms = octdec($fileperms);

		if (($fileperms < 0400) || ($fileperms > 0777))
		{
			$fileperms = 0644;
		}

		$this->fileperms = $fileperms;

		$db    = $this->getDatabase();
		$query = $db->getQuery(true)
			->select([
				$db->qn('path'),
				$db->qn('perms'),
			])->from($db->qn('#__admintools_customperms'))
			->order($db->qn('path') . ' ASC');

		$this->customperms = $db->setQuery($query)->loadAssocList('path');

		// Add cache, tmp and log to the exceptions
		$app              = Factory::getApplication();
		$this->skipDirs[] = rtrim(JPATH_CACHE, '/');
		$this->skipDirs[] = rtrim(JPATH_ROOT . '/cache', '/');
		$this->skipDirs[] = rtrim($app->get('tmp_path', JPATH_ROOT . '/tmp'), '/');
		$this->skipDirs[] = rtrim($app->get('log_path', JPATH_ROOT . '/logs'), '/');
		$this->skipDirs[] = JPATH_ADMINISTRATOR . '/logs';
		$this->skipDirs[] = JPATH_ADMINISTRATOR . '/log';
		$this->skipDirs[] = JPATH_ROOT . '/logs';
		$this->skipDirs[] = JPATH_ROOT . '/log';
	}

	/**
	 * Starts or resets the internal timer
	 */
	private function resetTimer(): void
	{
		$this->startTime = microtime(true);
	}

	/**
	 * Makes sure that no more than 3 seconds since the start of the timer have elapsed
	 *
	 * @return  bool
	 */
	private function haveEnoughTime(): bool
	{
		$now     = microtime(true);
		$elapsed = abs($now - $this->startTime);

		return $elapsed < 2;
	}

	/**
	 * Saves the file/folder stack in the session
	 */
	private function saveStack(): void
	{
		$db = $this->getDatabase();

		$query = $db->getQuery(true)
			->delete($db->quoteName('#__admintools_storage'))
			->where($db->quoteName('key') . ' = ' . $db->quote('fixperms_stack'));

		$db->setQuery($query)->execute();

		$object = (object) [
			'key'   => 'fixperms_stack',
			'value' => json_encode([
				'folders' => $this->folderStack,
				'files'   => $this->filesStack,
				'total'   => $this->totalFolders,
				'done'    => $this->doneFolders,
			]),
		];

		$db->insertObject('#__admintools_storage', $object);
	}

	/**
	 * Resets the file/folder stack saved in the session
	 */
	private function resetStack(): void
	{
		$db = $this->getDatabase();

		$query = $db->getQuery(true)
			->delete($db->quoteName('#__admintools_storage'))
			->where($db->quoteName('key') . ' = ' . $db->quote('fixperms_stack'));

		$db->setQuery($query)->execute();

		$this->folderStack  = [];
		$this->filesStack   = [];
		$this->totalFolders = 0;
		$this->doneFolders  = 0;
	}

	/**
	 * Loads the file/folder stack from the session
	 */
	private function loadStack(): void
	{
		$db = $this->getDatabase();

		$query = $db->getQuery(true)
			->select([$db->quoteName('value')])
			->from($db->quoteName('#__admintools_storage'))
			->where($db->quoteName('key') . ' = ' . $db->quote('fixperms_stack'));

		$stack = $db->setQuery($query)->loadResult();

		if (empty($stack))
		{
			$this->folderStack  = [];
			$this->filesStack   = [];
			$this->totalFolders = 0;
			$this->doneFolders  = 0;

			return;
		}

		$stack = json_decode($stack, true);

		$this->folderStack  = $stack['folders'];
		$this->filesStack   = $stack['files'];
		$this->totalFolders = $stack['total'];
		$this->doneFolders  = $stack['done'];
	}

	private function RealRun(): bool
	{
		while (empty($this->filesStack) && !empty($this->folderStack))
		{
			// Get a directory
			$dir = null;

			while (empty($dir) && !empty($this->folderStack))
			{
				// Get the next directory
				$dir = array_shift($this->folderStack);

				// Skip over non-directories and symlinks
				if (!@is_dir($dir) || @is_link($dir))
				{
					$dir = null;
					continue;
				}
				// Skip over . and ..
				$checkDir = str_replace('\\', '/', $dir);

				if (in_array(basename($checkDir), [
						'.', '..',
					]) || (substr($checkDir, -2) == '/.') || (substr($checkDir, -3) == '/..'))
				{
					$dir = null;
					continue;
				}

				// Check for custom permissions
				$reldir = $this->getRelativePath($dir);
				$perms  = $this->dirperms;

				if (array_key_exists($reldir, $this->customperms))
				{
					$perms = $this->customperms[$reldir]['perms'];
				}

				// Apply new permissions
				$this->chmod($dir, $perms ?: $this->dirperms);
				$this->doneFolders++;
				$this->getDirectories($dir);
				$this->getFiles($dir);

				if (!$this->haveEnoughTime())
				{
					// Gotta continue in the next step
					return true;
				}
			}
		}

		if (empty($this->filesStack) && empty($this->folderStack))
		{
			// Just finished
			$this->resetStack();

			return false;
		}

		if (!empty($this->filesStack) && $this->haveEnoughTime())
		{
			while (!empty($this->filesStack))
			{
				$file = array_shift($this->filesStack);

				// Skip over symlinks and non-files
				if (@is_link($file) || !@is_file($file))
				{
					continue;
				}

				$reldir = $this->getRelativePath($file);
				$perms  = $this->fileperms;

				if (array_key_exists($reldir, $this->customperms))
				{
					$perms = $this->customperms[$reldir]['perms'];
				}

				$this->chmod($file, $perms ?: $this->fileperms);
			}
		}

		if (empty($this->filesStack) && empty($this->folderStack))
		{
			// Just finished
			$this->resetStack();

			return false;
		}

		return true;
	}
}