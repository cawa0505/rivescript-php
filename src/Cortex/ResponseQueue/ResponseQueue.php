<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex\ResponseQueue;

use Axiom\Collections\Collection;
use Axiom\Rivescript\Cortex\Node;
use Axiom\Rivescript\Traits\Tags;

/**
 * ResponseQueue class
 *
 * The ResponseQueue releases the responses in order of sending them
 * back to the user.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Cortext\ResponseQueue
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class ResponseQueue extends Collection
{

    use Tags;

    /**
     * A container with responses.
     *
     * @var Collection<ResponseQueueItem>
     */
    protected Collection $responses;

    /**
     * Store the local interpreter options
     * for this instance in time (since they can change).
     *
     * @var array<string, string>
     */
    protected array $options = [];

    /**
     * The trigger string this ResponseQueue belongs to.
     *
     * @var string
     */
    protected string $trigger = "";

    /**
     * ResponseQueue constructor.
     *
     * @param string $trigger the trigger this queue belongs to.
     */
    public function __construct(string $trigger = "")
    {
        parent::__construct();

        $this->responses = new Collection([]);
        $this->trigger = $trigger;

        $this->options = synapse()->memory->local()->all();
    }

    /**
     * Attach a response to the queue.
     *
     * @param Node $node The node contains information about the command.
     *
     * @return void
     */
    public function attach(Node $node): void
    {
        $type = $this->determineResponseType($node->source());
        $queueItem =  new ResponseQueueItem($node->command(), $type, 0, $this->options);
        $this->responses->put($node->value(), $queueItem);
    }

    /**
     * Sort the responses by order.
     *
     * @param Collection<ResponseQueueItem> $responses The array containing the resources.
     *
     * @return Collection<ResponseQueueItem>
     */
    private function sortResponses(Collection $responses): Collection
    {
        return $responses->sort(
            function ($current, $previous) {
                return ($current->order < $previous->order) ? -1 : 1;
            }
        )->reverse();
    }

    /**
     * Check if a response is allowed to be returned by the bot or not.
     *
     * @param string            $response The response to validate.
     * @param ResponseQueueItem $item     The ResponseQueueItem.
     *
     * @return false|mixed
     */
    private function validateResponse(string $response, ResponseQueueItem $item)
    {
        $response = $this->parseTags($response);
        $responses = synapse()->responses;

        foreach ($responses as $class) {
            if (class_exists("\\Axiom\\Rivescript\\Cortex\\Responses\\{$class}")) {
                $class = "\\Axiom\\Rivescript\\Cortex\\Responses\\{$class}";
                $class = new $class($response, $item);

                $result = $class->parse();

                if ($result !== false) {
                    return $result;
                }
            }
        }


        return false;
    }

    /**
     * Merge the ^ continue responses to the last - response.
     *
     * @param Collection<ResponseQueueItem> $responses The array containing the responses.
     *
     * @return Collection<ResponseQueueItem>
     */
    protected function concatContinues(Collection $responses): Collection
    {
        $lastData = $responses->first();
        $lastResponse = "";

        $continues = Collection::make($responses->all());
        $continues->each(
            function (ResponseQueueItem $data, $response) use (&$lastData, &$lastResponse, &$continues) {

                if ($data->type === 'continue') {
                    $continues->remove($lastResponse);
                    $continues->remove($response);

                    /**
                     * none -- the default, nothing is added when continuation lines are joined together.
                     * space -- continuation lines are joined by a space character (\s)
                     * newline -- continuation lines are joined by a line break character (\n)
                     */
                    $options = $lastData->options;
                    $method = $options['concat'];

                    switch ($method) {
                        case 'space':
                            $lastResponse .= " {$response}";
                            break;

                        case 'newline':
                            $lastResponse .= "\n{$response}";
                            break;

                        case 'none':
                        default:
                            $lastResponse .= $response;
                            break;
                    }

                    $continues->put($lastResponse, $lastData);
                }

                if ($data->command !== '^') {
                    $lastData = $data;
                    $lastResponse = $response;
                }
            }
        );


        return $continues;
    }

    /**
     * Determine the order of responses by type.
     *
     * @param Collection<ResponseQueueItem> $responses The responses to inspect.
     *
     * @return Collection<ResponseQueueItem>
     */
    private function determineResponseOrder(Collection $responses): Collection
    {
        return $responses->each(
            function (ResponseQueueItem $data, $response) use ($responses) {
                if (isset($data->type)) {
                    switch ($data->type) {
                        case 'condition':
                            $data->order += 3000000;
                            break;
                        case 'weighted':
                        case 'atomic':
                            $data->order += 1000000;
                            break;
                    }

                    $responses->put($response, $data);
                }
            }
        );
    }

    /**
     * Determine the response type.
     *
     * @param string $response
     *
     * @return string
     */
    public function determineResponseType(string $response): string
    {
        $wildcards = [
            'weighted' => '{weight=(.+?)}',
            'condition' => '/^\*/',
            'continue' => '/^\^/',
            'atomic' => '/-/',
        ];

        foreach ($wildcards as $type => $pattern) {
            if (@preg_match_all($pattern, $response, $matches)) {
                return $type;
            }
        }

        return 'atomic';
    }

    /**
     * Process the Response Queue.
     *
     * @return false|int|string|null
     */
    public function process()
    {
        $sortedResponses = $this->determineResponseOrder($this->responses);

        $validResponses = new Collection([]);
        foreach ($sortedResponses as $response => $item) {
            $result = $this->validateResponse($response, $item);

            if ($result !== false) {
                $validResponses->put($result, $item);
            }
        }

        $validResponses = $this->concatContinues($validResponses);
        $validResponses = $this->sortResponses($validResponses);

        if ($validResponses->count() > 0) {
            return $validResponses->keys()->first();
        }

        return false;
    }
}
