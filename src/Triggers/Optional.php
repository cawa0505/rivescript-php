<?php

namespace Vulcan\Rivescript\Triggers;

use Vulcan\Rivescript\Contracts\Trigger;

class Optional implements Trigger
{
    /**
     * Parse the trigger.
     *
     * @param  integer  $key
     * @param  string  $trigger
     * @param  string  $message
     * @return array
     */
    public function parse($key, $trigger, $message)
    {
        @preg_match_all('/\[(.*?)\]/', $trigger, $optional);

        if (! empty($optional[0])) {
            foreach ($optional[0] as $optionKey => $option) {
                $search[]  = $option;
                $replace[] = '['.$optional[1][$optionKey].']?';
            }

            $parsedTrigger = str_replace($search, $replace, $trigger);

            print_r($parsedTrigger);
            echo "\n";

            if (@preg_match('#^'.$parsedTrigger.'$#', $message)) {
                return [
                    'match' => true,
                    'key'   => $key,
                    'data'  => []
                ];
            }
        }

        return ['match' => false];
    }
}
