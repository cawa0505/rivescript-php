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

use Axiom\Collections\Collection;
use Axiom\Rivescript\Cortex\Input as SourceInput;

/**
 * Add class
 *
 * This class is responsible parsing the <add> tag.
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
class OptionalTag extends Tag
{
    /**
     * Determines where this tag is allowed to
     * be used.
     *
     * @var array<string>
     */
    protected array $allowedSources = ["trigger", "response"];

    /**
     * Regex expression pattern.
     *
     * @var string
     */
    protected string $pattern = "/(\[)(?!\@)(.+?=*)(\])/ui";

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

        if ($this->matchesPattern($this->pattern, $source) === true) {
            $triggerString = $source;
            $matches = $this->getMatchesFromPattern($this->pattern, $triggerString);
            $sets = [];

            /**
             * Replace every "set" in the trigger to their index number
             * found in the string.
             *
             * Example:
             *
             * "I (am|love) a robot. I like (my|style)"
             *
             * Will be replaced with:
             *
             * "I {0} a robot. I like {1}"
             */
            foreach ($matches as $index => $match) {
                $set = explode("|", $match[2]);

                /**
                 * To the set we add and empty value. This will emulate
                 * the optional keywords not being used.
                 */
                $set[] = "";

                if (count($set) > 0) {
                    $triggerString = str_replace($match[0], "{{$index}}", $triggerString);
                    $sets [] = $set;
                }
            }

            $combinations = $this->getCombinations(...$sets);

            if (count($combinations) > 0) {
                $sentences = [];

                foreach ($combinations as $combination) {
                    $tmp = $triggerString;
                    foreach ($combination as $index => $string) {
                        $tmp = str_replace("{{$index}}", $string, $tmp);

                        if (empty($string) === true) {
                            $tmp = str_replace("\x20\x20", " ", $tmp);
                        }
                    }

                    $tmp = preg_replace('/(\()(?!\@)(.+?=*)(\))/ui', "", $tmp);

                    $sentences [] = trim($tmp);
                }

                $result = array_filter($sentences, static function (string $sentence) use ($input) {
                    return (strtolower($sentence) === strtolower($input->source()));
                });

                if (count($result) > 0) {
                    return $input->source();
                }
            }
        }
        return $source;
    }

    /**
     * Create a set of possible combinations for given arrays.
     *
     * Note: This function is taken from stackoverflow.com
     * first posted by Guilhermo Luna and later edited by user Amlette.
     *
     * @see https://stackoverflow.com/questions/8567082/how-to-generate-in-php-all-combinations-of-items-in-multiple-arrays/33259643#33259643
     *
     * @param array ...$arrays A set of arrays to combine.
     *
     * @return array|array[]
     *
     */
    private function getCombinations(array ...$arrays): array
    {
        $result = [[]];
        foreach ($arrays as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property => $property_value]);
                }
            }
            $result = $tmp;
        }
        return $result;
    }

    /**
     * Return the tag that the class represents.
     *
     * @return string
     */
    public function getTagName(): string
    {
        return "optional";
    }
}
