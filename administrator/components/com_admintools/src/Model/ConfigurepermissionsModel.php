<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Model;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Helper\Storage;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Database\ParameterType;

#[\AllowDynamicProperties]
class ConfigurepermissionsModel extends BaseDatabaseModel
{
	public $list = [];

	/**
	 * Saves the default file permissions to the Admin Tools parameters storage
	 */
	public function saveDefaults()
	{
		$dirperms  = $this->getState('dirperms');
		$fileperms = $this->getState('fileperms');

		$dirperms = octdec($dirperms);

		if (($dirperms < 0600) || ($dirperms > 0777))
		{
			$dirperms = 0755;
		}

		$fileperms = octdec($fileperms);
		if (($fileperms < 0600) || ($fileperms > 0777))
		{
			$fileperms = 0755;
		}

		$params = Storage::getInstance();

		$params->setValue('dirperms', '0' . decoct($dirperms));
		$params->setValue('fileperms', '0' . decoct($fileperms));
		$params->setValue('perms_show_hidden', $this->getState('perms_show_hidden', 0));

		$params->save();
	}

	/**
	 * Updates the listing with the records matching the filter_path state
	 */
	public function applyPath(): void
	{
		// Get and clean up the path
		$path    = $this->getState('path', '');
		$relpath = $this->getRelativePath($path);

		$this->setState('filter_path', $relpath);

		$db    = $this->getDatabase();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__admintools_customperms'));

		$fltPath = $this->getState('filter_path');

		if (!empty($fltPath))
		{
			$fltPath = $fltPath . '%';
			$query->where($db->quoteName('path') . ' LIKE :path')
				->bind(':path', $fltPath);
		}

		$fltPerms = $this->getState('perms', null);

		if (!empty($fltPerms))
		{
			$query->where($db->qn('perms') . ' = :permissions')
				->bind(':permissions', $fltPerms);
		}

		$this->list = $db->setQuery($query)->loadAssocList() ?: [];
	}

	public function getRelativePath(string $pathRelativeToRoot): string
	{
		$path = JPATH_ROOT . '/' . $pathRelativeToRoot;
		$path = Path::clean($path, '/');

		// Clean up the root
		$root = Path::clean(JPATH_ROOT, '/');

		// Find the relative path and get the custom permissions
		return ltrim(substr($path, strlen($root)), '/');
	}

	public function getListing(): array
	{
		$this->applyPath();

		$siteLocalPath = $this->getState('filter_path', '');
		$absolutePath  = JPATH_ROOT . '/' . $siteLocalPath;
		$params        = Storage::getInstance();
		$excludeFilter = $params->getValue('perms_show_hidden', 0) ? ['.*~'] : ['^\..*', '.*~'];
		$folders_raw   = Folder::folders($absolutePath) ?: [];
		$files_raw     = Folder::files($absolutePath, '.', false, false, [
			'.svn', 'CVS', '.DS_Store', '__MACOSX',
		], $excludeFilter) ?: [];

		return [
			'folders' => array_map([$this, 'convertIntoPathListing'], $folders_raw),
			'files'   => array_map([$this, 'convertIntoPathListing'], $files_raw),
			'crumbs'  => explode('/', $siteLocalPath),
		];
	}

	public function getPerms(string $pathRelativeToSiteRoot): string
	{
		$key = array_search($pathRelativeToSiteRoot, array_column($this->list, 'path'));

		return ($key === false) ? '' : $this->list[$key]['perms'];
	}

	public function savePermissions(bool $apply = false)
	{
		$this->applyAndSavePermissions($this->getState('folders', []) ?: [], $apply, 'folder');
		$this->applyAndSavePermissions($this->getState('files', []) ?: [], $apply, 'file');
	}

	private function applyAndSavePermissions(array $pathsAndPerms, bool $apply = false, string $type = 'folder')
	{
		if (empty($pathsAndPerms))
		{
			return;
		}

		// Delete all entries for the contained folders
		$db            = $this->getDatabase();
		$pathsToDelete = array_keys($pathsAndPerms);

		$query = $db->getQuery(true)
			->delete($db->quoteName('#__admintools_customperms'))
			->whereIn($db->quoteName('path'), $pathsToDelete, ParameterType::STRING);
		$db->setQuery($query)->execute();

		// Fix permissions, if requested
		if ($apply)
		{
			$params = Storage::getInstance();
			$defaultPerms = ($type === 'folder') ? 0755 : 0644;
			$defaultPerms = $params->getValue(($type === 'folder') ? 'dirperms' : 'fileperms', $defaultPerms);

			/** @var FixpermissionsModel $fixmodel */
			$fixmodel = $this->getMVCFactory()->createModel('Fixpermissions', 'Administrator');

			array_map(function ($path, $perms) use ($defaultPerms, $fixmodel) {
				$fixmodel->chmod($path, $perms ?: $defaultPerms);
			}, array_keys($pathsAndPerms), $pathsAndPerms);
		}

		// First, let's remove array items without custom permissions
		$pathsAndPerms = array_filter($pathsAndPerms, function ($perms) {
			return !empty($perms);
		});

		// Making sure there are any records with custom permissions left.
		if (empty($pathsAndPerms))
		{
			return;
		}

		// Save the remaining custom permissions to the database
		$query = $db->getQuery(true)
			->insert($db->qn('#__admintools_customperms'))
			->columns([
				$db->quoteName('path'),
				$db->quoteName('perms'),
			]);

		foreach ($pathsAndPerms as $folder => $perms)
		{
			$query->values($db->quote($folder) . ',' . $db->quote($perms));
		}

		$db->setQuery($query)->execute();
	}

	private function convertIntoPathListing(string $relativePath): array
	{
		$siteLocalPath      = $this->getState('filter_path', '');
		$siteLocalPath      .= empty($siteLocalPath) ? '' : '/';
		$perms              = $this->getPerms($siteLocalPath . $relativePath);
		$currentPermissions = @fileperms(JPATH_ROOT . '/' . $siteLocalPath . $relativePath);
		$ownerUser          = function_exists('fileowner') ? @fileowner(JPATH_ROOT . '/' . $siteLocalPath . $relativePath) : false;
		$ownerGroup         = function_exists('filegroup') ? @filegroup(JPATH_ROOT . '/' . $siteLocalPath . $relativePath) : false;

		return [
			'item'      => $relativePath,
			'path'      => $siteLocalPath . $relativePath,
			'perms'     => $perms,
			'realperms' => $currentPermissions,
			'uid'       => $ownerUser,
			'gid'       => $ownerGroup,
		];
	}
}