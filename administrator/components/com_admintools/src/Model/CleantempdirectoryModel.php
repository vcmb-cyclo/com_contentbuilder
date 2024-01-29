<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Model;

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\MVC\Model\BaseModel;

#[\AllowDynamicProperties]
class CleantempdirectoryModel extends BaseModel
{
	/** @var int Total numbers of folders in this site */
	public $totalFolders = 0;

	/** @var int Numbers of folders already processed */
	public $doneFolders = 0;

	/**
	 * Minimum age (in seconds) of files and folders to delete.
	 *
	 * Default: 60 seconds.
	 *
	 * @var   int
	 * @since 6.1.0
	 */
	private $minAge = 60;

	/** @var float The time the process started */
	private $startTime = null;

	/** @var array The folders to process */
	private $folderStack = [];

	/** @var array The files to process */
	private $filesStack = [];

	/**
	 * Scans $root for directories and updates $folderStack
	 *
	 * @param   string|null  $root  The full path of the directory to scan
	 *
	 * @throws  Exception
	 */
	public function getDirectories(?string $root = null): void
	{
		$tempDir    = Factory::getApplication()->get('tmp_path');
		$cutoffTime = PHP_INT_MAX;

		if (empty($root))
		{
			$root = $tempDir;
		}

		if ($root === $tempDir)
		{
			$cutoffTime = time() - $this->minAge;
		}

		$folders = Folder::folders($root, '.', false, true, []);

		if (empty($folders))
		{
			$folders = [];
		}

		$this->totalFolders += count($folders);

		// Filter folders by date
		$folders = array_filter($folders, function ($folder) use ($cutoffTime) {
			return (@filemtime($folder) ?: (@filemtime($folder . '/.') ?: 0)) < $cutoffTime;
		});

		if (!count($folders))
		{
			return;
		}

		foreach ($folders as $folder)
		{
			$this->getDirectories($folder);
			$this->getFiles($folder);

			$this->folderStack[] = $folder;
		}
	}

	/**
	 * Scans $root for files and updates $filesStack
	 *
	 * @param   string|null  $root  The full path of the directory to scan
	 *
	 * @throws  Exception
	 */
	public function getFiles(?string $root = null)
	{
		$tempDir    = Factory::getApplication()->get('tmp_path');
		$root       = $root ?: $tempDir;
		$cutoffTime = PHP_INT_MAX;

		if (empty($root))
		{
			return;
		}

		if ($root === $tempDir)
		{
			$cutoffTime = time() - $this->minAge;
		}

		$root    = rtrim($root, '/');
		$tempDir = rtrim($tempDir, '/');
		$files = Folder::files($root, '.', false, true, [], [], true) ?: [];

		// Filter files by modified date
		$files = array_filter($files, function ($file) use ($cutoffTime) {
			return (@filemtime($file) ?: 0) < $cutoffTime;
		});

		if ($root == $tempDir)
		{
			$this->filesStack = [];

			$files = array_filter($files, function ($folder) {
				return !in_array($folder, ['index.html', 'index.htm', '.htaccess', 'web.config']);
			});
		}

		$this->filesStack   = array_merge($this->filesStack, $files);
		$this->totalFolders += count($files);
	}

	public function startScanning()
	{
		$this->resetStack();
		$this->resetTimer();
		$this->getDirectories();
		$this->getFiles();

		$this->folderStack = $this->folderStack ?: [];
		$this->filesStack  = $this->filesStack ?: [];

		asort($this->folderStack);
		asort($this->filesStack);

		$this->saveStack();

		if (!$this->haveEnoughTime())
		{
			return true;
		}

		return $this->run(false);
	}

	public function run(bool $resetTimer = true)
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
	 * @return bool
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
		$stack = [
			'folders' => $this->folderStack,
			'files'   => $this->filesStack,
			'total'   => $this->totalFolders,
			'done'    => $this->doneFolders,
		];

		$stack = json_encode($stack);

		if (function_exists('base64_encode') && function_exists('base64_decode'))
		{
			if (function_exists('gzdeflate') && function_exists('gzinflate'))
			{
				$stack = gzdeflate($stack, 9);
			}

			$stack = base64_encode($stack);
		}

		Factory::getApplication()->getSession()->set('admintools.cleantmp_stack', $stack);
	}

	/**
	 * Resets the file/folder stack saved in the session
	 */
	private function resetStack(): void
	{
		Factory::getApplication()->getSession()->set('admintools.cleantmp_stack', null);

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
		$stack = Factory::getApplication()->getSession()->get('admintools.cleantmp_stack');

		if (empty($stack))
		{
			$this->folderStack  = [];
			$this->filesStack   = [];
			$this->totalFolders = 0;
			$this->doneFolders  = 0;

			return;
		}

		if (function_exists('base64_encode') && function_exists('base64_decode'))
		{
			$stack = base64_decode($stack);

			if (function_exists('gzdeflate') && function_exists('gzinflate'))
			{
				$stack = gzinflate($stack);
			}
		}

		$stack = json_decode($stack, true);

		$this->folderStack  = $stack['folders'];
		$this->filesStack   = $stack['files'];
		$this->totalFolders = $stack['total'];
		$this->doneFolders  = $stack['done'];
	}

	private function deletePath($path): bool
	{
		// Check to make sure the path valid and clean
		$path = (@realpath($path)) ?: $path;

		if (!@file_exists($path))
		{
			return true;
		}

		if (is_dir($path))
		{
			return Folder::delete($path);
		}

		return File::delete($path);
	}

	private function RealRun(): bool
	{
		while (!empty($this->filesStack) && $this->haveEnoughTime())
		{
			$file = array_pop($this->filesStack);
			$this->doneFolders++;
			$this->deletePath($file);
		}

		if (empty($this->filesStack))
		{
			while (!empty($this->folderStack) && $this->haveEnoughTime())
			{
				$folder = array_pop($this->folderStack);
				$this->doneFolders++;
				$this->deletePath($folder);
			}
		}

		if (empty($this->filesStack) && empty($this->folderStack))
		{
			// Just finished
			$this->resetStack();

			return false;
		}

		// If we have more folders or files, continue in the next step
		return true;
	}
}