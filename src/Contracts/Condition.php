<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Contracts;

/**
 * Condition interface
 *
 * The Condition interface is used by condition parsers.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Contracts
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
interface Condition
{
    /**
     * Parse condition.
     *
     * @param string $source
     *
     * @return bool
     */
    public function matches(string $source): bool;
}
