<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript;

use Axiom\Rivescript\Cortex\ContentLoader\ContentLoader;
use Axiom\Rivescript\Cortex\Input;
use Axiom\Rivescript\Cortex\Output;
use Axiom\Rivescript\Traits\Tags;

/**
 * Rivescript class
 *
 * The entry point for using the interpreter.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Cortext
 * @author   Shea Lewis <shea.lewis89@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.3.0
 */
class Rivescript extends ContentLoader
{
    use Tags;

    public const VERBOSITY_NORMAL = 0;
    public const VERBOSITY_VERBOSE = 1;
    public const VERBOSITY_VERY_VERBOSE = 2;
    public const VERBOSITY_DEBUG = 3;

    public $onSay = null;

    /**
     * A recursion limit before an attempt to
     * fetch a reply will be abandoned.
     *
     * @var int
     */
    public int $depth = 50;

    /**
     * Error messages.
     *
     * @var array|string[]
     */
    public array $errors = [
        "replyNotMatched" => "ERR: No Reply Matched",
        "replyNotFound" => "ERR: No Reply Found",
        "objectNotFound" => "[ERR: Object Not Found]",
        "deepRecursion" => "ERR: Deep Recursion Detected"
    ];

    /**
     * Flag to indicating if utf8
     * modes is enabled.
     *
     * @var bool
     */
    protected bool $utf8 = false;

    /**
     * Flag to indicate debug mode
     * is enabled or not.
     *
     * @var bool
     */
    public bool $debug = false;

    /**
     * Create a new Rivescript instance.
     *
     * @throws \Axiom\Rivescript\Exceptions\ContentLoadingException
     */
    public function __construct()
    {
        parent::__construct();

        include __DIR__ . '/bootstrap.php';

        synapse()->brain->setMaster($this);
        synapse()->rivescript = $this;

        $this->registerTags();
    }

    /**
     * Initialize the tags
     *
     * @return void
     */
    private function registerTags(): void
    {
        synapse()->tags->each(
            function ($tag) {
                $class = "\\Axiom\\Rivescript\\Cortex\\Tags\\$tag";
                $tagInstance = new $class();

                $tagInfo = $tagInstance->getTagName();
                if (is_array($tagInfo)) {
                    foreach ($tagInfo as $tagName) {
                        synapse()->memory->tags()->put($tagName, $tagInstance);
                    }
                } else {
                    synapse()->memory->tags()->put($tagInfo, $tagInstance);
                }
            }
        );
    }

    /**
     * Load Rivescript interpretable content.
     * Into the Interpreter.
     *
     * Please note: This supports
     *
     * - Directory path to Rivescript interpretable files.
     * - Array of absolute paths to Rivescript interpretable files
     * - Absolute string containing path to Rivescript interpretable file.
     * - A stream of text with Rivescript interpretable script.
     *
     * Please note 2:
     *
     * If you profile a directory with rivescript documents make sure they are
     * all interpretable rivescript will throw syntax errors while trying to
     * parse those files.
     *
     * @param array<string>|string $info The files to read
     *
     * @return void
     */
    public function load($info): void
    {
        parent::load($info);
        $this->processInformation();
    }

    /**
     * Stream new information into the brain.
     *
     * @param string $string The string of information to feed the brain.
     *
     * @return void
     */
    public function stream(string $string): void
    {
        fseek($this->getStream(), 0, SEEK_SET);
        rewind($this->getStream());

        $this->writeToMemory($string);
        $this->processInformation();
    }

    /**
     * Process new information in the
     * stream.
     *
     * @return void
     */
    private function processInformation(): void
    {
        synapse()->memory->local()->put('concat', 'none');
        synapse()->brain->teach($this->getStream());
    }

    /**
     * Set user variables.
     *
     * @param string $user  The user for this variable.
     * @param string $name  The name of the variable.
     * @param string $value The value of the variable.
     *
     * @return void
     */
    public function setUservar(string $user, string $name, string $value): void
    {
        synapse()->memory->user($user)->put($name, $value);
    }

    /**
     * Get user variable.
     *
     * @param string $user The user for this variable.
     * @param string $name The name of the variable.
     *
     * @return mixed
     */
    public function getUservar(string $user, string $name)
    {
        return synapse()->memory->user($user)->get($name);
    }

    /**
     * @param string $string
     * @param bool   $utf8
     *
     * @return string
     * @deprecated
     */
    private function stripNasties(string $string, bool $utf8): string
    {
        return preg_replace("/[^A-Za-z0-9 ]/m", "", $string);
    }

    /**
     * Log a message to say.
     *
     * @param string $message   The message to print out.
     * @param array  $args      (format) arguments for the message.
     * @param int    $verbosity The verbosity level of the message.
     *
     * @return void
     */
    public function say(string $message, array $args = [], int $verbosity = Rivescript::VERBOSITY_NORMAL): void
    {

        $message = $this->formatString($message, $args);

        if ($this->onSay) {
            call_user_func($this->onSay, $message, $verbosity);
        }
    }

    /**
     * Write a warning.
     *
     * @param string $message   The message to print out.
     * @param array  $args      (format) arguments for the message.
     * @param int    $verbosity The verbosity level of the message.
     *
     * @return void
     */
    public function warn(string $message, array $args = [], int $verbosity = Rivescript::VERBOSITY_NORMAL): void
    {
        $message = $this->formatString($message, $args);

        if ($this->onSay) {
            call_user_func($this->onSay, $message, $verbosity);
        }
    }


    /**
     * Create a string PDO style/
     *
     * @param string $msg  The message to write.
     * @param array  $args The arguments for the message.
     *
     * @return string
     */
    private function formatString(string $msg, array $args = []): string
    {
        $words = explode(' ', $msg);
        $parameters = array_filter($words, static function (string $part) {
            return ($part[0] === ':');
        });

        if (is_array($args) === true && count($args) > 0) {
            foreach ($parameters as $param) {
                $key = substr($param, 1);
                if (isset($args[$key]) === true) {
                    $msg = str_replace($param, $args[$key], $msg);
                }
            }
        }

        return $msg;
    }

    /**
     * Enable debug mode.
     *
     * @param bool $enabled Enable true/false.
     *
     * @return void
     */
    public function enableDebugMode(bool $enabled = true): void
    {
        synapse()->memory->global()->put('debug', true);
    }

    /**
     * Make the client respond to a message.
     *
     * @param string      $msg   The message the client has to process and respond to.
     * @param string      $user  The user id.
     * @param string|null $scope Not used at this point.
     *
     * @return string
     */
    public function reply(string $msg, string $user = 'local-user', string $scope = null): string
    {

        // FIXME: Must be $user, $message, Sscope
        //    $msg = $this->stripNasties($msg, "");
        synapse()->rivescript->say("Asked to reply to :user :msg", ['user' => $user, 'message' => $msg]);


        $input = new Input($msg, $user);
        $output = new Output();

        synapse()->input = $input;

        $output = $output->process();

        if (empty($output)) {
            $output = $this->errors['replyNotMatched'];
        }

        synapse()->memory->inputs()->push($msg);
        synapse()->memory->replies()->push($output);

        return $output;
    }
}
