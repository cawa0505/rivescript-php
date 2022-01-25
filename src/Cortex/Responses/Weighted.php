<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex\Responses;

use Axiom\Rivescript\Contracts\Response as ResponseContract;

/**
 * Weighted class
 *
 * The Weighted class adds a weight to a weighted response.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Cortext\Responses
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class Weighted extends Response implements ResponseContract
{

    /**
     * Parse the Weighted response.
     *
     * @return bool|string
     */
    public function parse()
    {
        if ($this->responseQueueItem()->getCommand() === '-') {
            $source = $this->source();
            $pattern = '/{weight=([0-9]+)}/';

            if ($this->matchesPattern($pattern, $source)) {
                $matches = $this->getMatchesFromPattern($pattern, $source)[0];

                if (isset($matches[0])) {
                    $this->responseQueueItem->order += (int)$matches[1];
                    $source = str_replace($matches[0], '', $source);
                }

                return $source;
            }
        }

        return false;
    }

    /**
     * Indicate the type of response this
     * class handles.
     *
     * @return string
     */
    public function getType(): string
    {
        return 'weighted';
    }
}
