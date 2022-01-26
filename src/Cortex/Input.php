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

/**
 * Input class
 *
 * The Input class stores information about what
 * the user typed to the bot.
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
class Input
{
    /**
     * The source string.
     *
     * @var string
     */
    protected string $source = '';

    /**
     * The original source string.
     *
     * @var string
     */
    protected string $original = '';

    /**
     * The user id.
     *
     * @var string
     */
    protected string $user = '';

    /**
     * Create a new Input instance.
     *
     * @param string $source The source string.
     * @param string $user   The user identifier.
     */
    public function __construct(string $source, string $user = '0')
    {
        $this->original = $source;
        $this->user = $user;

        $this->cleanOriginalSource();
    }

    /**
     * Return the original input.
     *
     * @return string
     */
    public function original(): string
    {
        return $this->original;
    }

    /**
     * Return the source input.
     *
     * @return string
     */
    public function source(): string
    {
        return $this->source;
    }

    /**
     * Return the current user speaking.
     *
     * @return string
     */
    public function user(): string
    {
        return $this->user;
    }

    /**
     * Clean the source input, so its in a state easily readable
     * by the interpreter.
     *
     * @return void
     */
    protected function cleanOriginalSource(): void
    {
        $patterns = synapse()->memory->substitute()->keys()->all();
        $replacements = synapse()->memory->substitute()->values()->all();

      //  $this->source = mb_strtolower($this->original);
        $this->source = $this->original;
       // $this->source = preg_replace($patterns, $replacements, $this->source);
       // $this->source = preg_replace('/[^\pL\d\s]+/u', '', $this->source);
       // $this->source = remove_whitespace($this->source);
    }
}
