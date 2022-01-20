<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex\Triggers;

use Axiom\Rivescript\Cortex\Input;
use Axiom\Rivescript\Traits\Regex;

/**
 * Alternation class
 *
 * The Alternation class determines if a provided trigger
 * is an Alternation.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Cortext\Triggers
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class Alternation extends Trigger
{
    use Regex;

    /**
     * @var array|string[]
     */
    protected array $signatures = [
        'alternation' => "__\x01\x02__",
        'optional' => "__\x02\x03__",
    ];

    /**
     * The Regex pattern to find sets
     * in the trigger.
     *
     * Note: This pattern ignores the set if a @ character
     * is inside to make sure we don't confuse them with arrays.
     *
     * @var string
     */
//    protected string $pattern = "/(\()(?!\@)(.+?=*)(\))/ui"; // oud alleen alternation
//    protected string $pattern = "/((\(|\[)(?!\@)(.+?=*)(\)|]))/ui"; // goed
    /**
     * @var string
     */
    protected string $pattern = "/(\(|\[)(?!@)(.+?=*)(\)|\])/iu"; // goed

    /**
     * Parse the trigger.
     *
     * @return bool|string
     */
    public function parse(string $trigger, Input $input): bool
    {
        if ($this->matchesPattern($this->pattern, $trigger) === true) {
            $triggerString = $trigger;
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

                if (count($set) > 0) {
                    if ($match[1] === '(') {
                        foreach ($set as $setIndex => $item) {
                            $set[$setIndex] = $this->signatures['alternation'] . $item;
                        }
                        $triggerString = str_replace($match[0], "{{$index}}", $triggerString);
                        $sets [] = $set;
                    }

                    /**
                     * We need to add possible optionals to the
                     * set as well. This programming is a bit odd
                     * but there is no way around it.
                     */
                    if ($match[1] === '[') {
                        $set[] = $this->signatures['optional']; // "__\x01\x20__";
                        $triggerString = str_replace($match[0], "{{$index}}", $triggerString);
                        $sets [] = $set;
                    }
                }
            }


            $combinations = $this->getCombinations(...$sets);

            if (count($combinations) > 0) {
                $sentences = [];

                foreach ($combinations as $combination) {
                    $tmp = $triggerString;
                    foreach ($combination as $index => $string) {
                        $tmp = str_replace("{{$index}}", $string, $tmp);
                    }

                    $tmp = str_replace([$this->signatures['optional'] . " ", $this->signatures['optional']], "", $tmp);
                    $tmp = trim($tmp);

                    $sentences [] = $tmp;
                }

                $signature = $this->signatures['alternation'];
                $cmp = [$this, 'isMatchesWithoutSignature'];

                $result = array_filter($sentences, static function (string $sentence) use ($input, $signature, $cmp) {
                    if (strpos($sentence, $signature) > -1) {
                        return $cmp(strtolower($sentence), strtolower($input->source()));
                    }
                    return false;
                });

                if (count($result) > 0) {
                    return $input->source();
                }
            }
        }
        return false;
    }

    /**
     * Find out if 2 string match exactly if the alternation signature
     * was removed.
     *
     * @param string $withSignature    The string with signatures before alternatives.
     * @param string $withoutSignature The string without signatures.
     *
     * @return bool
     */
    private function isMatchesWithoutSignature(string $withSignature, string $withoutSignature): bool
    {
        $with = explode(" ", $withSignature);
        $without = explode(" ", $withoutSignature);

       // echo "{$withSignature} vs {$withoutSignature}\n";
        $max = count($with);
        for ($i = 0; $i < $max; $i++) {
            if (isset($without[$i]) === false) {
                return false;
            }

            $strWith = str_replace($this->signatures['alternation'], "", $with[$i]);
            $strWithout = $without[$i];

            if ($strWith !== $strWithout) {
                return false;
            }
        }

        return true;
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
}
