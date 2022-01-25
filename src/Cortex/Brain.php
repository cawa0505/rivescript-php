<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex;

use Axiom\Rivescript\Rivescript;

/**
 * Brain class
 *
 * The Brain teaches the interpreter how to respond from
 * the user's input.
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
class Brain
{
    /**
     * A collection of topics.
     *
     * @var array<Topic>
     */
    protected array $topics;

    /**
     * @var \Axiom\Rivescript\Rivescript
     */
    protected Rivescript $master;

    /**
     * Create a new instance of Brain.
     */
    public function __construct()
    {
        $this->createTopic('random');
    }

    /**
     * Set the interpreter for future reference.
     *
     * @param \Axiom\Rivescript\Rivescript $master
     *
     * @return void
     */
    public function setMaster(Rivescript $master): void
    {
        $this->master = $master;
    }

    /**
     * Teach the Brain with new information.
     *
     * @param resource $stream the stream to read from.
     *
     * @return void
     */
    public function teach($stream): void
    {
        $commands = synapse()->commands;

        if (is_resource($stream)) {
            $lineNumber = 0;

            rewind($stream);

            while (!feof($stream)) {
                $line = fgets($stream);
                $node = new Node($line, $lineNumber++);

                $error = $node->checkSyntax();

                if ($error) {
                    $this->master->say("Error: {$error}");
                }
                if ($node->isInterrupted() === true || $node->isComment() === true || $node->isEmpty()) {
                    continue;
                }

                $commands->each(function ($command) use ($node) {
                    $class = "\\Axiom\\Rivescript\\Cortex\\Commands\\$command";
                    $commandClass = new $class();
                    $commandClass->parse($node);
                });
            }
        } else {
            echo "CANNOT TEACH INVALID RESOURCE STREAM\n";
        }
    }

    /**
     * Return a topic.
     *
     * @param string|null $name The name of the topic to return.
     *
     * @return mixed|null
     */
    public function topic(string $name = null)
    {
        if (is_null($name)) {
            $name = synapse()->memory->shortTerm()->get('topic') ?: 'random';
        }

        if (!isset($this->topics[$name])) {
            return null;
        }

        return $this->topics[$name];
    }

    /**
     * Create a new Topic.
     *
     * @param string $name The name of the topic to create.
     *
     * @return Topic
     */
    public function createTopic(string $name): Topic
    {
        $this->topics[$name] = new Topic($name);

        return $this->topics[$name];
    }
}
