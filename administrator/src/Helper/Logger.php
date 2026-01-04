<?php

/**
 * ContentBuilder Logs.
 *
 * Log file manager.
 *
 * @package     ContentBuilder
 * @subpackage  Site.Helper
 * @since       6.0.0
 * @author      Xavier DANO
 * @license     GNU/GPL
 */


declare(strict_types=1);

namespace Component\Contentbuilder\Administrator\Helper;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;

final class Logger
{
    private static bool $registered = false;

    private static function register(): void
    {
        if (self::$registered) {
            return;
        }

        Log::addLogger(
            ['text_file' => 'com_contentbuilder.admin.log'],
            Log::ALL,
            ['com_contentbuilder.admin']
        );

        Log::addLogger(
            ['text_file' => 'com_contentbuilder.site.log'],
            Log::ALL,
            ['com_contentbuilder.site']
        );

        self::$registered = true;
    }

    private static function category(): string
    {
        $app = Factory::getApplication();

        return $app->isClient('administrator')
            ? 'com_contentbuilder.admin'
            : 'com_contentbuilder.site';
    }

    /**
     * Contexte automatique (component/view/task), + quelques infos utiles.
     * Tu peux enlever ce que tu ne veux pas.
     */
    private static function baseContext(): array
    {
        $app   = Factory::getApplication();
        $input = $app->getInput();

        return [
            'client'    => $app->isClient('administrator') ? 'admin' : 'site',
            'component' => $input->getCmd('option', ''),
            'view'      => $input->getCmd('view', ''),
            'task'      => $input->getCmd('task', ''),
            'userId'    => (int) Factory::getUser()->id,
        ];
    }

    private static function format(string $message, array $context = []): string
    {
        $merged = self::baseContext();

        // Le contexte explicite fourni à l'appel prend le dessus
        foreach ($context as $k => $v) {
            $merged[$k] = $v;
        }

        $json = json_encode($merged, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $json ? ($message . ' | ' . $json) : $message;
    }

    /** Debug seulement si debug Joomla activé */
    public static function debug(string $message, array $context = []): void
    {
        if (!Factory::getApplication()->get('debug')) {
            return;
        }

        self::register();
        Log::add(self::format($message, $context), Log::DEBUG, self::category());
    }

    public static function info(string $message, array $context = []): void
    {
        self::register();
        Log::add(self::format($message, $context), Log::INFO, self::category());
    }

    public static function warning(string $message, array $context = []): void
    {
        self::register();
        Log::add(self::format($message, $context), Log::WARNING, self::category());
    }

    public static function error(string $message, array $context = []): void
    {
        self::register();
        Log::add(self::format($message, $context), Log::ERROR, self::category());
    }

    public static function exception(\Throwable $e, array $context = []): void
    {
        $context += [
            'exception' => get_class($e),
            'message'   => $e->getMessage(),
            'file'      => $e->getFile(),
            'line'      => $e->getLine(),
        ];

        self::error('Exception', $context);
    }
}
