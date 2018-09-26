<?php
/**
 * Tail plugin for Craft CMS 3.x
 *
 * An console command to tail your Craft log
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2018 Superbig
 */

namespace superbig\tail\console\controllers;

use craft\helpers\FileHelper;
use craft\helpers\Path;
use superbig\tail\Tail;

use Craft;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;
use Symfony\Component\Finder\Finder;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;


/**
 * Default Command
 *
 * @author    Superbig
 * @package   Tail
 * @since     1.0.0
 */
class DefaultController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * @var string The type of log to tail (web, queue, your-plugin)
     */
    public $type = 'web';

    /**
     * @var string How many lines to display initially
     */
    public $lines = null;

    /**
     * @var string Clear the terminal screen
     */
    public $clear = false;

    /**
     * @var string Hide stack traces
     */
    public $hideStackTraces = false;

    public function options($actionID)
    {
        return [
            'lines', 'clear', 'hideStackTraces', 'type',
        ];
    }

    public function optionAliases()
    {
        return [
            'l' => 'lines',
            't' => 'type',
            'c' => 'clear',
        ];
    }

    /**
     * Tail the latest logfile
     *
     * @return mixed
     */
    public function actionIndex()
    {

        $logDirectory = Craft::$app->getPath()->getLogPath();

        if (!$path = $this->findLatestLogFile($logDirectory, $this->type)) {
            $this->warn("Could not find a log file in `{$logDirectory}`.");

            return;
        }

        $lines       = $this->lines;
        $filters     = $this->getFilters();
        $tailCommand = "tail " . ($lines ? "-n {$lines}" : "") . "-f " . escapeshellarg($path) . " {$filters}";

        $this->handleClearOption();

        (new Process($tailCommand))
            ->setTty(true)
            ->setTimeout(null)
            ->run(function($type, $line) {
                $this->handleClearOption();
                $this->output->write($line);
            });

        return ExitCode::OK;
    }


    protected function findLatestLogFile(string $directory, $type = 'web')
    {
        $logFile = FileHelper::findFiles($directory, ['only' => [$type . '.log']]);

        return $logFile[0] ?? false;
    }

    protected function handleClearOption()
    {
        if (!$this->clear) {
            return;
        }

        $this->stdout(sprintf("\033\143\e[3J"));
    }

    protected function getFilters()
    {
        if ($this->hideStackTraces) {
            return '| grep -i -E "^\[\d{4}\-\d{2}\-\d{2} \d{2}:\d{2}:\d{2}\]|Next [\w\W]+?\:"';
        }
    }
}
