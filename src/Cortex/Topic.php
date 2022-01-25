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

use Axiom\Collections\Collection;

/**
 * Topic class
 *
 * Stores information about a topic.
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
class Topic
{
    /**
     * The name of this Topic.
     *
     * @var string
     */
    protected string $name;

    /**
     * The triggers for this Topic.
     *
     * @var Collection<string, mixed>
     */
    public Collection $triggers;

    /**
     * The responses for this Topic.
     *
     * @var Collection<string, mixed>
     */
    public Collection $responses;

    /**
     * This indicates if the > begin topic
     * is ok to finish.
     *
     * @var bool
     */
    public bool $ok = false;

    /**
     * Create a new Topic instance.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->triggers = new Collection([]);
        $this->responses = new Collection([]);
    }

    /**
     * Return triggers associated with this branch.
     *
     * @return Collection<string, mixed>
     */
    public function triggers(): Collection
    {
        return $this->triggers;
    }

    /**
     * Return the responses associated with this branch.
     *
     * @return Collection<string, mixed>
     */
    public function responses(): Collection
    {
        return $this->responses;
    }

    /**
     * Sort triggers based on type and word count from
     * largest to smallest.
     *
     * @param \Axiom\Collections\Collection $triggers
     *
     * @return \Axiom\Collections\Collection
     */
    public function sortTriggers(Collection $triggers): Collection
    {
        $triggers = $this->determineWordCount($triggers);
        $triggers = $this->determineTypeCount($triggers);

        return $triggers->sort(function ($current, $previous) {
            return ($current['order'] < $previous['order']) ? -1 : 1;
        })->reverse();
    }

    /**
     * Determine the order in the triggers.
     *
     * @param Collection<array> $triggers A collection of triggers.
     *
     * @return Collection<array>
     */
    protected function determineTypeCount(Collection $triggers): Collection
    {
        return $triggers->each(function ($data, $trigger) use ($triggers) {
            if (isset($data['type'])) {
                switch ($data['type']) {
                    case 'atomic':
                        $data['order'] += 4000000;
                        break;
                    case 'alphabetic':
                        $data['order'] += 3000000;
                        break;
                    case 'numeric':
                        $data['order'] += 2000000;
                        break;
                    case 'global':
                        $data['order'] += 1000000;
                        break;
                }

                $triggers->put($trigger, $data);
            }
        });
    }

    /**
     * Sort triggers based on word count from
     * largest to smallest.
     *
     * @param Collection<array> $triggers A collection of triggers.
     *
     * @return Collection<array>
     */
    protected function determineWordCount(Collection $triggers): Collection
    {
        return $triggers->each(function ($data, $trigger) use ($triggers) {
            $data['order'] = count(explode(' ', $trigger));

            $triggers->put($trigger, $data);
        });
    }

    public function isBegin(): bool
    {
        return ($this->name === "__begin__");
    }

    /**
     * Set the ok value.
     *
     * @param bool $value Ok value true or false.
     *
     * @return void
     */
    public function setOk(bool $value = false): void
    {
        $this->ok = $value;
    }

    /**
     * Indicate if this topic is ok to end
     * or not.
     *
     * Note: This is only used for the __begin__
     * topic.
     *
     * @return bool
     */
    public function isOk(): bool
    {
        return $this->ok;
    }
}
