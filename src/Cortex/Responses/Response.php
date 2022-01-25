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

use Axiom\Rivescript\Cortex\ResponseQueue\ResponseQueueItem;
use Axiom\Rivescript\Traits\Regex;

/**
 * Response class
 *
 * The Response class is a base class for all response types in this
 * directory, it contains some helpful functions.
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
abstract class Response
{
    use Regex;

    /**
     * @var ResponseQueueItem
     */
    protected ResponseQueueItem $responseQueueItem;

    /**
     * @var string
     */
    protected string $source = '';

    /**
     * @var string
     */
    protected string $original = "";

    /**
     * Responses must implement this method to
     * indicate the response type.
     *
     * @return string
     */
    abstract public function getType(): string;

    /**
     * Response constructor.
     *
     * @param string            $source            The response line.
     * @param ResponseQueueItem $responseQueueItem Queue information about the response line.
     */
    public function __construct(string $source, ResponseQueueItem $responseQueueItem)
    {
        $this->source = $source;
        $this->original = $this->source;
        $this->responseQueueItem = $responseQueueItem;
    }

    /**
     * @return string
     */
    public function source(): string
    {
        return $this->source;
    }

    /**
     * @return string
     */
    public function original(): string
    {
        return $this->original;
    }

    /**
     * Return the responseQueueItem.
     *
     * @return ResponseQueueItem
     */
    public function responseQueueItem(): ResponseQueueItem
    {
        return $this->responseQueueItem;
    }
}
