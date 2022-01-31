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

use Axiom\Rivescript\Traits\Regex;
use Axiom\Rivescript\Contracts\Tag as TagContract;
use LogicException;

/**
 * Tag class
 *
 * The tag base class.
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
abstract class Tag implements TagContract
{
    use Regex;

    /**
     * @var string
     */
    protected string $sourceType = "response";

    /**
     * Store the allowed sources.
     *
     * @var array<string>
     */
    protected array $allowedSources = [];

    /**
     * Defines the Regex structure
     * for a tag.
     *
     * @var string
     */
    protected string $pattern = "";

    /**
     * This variable can be overwritten by
     * a tag class to indicate it needs more
     * secure parsing.
     *
     * @var bool
     */
    protected bool $secure = false;

    /**
     * Create a new Tag instance.
     *
     * @param string $sourceType
     */
    final public function __construct(string $sourceType = "response")
    {
        $this->sourceType = $sourceType;

        if (!isset($this->allowedSources)) {
            throw new LogicException(get_class($this) . " must have an \"allowedSources\" property declared.");
        }
    }

    /**
     * Determine if the type of source is allowed.
     *
     * @return bool
     */
    public function sourceAllowed(): bool
    {
        return in_array($this->sourceType, $this->allowedSources, true);
    }

    /**
     * Detect a tag and its value.
     *
     * @param string $content The string to parse.
     *
     * @return array
     */
    private function _parseTag(string $content, string $tagName): array
    {
        $tag = "";
        $len = strlen($content);
        $reminder = '';

        for ($i = 0; $i < $len; $i++) {
            if (strtolower($tag) === strtolower($tagName)) {
                $reminder = substr($content, $i + 1);
                break;
            }

            if ($content[$i] === ' ') {
                $reminder = substr($content, $i + 1);
                break;
            } elseif ($content[$i] === '>') {
                $reminder = substr($content, $i + 1);
                return ['response' => "<" . $tag . ">", 'reminder' => $reminder];
            }

            $tag .= $content[$i];
        }

        $result = $this->secureSource($reminder, $tagName, ">");
        $reminder = $result['reminder'];

        $response = (isset($tags[$tag]) === true) ? $result['response'] : "<" . $tag . " " . $result['response'] . ">";

        return ['response' => $response, 'reminder' => $reminder];
    }

    /**
     * Parse tags if html is used.
     *
     * @param string $content The content string.
     * @param string $tagName The tag to parse.
     * @param string $endTag  The endTag.
     *
     * @return array
     */
    public function secureSource(string $content, string $tagName, string $endTag = ''): array
    {

        $response = '';
        $reminder = $content;
        $nextTag = strpos($reminder, '<');
        $nextEnd = $endTag ? strpos($reminder, $endTag) : strlen($reminder);

        while ($reminder !== '' && $nextTag > -1 && $nextTag < $nextEnd) {
            $response .= substr($reminder, 0, $nextTag);
            $reminder = substr($reminder, $nextTag + 1);

            $result = $this->_parseTag($reminder, $tagName);

            $response .= $result['response'];
            $reminder = $result['reminder'];
            $nextTag = strpos($reminder, '<');
            $nextEnd = $endTag ? strpos($reminder, $endTag) : strlen($reminder);
        }

        $response .= substr($reminder, 0, $nextEnd);
        $reminder = substr($nextEnd, $nextEnd + strlen($endTag));

        return ['response' => $response, 'reminder' => $reminder];
    }


    /**
     * Does the source have any matches?
     *
     * @param string $source
     *
     * @return bool
     */
    protected function hasMatches(string $source): bool
    {
        return $this->matchesPattern($this->pattern, $source);
    }

    /**
     * Get the regular expression matches from the source.
     *
     * @param string $source
     *
     * @return array[]
     */
    protected function getMatches(string $source): array
    {
        return $this->getMatchesFromPattern($this->pattern, $source) ?? [];
    }

    /**
     * @return mixed
     */
    abstract public function getTagName();
}
