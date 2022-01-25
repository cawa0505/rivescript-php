<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex\Triggers;

use Axiom\Rivescript\Contracts\Trigger as TriggerContract;
use Axiom\Rivescript\Cortex\Input;

/**
 * Trigger class
 *
 * The trigger class is the base class for the trigger
 * detectors in this directory. It will contain the parseTags()
 * function that all trigger detectors use.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Cortext\Triggers
 * @author   Shea Lewis <shea.lewis89@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.3.0
 */
abstract class Trigger implements TriggerContract
{

    /**
     * Parse the response through the available tags.
     *
     * @param string $trigger The trigger to parse tags on.
     * @param Input  $input   Input information.
     *
     * @return string
     */
    protected function parseTags(string $trigger, Input $input): string
    {
        synapse()->tags->each(function ($tag) use (&$trigger, $input) {
            $class = "\\Axiom\\Rivescript\\Cortex\\Tags\\$tag";
            $tagClass = new $class("trigger");

            $trigger = $tagClass->parse($trigger, $input);
        });

        return trim($trigger);
    }
}
