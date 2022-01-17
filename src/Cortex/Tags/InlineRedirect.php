<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex\Tags;

use Axiom\Rivescript\Cortex\Input as SourceInput;

/**
 * InlineRedirect class
 *
 * This class is responsible parsing the <@> tag.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Cortext\Tags
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class InlineRedirect extends Tag
{
    /**
     * Determines where this tag is allowed to
     * be used.
     *
     * @var array<string>
     */
    protected array $allowedSources = ["response"];

    /**
     * Regex expression pattern.
     *
     * @var string
     */
    protected string $pattern = "/({)@(.+?)(})|(<)@(>)/u";

    /**
     * Parse the source.
     *
     * @param string      $source The string containing the Tag.
     * @param SourceInput $input  The input information.
     *
     * @return string
     */
    public function parse(string $source, SourceInput $input): string
    {
        if (!$this->sourceAllowed()) {
            return $source;
        }

        if ($this->hasMatches($source)) {
            $matches = $this->getMatches($source)[0];
            $wildcards = synapse()->memory->shortTerm()->get("wildcards");

            $trigger = null;
            $target = null;
            $key = null;

            if ($matches[0] === "<@>" && is_array($wildcards) === true && count($wildcards) > 0) {
                $target = $wildcards[0];

                $key = trim(synapse()->memory->shortTerm()->get("trigger"));
                $trigger = synapse()->brain->topic()->triggers()->get($key);
            } elseif ($matches[1] === '{') {
                $target = trim($matches[2]);

                $key = trim(synapse()->memory->shortTerm()->get("trigger"));
                $trigger = synapse()->brain->topic()->triggers()->get($key);
            }

            $topic = synapse()->memory->shortTerm()->get("topic");

            if (is_null($trigger) === false && is_null($key) === false && is_null($target) === false) {
                $topic = synapse()->memory->shortTerm()->get("topic") ?: "random";
                $trigger["redirect"] = $target;


                synapse()->brain->topic($topic)->triggers()->put($key, $trigger);
                $source = str_replace($matches[0], '', $source);
            } elseif (is_null($trigger) === true) {
                $topic = synapse()->memory->shortTerm()->get("topic") ?: "random";
                $trigger = synapse()->brain->topic($topic)->triggers()->get($key);

                $trigger["redirect"] = $target;

                synapse()->brain->topic($topic)->triggers()->put($key, $trigger);
                $source = str_replace($matches[0], '', $source);
            } else {
                // Empty for now
            }
        }

        return $source;
    }

    /**
     * Return the tag that the class represents.
     *
     * @return string
     */
    public function getTagName(): string
    {
        return "redirect";
    }
}
