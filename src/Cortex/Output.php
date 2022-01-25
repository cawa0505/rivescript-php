<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex;

use Axiom\Rivescript\Traits\Regex;
use Axiom\Rivescript\Traits\Tags;

/**
 * Output class
 *
 * This class is responsible for generating the
 * bot response.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Cortext
 * @author   Shea Lewis <shea.lewis89@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.3.0
 */
class Output
{

    use Regex;
    use Tags;

    /**
     * The output string
     *
     * @var string
     */
    protected string $output = '';

    /**
     * Keep track of the recursion
     * in the output.
     *
     * @var int
     */
    protected int $recursion = 0;

    /**
     * Process the correct output response by the interpreter.
     *
     * @return string
     */
    public function process(): string
    {
        $triggers = synapse()->brain->topic()->triggers();
        $begin = synapse()->brain->topic("__begin__");

        $this->output = '';
//
        if ($begin) {
            synapse()->rivescript->say("Begin label found. Starting processing.");

            $request = $begin->triggers()->get("request");

            if ($request) {
                $request['responses']->process();

                /**
                 * Update the triggers after running the begin request.
                 */
                $triggers = synapse()->brain->topic()->triggers();
            }
        }

        foreach ($triggers as $trigger => $data) {
            $this->searchTriggers($trigger);
            if ($this->output !== '') {
                break;
            }
        }

        return trim($this->output);
    }

    /**
     * Search through available triggers to find a possible match.
     *
     * @param string $trigger The trigger to find responses for.
     *
     * @return void
     */
    protected function searchTriggers(string $trigger): void
    {
        synapse()->triggers->each(
            function ($class) use ($trigger) {
                $triggerClass = "\\Axiom\\Rivescript\\Cortex\\Triggers\\$class";
                $triggerInstance = new $triggerClass(synapse()->input);

                $found = $triggerInstance->parse($trigger, synapse()->input);

                if ($found === true) {
                    synapse()->rivescript->say("Found trigger {$trigger} van {$triggerClass}...");
                    synapse()->memory->shortTerm()->put('trigger', $trigger);
                    $this->output = $this->getResponse($trigger);
                    return false;
                }
            }
        );
    }

    /**
     * Fetch a response from the found trigger.
     *
     * @param string $trigger The trigger to get a response for.
     *
     * @return string
     */
    protected function getResponse(string $trigger): string
    {

        $topic = synapse()->memory->shortTerm()->get('topic') ?? 'random';
        $originalTrigger = synapse()->brain->topic($topic)->triggers()->get($trigger);


        // FIXME: Temp fix for rsts
        if (isset($originalTrigger['responses']) === false) {
            synapse()->rivescript->say("No response found.");
            $this->output = false;
            return $this->output;
        }

        /**
         * Get the best suitable response from
         * the ResponseQueue.
         */
        $response = $originalTrigger['responses']->process();
        $output = $this->parseResponse($response);

        /**
         * It could be possible that tags have altered the trigger.
         * If so evaluate possible changes.
         */
        $processedTrigger = synapse()->brain->topic()->triggers()->get($trigger, null);
        $processedTopic = synapse()->memory->shortTerm()->get('topic') ?? 'random';

        synapse()->rivescript->say("Topic {$topic} vs {$processedTopic}");
        if ($topic !== $processedTopic) {
            synapse()->rivescript->say("Detected topic change");
            //    $this->output = false;
            //   return $this->getResponse( synapse()->input->source());
        }

        if (isset($processedTrigger['redirect'])) {
            $target = synapse()->brain->topic()->triggers()->get($processedTrigger['redirect']);
            if ($target === null) {
                $this->output = false;
                return $this->getResponse("*");
            }

            /**
             * If we redirect from Trigger A to Trigger B the context of the
             * user input changes from the line that triggered "Trigger A" to
             * be "Trigger A" as the user input.
             */
            synapse()->rivescript->say("{trigger} triggered a redirect to {$processedTrigger['redirect']}");

            $input = new Input($processedTrigger['redirect'], 0);
            $this->input = $input;
            synapse()->input = new Input($processedTrigger['redirect'], 0);

            $output .= $this->getResponse($processedTrigger['redirect']);
        }

        return $output;
    }

    /**
     * Parse the response through the available tags.
     *
     * @param string $response
     *
     * @return string
     */
    protected function parseResponse(string $response): string
    {
        synapse()->tags->each(
            function ($tag) use (&$response) {
                $class = "\\Axiom\\Rivescript\\Cortex\\Tags\\$tag";
                $tagClass = new $class();

                $response = $tagClass->parse($response, synapse()->input);
            }
        );

        return $response;
    }
}
