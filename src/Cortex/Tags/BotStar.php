<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex\Tags;

use Axiom\Rivescript\Cortex\Input as SourceInput;

/**
 * Star class
 *
 * This class is responsible parsing the <botstar>/<botstarN>... tag.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Cortext\Tags
 * @author   Shea Lewis <shea.lewis89@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.3.0
 */
class BotStar extends Tag
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
    protected string $pattern = "/<botstar(\d+)?>/i";

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
            $matches = $this->getMatches($source);
            $wildcards = synapse()->memory->shortTerm()->get("wildcards");

            foreach ($matches as $match) {
                $index = (empty($match[1]) ? 0 : $match[1] - 1);
                $source = str_replace($match[0], $wildcards[$index], $source);
            }
        }

        return $source;
    }

    /**
     * Return the tag that the class represents.
     *
     * @return array
     */
    public function getTagName(): array
    {
        return ["botstar", "botstar1", "botstar2", "botstar3", "botstar4", "botstar5", "botstar6", "botstar7", "botstar8", "botstar9"];
    }
}
