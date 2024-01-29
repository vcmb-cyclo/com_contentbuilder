<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Model;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Helper\Storage;
use Joomla\CMS\MVC\Model\BaseModel;

#[\AllowDynamicProperties]
class SeoandlinktoolsModel extends BaseModel
{
	public $defaultConfig = [
		'linkmigration' => 0,
		'migratelist'   => '',
	];

	public function getConfig(): array
	{
		$params = Storage::getInstance();
		$config = [];

		foreach ($this->defaultConfig as $k => $v)
		{
			$config[$k] = $params->getValue($k, $v);
		}

		return $config;
	}

	public function saveConfig(array $newParams): void
	{
		$params = Storage::getInstance();

		foreach ($newParams as $key => $value)
		{
			$params->setValue($key, $value);
		}

		$params->save();
	}
}