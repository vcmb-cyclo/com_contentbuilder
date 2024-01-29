<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Model\UpgradeHandler;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Helper\Storage;
use Akeeba\Component\AdminTools\Administrator\Model\UpgradeModel;
use Joomla\CMS\Installer\Adapter\PackageAdapter;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseInterface;


/**
 * Custom UpgradeModel handler for updating the settings in the server configuration makers.
 *
 * This handler updates the settings in the saved configuration of the .htaccess Maker, NginX Conf Maker and Web.Config
 * Maker, if these features were previously configured. This is how we can update the list of explicitly allowed files
 * and folders whne things change in Joomla or our software.
 *
 * It does NOT regenerate the server configuration file. This is something the user will have to do manually. The user
 * will, however, be told to do that when they visit the control panel page of the component.
 *
 * @since 7.0.5
 */
class ServerConfMakerUpdates
{
	/**
	 * The UpgradeModel instance we belong to.
	 *
	 * @var   UpgradeModel
	 * @since 7.0.5
	 */
	private $upgradeModel;

	/**
	 * Joomla database driver object
	 *
	 * @var   DatabaseInterface
	 * @since 7.0.5
	 */
	private $dbo;

	/**
	 * Constructor.
	 *
	 * @param   UpgradeModel  $upgradeModel  The UpgradeModel instance we belong to
	 *
	 * @since   9.0.0
	 */
	public function __construct(UpgradeModel $upgradeModel, DatabaseDriver $dbo)
	{
		$this->upgradeModel = $upgradeModel;
		$this->dbo          = $dbo;
	}

	public function onUpdate(string $type, ?PackageAdapter $parent = null)
	{
		if (!class_exists('Akeeba\Component\AdminTools\Administrator\Helper\Storage'))
		{
			@include_once __DIR__ . '/../../Helper/Storage.php';
		}

		if (!class_exists('Akeeba\Component\AdminTools\Administrator\Helper\Storage'))
		{
			return;
		}

		$storage = new Storage();

		$storageKeys = [
			'htconfig',
			'nginxconfig',
			'wcconfig',
		];

		$dirty = false;

		foreach ($storageKeys as $storageKey)
		{
			// Get the saved server config settings
			$savedConfig = $storage->getValue($storageKey, '');

			// No settings for this server config maker; skip over
			if (empty(trim($savedConfig)))
			{
				continue;
			}

			// Decode server config settings
			if (function_exists('base64_encode') && function_exists('base64_encode'))
			{
				$savedConfig = @base64_decode($savedConfig);
			}

			$savedConfig = @json_decode($savedConfig, true);

			//  If decoding failed, skip over this server config maker.
			if (empty($savedConfig))
			{
				continue;
			}

			// Flag the need to save changes
			$dirty = true;

			/**
			 * Update files exempted from the server front- and backend protection.
			 * - REMOVE: administrator/components/com_admintools/restore.php
			 * - ADD: administrator/components/com_akeebabackup/restore.php
			 * - ADD: administrator/components/com_joomlaupdate/restore.php
			 * - ADD: administrator/components/com_joomlaupdate/extract.php
			 */
			$remove = [
				'administrator/components/com_admintools/restore.php'
			];
			$add = [
				"administrator/components/com_akeebabackup/restore.php",
				"administrator/components/com_joomlaupdate/restore.php",
				"administrator/components/com_joomlaupdate/extract.php",
			];
			$savedConfig['exceptionfiles'] = array_merge($savedConfig['exceptionfiles'], $add);
			$savedConfig['exceptionfiles'] = array_diff($savedConfig['exceptionfiles'], $remove);
			$savedConfig['exceptionfiles'] = array_unique($savedConfig['exceptionfiles']);

			// Update backend file types
			$savedConfig['bepextypes'] = array_merge($savedConfig['bepextypes'], [
				'jpe', 'jpg', 'jpeg', 'jp2', 'jpe2', 'png', 'gif', 'bmp', 'css', 'js',
				'swf', 'html', 'mpg', 'mp3', 'mpeg', 'mp4', 'avi', 'wav', 'ogg', 'ogv',
				'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx', 'zip', 'rar', 'pdf', 'xps',
				'txt', '7z', 'svg', 'odt', 'ods', 'odp', 'flv', 'mov', 'htm', 'ttf',
				'woff', 'woff2', 'eot', 'webp',
				'JPG', 'JPEG', 'PNG', 'GIF', 'CSS', 'JS', 'TTF', 'WOFF', 'WOFF2', 'EOT', 'WEBP', 'xsl'
			]);
			$savedConfig['bepextypes'] = array_unique($savedConfig['bepextypes']);

			// Update frontend file types
			$savedConfig['fepextypes'] = array_merge($savedConfig['fepextypes'], [
				'jpe', 'jpg', 'jpeg', 'jp2', 'jpe2', 'png', 'gif', 'bmp', 'css', 'js',
				'swf', 'html', 'mpg', 'mp3', 'mpeg', 'mp4', 'avi', 'wav', 'ogg', 'ogv',
				'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx', 'zip', 'rar', 'pdf', 'xps',
				'txt', '7z', 'svg', 'odt', 'ods', 'odp', 'flv', 'mov', 'ico', 'htm',
				'ttf', 'woff', 'woff2', 'eot', 'webp',
				'JPG', 'JPEG', 'PNG', 'GIF', 'CSS', 'JS', 'TTF', 'WOFF', 'WOFF2', 'EOT', 'WEBP', 'xsl'
			]);
			$savedConfig['fepextypes'] = array_unique($savedConfig['fepextypes']);

			// Update directories where everything except .php files are allowed
			$savedConfig['exceptiondirs'] = array_merge($savedConfig['exceptiondirs'], [
				'.well-known'
			]);
			$savedConfig['exceptiondirs'] = array_unique($savedConfig['exceptiondirs']);

			// Save the configuration back to the database
			$savedConfig       = json_encode($savedConfig);

			if (function_exists('base64_encode') && function_exists('base64_encode'))
			{
				$savedConfig = base64_encode($savedConfig);
			}

			$storage->setValue($storageKey, $savedConfig);
		}

		if ($dirty)
		{
			$storage->save();
		}
	}
}