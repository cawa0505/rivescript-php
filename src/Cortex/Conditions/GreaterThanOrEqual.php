<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex\Conditions;

use Axiom\Rivescript\Contracts\Condition as ConditionContract;

/**
 * GreaterThanOrEqual class
 *
 * This class handles >= greater than or equals condition.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Cortext\Conditions
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class GreaterThanOrEqual extends Condition implements ConditionContract
{

    /**
     * Handle conditions '>=' also known as greater than or equal to.
     *
     * @param string $source The source to parse.
     *
     * @return bool
     */
    public function matches(string $source): bool
    {
        $pattern = "/^([\S]+) (>=) ([\S]+) =>/";

        if ($this->matchesPattern($pattern, $source) === true) {
            $matches = $this->getMatchesFromPattern($pattern, $source);

            if ((isset($matches[0]) === true && count($matches[0]) >= 2)) {
                return ($matches[0][1] >= $matches[0][3]);
            }
        }

        return false;
    }
}
