<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Support;

use Monolog\Logger as Monolog;
use Monolog\Handler\StreamHandler;

/**
 * Logger class
 *
 * This class is a wrapper around mono logger. It will
 * log entries to the log file.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Support
 * @author   Shea Lewis <shea.lewis89@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.3.0
 */
class Logger
{
    /**
     * @var \Monolog\Logger
     */
    protected Monolog $logger;

    /**
     * Create a new Logger instance.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->logger = new Monolog('rivescript');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/' . date('m-d-y') . '.log', Monolog::DEBUG));
    }

    /**
     * Adds a log record at the DEBUG level.
     *
     * @param string  $message The log message
     * @param array[] $context The log context
     *
     * @return bool Whether the record has been processed
     */
    public function debug(string $message, array $context = []): bool
    {
        return $this->logger->debug($message, $context);
    }

    /**
     * Adds a log record at the WARNING level.
     *
     * @param string  $message The log message
     * @param array[] $context The log context
     *
     * @return bool Whether the record has been processed
     */
    public function warning(string $message, array $context = []): bool
    {
        return $this->logger->warning($message, $context);
    }
}
