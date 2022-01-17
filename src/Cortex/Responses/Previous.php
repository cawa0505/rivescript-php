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
 * ContinueResponse class
 *
 * The Previous class determines if the response type
 * is a previous type.
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
class Previous extends Response implements ResponseContract
{

    /**
     * Handle Continue responses.
     *
     * @return false|string
     */
    public function parse()
    {
        if ($this->responseQueueItem()->getCommand() === '%') {
            return $this->source();
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
        return 'previous';
    }
}
