<?php
/**
 * Tail plugin for Craft CMS 3.x
 *
 * An console command to tail your Craft log
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2018 Superbig
 */

namespace superbig\tail;


use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\console\Application as ConsoleApplication;

use yii\base\Event;

/**
 * Class Tail
 *
 * @author    Superbig
 * @package   Tail
 * @since     1.0.0
 *
 */
class Tail extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Tail
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'superbig\tail\console\controllers';
        }
    }

    // Protected Methods
    // =========================================================================

}
